<?php

namespace App\Services;

use App\Models\EstadoTicket;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TicketService
{
    public function __construct(
        private readonly TicketNotificacionService $notificacionService,
        private readonly TicketRelacionService $relacionService
    ) {
    }

    public function createTicket(User $user, array $data): Ticket
    {
        $estadoNuevo = EstadoTicket::query()
            ->where('nombre', EstadoTicket::NUEVO)
            ->firstOrFail();

        return DB::transaction(function () use ($user, $data, $estadoNuevo) {
            $ticket = Ticket::create([
                'asunto' => $data['asunto'],
                'descripcion' => $data['descripcion'],
                'solicitante_id' => $user->id,
                'sistema_id' => $data['sistema_id'],
                'estado_id' => $estadoNuevo->id,
                'responsable_actual_id' => null,
                'interno' => false,
            ]);

            $referenciaTicketId = $data['referencia_ticket_id'] ?? null;
            if ($referenciaTicketId) {
                try {
                    $this->relacionService->create($user, $ticket, [
                        'ticket_relacionado_id' => (int) $referenciaTicketId,
                        'tipo_relacion' => 'reabre',
                    ]);
                } catch (ValidationException $exception) {
                    $errors = $exception->errors();

                    if (array_key_exists('ticket_relacionado_id', $errors)) {
                        $errors['referencia_ticket_id'] = $errors['ticket_relacionado_id'];
                        unset($errors['ticket_relacionado_id']);
                    }

                    throw ValidationException::withMessages($errors);
                }
            }

            $this->notificacionService->ticketCreado($ticket);

            return $ticket->refresh();
        });
    }
}
