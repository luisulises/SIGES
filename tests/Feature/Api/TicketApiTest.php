<?php

namespace Tests\Feature\Api;

use App\Models\EstadoTicket;
use App\Models\Role;
use App\Models\Sistema;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TicketApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_login_returns_token_and_allows_access(): void
    {
        $user = User::factory()->create([
            'email' => 'cliente@example.com',
            'password' => Hash::make('secret'),
            'rol_id' => $this->roleId(Role::CLIENTE_INTERNO),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'token_type']);

        $token = $response->json('token');

        $this->getJson('/api/tickets', [
            'Authorization' => 'Bearer '.$token,
        ])->assertOk();
    }

    public function test_crear_ticket_asigna_estado_nuevo_y_solicitante(): void
    {
        $user = User::factory()->create([
            'rol_id' => $this->roleId(Role::CLIENTE_INTERNO),
        ]);
        $sistema = Sistema::create(['nombre' => 'Core', 'activo' => true]);
        $estadoNuevo = EstadoTicket::where('nombre', EstadoTicket::NUEVO)->firstOrFail();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/tickets', [
            'asunto' => 'Falla de impresion',
            'descripcion' => 'La impresora no responde.',
            'sistema_id' => $sistema->id,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.estado_id', $estadoNuevo->id)
            ->assertJsonPath('data.responsable_actual_id', null)
            ->assertJsonPath('data.solicitante_id', $user->id)
            ->assertJsonPath('data.interno', false);
    }

    public function test_no_permite_crear_ticket_con_sistema_inactivo(): void
    {
        $user = User::factory()->create([
            'rol_id' => $this->roleId(Role::CLIENTE_INTERNO),
        ]);
        $sistemaInactivo = Sistema::create(['nombre' => 'Inactivo', 'activo' => false]);

        Sanctum::actingAs($user);

        $this->postJson('/api/tickets', [
            'asunto' => 'Ticket con sistema inactivo',
            'descripcion' => 'No deberia permitir.',
            'sistema_id' => $sistemaInactivo->id,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['sistema_id']);
    }

    public function test_update_rechaza_cambio_de_asunto_o_descripcion(): void
    {
        $user = User::factory()->create([
            'rol_id' => $this->roleId(Role::CLIENTE_INTERNO),
        ]);
        $ticket = $this->makeTicket(['solicitante_id' => $user->id]);

        Sanctum::actingAs($user);

        $this->patchJson("/api/tickets/{$ticket->id}", [
            'asunto' => 'Nuevo asunto',
        ])->assertStatus(422);
    }

    public function test_cliente_interno_solo_ve_sus_tickets_no_internos(): void
    {
        $cliente = User::factory()->create([
            'rol_id' => $this->roleId(Role::CLIENTE_INTERNO),
        ]);
        $otro = User::factory()->create([
            'rol_id' => $this->roleId(Role::CLIENTE_INTERNO),
        ]);

        $visible = $this->makeTicket([
            'solicitante_id' => $cliente->id,
            'interno' => false,
        ]);
        $this->makeTicket([
            'solicitante_id' => $cliente->id,
            'interno' => true,
        ]);
        $internoAjeno = $this->makeTicket([
            'solicitante_id' => $otro->id,
            'interno' => true,
        ]);

        DB::table('involucrados_ticket')->insert([
            'ticket_id' => $internoAjeno->id,
            'usuario_id' => $cliente->id,
            'agregado_por_id' => $otro->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sanctum::actingAs($cliente);

        $response = $this->getJson('/api/tickets');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $visible->id);
    }

    public function test_soporte_ve_asignados_o_involucrados(): void
    {
        $soporte = User::factory()->create([
            'rol_id' => $this->roleId(Role::SOPORTE),
        ]);
        $otro = User::factory()->create([
            'rol_id' => $this->roleId(Role::CLIENTE_INTERNO),
        ]);

        $asignado = $this->makeTicket([
            'responsable_actual_id' => $soporte->id,
        ]);
        $involucrado = $this->makeTicket([
            'responsable_actual_id' => null,
            'solicitante_id' => $otro->id,
        ]);
        $noVisible = $this->makeTicket([
            'responsable_actual_id' => null,
            'solicitante_id' => $otro->id,
        ]);

        DB::table('involucrados_ticket')->insert([
            'ticket_id' => $involucrado->id,
            'usuario_id' => $soporte->id,
            'agregado_por_id' => $otro->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sanctum::actingAs($soporte);

        $ids = $this->getJson('/api/tickets')
            ->assertOk()
            ->json('data.*.id');

        $this->assertContains($asignado->id, $ids);
        $this->assertContains($involucrado->id, $ids);
        $this->assertNotContains($noVisible->id, $ids);
    }

    public function test_coordinador_ve_tickets_de_sus_sistemas_o_involucrados(): void
    {
        $coordinador = User::factory()->create([
            'rol_id' => $this->roleId(Role::COORDINADOR),
        ]);
        $otro = User::factory()->create([
            'rol_id' => $this->roleId(Role::CLIENTE_INTERNO),
        ]);

        $sistemaPropio = Sistema::create(['nombre' => 'App 1', 'activo' => true]);
        $sistemaAjeno = Sistema::create(['nombre' => 'App 2', 'activo' => true]);

        DB::table('sistemas_coordinadores')->insert([
            'sistema_id' => $sistemaPropio->id,
            'usuario_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $propio = $this->makeTicket([
            'sistema_id' => $sistemaPropio->id,
        ]);
        $involucrado = $this->makeTicket([
            'sistema_id' => $sistemaAjeno->id,
        ]);
        $noVisible = $this->makeTicket([
            'sistema_id' => $sistemaAjeno->id,
        ]);

        DB::table('involucrados_ticket')->insert([
            'ticket_id' => $involucrado->id,
            'usuario_id' => $coordinador->id,
            'agregado_por_id' => $otro->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sanctum::actingAs($coordinador);

        $ids = $this->getJson('/api/tickets')
            ->assertOk()
            ->json('data.*.id');

        $this->assertContains($propio->id, $ids);
        $this->assertContains($involucrado->id, $ids);
        $this->assertNotContains($noVisible->id, $ids);
    }

    public function test_admin_ve_todo_y_ordenado_por_updated_at_desc(): void
    {
        $admin = User::factory()->create([
            'rol_id' => $this->roleId(Role::ADMIN),
        ]);

        $primero = $this->makeTicket();
        $segundo = $this->makeTicket();
        $primero->update(['updated_at' => now()->addMinutes(5)]);

        Sanctum::actingAs($admin);

        $ids = $this->getJson('/api/tickets')
            ->assertOk()
            ->json('data.*.id');

        $this->assertSame([$primero->id, $segundo->id], $ids);
    }

    private function roleId(string $nombre): int
    {
        return Role::query()->where('nombre', $nombre)->value('id');
    }

    private function makeTicket(array $overrides = []): Ticket
    {
        $sistema = $overrides['sistema_id'] ?? Sistema::create(['nombre' => fake()->unique()->word(), 'activo' => true])->id;
        $estado = EstadoTicket::where('nombre', EstadoTicket::NUEVO)->firstOrFail();
        $solicitanteId = $overrides['solicitante_id'] ?? User::factory()->create([
            'rol_id' => $this->roleId(Role::CLIENTE_INTERNO),
        ])->id;

        return Ticket::create(array_merge([
            'asunto' => 'Asunto base',
            'descripcion' => 'Detalle base',
            'solicitante_id' => $solicitanteId,
            'sistema_id' => $sistema,
            'estado_id' => $estado->id,
            'responsable_actual_id' => null,
            'interno' => false,
        ], $overrides));
    }
}
