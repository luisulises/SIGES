<?php

namespace Tests\Feature\Api;

use App\Models\EstadoTicket;
use App\Models\Role;
use App\Models\Sistema;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TicketHistorialTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_admin_puede_ver_historial_completo(): void
    {
        $admin = $this->makeUser(Role::ADMIN);
        $cliente = $this->makeUser(Role::CLIENTE_INTERNO);
        $coordinador = $this->makeUser(Role::COORDINADOR);
        $soporte = $this->makeUser(Role::SOPORTE);

        $sistema = Sistema::create(['nombre' => 'Historial', 'activo' => true]);
        DB::table('sistemas_coordinadores')->insert([
            'sistema_id' => $sistema->id,
            'usuario_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ticket = $this->makeTicket($sistema->id, [
            'solicitante_id' => $cliente->id,
        ]);

        $prioridadId = DB::table('prioridades')->insertGetId([
            'nombre' => 'Alta',
            'orden' => 1,
            'activo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $tipoId = DB::table('tipos_solicitud')->insertGetId([
            'nombre' => 'Incidente',
            'activo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sanctum::actingAs($coordinador);
        $this->patchJson("/api/tickets/{$ticket->id}/operativo", [
            'responsable_id' => $soporte->id,
            'prioridad_id' => $prioridadId,
            'fecha_compromiso' => now()->addDay()->toISOString(),
        ])->assertOk();

        $this->postJson("/api/tickets/{$ticket->id}/estado", [
            'estado' => EstadoTicket::EN_ANALISIS,
        ])->assertOk();

        $this->postJson("/api/tickets/{$ticket->id}/estado", [
            'estado' => EstadoTicket::ASIGNADO,
        ])->assertOk();

        Sanctum::actingAs($soporte);
        $this->postJson("/api/tickets/{$ticket->id}/estado", [
            'estado' => EstadoTicket::EN_PROGRESO,
        ])->assertOk();

        $this->patchJson("/api/tickets/{$ticket->id}/operativo", [
            'tipo_solicitud_id' => $tipoId,
            'fecha_entrega' => now()->toISOString(),
            'resolucion' => 'Se aplico fix.',
        ])->assertOk();

        $this->postJson("/api/tickets/{$ticket->id}/estado", [
            'estado' => EstadoTicket::RESUELTO,
        ])->assertOk();

        Sanctum::actingAs($cliente);
        $this->postJson("/api/tickets/{$ticket->id}/cerrar")->assertOk();

        Sanctum::actingAs($admin);
        $response = $this->getJson("/api/tickets/{$ticket->id}/historial");

        $response->assertOk();
        $events = $response->json('data');
        $this->assertNotEmpty($events);
        $this->assertTrue(
            collect($events)->pluck('tipo_evento')->contains('asignacion_cambiada')
        );
        $this->assertTrue(
            collect($events)->pluck('tipo_evento')->contains('prioridad_cambiada')
        );
        $this->assertTrue(
            collect($events)->pluck('tipo_evento')->contains('estado_cambiado')
        );
        $this->assertTrue(
            collect($events)->pluck('tipo_evento')->contains('cierre')
        );
    }

    public function test_admin_ve_eventos_de_comentario_y_adjunto(): void
    {
        Storage::fake();

        $admin = $this->makeUser(Role::ADMIN);
        $cliente = $this->makeUser(Role::CLIENTE_INTERNO);

        $sistema = Sistema::create(['nombre' => 'Historial comentarios', 'activo' => true]);
        $ticket = $this->makeTicket($sistema->id, [
            'solicitante_id' => $cliente->id,
        ]);

        Sanctum::actingAs($cliente);
        $comentarioResponse = $this->postJson("/api/tickets/{$ticket->id}/comentarios", [
            'cuerpo' => 'Comentario publico',
            'visibilidad' => 'publico',
        ])->assertCreated();

        $comentarioId = $comentarioResponse->json('data.id');

        $file = UploadedFile::fake()->create('evidencia.txt', 10, 'text/plain');
        $this->postJson("/api/tickets/{$ticket->id}/adjuntos", [
            'archivo' => $file,
            'comentario_id' => $comentarioId,
        ])->assertCreated();

        Sanctum::actingAs($admin);
        $response = $this->getJson("/api/tickets/{$ticket->id}/historial");
        $response->assertOk();

        $tipos = collect($response->json('data'))->pluck('tipo_evento')->unique()->values();

        $this->assertTrue($tipos->contains('comentario_creado'));
        $this->assertTrue($tipos->contains('adjunto_creado'));
    }

    public function test_cliente_solo_ve_eventos_de_estado_y_cierre_cancelacion(): void
    {
        $cliente = $this->makeUser(Role::CLIENTE_INTERNO);
        $coordinador = $this->makeUser(Role::COORDINADOR);
        $soporte = $this->makeUser(Role::SOPORTE);

        $sistema = Sistema::create(['nombre' => 'Historial cliente', 'activo' => true]);
        DB::table('sistemas_coordinadores')->insert([
            'sistema_id' => $sistema->id,
            'usuario_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ticket = $this->makeTicket($sistema->id, [
            'solicitante_id' => $cliente->id,
        ]);

        $prioridadId = DB::table('prioridades')->insertGetId([
            'nombre' => 'Media',
            'orden' => 2,
            'activo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sanctum::actingAs($coordinador);
        $this->patchJson("/api/tickets/{$ticket->id}/operativo", [
            'responsable_id' => $soporte->id,
            'prioridad_id' => $prioridadId,
        ])->assertOk();

        $this->postJson("/api/tickets/{$ticket->id}/estado", [
            'estado' => EstadoTicket::EN_ANALISIS,
        ])->assertOk();

        $this->postJson("/api/tickets/{$ticket->id}/estado", [
            'estado' => EstadoTicket::ASIGNADO,
        ])->assertOk();

        Sanctum::actingAs($soporte);
        $this->postJson("/api/tickets/{$ticket->id}/estado", [
            'estado' => EstadoTicket::EN_PROGRESO,
        ])->assertOk();

        $this->patchJson("/api/tickets/{$ticket->id}/operativo", [
            'resolucion' => 'OK',
        ])->assertOk();

        $this->postJson("/api/tickets/{$ticket->id}/estado", [
            'estado' => EstadoTicket::RESUELTO,
        ])->assertOk();

        Sanctum::actingAs($cliente);
        $this->postJson("/api/tickets/{$ticket->id}/cerrar")->assertOk();

        $response = $this->getJson("/api/tickets/{$ticket->id}/historial");
        $response->assertOk();

        $tipos = collect($response->json('data'))->pluck('tipo_evento')->unique()->values();

        $this->assertTrue($tipos->contains('estado_cambiado'));
        $this->assertTrue($tipos->contains('cierre'));
        $this->assertFalse($tipos->contains('asignacion_cambiada'));
        $this->assertFalse($tipos->contains('prioridad_cambiada'));
        $this->assertFalse($tipos->contains('resolucion_registrada'));
    }

    public function test_usuario_sin_visibilidad_no_puede_ver_historial(): void
    {
        $clienteSolicitante = $this->makeUser(Role::CLIENTE_INTERNO);
        $otroCliente = $this->makeUser(Role::CLIENTE_INTERNO);

        $sistema = Sistema::create(['nombre' => 'Privado', 'activo' => true]);
        $ticket = $this->makeTicket($sistema->id, [
            'solicitante_id' => $clienteSolicitante->id,
        ]);

        Sanctum::actingAs($otroCliente);
        $this->getJson("/api/tickets/{$ticket->id}/historial")->assertStatus(403);
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
            'asunto' => 'Historial',
            'descripcion' => 'Detalle',
            'solicitante_id' => $solicitanteId,
            'sistema_id' => $sistemaId,
            'estado_id' => $estadoId,
            'responsable_actual_id' => null,
            'interno' => false,
        ], $overrides));
    }
}
