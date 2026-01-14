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

class TicketWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_soporte_puede_cambiar_estado_si_esta_asignado(): void
    {
        $soporte = $this->makeUser(Role::SOPORTE);
        $ticket = $this->makeTicket(EstadoTicket::ASIGNADO, [
            'responsable_actual_id' => $soporte->id,
        ]);

        Sanctum::actingAs($soporte);

        $response = $this->postJson("/api/tickets/{$ticket->id}/estado", [
            'estado' => EstadoTicket::EN_PROGRESO,
        ]);

        $response->assertOk();
        $this->assertSame(
            EstadoTicket::EN_PROGRESO,
            $ticket->fresh()->estado()->value('nombre')
        );
    }

    public function test_transicion_requiere_responsable_cuando_regla_lo_exige(): void
    {
        $coordinador = $this->makeUser(Role::COORDINADOR);
        $sistema = Sistema::create(['nombre' => 'Workflow', 'activo' => true]);
        DB::table('sistemas_coordinadores')->insert([
            'sistema_id' => $sistema->id,
            'usuario_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $ticket = $this->makeTicket(EstadoTicket::ASIGNADO, [
            'sistema_id' => $sistema->id,
            'responsable_actual_id' => null,
        ]);

        Sanctum::actingAs($coordinador);

        $this->postJson("/api/tickets/{$ticket->id}/estado", [
            'estado' => EstadoTicket::EN_PROGRESO,
        ])->assertStatus(422);
    }

    public function test_soporte_no_puede_cambiar_estado_si_no_esta_asignado(): void
    {
        $soporte = $this->makeUser(Role::SOPORTE);
        $ticket = $this->makeTicket(EstadoTicket::ASIGNADO);

        Sanctum::actingAs($soporte);

        $this->postJson("/api/tickets/{$ticket->id}/estado", [
            'estado' => EstadoTicket::EN_PROGRESO,
        ])->assertStatus(403);
    }

    public function test_cerrar_requiere_resuelto_y_resolucion(): void
    {
        $cliente = $this->makeUser(Role::CLIENTE_INTERNO);
        $ticket = $this->makeTicket(EstadoTicket::RESUELTO, [
            'solicitante_id' => $cliente->id,
        ]);

        Sanctum::actingAs($cliente);

        $this->postJson("/api/tickets/{$ticket->id}/cerrar")
            ->assertStatus(422);

        $ticket->resolucion = 'Se reinicio el servicio.';
        $ticket->save();

        $this->postJson("/api/tickets/{$ticket->id}/cerrar")
            ->assertOk();

        $ticket->refresh();
        $this->assertSame(EstadoTicket::CERRADO, $ticket->estado()->value('nombre'));
        $this->assertNotNull($ticket->cerrado_at);
    }

    public function test_cancelar_permite_solicitante(): void
    {
        $cliente = $this->makeUser(Role::CLIENTE_INTERNO);
        $ticket = $this->makeTicket(EstadoTicket::EN_ANALISIS, [
            'solicitante_id' => $cliente->id,
        ]);

        Sanctum::actingAs($cliente);

        $this->postJson("/api/tickets/{$ticket->id}/cancelar")
            ->assertOk();

        $ticket->refresh();
        $this->assertSame(EstadoTicket::CANCELADO, $ticket->estado()->value('nombre'));
        $this->assertNotNull($ticket->cancelado_at);
    }

    public function test_soporte_no_puede_cancelar_si_no_es_solicitante(): void
    {
        $soporte = $this->makeUser(Role::SOPORTE);
        $ticket = $this->makeTicket(EstadoTicket::EN_ANALISIS);

        Sanctum::actingAs($soporte);

        $this->postJson("/api/tickets/{$ticket->id}/cancelar")
            ->assertStatus(403);
    }

    private function makeUser(string $rolNombre): User
    {
        $rolId = Role::query()->where('nombre', $rolNombre)->value('id');

        return User::factory()->create([
            'rol_id' => $rolId,
        ]);
    }

    private function makeTicket(string $estadoNombre, array $overrides = []): Ticket
    {
        $estadoId = EstadoTicket::query()->where('nombre', $estadoNombre)->value('id');
        $sistemaId = $overrides['sistema_id'] ?? Sistema::create([
            'nombre' => fake()->unique()->word(),
            'activo' => true,
        ])->id;
        $solicitanteId = $overrides['solicitante_id'] ?? $this->makeUser(Role::CLIENTE_INTERNO)->id;

        return Ticket::create(array_merge([
            'asunto' => 'Estado cambio',
            'descripcion' => 'Detalle',
            'solicitante_id' => $solicitanteId,
            'sistema_id' => $sistemaId,
            'estado_id' => $estadoId,
            'responsable_actual_id' => null,
            'interno' => false,
        ], $overrides));
    }
}
