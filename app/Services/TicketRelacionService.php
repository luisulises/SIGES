<?php

namespace App\Services;

use App\Models\EstadoTicket;
use App\Models\RelacionTicket;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TicketRelacionService
{
    public function __construct(
        private readonly TicketVisibilityService $visibilityService,
        private readonly TicketWorkflowService $workflowService,
        private readonly TicketAuditoriaService $auditoriaService
    ) {
    }

    public function list(User $user, Ticket $ticket): Collection
    {
        $this->assertUserCanView($user, $ticket);

        $relaciones = RelacionTicket::query()
            ->where(function ($query) use ($ticket) {
                $query->where('ticket_id', $ticket->id)
                    ->orWhere('ticket_relacionado_id', $ticket->id);
            })
            ->with([
                'creadoPor:id,nombre',
                'ticket:id,asunto,estado_id,sistema_id,updated_at',
                'ticketRelacionado:id,asunto,estado_id,sistema_id,updated_at',
            ])
            ->orderBy('created_at')
            ->get();

        $candidateIds = $relaciones
            ->map(function (RelacionTicket $relacion) use ($ticket) {
                return $relacion->ticket_id === $ticket->id
                    ? $relacion->ticket_relacionado_id
                    : $relacion->ticket_id;
            })
            ->unique()
            ->values();

        if ($candidateIds->isEmpty()) {
            return $relaciones;
        }

        $visibleOtherIds = $this->visibilityService
            ->visibleTicketsQuery($user)
            ->whereIn('tickets.id', $candidateIds)
            ->pluck('tickets.id')
            ->all();

        return $relaciones
            ->filter(function (RelacionTicket $relacion) use ($ticket, $visibleOtherIds) {
                $otherId = $relacion->ticket_id === $ticket->id
                    ? $relacion->ticket_relacionado_id
                    : $relacion->ticket_id;

                return in_array($otherId, $visibleOtherIds, true);
            })
            ->values();
    }

    /**
     * @param array{ticket_relacionado_id:int, tipo_relacion:string} $data
     */
    public function create(User $user, Ticket $ticket, array $data): RelacionTicket
    {
        $this->assertUserCanView($user, $ticket);

        $ticketRelacionado = Ticket::query()->findOrFail($data['ticket_relacionado_id']);
        $this->assertUserCanView($user, $ticketRelacionado);

        if ($ticketRelacionado->id === $ticket->id) {
            throw ValidationException::withMessages([
                'ticket_relacionado_id' => 'No se permite relacionar un ticket consigo mismo.',
            ]);
        }

        $tipoRelacion = $data['tipo_relacion'];

        if ($tipoRelacion === 'reabre') {
            $estadoCerradoId = EstadoTicket::query()
                ->where('nombre', EstadoTicket::CERRADO)
                ->value('id');
            $estadoCanceladoId = EstadoTicket::query()
                ->where('nombre', EstadoTicket::CANCELADO)
                ->value('id');

            $allowedEstadoIds = array_values(array_filter([
                $estadoCerradoId ? (int) $estadoCerradoId : null,
                $estadoCanceladoId ? (int) $estadoCanceladoId : null,
            ]));

            if ($allowedEstadoIds !== [] && ! in_array((int) $ticketRelacionado->estado_id, $allowedEstadoIds, true)) {
                throw ValidationException::withMessages([
                    'ticket_relacionado_id' => 'El ticket referenciado debe estar en estado Cerrado o Cancelado.',
                ]);
            }
        }

        if ($tipoRelacion === 'duplicado_de') {
            $estadoCanceladoId = EstadoTicket::query()
                ->where('nombre', EstadoTicket::CANCELADO)
                ->value('id');

            if ($estadoCanceladoId && (int) $ticketRelacionado->estado_id === (int) $estadoCanceladoId) {
                throw ValidationException::withMessages([
                    'ticket_relacionado_id' => 'El ticket valido no puede estar Cancelado.',
                ]);
            }
        }

        if ($this->relationExists($ticket->id, $ticketRelacionado->id, $tipoRelacion)) {
            throw ValidationException::withMessages([
                'relacion' => 'La relacion ya existe.',
            ]);
        }

        return DB::transaction(function () use ($user, $ticket, $ticketRelacionado, $tipoRelacion) {
            if ($tipoRelacion === 'duplicado_de') {
                $this->assertUserCanMarkDuplicado($user, $ticket);

                $estadoCanceladoId = EstadoTicket::query()
                    ->where('nombre', EstadoTicket::CANCELADO)
                    ->value('id');

                if ((int) $ticket->estado_id !== (int) $estadoCanceladoId) {
                    $this->workflowService->cancel($user, $ticket);
                }
            }

            $relacion = RelacionTicket::create([
                'ticket_id' => $ticket->id,
                'ticket_relacionado_id' => $ticketRelacionado->id,
                'tipo_relacion' => $tipoRelacion,
                'creado_por_id' => $user->id,
            ]);

            $ticket->touch();
            $ticketRelacionado->touch();

            $this->auditoriaService->record(
                $ticket,
                $user,
                'relacion_creada',
                null,
                [
                    'tipo_relacion' => $tipoRelacion,
                    'ticket_relacionado_id' => $ticketRelacionado->id,
                ]
            );

            $this->auditoriaService->record(
                $ticketRelacionado,
                $user,
                'relacion_creada',
                null,
                [
                    'tipo_relacion' => $tipoRelacion,
                    'ticket_relacionado_id' => $ticket->id,
                ]
            );

            return $relacion->load([
                'creadoPor:id,nombre',
                'ticket:id,asunto,estado_id,sistema_id,updated_at',
                'ticketRelacionado:id,asunto,estado_id,sistema_id,updated_at',
            ]);
        });
    }

    private function relationExists(int $ticketId, int $ticketRelacionadoId, string $tipoRelacion): bool
    {
        return RelacionTicket::query()
            ->where('tipo_relacion', $tipoRelacion)
            ->where(function ($query) use ($ticketId, $ticketRelacionadoId) {
                $query->where(function ($subquery) use ($ticketId, $ticketRelacionadoId) {
                    $subquery->where('ticket_id', $ticketId)
                        ->where('ticket_relacionado_id', $ticketRelacionadoId);
                })->orWhere(function ($subquery) use ($ticketId, $ticketRelacionadoId) {
                    $subquery->where('ticket_id', $ticketRelacionadoId)
                        ->where('ticket_relacionado_id', $ticketId);
                });
            })
            ->exists();
    }

    private function assertUserCanView(User $user, Ticket $ticket): void
    {
        if (! $this->visibilityService->userCanView($user, $ticket)) {
            throw new AuthorizationException('No autorizado.');
        }
    }

    private function assertUserCanMarkDuplicado(User $user, Ticket $ticket): void
    {
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
