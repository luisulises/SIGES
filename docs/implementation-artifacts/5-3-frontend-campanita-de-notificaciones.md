# Story 5.3: Frontend - Campanita de notificaciones

Status: done

## Story

As a usuario,
I want una campanita de notificaciones,
so that vea eventos recientes del ticket.

## Acceptance Criteria

1. La UI muestra un icono de campanita con conteo de notificaciones no leidas.
2. Al abrir la campanita, se listan notificaciones recientes con tipo_evento, ticket y fecha.
3. El usuario puede marcar notificaciones como leidas desde la UI.
4. La campanita se actualiza automaticamente en <= 60 s.

## Tasks / Subtasks

- [x] Agregar componente de campanita en layout principal (AC: #1-#4)
- [x] Consumir endpoint de notificaciones y renderizar lista (AC: #2)
- [x] Implementar accion de marcar como leida (AC: #3)
- [x] Actualizar conteo con polling <= 60 s (AC: #4)

## Dev Notes

- Reusar polling global si existe; evitar polling duplicado por vista.
- Respetar formatos de fecha en UI.

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

- Verificado (QA) 2026-01-29: `npm.cmd -s run build` OK; campanita hace polling cada 60s a `/api/notificaciones`.

### File List

- resources/js/Components/NotificationsBell.vue
- resources/js/Layouts/AuthenticatedLayout.vue
