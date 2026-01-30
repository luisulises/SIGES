<?php

namespace App\Services;

use App\Models\RegistroTiempoTicket;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TicketTiempoService
{
    public function __construct(
        private readonly TicketVisibilityService $visibilityService,
        private readonly TicketAuditoriaService $auditoriaService
    ) {
    }

    public function list(User $user, Ticket $ticket, int $perPage = 50): LengthAwarePaginator
    {
        $this->assertUserCanManageTiempo($user, $ticket);

        return RegistroTiempoTicket::query()
            ->where('ticket_id', $ticket->id)
            ->with('autor:id,nombre')
            ->orderBy('created_at')
            ->paginate($perPage);
    }

    /**
     * @param array{minutos:int, nota?:string|null} $data
     */
    public function create(User $user, Ticket $ticket, array $data): RegistroTiempoTicket
    {
        $this->assertUserCanManageTiempo($user, $ticket);

        $registro = RegistroTiempoTicket::create([
            'ticket_id' => $ticket->id,
            'autor_id' => $user->id,
            'minutos' => (int) $data['minutos'],
            'nota' => $data['nota'] ?? null,
        ]);

        $this->auditoriaService->record(
            $ticket,
            $user,
            'tiempo_registrado',
            null,
            [
                'registro_id' => $registro->id,
                'minutos' => $registro->minutos,
            ]
        );

        return $registro->load('autor:id,nombre');
    }

    private function assertUserCanManageTiempo(User $user, Ticket $ticket): void
    {
        if (! $this->visibilityService->userCanView($user, $ticket)) {
            throw new AuthorizationException('No autorizado.');
        }

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

    private function isCoordinadorDeSistema(User $user, int $sistemaId): bool
    {
        return DB::table('sistemas_coordinadores')
            ->where('usuario_id', $user->id)
            ->where('sistema_id', $sistemaId)
            ->exists();
    }
}
