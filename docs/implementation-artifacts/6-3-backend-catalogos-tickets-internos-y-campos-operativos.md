# Story 6.3: Backend - Catalogos, tickets internos y campos operativos

Status: ready-for-dev

## Story

As a administrador,
I want gestionar catalogos e indicar tickets internos,
so that la operacion sea consistente.

## Acceptance Criteria

1. Admin puede crear/editar/desactivar catalogos (sistemas, prioridades, tipos_solicitud).
2. Admin puede marcar un ticket como interno (solo visible para roles internos).
3. Admin puede editar cualquier campo operativo de un ticket.
4. La visibilidad de tickets internos bloquea acceso a cliente interno (incluso si esta involucrado).

## Tasks / Subtasks

- [ ] Implementar endpoints CRUD para catalogos (AC: #1)
  - [ ] Validar unicidad de nombre y uso de activo
- [ ] Implementar flag de ticket interno y su validacion (AC: #2, #4)
- [ ] Implementar endpoints de actualizacion de campos operativos para admin (AC: #3)
- [ ] Pruebas de feature para catalogos y tickets internos (AC: #1-#4)

## Dev Notes

- Si tickets ya tienen flujo operativo, reutilizar el mismo endpoint con permisos admin.
- En MVP, catalogos administrables: sistemas, prioridades y tipos_solicitud (estados quedan fijos).
- Usar tickets.interno (bool, default false) como indicador (definido en DB-1.1).
- Ticket interno afecta visibilidad en listados y detalle (coordinar con filtros de Epic 1).

### Project Structure Notes

- app/Http/Controllers/Api, app/Services, app/Models, app/Policies
- database/migrations, resources/js, resources/css

### References

- _bmad-output/planning-artifacts/epics.md#Epic-6
- _bmad-output/planning-artifacts/architecture.md#Implementation-Patterns-Consistency-Rules
- _bmad-output/planning-artifacts/architecture.md#Stack-Versions-confirmed

## Dev Agent Record

### Agent Model Used

TBD

### Debug Log References

### Completion Notes List

### File List

