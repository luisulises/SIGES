<?php

namespace Tests\Feature\Api;

use App\Models\EstadoTicket;
use App\Models\Prioridad;
use App\Models\Role;
use App\Models\Sistema;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TicketBusquedaMetricasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_solo_admin_o_coordinador_puede_buscar_y_ver_metricas(): void
    {
        $cliente = User::factory()->create([
            'rol_id' => $this->roleId(Role::CLIENTE_INTERNO),
        ]);

        Sanctum::actingAs($cliente);

        $this->getJson('/api/tickets/busqueda')->assertStatus(403);
        $this->getJson('/api/tickets/metricas')->assertStatus(403);
    }

    public function test_busqueda_y_metricas_respetan_visibilidad_de_coordinador(): void
    {
        $coordinador = User::factory()->create([
            'rol_id' => $this->roleId(Role::COORDINADOR),
        ]);

        $sistemaPropio = Sistema::create(['nombre' => 'Sistema A', 'activo' => true]);
        $sistemaAjeno = Sistema::create(['nombre' => 'Sistema B', 'activo' => true]);

        DB::table('sistemas_coordinadores')->insert([
            'sistema_id' => $sistemaPropio->id,
            'usuario_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $estadoNuevoId = (int) EstadoTicket::query()->where('nombre', EstadoTicket::NUEVO)->value('id');
        $estadoResueltoId = (int) EstadoTicket::query()->where('nombre', EstadoTicket::RESUELTO)->value('id');

        $prioridad = Prioridad::create([
            'nombre' => 'Alta',
            'orden' => 1,
            'activo' => true,
        ]);

        $ticketPropio = Ticket::create([
            'asunto' => 'Alpha',
            'descripcion' => 'Desc',
            'solicitante_id' => $coordinador->id,
            'sistema_id' => $sistemaPropio->id,
            'estado_id' => $estadoNuevoId,
            'responsable_actual_id' => null,
            'interno' => false,
            'prioridad_id' => $prioridad->id,
        ]);

        $ticketAjeno = Ticket::create([
            'asunto' => 'Beta',
            'descripcion' => 'Desc',
            'solicitante_id' => $coordinador->id,
            'sistema_id' => $sistemaAjeno->id,
            'estado_id' => $estadoResueltoId,
            'responsable_actual_id' => null,
            'interno' => false,
            'prioridad_id' => null,
        ]);

        DB::table('involucrados_ticket')->insert([
            'ticket_id' => $ticketAjeno->id,
            'usuario_id' => $coordinador->id,
            'agregado_por_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        Sanctum::actingAs($coordinador);

        $ids = $this->getJson('/api/tickets/busqueda')
            ->assertOk()
            ->json('data.*.id');

        $this->assertContains($ticketPropio->id, $ids);
        $this->assertContains($ticketAjeno->id, $ids);

        $idsAlpha = $this->getJson('/api/tickets/busqueda?asunto=Alpha')
            ->assertOk()
            ->json('data.*.id');

        $this->assertSame([$ticketPropio->id], $idsAlpha);

        $idsAlphaLower = $this->getJson('/api/tickets/busqueda?asunto=alpha')
            ->assertOk()
            ->json('data.*.id');

        $this->assertSame([$ticketPropio->id], $idsAlphaLower);

        $metricas = $this->getJson('/api/tickets/metricas')
            ->assertOk()
            ->json('data');

        $this->assertSame(2, $metricas['total']);

        $porEstado = collect($metricas['por_estado']);
        $this->assertTrue($porEstado->contains(fn ($row) => (int) $row['estado_id'] === $estadoNuevoId && (int) $row['total'] === 1));
        $this->assertTrue($porEstado->contains(fn ($row) => (int) $row['estado_id'] === $estadoResueltoId && (int) $row['total'] === 1));

        $porPrioridad = collect($metricas['por_prioridad']);
        $this->assertTrue($porPrioridad->contains(fn ($row) => $row['prioridad'] === 'Alta' && (int) $row['total'] === 1));
        $this->assertTrue($porPrioridad->contains(fn ($row) => $row['prioridad'] === 'Sin prioridad' && (int) $row['total'] === 1));
    }

    private function roleId(string $nombre): int
    {
        return (int) Role::query()->where('nombre', $nombre)->value('id');
    }
}
