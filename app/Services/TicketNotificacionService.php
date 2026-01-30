<?php

namespace App\Services;

use App\Models\InvolucradoTicket;
use App\Models\Notificacion;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class TicketNotificacionService
{
    /**
     * @param array<int> $usuarioIds
     */
    private function recordInApp(array $usuarioIds, Ticket $ticket, string $tipoEvento): void
    {
        $usuarioIds = array_values(array_unique(array_filter($usuarioIds)));
        if (count($usuarioIds) === 0) {
            return;
        }

        $activeUserIds = DB::table('usuarios')
            ->whereIn('id', $usuarioIds)
            ->where('activo', true)
            ->pluck('id')
            ->all();

        if (count($activeUserIds) === 0) {
            return;
        }

        $now = now();
        $rows = array_map(
            fn (int $usuarioId) => [
                'usuario_id' => $usuarioId,
                'ticket_id' => $ticket->id,
                'tipo_evento' => $tipoEvento,
                'canal' => 'in_app',
                'leido_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            $activeUserIds
        );

        Notificacion::query()->insert($rows);
    }

    /**
     * Destinatarios base: solicitante, responsable actual, involucrados activos, coordinadores del sistema.
     *
     * @return array<int>
     */
    private function destinatariosTicket(Ticket $ticket): array
    {
        $ids = [];

        $ids[] = $ticket->solicitante_id;

        if ($ticket->responsable_actual_id) {
            $ids[] = $ticket->responsable_actual_id;
        }

        $involucrados = InvolucradoTicket::query()
            ->where('ticket_id', $ticket->id)
            ->whereNull('deleted_at')
            ->pluck('usuario_id')
            ->all();
        $ids = array_merge($ids, $involucrados);

        $coordinadores = DB::table('sistemas_coordinadores')
            ->where('sistema_id', $ticket->sistema_id)
            ->pluck('usuario_id')
            ->all();
        $ids = array_merge($ids, $coordinadores);

        return $ids;
    }

    public function ticketCreado(Ticket $ticket): void
    {
        $this->recordInApp($this->destinatariosTicket($ticket), $ticket, 'creacion');
    }

    public function asignacionCambiada(Ticket $ticket, ?int $responsableAntesId, ?int $responsableDespuesId): void
    {
        $ids = $this->destinatariosTicket($ticket);

        if ($responsableAntesId) {
            $ids[] = $responsableAntesId;
        }
        if ($responsableDespuesId) {
            $ids[] = $responsableDespuesId;
        }

        $this->recordInApp($ids, $ticket, 'asignacion');
    }

    public function estadoCambiado(Ticket $ticket): void
    {
        $this->recordInApp($this->destinatariosTicket($ticket), $ticket, 'cambio_estado');
    }

    public function comentarioPublico(Ticket $ticket): void
    {
        $this->recordInApp($this->destinatariosTicket($ticket), $ticket, 'comentario_publico');
    }

    public function cierre(Ticket $ticket): void
    {
        $this->recordInApp($this->destinatariosTicket($ticket), $ticket, 'cierre');
    }

    public function cancelacion(Ticket $ticket): void
    {
        $this->recordInApp($this->destinatariosTicket($ticket), $ticket, 'cancelacion');
    }
}

