<?php

namespace App\Services;

use App\Models\EventoAuditoriaTicket;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TicketHistorialService
{
    public function __construct(private readonly TicketVisibilityService $visibilityService)
    {
    }

    public function list(User $user, Ticket $ticket, int $perPage = 50): LengthAwarePaginator
    {
        $this->assertUserCanView($user, $ticket);

        $query = EventoAuditoriaTicket::query()
            ->where('ticket_id', $ticket->id)
            ->with('actor:id,nombre')
            ->orderBy('created_at');

        if ($user->isClienteInterno()) {
            $query->whereIn('tipo_evento', ['estado_cambiado', 'cierre', 'cancelacion']);
        }

        return $query->paginate($perPage);
    }

    private function assertUserCanView(User $user, Ticket $ticket): void
    {
        if (! $this->visibilityService->userCanView($user, $ticket)) {
            throw new AuthorizationException('No autorizado.');
        }
    }
}
