<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TicketVisibilityService
{
    public function visibleTicketsQuery(User $user): Builder
    {
        $query = Ticket::query();

        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isClienteInterno()) {
            $query->where('interno', false);
        }

        $query->where(function (Builder $subquery) use ($user) {
            $roleApplied = false;

            if ($user->isClienteInterno()) {
                $subquery->where('solicitante_id', $user->id);
                $roleApplied = true;
            }

            if ($user->isSoporte()) {
                $subquery->where('responsable_actual_id', $user->id);
                $roleApplied = true;
            }

            if ($user->isCoordinador()) {
                $sistemaIds = DB::table('sistemas_coordinadores')
                    ->where('usuario_id', $user->id)
                    ->pluck('sistema_id');

                if ($sistemaIds->isNotEmpty()) {
                    $subquery->whereIn('sistema_id', $sistemaIds);
                    $roleApplied = true;
                }
            }

            if (! $roleApplied) {
                $subquery->whereRaw('1 = 0');
            }

            if ($this->supportsInvolucrados()) {
                $subquery->orWhereExists(function ($exists) use ($user) {
                    $exists->select(DB::raw(1))
                        ->from('involucrados_ticket')
                        ->whereColumn('involucrados_ticket.ticket_id', 'tickets.id')
                        ->where('involucrados_ticket.usuario_id', $user->id);

                    if ($this->supportsInvolucradosSoftDeletes()) {
                        $exists->whereNull('involucrados_ticket.deleted_at');
                    }
                });
            }
        });

        return $query;
    }

    public function userCanView(User $user, Ticket $ticket): bool
    {
        return $this->visibleTicketsQuery($user)
            ->whereKey($ticket->id)
            ->exists();
    }

    private function supportsInvolucrados(): bool
    {
        return Schema::hasTable('involucrados_ticket');
    }

    private function supportsInvolucradosSoftDeletes(): bool
    {
        return Schema::hasColumn('involucrados_ticket', 'deleted_at');
    }
}
