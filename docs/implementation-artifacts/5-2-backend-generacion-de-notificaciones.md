# Story 5.2: Backend - Generacion de notificaciones

Status: done

## Story

As a usuario,
I want recibir notificaciones por eventos clave,
so that este informado sin perseguir a nadie.

## Acceptance Criteria

1. Se generan notificaciones in_app para eventos: creacion de ticket, asignacion/reasignacion, cambio de estado, comentario publico, cierre/cancelacion.
2. El solicitante y los involucrados activos reciben notificaciones de cambios relevantes en su ticket.
3. Soporte/coordinador reciben notificacion cuando se les asigna un ticket.
4. Las notificaciones quedan registradas en notificaciones con usuario_id, ticket_id, tipo_evento y canal.
5. El usuario puede marcar notificaciones como leidas.

## Tasks / Subtasks

- [x] Implementar servicio de notificaciones para eventos clave (AC: #1-#4)
  - [x] Definir destinatarios segun rol (solicitante, responsable, involucrados, coordinador)
  - [x] Excluir usuarios desactivados de los destinatarios
- [x] Integrar servicio en eventos de tickets (AC: #1-#3)
  - [x] Creacion, asignacion, cambio de estado, comentario publico, cierre/cancelacion
- [x] Implementar endpoint para listar notificaciones in_app por usuario (AC: #4)
  - [x] Filtro por leido_at null y paginacion
- [x] Implementar endpoint para marcar notificaciones como leidas (AC: #5)
- [x] Pruebas de feature para generacion y lectura (AC: #1-#5)

## Dev Notes

- Correo queda post-MVP; en MVP usar solo canal in_app.
- Evitar notificar cambios internos a solicitante si no aplica (comentarios internos).
- Usuarios desactivados no deben recibir notificaciones.
- Mantener consistencia con visibilidad de comentarios/adjuntos.

### Project Structure Notes

- app/Http/Controllers/Api, app/Services, app/Models, app/Policies
- database/migrations, resources/js, resources/css

### References

- _bmad-output/planning-artifacts/epics.md#Epic-5
- _bmad-output/planning-artifacts/architecture.md#Implementation-Patterns-Consistency-Rules
- _bmad-output/planning-artifacts/architecture.md#Stack-Versions-confirmed

## Dev Agent Record

### Agent Model Used

GPT-5.2 (Codex CLI)

### Debug Log References

### Completion Notes List

- Verificado (QA) 2026-01-29: `php artisan test --compact` (71 passed) + rutas `GET /api/notificaciones` y `POST /api/notificaciones/{notificacion}/leer`.

### File List

- app/Services/TicketNotificacionService.php
- app/Services/TicketService.php
- app/Services/TicketOperativoService.php
- app/Services/TicketWorkflowService.php
- app/Services/TicketComentarioService.php
- app/Http/Controllers/Api/NotificacionController.php
- app/Http/Resources/NotificacionResource.php
- routes/api.php
- tests/Feature/Api/NotificacionTest.php
