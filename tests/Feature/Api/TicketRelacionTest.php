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

class TicketRelacionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_admin_puede_crear_y_listar_relacion(): void
    {
        $admin = $this->makeUser(Role::ADMIN);
        $sistema = Sistema::create(['nombre' => 'Relaciones', 'activo' => true]);
        $ticketA = $this->makeTicket($sistema->id);
        $ticketB = $this->makeTicket($sistema->id);

        Sanctum::actingAs($admin);

        $this->postJson("/api/tickets/{$ticketA->id}/relaciones", [
            'ticket_relacionado_id' => $ticketB->id,
            'tipo_relacion' => 'relacionado',
        ])->assertCreated();

        $this->getJson("/api/tickets/{$ticketA->id}/relaciones")
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->getJson("/api/tickets/{$ticketB->id}/relaciones")
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_no_permite_auto_relacion_y_duplicados(): void
    {
        $admin = $this->makeUser(Role::ADMIN);
        $sistema = Sistema::create(['nombre' => 'Duplicados', 'activo' => true]);
        $ticketA = $this->makeTicket($sistema->id);
        $ticketB = $this->makeTicket($sistema->id);

        Sanctum::actingAs($admin);

        $this->postJson("/api/tickets/{$ticketA->id}/relaciones", [
            'ticket_relacionado_id' => $ticketA->id,
            'tipo_relacion' => 'relacionado',
        ])->assertStatus(422);

        $this->postJson("/api/tickets/{$ticketA->id}/relaciones", [
            'ticket_relacionado_id' => $ticketB->id,
            'tipo_relacion' => 'relacionado',
        ])->assertCreated();

        $this->postJson("/api/tickets/{$ticketA->id}/relaciones", [
            'ticket_relacionado_id' => $ticketB->id,
            'tipo_relacion' => 'relacionado',
        ])->assertStatus(422);
    }

    public function test_soporte_no_puede_marcar_duplicado(): void
    {
        $soporte = $this->makeUser(Role::SOPORTE);
        $sistema = Sistema::create(['nombre' => 'Soporte', 'activo' => true]);
        $ticketA = $this->makeTicket($sistema->id, [
            'responsable_actual_id' => $soporte->id,
        ]);
        $ticketB = $this->makeTicket($sistema->id, [
            'responsable_actual_id' => $soporte->id,
        ]);

        Sanctum::actingAs($soporte);

        $this->postJson("/api/tickets/{$ticketA->id}/relaciones", [
            'ticket_relacionado_id' => $ticketB->id,
            'tipo_relacion' => 'duplicado_de',
        ])->assertStatus(403);
    }

    public function test_coordinador_puede_marcar_duplicado_y_cancela_ticket(): void
    {
        $coordinador = $this->makeUser(Role::COORDINADOR);
        $sistema = Sistema::create(['nombre' => 'Coordinador', 'activo' => true]);
        DB::table('sistemas_coordinadores')->insert([
            'sistema_id' => $sistema->id,
            'usuario_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $estadoAnalisisId = EstadoTicket::query()->where('nombre', EstadoTicket::EN_ANALISIS)->value('id');

        $duplicado = $this->makeTicket($sistema->id, [
            'estado_id' => $estadoAnalisisId,
        ]);
        $valido = $this->makeTicket($sistema->id);

        Sanctum::actingAs($coordinador);

        $this->postJson("/api/tickets/{$duplicado->id}/relaciones", [
            'ticket_relacionado_id' => $valido->id,
            'tipo_relacion' => 'duplicado_de',
        ])->assertCreated();

        $duplicado->refresh();
        $estadoCanceladoId = EstadoTicket::query()->where('nombre', EstadoTicket::CANCELADO)->value('id');
        $this->assertSame($estadoCanceladoId, $duplicado->estado_id);
        $this->assertNotNull($duplicado->cancelado_at);

        $this->getJson("/api/tickets/{$valido->id}/relaciones")
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_cliente_no_ve_relacion_a_ticket_no_visible(): void
    {
        $admin = $this->makeUser(Role::ADMIN);
        $cliente = $this->makeUser(Role::CLIENTE_INTERNO);

        $sistema = Sistema::create(['nombre' => 'Visibilidad', 'activo' => true]);

        $ticketVisible = $this->makeTicket($sistema->id, [
            'solicitante_id' => $cliente->id,
            'interno' => false,
        ]);
        $ticketInterno = $this->makeTicket($sistema->id, [
            'interno' => true,
        ]);

        Sanctum::actingAs($admin);
        $this->postJson("/api/tickets/{$ticketVisible->id}/relaciones", [
            'ticket_relacionado_id' => $ticketInterno->id,
            'tipo_relacion' => 'relacionado',
        ])->assertCreated();

        Sanctum::actingAs($cliente);
        $this->getJson("/api/tickets/{$ticketVisible->id}/relaciones")
            ->assertOk()
            ->assertJsonCount(0, 'data');
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
        $estadoId = $overrides['estado_id'] ?? EstadoTicket::query()->where('nombre', EstadoTicket::NUEVO)->value('id');
        $solicitanteId = $overrides['solicitante_id'] ?? $this->makeUser(Role::CLIENTE_INTERNO)->id;

        return Ticket::create(array_merge([
            'asunto' => 'Relacion',
            'descripcion' => 'Detalle',
            'solicitante_id' => $solicitanteId,
            'sistema_id' => $sistemaId,
            'estado_id' => $estadoId,
            'responsable_actual_id' => null,
            'interno' => false,
        ], $overrides));
    }
}
