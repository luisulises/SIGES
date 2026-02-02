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

class TicketOperativoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_coordinador_puede_asignar_responsable_y_registrar_asignacion(): void
    {
        $coordinador = $this->makeUser(Role::COORDINADOR);
        $sistema = Sistema::create(['nombre' => 'Operaciones', 'activo' => true]);
        DB::table('sistemas_coordinadores')->insert([
            'sistema_id' => $sistema->id,
            'usuario_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $ticket = $this->makeTicket($sistema->id);
        $responsable = $this->makeUser(Role::SOPORTE);

        Sanctum::actingAs($coordinador);

        $this->patchJson("/api/tickets/{$ticket->id}/operativo", [
            'responsable_id' => $responsable->id,
        ])->assertOk();

        $ticket->refresh();
        $this->assertSame($responsable->id, $ticket->responsable_actual_id);

        $asignacion = DB::table('asignaciones_ticket')->where('ticket_id', $ticket->id)->first();
        $this->assertNotNull($asignacion);
        $this->assertSame($responsable->id, $asignacion->responsable_id);
        $this->assertSame($coordinador->id, $asignacion->asignado_por_id);
    }

    public function test_no_permite_asignar_responsable_que_no_es_soporte(): void
    {
        $coordinador = $this->makeUser(Role::COORDINADOR);
        $sistema = Sistema::create(['nombre' => 'Validacion responsable', 'activo' => true]);
        DB::table('sistemas_coordinadores')->insert([
            'sistema_id' => $sistema->id,
            'usuario_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $ticket = $this->makeTicket($sistema->id);
        $noSoporte = $this->makeUser(Role::CLIENTE_INTERNO);

        Sanctum::actingAs($coordinador);

        $this->patchJson("/api/tickets/{$ticket->id}/operativo", [
            'responsable_id' => $noSoporte->id,
        ])->assertStatus(422)->assertJsonValidationErrors(['responsable_id']);
    }

    public function test_no_permite_asignar_responsable_inactivo(): void
    {
        $coordinador = $this->makeUser(Role::COORDINADOR);
        $sistema = Sistema::create(['nombre' => 'Validacion responsable inactivo', 'activo' => true]);
        DB::table('sistemas_coordinadores')->insert([
            'sistema_id' => $sistema->id,
            'usuario_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $ticket = $this->makeTicket($sistema->id);
        $soporteInactivo = $this->makeUser(Role::SOPORTE);
        $soporteInactivo->update(['activo' => false]);

        Sanctum::actingAs($coordinador);

        $this->patchJson("/api/tickets/{$ticket->id}/operativo", [
            'responsable_id' => $soporteInactivo->id,
        ])->assertStatus(422)->assertJsonValidationErrors(['responsable_id']);
    }

    public function test_reasignar_cierra_asignacion_previa(): void
    {
        $coordinador = $this->makeUser(Role::COORDINADOR);
        $sistema = Sistema::create(['nombre' => 'Reasignacion', 'activo' => true]);
        DB::table('sistemas_coordinadores')->insert([
            'sistema_id' => $sistema->id,
            'usuario_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $responsableAnterior = $this->makeUser(Role::SOPORTE);
        $responsableNuevo = $this->makeUser(Role::SOPORTE);

        $ticket = $this->makeTicket($sistema->id, [
            'responsable_actual_id' => $responsableAnterior->id,
        ]);

        DB::table('asignaciones_ticket')->insert([
            'ticket_id' => $ticket->id,
            'responsable_id' => $responsableAnterior->id,
            'asignado_por_id' => $coordinador->id,
            'asignado_at' => now()->subHour(),
            'desasignado_at' => null,
            'created_at' => now()->subHour(),
            'updated_at' => now()->subHour(),
        ]);

        Sanctum::actingAs($coordinador);

        $this->patchJson("/api/tickets/{$ticket->id}/operativo", [
            'responsable_id' => $responsableNuevo->id,
        ])->assertOk();

        $anterior = DB::table('asignaciones_ticket')
            ->where('ticket_id', $ticket->id)
            ->where('responsable_id', $responsableAnterior->id)
            ->first();
        $nuevo = DB::table('asignaciones_ticket')
            ->where('ticket_id', $ticket->id)
            ->where('responsable_id', $responsableNuevo->id)
            ->first();

        $this->assertNotNull($anterior->desasignado_at);
        $this->assertNull($nuevo->desasignado_at);
    }

    public function test_coordinador_puede_quitar_responsable(): void
    {
        $coordinador = $this->makeUser(Role::COORDINADOR);
        $sistema = Sistema::create(['nombre' => 'Quitar responsable', 'activo' => true]);
        DB::table('sistemas_coordinadores')->insert([
            'sistema_id' => $sistema->id,
            'usuario_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $responsable = $this->makeUser(Role::SOPORTE);

        $ticket = $this->makeTicket($sistema->id, [
            'responsable_actual_id' => $responsable->id,
        ]);

        DB::table('asignaciones_ticket')->insert([
            'ticket_id' => $ticket->id,
            'responsable_id' => $responsable->id,
            'asignado_por_id' => $coordinador->id,
            'asignado_at' => now()->subHour(),
            'desasignado_at' => null,
            'created_at' => now()->subHour(),
            'updated_at' => now()->subHour(),
        ]);

        Sanctum::actingAs($coordinador);

        $this->patchJson("/api/tickets/{$ticket->id}/operativo", [
            'responsable_id' => null,
        ])->assertOk();

        $ticket->refresh();
        $this->assertNull($ticket->responsable_actual_id);

        $asignacion = DB::table('asignaciones_ticket')
            ->where('ticket_id', $ticket->id)
            ->where('responsable_id', $responsable->id)
            ->first();

        $this->assertNotNull($asignacion->desasignado_at);
    }

    public function test_soporte_asignado_puede_actualizar_campos_operativos(): void
    {
        $soporte = $this->makeUser(Role::SOPORTE);
        $sistema = Sistema::create(['nombre' => 'Entrega', 'activo' => true]);
        $ticket = $this->makeTicket($sistema->id, [
            'responsable_actual_id' => $soporte->id,
        ]);

        $tipoId = DB::table('tipos_solicitud')->insertGetId([
            'nombre' => 'Soporte',
            'activo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sanctum::actingAs($soporte);

        $payload = [
            'tipo_solicitud_id' => $tipoId,
            'fecha_entrega' => now()->toISOString(),
            'resolucion' => 'Se ajusto configuracion.',
        ];

        $this->patchJson("/api/tickets/{$ticket->id}/operativo", $payload)->assertOk();

        $ticket->refresh();
        $this->assertSame($tipoId, $ticket->tipo_solicitud_id);
        $this->assertNotNull($ticket->fecha_entrega);
        $this->assertSame($payload['resolucion'], $ticket->resolucion);
    }

    public function test_coordinador_puede_actualizar_prioridad_y_fecha_compromiso_y_sistema(): void
    {
        $coordinador = $this->makeUser(Role::COORDINADOR);
        $sistema = Sistema::create(['nombre' => 'Base', 'activo' => true]);
        DB::table('sistemas_coordinadores')->insert([
            'sistema_id' => $sistema->id,
            'usuario_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ticket = $this->makeTicket($sistema->id);
        $prioridadId = DB::table('prioridades')->insertGetId([
            'nombre' => 'Alta',
            'orden' => 1,
            'activo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $nuevoSistema = Sistema::create(['nombre' => 'Nuevo', 'activo' => true]);

        Sanctum::actingAs($coordinador);

        $payload = [
            'prioridad_id' => $prioridadId,
            'fecha_compromiso' => now()->addDay()->toISOString(),
            'sistema_id' => $nuevoSistema->id,
        ];

        $this->patchJson("/api/tickets/{$ticket->id}/operativo", $payload)->assertOk();

        $ticket->refresh();
        $this->assertSame($prioridadId, $ticket->prioridad_id);
        $this->assertSame($nuevoSistema->id, $ticket->sistema_id);
        $this->assertNotNull($ticket->fecha_compromiso);
    }

    public function test_soporte_no_asignado_no_puede_actualizar(): void
    {
        $soporte = $this->makeUser(Role::SOPORTE);
        $sistema = Sistema::create(['nombre' => 'Bloqueo', 'activo' => true]);
        $ticket = $this->makeTicket($sistema->id);

        Sanctum::actingAs($soporte);

        $this->patchJson("/api/tickets/{$ticket->id}/operativo", [
            'resolucion' => 'Intento',
        ])->assertStatus(403);
    }

    public function test_prioridad_inactiva_rechaza_actualizacion(): void
    {
        $coordinador = $this->makeUser(Role::COORDINADOR);
        $sistema = Sistema::create(['nombre' => 'Validaciones', 'activo' => true]);
        DB::table('sistemas_coordinadores')->insert([
            'sistema_id' => $sistema->id,
            'usuario_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ticket = $this->makeTicket($sistema->id);
        $prioridadId = DB::table('prioridades')->insertGetId([
            'nombre' => 'Inactiva',
            'orden' => 2,
            'activo' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sanctum::actingAs($coordinador);

        $this->patchJson("/api/tickets/{$ticket->id}/operativo", [
            'prioridad_id' => $prioridadId,
        ])->assertStatus(422);
    }

    public function test_admin_puede_marcar_ticket_como_interno(): void
    {
        $admin = $this->makeUser(Role::ADMIN);
        $sistema = Sistema::create(['nombre' => 'Internos', 'activo' => true]);
        $ticket = $this->makeTicket($sistema->id, ['interno' => false]);

        Sanctum::actingAs($admin);

        $this->patchJson("/api/tickets/{$ticket->id}/operativo", [
            'interno' => true,
        ])->assertOk();

        $ticket->refresh();
        $this->assertTrue($ticket->interno);
    }

    public function test_no_admin_no_puede_marcar_ticket_como_interno(): void
    {
        $coordinador = $this->makeUser(Role::COORDINADOR);
        $sistema = Sistema::create(['nombre' => 'Internos 2', 'activo' => true]);
        DB::table('sistemas_coordinadores')->insert([
            'sistema_id' => $sistema->id,
            'usuario_id' => $coordinador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ticket = $this->makeTicket($sistema->id, ['interno' => false]);

        Sanctum::actingAs($coordinador);

        $this->patchJson("/api/tickets/{$ticket->id}/operativo", [
            'interno' => true,
        ])->assertStatus(403);
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
            'asunto' => 'Operacion',
            'descripcion' => 'Detalle',
            'solicitante_id' => $solicitanteId,
            'sistema_id' => $sistemaId,
            'estado_id' => $estadoId,
            'responsable_actual_id' => null,
            'interno' => false,
        ], $overrides));
    }
}
