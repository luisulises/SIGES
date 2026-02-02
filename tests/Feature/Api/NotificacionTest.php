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

    public function test_asignacion_genera_notificacion_in_app(): void
    {
        $cliente = $this->makeUser(Role::CLIENTE_INTERNO);
        $coordinador = $this->makeUser(Role::COORDINADOR);
        $soporte = $this->makeUser(Role::SOPORTE);

        $sistema = Sistema::create(['nombre' => 'Asignacion', 'activo' => true]);
        DB::table('sistemas_coordinadores')->insert([
            'sistema_id' => $sistema->id,
            'usuario_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sanctum::actingAs($cliente);
        $ticketResponse = $this->postJson('/api/tickets', [
            'asunto' => 'Ticket asignacion',
            'sistema_id' => $sistema->id,
            'descripcion' => 'Detalle',
        ])->assertCreated();

        $ticketId = $ticketResponse->json('data.id');

        Sanctum::actingAs($coordinador);
        $this->patchJson("/api/tickets/{$ticketId}/operativo", [
            'responsable_id' => $soporte->id,
        ])->assertOk();

        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $soporte->id,
            'ticket_id' => $ticketId,
            'tipo_evento' => 'asignacion',
            'canal' => 'in_app',
        ]);
    }

    public function test_cambio_estado_genera_notificacion_in_app(): void
    {
        $cliente = $this->makeUser(Role::CLIENTE_INTERNO);
        $coordinador = $this->makeUser(Role::COORDINADOR);

        $sistema = Sistema::create(['nombre' => 'Estado', 'activo' => true]);
        DB::table('sistemas_coordinadores')->insert([
            'sistema_id' => $sistema->id,
            'usuario_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sanctum::actingAs($cliente);
        $ticketResponse = $this->postJson('/api/tickets', [
            'asunto' => 'Ticket estado',
            'sistema_id' => $sistema->id,
            'descripcion' => 'Detalle',
        ])->assertCreated();

        $ticketId = $ticketResponse->json('data.id');

        Sanctum::actingAs($coordinador);
        $this->postJson("/api/tickets/{$ticketId}/estado", [
            'estado' => 'En analisis',
        ])->assertOk();

        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $cliente->id,
            'ticket_id' => $ticketId,
            'tipo_evento' => 'cambio_estado',
            'canal' => 'in_app',
        ]);
    }

    public function test_comentario_publico_genera_notificacion_y_excluye_usuarios_inactivos(): void
    {
        $cliente = $this->makeUser(Role::CLIENTE_INTERNO);
        $coordinador = $this->makeUser(Role::COORDINADOR);
        $soporte = $this->makeUser(Role::SOPORTE);
        $inactivo = $this->makeInactiveUser(Role::CLIENTE_INTERNO);

        $sistema = Sistema::create(['nombre' => 'Comentario', 'activo' => true]);
        DB::table('sistemas_coordinadores')->insert([
            'sistema_id' => $sistema->id,
            'usuario_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sanctum::actingAs($cliente);
        $ticketResponse = $this->postJson('/api/tickets', [
            'asunto' => 'Ticket comentario',
            'sistema_id' => $sistema->id,
            'descripcion' => 'Detalle',
        ])->assertCreated();

        $ticketId = $ticketResponse->json('data.id');

        Sanctum::actingAs($coordinador);
        $this->patchJson("/api/tickets/{$ticketId}/operativo", [
            'responsable_id' => $soporte->id,
        ])->assertOk();

        DB::table('involucrados_ticket')->insert([
            'ticket_id' => $ticketId,
            'usuario_id' => $inactivo->id,
            'agregado_por_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        Sanctum::actingAs($soporte);
        $this->postJson("/api/tickets/{$ticketId}/comentarios", [
            'cuerpo' => 'Comentario publico',
            'visibilidad' => 'publico',
        ])->assertCreated();

        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $cliente->id,
            'ticket_id' => $ticketId,
            'tipo_evento' => 'comentario_publico',
            'canal' => 'in_app',
        ]);

        $this->assertDatabaseMissing('notificaciones', [
            'usuario_id' => $inactivo->id,
            'ticket_id' => $ticketId,
            'tipo_evento' => 'comentario_publico',
            'canal' => 'in_app',
        ]);
    }

    public function test_cierre_y_cancelacion_generan_notificacion_in_app(): void
    {
        $cliente = $this->makeUser(Role::CLIENTE_INTERNO);
        $coordinador = $this->makeUser(Role::COORDINADOR);

        $sistema = Sistema::create(['nombre' => 'Cierre', 'activo' => true]);
        DB::table('sistemas_coordinadores')->insert([
            'sistema_id' => $sistema->id,
            'usuario_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ticket = Ticket::create([
            'asunto' => 'Ticket cierre',
            'descripcion' => 'Detalle',
            'solicitante_id' => $cliente->id,
            'sistema_id' => $sistema->id,
            'estado_id' => DB::table('estados_ticket')->where('nombre', EstadoTicket::RESUELTO)->value('id'),
            'responsable_actual_id' => null,
            'interno' => false,
            'resolucion' => 'Listo.',
        ]);

        Sanctum::actingAs($cliente);
        $this->postJson("/api/tickets/{$ticket->id}/cerrar")->assertOk();

        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $cliente->id,
            'ticket_id' => $ticket->id,
            'tipo_evento' => 'cierre',
            'canal' => 'in_app',
        ]);

        $ticket2 = Ticket::create([
            'asunto' => 'Ticket cancelacion',
            'descripcion' => 'Detalle',
            'solicitante_id' => $cliente->id,
            'sistema_id' => $sistema->id,
            'estado_id' => DB::table('estados_ticket')->where('nombre', EstadoTicket::NUEVO)->value('id'),
            'responsable_actual_id' => null,
            'interno' => false,
        ]);

        $this->postJson("/api/tickets/{$ticket2->id}/cancelar")->assertOk();

        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $cliente->id,
            'ticket_id' => $ticket2->id,
            'tipo_evento' => 'cancelacion',
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

    public function test_no_exponer_notificaciones_de_ticket_interno_a_cliente(): void
    {
        $cliente = $this->makeUser(Role::CLIENTE_INTERNO);
        $coordinador = $this->makeUser(Role::COORDINADOR);
        $admin = $this->makeUser(Role::ADMIN);

        $sistema = Sistema::create(['nombre' => 'Internos notificaciones', 'activo' => true]);
        DB::table('sistemas_coordinadores')->insert([
            'sistema_id' => $sistema->id,
            'usuario_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sanctum::actingAs($cliente);
        $ticketResponse = $this->postJson('/api/tickets', [
            'asunto' => 'Ticket que luego sera interno',
            'sistema_id' => $sistema->id,
            'descripcion' => 'Detalle',
        ])->assertCreated();

        $ticketId = $ticketResponse->json('data.id');

        $before = $this->getJson('/api/notificaciones')->assertOk();
        $this->assertSame(1, $before->json('meta.unread_count'));
        $this->assertNotEmpty($before->json('data'));
        $notificacionId = $before->json('data.0.id');

        Sanctum::actingAs($admin);
        $this->patchJson("/api/tickets/{$ticketId}/operativo", [
            'interno' => true,
        ])->assertOk();

        Sanctum::actingAs($cliente);
        $after = $this->getJson('/api/notificaciones')->assertOk();
        $this->assertSame(0, $after->json('meta.unread_count'));
        $this->assertEmpty($after->json('data'));

        $mark = $this->postJson("/api/notificaciones/{$notificacionId}/leer")->assertOk();
        $this->assertNull($mark->json('data.ticket'));
    }

    private function makeUser(string $rolNombre): User
    {
        $rolId = Role::query()->where('nombre', $rolNombre)->value('id');

        return User::factory()->create([
            'rol_id' => $rolId,
            'activo' => true,
        ]);
    }

    private function makeInactiveUser(string $rolNombre): User
    {
        $rolId = Role::query()->where('nombre', $rolNombre)->value('id');

        return User::factory()->create([
            'rol_id' => $rolId,
            'activo' => false,
        ]);
    }
}
