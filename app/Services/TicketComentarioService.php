<?php

namespace App\Services;

use App\Models\ComentarioTicket;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class TicketComentarioService
{
    public function __construct(
        private readonly TicketVisibilityService $visibilityService,
        private readonly TicketAuditoriaService $auditoriaService,
        private readonly TicketNotificacionService $notificacionService
    )
    {
    }

    public function list(User $user, Ticket $ticket): Collection
    {
        $this->assertUserCanView($user, $ticket);

        $includeInternos = $this->isRolInterno($user);

        return ComentarioTicket::query()
            ->where('ticket_id', $ticket->id)
            ->when(! $includeInternos, fn ($query) => $query->where('visibilidad', 'publico'))
            ->with([
                'autor:id,nombre',
                'adjuntos' => function ($query) use ($includeInternos) {
                    if (! $includeInternos) {
                        $query->where('visibilidad', 'publico');
                    }

                    $query->with('cargadoPor:id,nombre')->orderBy('created_at');
                },
            ])
            ->orderBy('created_at')
            ->get();
    }

    /**
     * @param array{cuerpo:string, visibilidad:string} $data
     */
    public function create(User $user, Ticket $ticket, array $data): ComentarioTicket
    {
        $this->assertUserCanView($user, $ticket);

        if ($user->isClienteInterno()) {
            if ($ticket->solicitante_id !== $user->id) {
                throw new AuthorizationException('No autorizado.');
            }

            if ($data['visibilidad'] !== 'publico') {
                throw ValidationException::withMessages([
                    'visibilidad' => 'No puedes crear comentarios internos.',
                ]);
            }
        } else {
            $this->assertUserCanOperate($user, $ticket);
        }

        $comentario = ComentarioTicket::create([
            'ticket_id' => $ticket->id,
            'autor_id' => $user->id,
            'cuerpo' => $data['cuerpo'],
            'visibilidad' => $data['visibilidad'],
        ]);

        $ticket->touch();

        $this->auditoriaService->record(
            $ticket,
            $user,
            'comentario_creado',
            null,
            [
                'comentario_id' => $comentario->id,
                'visibilidad' => $comentario->visibilidad,
            ]
        );

        if ($data['visibilidad'] === 'publico') {
            $this->notificacionService->comentarioPublico($ticket);
        }

        return $comentario->load(['autor:id,nombre', 'adjuntos']);
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
