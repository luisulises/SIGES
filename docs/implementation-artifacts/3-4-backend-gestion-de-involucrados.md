# Story 3.4: Backend - Gestion de involucrados

Status: ready-for-dev

## Story

As a coordinador,
I want agregar o remover involucrados,
so that las personas relevantes sigan el ticket.

## Acceptance Criteria

1. Coordinador o admin puede agregar involucrados a un ticket de sus sistemas; el involucrado queda registrado en involucrados_ticket.
2. Coordinador o admin puede remover involucrados; el registro se elimina (o marca inactivo si se decide soft delete).
3. No se permite duplicar involucrados (unico por ticket_id + usuario_id).
4. Los involucrados agregados pueden ver el ticket aunque no sean solicitante/responsable (excepto cliente interno en tickets internos).

## Tasks / Subtasks

- [ ] Implementar endpoints para agregar y remover involucrados (AC: #1-#3)
  - [ ] Validar visibilidad y rol antes de modificar
  - [ ] Evitar duplicados (manejo de unique constraint)
- [ ] Implementar endpoint para listar involucrados por ticket (AC: #4)
- [ ] Pruebas de feature para agregar/remover/listar (AC: #1-#4)

## Dev Notes

- En MVP basta con hard delete del involucrado; si se requiere historial, migrar a soft delete.
- La visibilidad extendida aplica a listado/detalle de tickets (coordinar con endpoints de Epic 1/2).
- En tickets internos, clientes internos no deben tener visibilidad aunque esten involucrados.
- Si existe auditoria, registrar evento de alta/baja de involucrado.

### Project Structure Notes

- app/Http/Controllers/Api, app/Services, app/Models, app/Policies
- database/migrations, resources/js, resources/css

### References

- _bmad-output/planning-artifacts/epics.md#Epic-3
- _bmad-output/planning-artifacts/architecture.md#Implementation-Patterns-Consistency-Rules
- _bmad-output/planning-artifacts/architecture.md#Stack-Versions-confirmed

## Dev Agent Record

### Agent Model Used

TBD

### Debug Log References

### Completion Notes List

### File List

