<?php

namespace App\Services;

use App\Models\EstadoTicket;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TicketWorkflowService
{
    public function __construct(private readonly TicketVisibilityService $visibilityService)
    {
    }

    public function transition(User $user, Ticket $ticket, string $estadoDestinoNombre): Ticket
    {
        $this->assertUserCanView($user, $ticket);
        $this->assertUserCanOperate($user, $ticket);

        $estadoDestinoId = $this->estadoIdByName($estadoDestinoNombre);
        $regla = $this->findReglaOrFail($user, $ticket->estado_id, $estadoDestinoId);

        if ($regla->requiere_responsable && ! $ticket->responsable_actual_id) {
            throw ValidationException::withMessages([
                'estado' => 'Se requiere responsable para esta transicion.',
            ]);
        }

        $ticket->estado_id = $estadoDestinoId;
        $ticket->save();

        return $ticket;
    }

    public function close(User $user, Ticket $ticket): Ticket
    {
        $this->assertUserCanView($user, $ticket);
        $this->assertUserCanCloseOrCancel($user, $ticket);

        $estadoResueltoId = $this->estadoIdByName(EstadoTicket::RESUELTO);

        if ($ticket->estado_id !== $estadoResueltoId) {
            throw ValidationException::withMessages([
                'estado' => 'El ticket debe estar en estado Resuelto para cerrar.',
            ]);
        }

        if (! $ticket->resolucion) {
            throw ValidationException::withMessages([
                'resolucion' => 'Se requiere una resolucion para cerrar el ticket.',
            ]);
        }

        $estadoCerradoId = $this->estadoIdByName(EstadoTicket::CERRADO);
        $this->findReglaOrFail($user, $ticket->estado_id, $estadoCerradoId);

        $ticket->estado_id = $estadoCerradoId;
        $ticket->cerrado_at = now();
        $ticket->save();

        return $ticket;
    }

    public function cancel(User $user, Ticket $ticket): Ticket
    {
        $this->assertUserCanView($user, $ticket);
        $this->assertUserCanCloseOrCancel($user, $ticket);

        $estadoCanceladoId = $this->estadoIdByName(EstadoTicket::CANCELADO);
        $this->findReglaOrFail($user, $ticket->estado_id, $estadoCanceladoId);

        $ticket->estado_id = $estadoCanceladoId;
        $ticket->cancelado_at = now();
        $ticket->save();

        return $ticket;
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

    private function assertUserCanCloseOrCancel(User $user, Ticket $ticket): void
    {
        if ($user->isAdmin()) {
            return;
        }

        if ($user->isCoordinador() && $this->isCoordinadorDeSistema($user, $ticket->sistema_id)) {
            return;
        }

        if ($ticket->solicitante_id === $user->id) {
            return;
        }

        throw new AuthorizationException('No autorizado.');
    }

    private function findReglaOrFail(User $user, int $estadoOrigenId, int $estadoDestinoId): object
    {
        $regla = DB::table('reglas_transicion_estado')
            ->where('estado_origen_id', $estadoOrigenId)
            ->where('estado_destino_id', $estadoDestinoId)
            ->where('rol_id', $user->rol_id)
            ->first();

        if (! $regla) {
            throw ValidationException::withMessages([
                'estado' => 'Transicion no permitida para el rol.',
            ]);
        }

        return $regla;
    }

    private function estadoIdByName(string $nombre): int
    {
        $estadoId = EstadoTicket::query()
            ->where('nombre', $nombre)
            ->value('id');

        if (! $estadoId) {
            throw ValidationException::withMessages([
                'estado' => 'Estado no encontrado.',
            ]);
        }

        return $estadoId;
    }

    private function isCoordinadorDeSistema(User $user, int $sistemaId): bool
    {
        return DB::table('sistemas_coordinadores')
            ->where('usuario_id', $user->id)
            ->where('sistema_id', $sistemaId)
            ->exists();
    }
}
