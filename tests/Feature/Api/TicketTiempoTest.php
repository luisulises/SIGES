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

class TicketTiempoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_soporte_asignado_puede_registrar_y_listar_tiempo(): void
    {
        $soporte = $this->makeUser(Role::SOPORTE);
        $sistema = Sistema::create(['nombre' => 'Tiempo', 'activo' => true]);
        $ticket = $this->makeTicket($sistema->id, [
            'responsable_actual_id' => $soporte->id,
        ]);

        Sanctum::actingAs($soporte);

        $this->postJson("/api/tickets/{$ticket->id}/tiempo", [
            'minutos' => 30,
            'nota' => 'Diagnostico.',
        ])->assertCreated();

        $this->getJson("/api/tickets/{$ticket->id}/tiempo")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.minutos', 30);
    }

    public function test_cliente_interno_no_puede_ver_ni_registrar_tiempo(): void
    {
        $cliente = $this->makeUser(Role::CLIENTE_INTERNO);
        $sistema = Sistema::create(['nombre' => 'Cliente', 'activo' => true]);
        $ticket = $this->makeTicket($sistema->id, [
            'solicitante_id' => $cliente->id,
        ]);

        Sanctum::actingAs($cliente);

        $this->postJson("/api/tickets/{$ticket->id}/tiempo", [
            'minutos' => 15,
        ])->assertStatus(403);

        $this->getJson("/api/tickets/{$ticket->id}/tiempo")->assertStatus(403);
    }

    public function test_soporte_no_asignado_no_puede_registrar_tiempo(): void
    {
        $soporte = $this->makeUser(Role::SOPORTE);
        $sistema = Sistema::create(['nombre' => 'No asignado', 'activo' => true]);
        $ticket = $this->makeTicket($sistema->id);

        Sanctum::actingAs($soporte);

        $this->postJson("/api/tickets/{$ticket->id}/tiempo", [
            'minutos' => 10,
        ])->assertStatus(403);
    }

    public function test_coordinador_de_sistema_puede_registrar_tiempo(): void
    {
        $coordinador = $this->makeUser(Role::COORDINADOR);
        $sistema = Sistema::create(['nombre' => 'Coord', 'activo' => true]);
        DB::table('sistemas_coordinadores')->insert([
            'sistema_id' => $sistema->id,
            'usuario_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $ticket = $this->makeTicket($sistema->id);

        Sanctum::actingAs($coordinador);

        $this->postJson("/api/tickets/{$ticket->id}/tiempo", [
            'minutos' => 45,
        ])->assertCreated();

        $this->getJson("/api/tickets/{$ticket->id}/tiempo")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.minutos', 45);
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
    private function makeTicket(int $sistemaId, array $overrides = []): Ticket
    {
        $estadoId = EstadoTicket::query()->where('nombre', EstadoTicket::NUEVO)->value('id');
        $solicitanteId = $overrides['solicitante_id'] ?? $this->makeUser(Role::CLIENTE_INTERNO)->id;

        return Ticket::create(array_merge([
            'asunto' => 'Tiempo',
            'descripcion' => 'Detalle',
            'solicitante_id' => $solicitanteId,
            'sistema_id' => $sistemaId,
            'estado_id' => $estadoId,
            'responsable_actual_id' => null,
            'interno' => false,
        ], $overrides));
    }
}

