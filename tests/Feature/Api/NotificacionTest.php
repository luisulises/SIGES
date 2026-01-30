<?php

namespace Tests\Feature\Api;

use App\Models\Role;
use App\Models\Sistema;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NotificacionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_crear_ticket_genera_notificacion_in_app(): void
    {
        $cliente = $this->makeUser(Role::CLIENTE_INTERNO);
        $coordinador = $this->makeUser(Role::COORDINADOR);

        $sistema = Sistema::create(['nombre' => 'Notificaciones', 'activo' => true]);
        DB::table('sistemas_coordinadores')->insert([
            'sistema_id' => $sistema->id,
            'usuario_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sanctum::actingAs($cliente);

        $this->postJson('/api/tickets', [
            'asunto' => 'Ticket con notificacion',
            'sistema_id' => $sistema->id,
            'descripcion' => 'Detalle',
        ])->assertCreated();

        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $cliente->id,
            'tipo_evento' => 'creacion',
            'canal' => 'in_app',
        ]);

        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $coordinador->id,
            'tipo_evento' => 'creacion',
            'canal' => 'in_app',
        ]);
    }

    public function test_endpoint_lista_y_marca_leidas(): void
    {
        $cliente = $this->makeUser(Role::CLIENTE_INTERNO);
        $sistema = Sistema::create(['nombre' => 'Notificaciones 2', 'activo' => true]);

        $ticket = Ticket::create([
            'asunto' => 'T',
            'descripcion' => 'D',
            'solicitante_id' => $cliente->id,
            'sistema_id' => $sistema->id,
            'estado_id' => DB::table('estados_ticket')->where('nombre', 'Nuevo')->value('id'),
            'responsable_actual_id' => null,
            'interno' => false,
        ]);

        DB::table('notificaciones')->insert([
            'usuario_id' => $cliente->id,
            'ticket_id' => $ticket->id,
            'tipo_evento' => 'cambio_estado',
            'canal' => 'in_app',
            'leido_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sanctum::actingAs($cliente);

        $list = $this->getJson('/api/notificaciones')->assertOk();
        $this->assertSame(1, $list->json('meta.unread_count'));
        $this->assertNotEmpty($list->json('data'));

        $id = $list->json('data.0.id');

        $this->postJson("/api/notificaciones/{$id}/leer")->assertOk();

        $this->assertDatabaseHas('notificaciones', [
            'id' => $id,
        ]);

        $after = $this->getJson('/api/notificaciones')->assertOk();
        $this->assertSame(0, $after->json('meta.unread_count'));
    }

    private function makeUser(string $rolNombre): User
    {
        $rolId = Role::query()->where('nombre', $rolNombre)->value('id');

        return User::factory()->create([
            'rol_id' => $rolId,
            'activo' => true,
        ]);
    }
}

