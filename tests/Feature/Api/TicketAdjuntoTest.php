<?php

namespace Tests\Feature\Api;

use App\Models\ComentarioTicket;
use App\Models\EstadoTicket;
use App\Models\Role;
use App\Models\Sistema;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TicketAdjuntoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_cliente_interno_puede_subir_adjunto_publico_a_su_ticket(): void
    {
        Storage::fake(config('filesystems.default'));

        $cliente = $this->makeUser(Role::CLIENTE_INTERNO);
        $ticket = $this->makeTicket(['solicitante_id' => $cliente->id]);
        $comentario = ComentarioTicket::create([
            'ticket_id' => $ticket->id,
            'autor_id' => $cliente->id,
            'cuerpo' => 'Adjunto evidencia.',
            'visibilidad' => 'publico',
        ]);

        Sanctum::actingAs($cliente);

        $response = $this->withHeader('Accept', 'application/json')->post("/api/tickets/{$ticket->id}/adjuntos", [
            'archivo' => UploadedFile::fake()->create('evidencia.pdf', 100, 'application/pdf'),
            'comentario_id' => $comentario->id,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.ticket_id', $ticket->id)
            ->assertJsonPath('data.comentario_id', $comentario->id)
            ->assertJsonPath('data.visibilidad', 'publico')
            ->assertJsonPath('data.nombre_archivo', 'evidencia.pdf');

        $path = $response->json('data.clave_almacenamiento');

        Storage::disk(config('filesystems.default'))->assertExists($path);
        $this->assertDatabaseHas('adjuntos', [
            'ticket_id' => $ticket->id,
            'comentario_id' => $comentario->id,
            'nombre_archivo' => 'evidencia.pdf',
            'visibilidad' => 'publico',
        ]);
    }

    public function test_rechaza_subir_adjunto_sin_comentario_id(): void
    {
        Storage::fake(config('filesystems.default'));

        $cliente = $this->makeUser(Role::CLIENTE_INTERNO);
        $ticket = $this->makeTicket(['solicitante_id' => $cliente->id]);

        Sanctum::actingAs($cliente);

        $this->withHeader('Accept', 'application/json')->post("/api/tickets/{$ticket->id}/adjuntos", [
            'archivo' => UploadedFile::fake()->create('evidencia.pdf', 10, 'application/pdf'),
        ])->assertStatus(422)->assertJsonStructure(['errors' => ['comentario_id']]);
    }

    public function test_validacion_rechaza_tipo_o_tamano_de_archivo(): void
    {
        Storage::fake(config('filesystems.default'));

        $cliente = $this->makeUser(Role::CLIENTE_INTERNO);
        $ticket = $this->makeTicket(['solicitante_id' => $cliente->id]);
        $comentario = ComentarioTicket::create([
            'ticket_id' => $ticket->id,
            'autor_id' => $cliente->id,
            'cuerpo' => 'Adjunto evidencia.',
            'visibilidad' => 'publico',
        ]);

        Sanctum::actingAs($cliente);

        $this->withHeader('Accept', 'application/json')->post("/api/tickets/{$ticket->id}/adjuntos", [
            'archivo' => UploadedFile::fake()->create('virus.exe', 10, 'application/octet-stream'),
            'comentario_id' => $comentario->id,
        ])->assertStatus(422);

        $this->withHeader('Accept', 'application/json')->post("/api/tickets/{$ticket->id}/adjuntos", [
            'archivo' => UploadedFile::fake()->create('grande.pdf', 10241, 'application/pdf'),
            'comentario_id' => $comentario->id,
        ])->assertStatus(422);
    }

    public function test_adjunto_en_comentario_hereda_visibilidad_y_se_filtra_en_listado(): void
    {
        Storage::fake(config('filesystems.default'));

        $cliente = $this->makeUser(Role::CLIENTE_INTERNO);
        $soporte = $this->makeUser(Role::SOPORTE);
        $ticket = $this->makeTicket([
            'solicitante_id' => $cliente->id,
            'responsable_actual_id' => $soporte->id,
        ]);

        $comentarioInterno = ComentarioTicket::create([
            'ticket_id' => $ticket->id,
            'autor_id' => $soporte->id,
            'cuerpo' => 'Comentario interno.',
            'visibilidad' => 'interno',
        ]);

        Sanctum::actingAs($soporte);

        $this->withHeader('Accept', 'application/json')->post("/api/tickets/{$ticket->id}/adjuntos", [
            'archivo' => UploadedFile::fake()->create('interno.pdf', 50, 'application/pdf'),
            'comentario_id' => $comentarioInterno->id,
        ])->assertCreated()->assertJsonPath('data.visibilidad', 'interno');

        Sanctum::actingAs($cliente);

        $this->getJson("/api/tickets/{$ticket->id}/adjuntos")
            ->assertOk()
            ->assertJsonCount(0, 'data');

        Sanctum::actingAs($soporte);

        $this->getJson("/api/tickets/{$ticket->id}/adjuntos")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.nombre_archivo', 'interno.pdf');
    }

    public function test_rechaza_comentario_id_de_otro_ticket(): void
    {
        Storage::fake(config('filesystems.default'));

        $soporte = $this->makeUser(Role::SOPORTE);
        $ticketA = $this->makeTicket(['responsable_actual_id' => $soporte->id]);
        $ticketB = $this->makeTicket(['responsable_actual_id' => $soporte->id]);

        $comentarioA = ComentarioTicket::create([
            'ticket_id' => $ticketA->id,
            'autor_id' => $soporte->id,
            'cuerpo' => 'Comentario A.',
            'visibilidad' => 'publico',
        ]);

        Sanctum::actingAs($soporte);

        $this->withHeader('Accept', 'application/json')->post("/api/tickets/{$ticketB->id}/adjuntos", [
            'archivo' => UploadedFile::fake()->create('archivo.pdf', 10, 'application/pdf'),
            'comentario_id' => $comentarioA->id,
        ])->assertStatus(422)->assertJsonPath('errors.comentario_id.0', 'El comentario no pertenece a este ticket.');
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
