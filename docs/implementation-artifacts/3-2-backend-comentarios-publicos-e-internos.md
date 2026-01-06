# Story 3.2: Backend - Comentarios publicos e internos

Status: ready-for-dev

## Story

As a usuario,
I want agregar comentarios publicos o internos,
so that pueda comunicar avances con el nivel correcto de visibilidad.

## Acceptance Criteria

1. Cliente interno puede crear comentarios publicos en sus tickets.
2. Soporte/coordinador/admin puede crear comentarios publicos en tickets que puede gestionar.
3. Comentarios internos solo pueden ser creados por soporte/coordinador/admin y solo son visibles para esos roles.
4. Comentarios publicos son visibles para solicitante, responsables y roles internos con acceso al ticket.
5. El comentario queda registrado con autor_id, visibilidad y timestamps.

## Tasks / Subtasks

- [ ] Implementar endpoint para crear comentario (AC: #1-#5)
  - [ ] Validar visibilidad permitida segun rol
  - [ ] Validar acceso al ticket antes de crear
- [ ] Implementar endpoint para listar comentarios por ticket (AC: #3, #4)
  - [ ] Filtrar comentarios internos segun rol
- [ ] Pruebas de feature por rol (AC: #1-#5)

## Dev Notes

- Usar visibilidad en comentarios_ticket: publico|interno.
- Rechazar creacion de comentario interno por cliente interno.
- Si existe auditoria, registrar evento de comentario creado.

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

