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

class TicketInvolucradoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_coordinador_puede_agregar_y_remover_involucrados_con_soft_delete_y_restaurar(): void
    {
        $coordinador = $this->makeUser(Role::COORDINADOR);
        $sistema = Sistema::create(['nombre' => 'Modulo', 'activo' => true]);
        DB::table('sistemas_coordinadores')->insert([
            'sistema_id' => $sistema->id,
            'usuario_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ticket = $this->makeTicket(['sistema_id' => $sistema->id]);
        $usuario = $this->makeUser(Role::SOPORTE);

        Sanctum::actingAs($coordinador);

        $this->postJson("/api/tickets/{$ticket->id}/involucrados", [
            'usuario_id' => $usuario->id,
        ])->assertCreated();

        $this->assertDatabaseHas('involucrados_ticket', [
            'ticket_id' => $ticket->id,
            'usuario_id' => $usuario->id,
            'deleted_at' => null,
        ]);

        $this->deleteJson("/api/tickets/{$ticket->id}/involucrados/{$usuario->id}")
            ->assertNoContent();

        $this->assertSoftDeleted('involucrados_ticket', [
            'ticket_id' => $ticket->id,
            'usuario_id' => $usuario->id,
        ]);

        $this->postJson("/api/tickets/{$ticket->id}/involucrados", [
            'usuario_id' => $usuario->id,
        ])->assertOk();

        $this->assertDatabaseHas('involucrados_ticket', [
            'ticket_id' => $ticket->id,
            'usuario_id' => $usuario->id,
            'deleted_at' => null,
        ]);
    }

    public function test_no_permite_agregar_involucrado_inactivo(): void
    {
        $coordinador = $this->makeUser(Role::COORDINADOR);
        $sistema = Sistema::create(['nombre' => 'Involucrados inactivos', 'activo' => true]);
        DB::table('sistemas_coordinadores')->insert([
            'sistema_id' => $sistema->id,
            'usuario_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ticket = $this->makeTicket(['sistema_id' => $sistema->id]);
        $usuarioInactivo = $this->makeUser(Role::SOPORTE);
        $usuarioInactivo->update([
            'activo' => false,
            'desactivado_at' => now(),
        ]);

        Sanctum::actingAs($coordinador);

        $this->postJson("/api/tickets/{$ticket->id}/involucrados", [
            'usuario_id' => $usuarioInactivo->id,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['usuario_id']);
    }

    public function test_involucrado_soft_deleted_no_da_visibilidad(): void
    {
        $soporte = $this->makeUser(Role::SOPORTE);
        $cliente = $this->makeUser(Role::CLIENTE_INTERNO);
        $ticket = $this->makeTicket([
            'solicitante_id' => $cliente->id,
            'responsable_actual_id' => null,
        ]);

        DB::table('involucrados_ticket')->insert([
            'ticket_id' => $ticket->id,
            'usuario_id' => $soporte->id,
            'agregado_por_id' => $cliente->id,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        Sanctum::actingAs($soporte);

        $ids = $this->getJson('/api/tickets')
            ->assertOk()
            ->json('data.*.id');

        $this->assertContains($ticket->id, $ids);

        DB::table('involucrados_ticket')
            ->where('ticket_id', $ticket->id)
            ->where('usuario_id', $soporte->id)
            ->update(['deleted_at' => now(), 'updated_at' => now()]);

        $idsAfterDelete = $this->getJson('/api/tickets')
            ->assertOk()
            ->json('data.*.id');

        $this->assertNotContains($ticket->id, $idsAfterDelete);
    }

    private function makeUser(string $rolNombre): User
    {
        $rolId = Role::query()->where('nombre', $rolNombre)->value('id');

        return User::factory()->create([
            'rol_id' => $rolId,
        ]);
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function makeTicket(array $overrides = []): Ticket
    {
        $sistemaId = $overrides['sistema_id'] ?? Sistema::create([
            'nombre' => fake()->unique()->word(),
            'activo' => true,
        ])->id;
        $estadoId = EstadoTicket::query()->where('nombre', EstadoTicket::NUEVO)->value('id');
        $solicitanteId = $overrides['solicitante_id'] ?? $this->makeUser(Role::CLIENTE_INTERNO)->id;

        return Ticket::create(array_merge([
            'asunto' => 'Asunto',
            'descripcion' => 'Descripcion',
            'solicitante_id' => $solicitanteId,
            'sistema_id' => $sistemaId,
            'estado_id' => $estadoId,
            'responsable_actual_id' => null,
            'interno' => false,
        ], $overrides));
    }
}
