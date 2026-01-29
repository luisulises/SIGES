<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TicketOperativoService
{
    public function __construct(
        private readonly TicketVisibilityService $visibilityService,
        private readonly TicketAuditoriaService $auditoriaService
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(User $user, Ticket $ticket, array $data): Ticket
    {
        $this->assertUserCanView($user, $ticket);

        if (empty($data)) {
            throw ValidationException::withMessages([
                'operacion' => 'No hay cambios para aplicar.',
            ]);
        }

        $before = [
            'responsable_actual_id' => $ticket->responsable_actual_id,
            'prioridad_id' => $ticket->prioridad_id,
            'fecha_compromiso' => optional($ticket->fecha_compromiso)->toISOString(),
            'tipo_solicitud_id' => $ticket->tipo_solicitud_id,
            'fecha_entrega' => optional($ticket->fecha_entrega)->toISOString(),
            'resolucion' => $ticket->resolucion,
            'sistema_id' => $ticket->sistema_id,
        ];

        return DB::transaction(function () use ($user, $ticket, $data, $before) {
            $now = now();

            if (array_key_exists('responsable_id', $data)) {
                $this->assertUserCanAssign($user, $ticket);

                if ($data['responsable_id'] === null) {
                    $this->clearResponsable($ticket, $now);
                } else {
                    $this->applyResponsable($ticket, (int) $data['responsable_id'], $user->id, $now);
                }
            }

            if ($this->hasCoordinatorFields($data)) {
                $this->assertUserCanCoordinate($user, $ticket);
            }

            if ($this->hasSoporteFields($data)) {
                $this->assertUserCanSoporte($user, $ticket);
            }

            if (array_key_exists('prioridad_id', $data)) {
                $this->ensureCatalogoActivo('prioridades', $data['prioridad_id'], 'prioridad_id');
                $ticket->prioridad_id = $data['prioridad_id'];
            }

            if (array_key_exists('fecha_compromiso', $data)) {
                $ticket->fecha_compromiso = $data['fecha_compromiso'];
            }

            if (array_key_exists('tipo_solicitud_id', $data)) {
                $this->ensureCatalogoActivo('tipos_solicitud', $data['tipo_solicitud_id'], 'tipo_solicitud_id');
                $ticket->tipo_solicitud_id = $data['tipo_solicitud_id'];
            }

            if (array_key_exists('fecha_entrega', $data)) {
                $ticket->fecha_entrega = $data['fecha_entrega'];
            }

            if (array_key_exists('resolucion', $data)) {
                $ticket->resolucion = $data['resolucion'];
            }

            if (array_key_exists('sistema_id', $data)) {
                $this->ensureCatalogoActivo('sistemas', $data['sistema_id'], 'sistema_id');
                $ticket->sistema_id = $data['sistema_id'];
            }

            $ticket->save();

            $ticket->refresh();

            $after = [
                'responsable_actual_id' => $ticket->responsable_actual_id,
                'prioridad_id' => $ticket->prioridad_id,
                'fecha_compromiso' => optional($ticket->fecha_compromiso)->toISOString(),
                'tipo_solicitud_id' => $ticket->tipo_solicitud_id,
                'fecha_entrega' => optional($ticket->fecha_entrega)->toISOString(),
                'resolucion' => $ticket->resolucion,
                'sistema_id' => $ticket->sistema_id,
            ];

            if (array_key_exists('responsable_id', $data) && $before['responsable_actual_id'] !== $after['responsable_actual_id']) {
                $this->auditoriaService->record(
                    $ticket,
                    $user,
                    'asignacion_cambiada',
                    ['responsable_actual_id' => $before['responsable_actual_id']],
                    ['responsable_actual_id' => $after['responsable_actual_id']]
                );
            }

            if (array_key_exists('prioridad_id', $data) && $before['prioridad_id'] !== $after['prioridad_id']) {
                $this->auditoriaService->record(
                    $ticket,
                    $user,
                    'prioridad_cambiada',
                    ['prioridad_id' => $before['prioridad_id']],
                    ['prioridad_id' => $after['prioridad_id']]
                );
            }

            if (array_key_exists('fecha_compromiso', $data) && $before['fecha_compromiso'] !== $after['fecha_compromiso']) {
                $this->auditoriaService->record(
                    $ticket,
                    $user,
                    'fecha_compromiso_cambiada',
                    ['fecha_compromiso' => $before['fecha_compromiso']],
                    ['fecha_compromiso' => $after['fecha_compromiso']]
                );
            }

            if (array_key_exists('tipo_solicitud_id', $data) && $before['tipo_solicitud_id'] !== $after['tipo_solicitud_id']) {
                $this->auditoriaService->record(
                    $ticket,
                    $user,
                    'tipo_cambiado',
                    ['tipo_solicitud_id' => $before['tipo_solicitud_id']],
                    ['tipo_solicitud_id' => $after['tipo_solicitud_id']]
                );
            }

            if (array_key_exists('fecha_entrega', $data) && $before['fecha_entrega'] !== $after['fecha_entrega']) {
                $this->auditoriaService->record(
                    $ticket,
                    $user,
                    'fecha_entrega_cambiada',
                    ['fecha_entrega' => $before['fecha_entrega']],
                    ['fecha_entrega' => $after['fecha_entrega']]
                );
            }

            if (array_key_exists('resolucion', $data) && $before['resolucion'] !== $after['resolucion']) {
                $this->auditoriaService->record(
                    $ticket,
                    $user,
                    'resolucion_registrada',
                    ['resolucion' => $before['resolucion']],
                    ['resolucion' => $after['resolucion']]
                );
            }

            if (array_key_exists('sistema_id', $data) && $before['sistema_id'] !== $after['sistema_id']) {
                $this->auditoriaService->record(
                    $ticket,
                    $user,
                    'sistema_cambiado',
                    ['sistema_id' => $before['sistema_id']],
                    ['sistema_id' => $after['sistema_id']]
                );
            }

            return $ticket;
        });
    }

    private function assertUserCanView(User $user, Ticket $ticket): void
    {
        if (! $this->visibilityService->userCanView($user, $ticket)) {
            throw new AuthorizationException('No autorizado.');
        }
    }

    private function assertUserCanAssign(User $user, Ticket $ticket): void
    {
        if ($user->isAdmin()) {
            return;
        }

        if ($user->isCoordinador() && $this->isCoordinadorDeSistema($user, $ticket->sistema_id)) {
            return;
        }

        throw new AuthorizationException('No autorizado.');
    }

    private function assertUserCanCoordinate(User $user, Ticket $ticket): void
    {
        if ($user->isAdmin()) {
            return;
        }

        if ($user->isCoordinador() && $this->isCoordinadorDeSistema($user, $ticket->sistema_id)) {
            return;
        }

        throw new AuthorizationException('No autorizado.');
    }

    private function assertUserCanSoporte(User $user, Ticket $ticket): void
    {
        if ($user->isAdmin()) {
            return;
        }

        if ($user->isSoporte() && $ticket->responsable_actual_id === $user->id) {
            return;
        }

        throw new AuthorizationException('No autorizado.');
    }

    private function clearResponsable(Ticket $ticket, $now): void
    {
        if ($ticket->responsable_actual_id === null) {
            return;
        }

        DB::table('asignaciones_ticket')
            ->where('ticket_id', $ticket->id)
            ->whereNull('desasignado_at')
            ->update([
                'desasignado_at' => $now,
                'updated_at' => $now,
            ]);

        $ticket->responsable_actual_id = null;
    }

    private function applyResponsable(Ticket $ticket, int $responsableId, int $asignadoPorId, $now): void
    {
        if ($ticket->responsable_actual_id === $responsableId) {
            return;
        }

        DB::table('asignaciones_ticket')
            ->where('ticket_id', $ticket->id)
            ->whereNull('desasignado_at')
            ->update([
                'desasignado_at' => $now,
                'updated_at' => $now,
            ]);

        DB::table('asignaciones_ticket')->insert([
            'ticket_id' => $ticket->id,
            'responsable_id' => $responsableId,
            'asignado_por_id' => $asignadoPorId,
            'asignado_at' => $now,
            'desasignado_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $ticket->responsable_actual_id = $responsableId;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function hasCoordinatorFields(array $data): bool
    {
        return array_key_exists('prioridad_id', $data)
            || array_key_exists('fecha_compromiso', $data)
            || array_key_exists('sistema_id', $data);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function hasSoporteFields(array $data): bool
    {
        return array_key_exists('tipo_solicitud_id', $data)
            || array_key_exists('fecha_entrega', $data)
            || array_key_exists('resolucion', $data);
    }

    private function ensureCatalogoActivo(string $table, $id, string $field): void
    {
        if ($id === null) {
            return;
        }

        $activo = DB::table($table)
            ->where('id', $id)
            ->where('activo', true)
            ->exists();

        if (! $activo) {
            throw ValidationException::withMessages([
                $field => 'El valor seleccionado no esta activo.',
            ]);
        }
    }

    private function isCoordinadorDeSistema(User $user, int $sistemaId): bool
    {
        return DB::table('sistemas_coordinadores')
            ->where('usuario_id', $user->id)
            ->where('sistema_id', $sistemaId)
            ->exists();
    }
}
