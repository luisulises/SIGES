<?php

namespace Tests\Feature\Api;

use App\Models\ComentarioTicket;
use App\Models\EstadoTicket;
use App\Models\Role;
use App\Models\Sistema;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TicketComentarioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_cliente_interno_puede_crear_comentario_publico_en_su_ticket(): void
    {
        $cliente = $this->makeUser(Role::CLIENTE_INTERNO);
        $ticket = $this->makeTicket(['solicitante_id' => $cliente->id]);

        Sanctum::actingAs($cliente);

        $response = $this->postJson("/api/tickets/{$ticket->id}/comentarios", [
            'cuerpo' => 'Seguimiento del ticket.',
            'visibilidad' => 'publico',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.ticket_id', $ticket->id)
            ->assertJsonPath('data.autor.id', $cliente->id)
            ->assertJsonPath('data.visibilidad', 'publico')
            ->assertJsonPath('data.cuerpo', 'Seguimiento del ticket.');

        $this->assertDatabaseHas('comentarios_ticket', [
            'ticket_id' => $ticket->id,
            'autor_id' => $cliente->id,
            'visibilidad' => 'publico',
        ]);
    }

    public function test_cliente_interno_no_puede_crear_comentario_interno(): void
    {
        $cliente = $this->makeUser(Role::CLIENTE_INTERNO);
        $ticket = $this->makeTicket(['solicitante_id' => $cliente->id]);

        Sanctum::actingAs($cliente);

        $this->postJson("/api/tickets/{$ticket->id}/comentarios", [
            'cuerpo' => 'Esto no debería ser interno.',
            'visibilidad' => 'interno',
        ])->assertStatus(422)
            ->assertJsonPath('errors.visibilidad.0', 'No puedes crear comentarios internos.');
    }

    public function test_soporte_asignado_puede_crear_comentario_interno(): void
    {
        $soporte = $this->makeUser(Role::SOPORTE);
        $ticket = $this->makeTicket(['responsable_actual_id' => $soporte->id]);

        Sanctum::actingAs($soporte);

        $this->postJson("/api/tickets/{$ticket->id}/comentarios", [
            'cuerpo' => 'Nota interna para el equipo.',
            'visibilidad' => 'interno',
        ])->assertCreated()
            ->assertJsonPath('data.visibilidad', 'interno');
    }

    public function test_comentario_actualiza_updated_at_del_ticket(): void
    {
        $cliente = $this->makeUser(Role::CLIENTE_INTERNO);
        $ticket = $this->makeTicket(['solicitante_id' => $cliente->id]);

        DB::table('tickets')
            ->where('id', $ticket->id)
            ->update(['updated_at' => now()->subDay()]);
        $ticket->refresh();
        $before = $ticket->updated_at->copy();

        Sanctum::actingAs($cliente);

        $this->postJson("/api/tickets/{$ticket->id}/comentarios", [
            'cuerpo' => 'Actualiza timestamp.',
            'visibilidad' => 'publico',
        ])->assertCreated();

        $ticket->refresh();
        $this->assertTrue($ticket->updated_at->greaterThan($before));
    }

    public function test_cliente_interno_lista_solo_comentarios_publicos(): void
    {
        $cliente = $this->makeUser(Role::CLIENTE_INTERNO);
        $soporte = $this->makeUser(Role::SOPORTE);
        $ticket = $this->makeTicket([
            'solicitante_id' => $cliente->id,
            'responsable_actual_id' => $soporte->id,
        ]);

        $publico = ComentarioTicket::create([
            'ticket_id' => $ticket->id,
            'autor_id' => $cliente->id,
            'cuerpo' => 'Comentario público.',
            'visibilidad' => 'publico',
        ]);
        $interno = ComentarioTicket::create([
            'ticket_id' => $ticket->id,
            'autor_id' => $soporte->id,
            'cuerpo' => 'Comentario interno.',
            'visibilidad' => 'interno',
        ]);

        Sanctum::actingAs($cliente);

        $ids = $this->getJson("/api/tickets/{$ticket->id}/comentarios")
            ->assertOk()
            ->json('data.*.id');

        $this->assertContains($publico->id, $ids);
        $this->assertNotContains($interno->id, $ids);
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
