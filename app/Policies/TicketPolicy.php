<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketVisibilityService;
use Illuminate\Support\Facades\DB;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return app(TicketVisibilityService::class)->userCanView($user, $ticket);
    }

    public function operate(User $user, Ticket $ticket): bool
    {
        if (! $this->view($user, $ticket)) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCoordinador() && $this->isCoordinadorDeSistema($user, (int) $ticket->sistema_id)) {
            return true;
        }

        return $user->isSoporte() && (int) $ticket->responsable_actual_id === (int) $user->id;
    }

    public function assign(User $user, Ticket $ticket): bool
    {
        if (! $this->view($user, $ticket)) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $user->isCoordinador() && $this->isCoordinadorDeSistema($user, (int) $ticket->sistema_id);
    }

    public function closeOrCancel(User $user, Ticket $ticket): bool
    {
        if (! $this->view($user, $ticket)) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCoordinador() && $this->isCoordinadorDeSistema($user, (int) $ticket->sistema_id)) {
            return true;
        }

        return (int) $ticket->solicitante_id === (int) $user->id;
    }

    private function isCoordinadorDeSistema(User $user, int $sistemaId): bool
    {
        return DB::table('sistemas_coordinadores')
            ->where('usuario_id', $user->id)
            ->where('sistema_id', $sistemaId)
            ->exists();
    }
}
