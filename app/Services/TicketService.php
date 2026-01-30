<?php

namespace App\Services;

use App\Models\EstadoTicket;
use App\Models\Ticket;
use App\Models\User;

class TicketService
{
    public function __construct(private readonly TicketNotificacionService $notificacionService)
    {
    }

    public function createTicket(User $user, array $data): Ticket
    {
        $estadoNuevo = EstadoTicket::query()
            ->where('nombre', EstadoTicket::NUEVO)
            ->firstOrFail();

        $ticket = Ticket::create([
            'asunto' => $data['asunto'],
            'descripcion' => $data['descripcion'],
            'solicitante_id' => $user->id,
            'sistema_id' => $data['sistema_id'],
            'estado_id' => $estadoNuevo->id,
            'responsable_actual_id' => null,
            'interno' => false,
        ]);

        $this->notificacionService->ticketCreado($ticket);

        return $ticket;
    }
}
