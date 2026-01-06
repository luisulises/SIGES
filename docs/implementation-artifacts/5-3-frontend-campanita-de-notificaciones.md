# Story 5.3: Frontend - Campanita de notificaciones

Status: ready-for-dev

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

- [ ] Agregar componente de campanita en layout principal (AC: #1-#4)
- [ ] Consumir endpoint de notificaciones y renderizar lista (AC: #2)
- [ ] Implementar accion de marcar como leida (AC: #3)
- [ ] Actualizar conteo con polling <= 60 s (AC: #4)

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

TBD

### Debug Log References

### Completion Notes List

### File List

