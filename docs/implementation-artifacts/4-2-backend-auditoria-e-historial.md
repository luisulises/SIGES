# Story 4.2: Backend - Auditoria e historial

Status: done

## Story

As a coordinador,
I want historial de cambios del ticket,
so that pueda explicar decisiones y avances.

## Acceptance Criteria

1. Los cambios operativos (estado, asignacion, prioridad, fechas, tipo, sistema, resolucion, cierre/cancelacion) y eventos de colaboracion (comentarios y adjuntos) generan eventos en eventos_auditoria_ticket.
2. Coordinador puede consultar el historial de tickets de sus sistemas; admin puede consultar cualquier historial.
3. Los eventos se devuelven en orden cronologico ascendente con actor, tipo_evento y valores antes/despues.
4. Cliente interno solo ve eventos de estado y cierre/cancelacion (no cambios internos).

## Tasks / Subtasks

- [x] Implementar servicio de auditoria para registrar eventos (AC: #1)
  - [x] Integrar en cambios de estado/asignacion/campos operativos
- [x] Implementar endpoint para listar historial por ticket (AC: #2, #3, #4)
  - [x] Aplicar filtro de visibilidad por rol
  - [x] Ordenar por created_at asc
- [x] Pruebas de feature para historial por rol (AC: #2-#4)

## Dev Notes

- Mantener eventos append-only; no editar ni borrar registros.
- tipo_evento recomendado: estado_cambiado, asignacion_cambiada, prioridad_cambiada, fecha_compromiso_cambiada, fecha_entrega_cambiada, sistema_cambiado, tipo_cambiado, resolucion_registrada, cierre, cancelacion, comentario_creado, adjunto_creado.
- valores_antes/despues pueden guardarse como JSON string.

### Project Structure Notes

- app/Http/Controllers/Api, app/Services, app/Models, app/Policies
- database/migrations, resources/js, resources/css

### References

- _bmad-output/planning-artifacts/epics.md#Epic-4
- _bmad-output/planning-artifacts/architecture.md#Implementation-Patterns-Consistency-Rules
- _bmad-output/planning-artifacts/architecture.md#Stack-Versions-confirmed

## Dev Agent Record

### Agent Model Used

GPT-5.2 (Codex CLI)

### Debug Log References

### Completion Notes List

- Verificado (QA) 2026-01-29: `php artisan test --compact` (69 passed) y endpoint `GET /api/tickets/{ticket}/historial` presente en `php artisan route:list --path=api`.

### File List

- app/Services/TicketAuditoriaService.php
- app/Services/TicketHistorialService.php
- app/Services/TicketWorkflowService.php
- app/Services/TicketOperativoService.php
- app/Http/Controllers/Api/TicketHistorialController.php
- app/Http/Resources/EventoAuditoriaTicketResource.php
- routes/api.php
- tests/Feature/Api/TicketHistorialTest.php
