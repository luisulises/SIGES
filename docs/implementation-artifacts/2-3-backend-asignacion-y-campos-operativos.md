# Story 2.3: Backend - Asignacion y campos operativos

Status: done

## Story

As a coordinador,
I want asignar responsable, prioridad y fechas,
so that el trabajo quede ordenado.

## Acceptance Criteria

1. Coordinador o admin puede asignar/reasignar responsable; tickets.responsable_actual_id se actualiza y se registra en asignaciones_ticket.
2. Al reasignar, la asignacion previa se cierra con desasignado_at y se crea una nueva asignacion con asignado_at.
3. Coordinador o admin puede actualizar prioridad_id y fecha_compromiso.
4. Soporte (asignado) o admin puede actualizar tipo_solicitud_id, fecha_entrega y resolucion.
5. Coordinador o admin puede cambiar sistema_id del ticket.
6. Cualquier actualizacion operativa valida se refleja en el ticket y respeta visibilidad por rol del ticket.

## Tasks / Subtasks

- [x] Definir endpoint/accion para actualizar campos operativos (AC: #1-#6)
  - [x] Validar visibilidad del ticket por rol antes de cambios
  - [x] Validar que catalogos (prioridad/tipo/sistema) existan y esten activos
- [x] Implementar asignacion/reasignacion (AC: #1, #2)
  - [x] Actualizar tickets.responsable_actual_id
  - [x] Insertar en asignaciones_ticket y cerrar asignacion previa si aplica
- [x] Implementar cambios de prioridad y fecha_compromiso (AC: #3)
- [x] Implementar cambios de tipo_solicitud, fecha_entrega y resolucion (AC: #4)
- [x] Implementar cambio de sistema_id (AC: #5)
- [x] Pruebas de feature por rol para cambios permitidos y bloqueados (AC: #1-#6)

## Dev Notes

- Campos operativos viven en tickets; la trazabilidad de responsable vive en asignaciones_ticket.
- Soporte solo puede operar tickets asignados; coordinador solo tickets de sus sistemas; admin puede operar cualquier ticket.
- Si existe servicio de auditoria (Epic 4), emitir evento por cada cambio operativo.

### Project Structure Notes

- app/Http/Controllers/Api, app/Services, app/Models, app/Policies
- database/migrations, resources/js, resources/css

### References

- _bmad-output/planning-artifacts/epics.md#Epic-2
- _bmad-output/planning-artifacts/architecture.md#Implementation-Patterns-Consistency-Rules
- _bmad-output/planning-artifacts/architecture.md#Stack-Versions-confirmed

## Dev Agent Record

### Agent Model Used

Codex (GPT-5)

### Debug Log References

- `php artisan test --filter TicketOperativoTest`

### Completion Notes List

- Endpoint de operacion de tickets con control por rol y validaciones de catalogos activos.
- Asignaciones registran historico con `asignado_at`/`desasignado_at`.

### File List

- app/Http/Controllers/Api/TicketOperativoController.php
- app/Http/Requests/Api/UpdateTicketOperativoRequest.php
- app/Http/Resources/TicketResource.php
- app/Models/Ticket.php
- app/Services/TicketOperativoService.php
- routes/api.php
- tests/Feature/Api/TicketOperativoTest.php

