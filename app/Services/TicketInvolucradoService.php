<?php

namespace App\Services;

use App\Models\InvolucradoTicket;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TicketInvolucradoService
{
    public function __construct(
        private readonly TicketVisibilityService $visibilityService,
        private readonly TicketAuditoriaService $auditoriaService
    ) {
    }

    public function list(User $user, Ticket $ticket): Collection
    {
        $this->assertUserCanView($user, $ticket);

        $usuarioColumns = $user->isClienteInterno()
            ? ['id', 'nombre']
            : ['id', 'nombre', 'email'];

        return InvolucradoTicket::query()
            ->where('ticket_id', $ticket->id)
            ->with([
                'usuario' => fn ($query) => $query->select($usuarioColumns),
            ])
            ->orderBy('created_at')
            ->get();
    }

    public function add(User $user, Ticket $ticket, int $usuarioId): InvolucradoTicket
    {
        $this->assertUserCanManage($user, $ticket);

        $existing = InvolucradoTicket::withTrashed()
            ->where('ticket_id', $ticket->id)
            ->where('usuario_id', $usuarioId)
            ->first();

        if ($existing && ! $existing->trashed()) {
            return $existing->load('usuario:id,nombre,email');
        }

        if ($existing && $existing->trashed()) {
            $existing->restore();
            $existing->agregado_por_id = $user->id;
            $existing->save();

            $ticket->touch();

            $this->auditoriaService->record(
                $ticket,
                $user,
                'involucrado_agregado',
                null,
                [
                    'usuario_id' => $usuarioId,
                ]
            );

            return $existing->load('usuario:id,nombre,email');
        }

        $created = InvolucradoTicket::create([
            'ticket_id' => $ticket->id,
            'usuario_id' => $usuarioId,
            'agregado_por_id' => $user->id,
        ]);

        $ticket->touch();

        $this->auditoriaService->record(
            $ticket,
            $user,
            'involucrado_agregado',
            null,
            [
                'usuario_id' => $usuarioId,
            ]
        );

        return $created->load('usuario:id,nombre,email');
    }

    public function remove(User $user, Ticket $ticket, int $usuarioId): void
    {
        $this->assertUserCanManage($user, $ticket);

        $involucrado = InvolucradoTicket::query()
            ->where('ticket_id', $ticket->id)
            ->where('usuario_id', $usuarioId)
            ->first();

        if (! $involucrado || $involucrado->trashed()) {
            throw ValidationException::withMessages([
                'usuario_id' => 'El usuario no es un involucrado activo.',
            ]);
        }

        $involucrado->delete();
        $ticket->touch();

        $this->auditoriaService->record(
            $ticket,
            $user,
            'involucrado_removido',
            null,
            [
                'usuario_id' => $usuarioId,
            ]
        );
    }

    private function assertUserCanView(User $user, Ticket $ticket): void
    {
        if (! $this->visibilityService->userCanView($user, $ticket)) {
            throw new AuthorizationException('No autorizado.');
        }
    }

    private function assertUserCanManage(User $user, Ticket $ticket): void
    {
        $this->assertUserCanView($user, $ticket);

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isCoordinador() && $this->isCoordinadorDeSistema($user, $ticket->sistema_id)) {
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
