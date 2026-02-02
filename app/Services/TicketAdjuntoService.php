<?php

namespace App\Services;

use App\Models\Adjunto;
use App\Models\ComentarioTicket;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TicketAdjuntoService
{
    public function __construct(
        private readonly TicketVisibilityService $visibilityService,
        private readonly TicketAuditoriaService $auditoriaService
    )
    {
    }

    public function list(User $user, Ticket $ticket): Collection
    {
        $this->assertUserCanView($user, $ticket);

        $includeInternos = $this->isRolInterno($user);

        return Adjunto::query()
            ->where('ticket_id', $ticket->id)
            ->when(! $includeInternos, fn ($query) => $query->where('visibilidad', 'publico'))
            ->with('cargadoPor:id,nombre')
            ->orderBy('created_at')
            ->get();
    }

    public function store(User $user, Ticket $ticket, UploadedFile $file, int $comentarioId): Adjunto
    {
        $this->assertUserCanView($user, $ticket);

        if ($user->isClienteInterno()) {
            if ($ticket->solicitante_id !== $user->id) {
                throw new AuthorizationException('No autorizado.');
            }
        } else {
            $this->assertUserCanOperate($user, $ticket);
        }

        $comentario = ComentarioTicket::query()->findOrFail($comentarioId);
        if ($comentario->ticket_id !== $ticket->id) {
            throw ValidationException::withMessages([
                'comentario_id' => 'El comentario no pertenece a este ticket.',
            ]);
        }

        if ($comentario->visibilidad === 'interno' && ! $this->isRolInterno($user)) {
            throw new AuthorizationException('No autorizado.');
        }

        $visibilidad = $comentario->visibilidad;

        $disk = Storage::disk(config('filesystems.default'));

        $extension = $file->getClientOriginalExtension();
        $filename = (string) Str::uuid().($extension ? '.'.$extension : '');
        $path = $disk->putFileAs("adjuntos/tickets/{$ticket->id}", $file, $filename);

        $adjunto = Adjunto::create([
            'ticket_id' => $ticket->id,
            'comentario_id' => $comentario->id,
            'cargado_por_id' => $user->id,
            'nombre_archivo' => $file->getClientOriginalName(),
            'clave_almacenamiento' => $path,
            'visibilidad' => $visibilidad,
        ]);

        $ticket->touch();

        $this->auditoriaService->record(
            $ticket,
            $user,
            'adjunto_creado',
            null,
            [
                'adjunto_id' => $adjunto->id,
                'comentario_id' => $comentario->id,
                'nombre_archivo' => $adjunto->nombre_archivo,
                'visibilidad' => $adjunto->visibilidad,
            ]
        );

        return $adjunto->load('cargadoPor:id,nombre');
    }

    private function assertUserCanView(User $user, Ticket $ticket): void
    {
        if (! $this->visibilityService->userCanView($user, $ticket)) {
            throw new AuthorizationException('No autorizado.');
        }
    }

    private function assertUserCanOperate(User $user, Ticket $ticket): void
    {
        if ($user->isAdmin()) {
            return;
        }

        if ($user->isCoordinador() && $this->isCoordinadorDeSistema($user, $ticket->sistema_id)) {
            return;
        }

        if ($user->isSoporte() && $ticket->responsable_actual_id === $user->id) {
            return;
        }

        throw new AuthorizationException('No autorizado.');
    }

    private function isRolInterno(User $user): bool
    {
        return $user->isAdmin() || $user->isCoordinador() || $user->isSoporte();
    }

    private function isCoordinadorDeSistema(User $user, int $sistemaId): bool
    {
        return DB::table('sistemas_coordinadores')
            ->where('usuario_id', $user->id)
            ->where('sistema_id', $sistemaId)
            ->exists();
    }
}
