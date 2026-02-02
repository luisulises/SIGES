<?php

namespace Tests\Feature\Api;

use App\Models\EstadoTicket;
use App\Models\Role;
use App\Models\Sistema;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminUsuarioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_admin_puede_crear_editar_y_desactivar_usuario_y_cierra_asignacion_activa(): void
    {
        $admin = User::factory()->create([
            'rol_id' => $this->roleId(Role::ADMIN),
        ]);

        Sanctum::actingAs($admin);

        $createResponse = $this->postJson('/api/admin/usuarios', [
            'nombre' => 'Soporte Uno',
            'email' => 'soporte.uno@example.com',
            'rol_id' => $this->roleId(Role::SOPORTE),
            'password' => 'super-secret',
        ]);

        $createResponse->assertCreated()
            ->assertJsonPath('data.email', 'soporte.uno@example.com')
            ->assertJsonPath('data.activo', true);

        $usuarioId = (int) $createResponse->json('data.id');
        $usuario = User::query()->findOrFail($usuarioId);

        $this->patchJson("/api/admin/usuarios/{$usuario->id}", [
            'rol_id' => $this->roleId(Role::COORDINADOR),
        ])->assertOk()
            ->assertJsonPath('data.rol_id', $this->roleId(Role::COORDINADOR));

        $sistema = Sistema::create(['nombre' => 'Core', 'activo' => true]);
        $estadoNuevoId = EstadoTicket::query()->where('nombre', EstadoTicket::NUEVO)->value('id');

        $ticket = Ticket::create([
            'asunto' => 'Ticket asignado',
            'descripcion' => 'Desc',
            'solicitante_id' => $admin->id,
            'sistema_id' => $sistema->id,
            'estado_id' => $estadoNuevoId,
            'responsable_actual_id' => $usuario->id,
            'interno' => false,
        ]);

        DB::table('asignaciones_ticket')->insert([
            'ticket_id' => $ticket->id,
            'responsable_id' => $usuario->id,
            'asignado_por_id' => $admin->id,
            'asignado_at' => now(),
            'desasignado_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->patchJson("/api/admin/usuarios/{$usuario->id}", [
            'activo' => false,
        ])->assertOk()
            ->assertJsonPath('data.activo', false);

        $this->assertDatabaseHas('usuarios', [
            'id' => $usuario->id,
            'activo' => false,
        ]);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'responsable_actual_id' => null,
        ]);

        $this->assertDatabaseHas('asignaciones_ticket', [
            'ticket_id' => $ticket->id,
            'responsable_id' => $usuario->id,
        ]);

        $row = DB::table('asignaciones_ticket')
            ->where('ticket_id', $ticket->id)
            ->where('responsable_id', $usuario->id)
            ->first();

        $this->assertNotNull($row?->desasignado_at);
    }

    public function test_no_admin_no_puede_usar_endpoints_admin(): void
    {
        $coordinador = User::factory()->create([
            'rol_id' => $this->roleId(Role::COORDINADOR),
        ]);

        Sanctum::actingAs($coordinador);

        $this->getJson('/api/admin/usuarios')->assertStatus(403);
        $this->getJson('/api/admin/roles')->assertStatus(403);
    }

    public function test_usuario_inactivo_no_puede_operar_api(): void
    {
        $cliente = User::factory()->create([
            'rol_id' => $this->roleId(Role::CLIENTE_INTERNO),
            'activo' => false,
        ]);

        Sanctum::actingAs($cliente);

        $this->getJson('/api/tickets')
            ->assertStatus(403)
            ->assertJsonPath('message', 'Usuario inactivo.');
    }

    private function roleId(string $nombre): int
    {
        return (int) Role::query()->where('nombre', $nombre)->value('id');
    }
}

