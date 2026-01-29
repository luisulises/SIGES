<?php

namespace App\Services;

use App\Models\EventoAuditoriaTicket;
use App\Models\Ticket;
use App\Models\User;

class TicketAuditoriaService
{
    /**
     * @param array<string, mixed>|null $valorAntes
     * @param array<string, mixed>|null $valorDespues
     * @param array<string, mixed>|null $metadatos
     */
    public function record(
        Ticket $ticket,
        User $actor,
        string $tipoEvento,
        ?array $valorAntes = null,
        ?array $valorDespues = null,
        ?array $metadatos = null
    ): void {
        EventoAuditoriaTicket::create([
            'ticket_id' => $ticket->id,
            'actor_id' => $actor->id,
            'tipo_evento' => $tipoEvento,
            'valor_antes' => $valorAntes,
            'valor_despues' => $valorDespues,
            'metadatos' => $metadatos,
        ]);
    }
}

