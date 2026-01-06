# Story 3.3: Backend - Adjuntos en tickets y comentarios

Status: ready-for-dev

## Story

As a usuario,
I want adjuntar archivos en tickets o comentarios,
so that pueda aportar evidencia.

## Acceptance Criteria

1. Usuario con acceso al ticket puede adjuntar archivos al ticket o a un comentario (comentario_id opcional) segun su rol.
2. El adjunto queda registrado en adjuntos con ticket_id, comentario_id (nullable), cargado_por_id, nombre_archivo, clave_almacenamiento y visibilidad.
3. Si existe comentario_id, el adjunto hereda la visibilidad del comentario asociado; si no, se considera adjunto publico del ticket.
4. Usuarios sin acceso al ticket no pueden adjuntar archivos.

## Tasks / Subtasks

- [ ] Implementar endpoint para subir adjunto a ticket o comentario (AC: #1-#4)
  - [ ] Validar acceso al ticket y permiso de comentario segun rol
  - [ ] Validar que comentario_id pertenece al ticket si se envia
  - [ ] Guardar metadata en adjuntos con visibilidad heredada si aplica
- [ ] Implementar endpoint para listar adjuntos en un ticket/comentario (AC: #2, #3)
  - [ ] Filtrar adjuntos internos segun rol
- [ ] Pruebas de feature por rol para adjuntar y listar (AC: #1-#4)

## Dev Notes

- Almacenamiento fisico fuera de BD; en BD solo metadatos (clave_almacenamiento).
- Validar tamano y tipo permitido en request (definir limites en config).
- Adjuntos pueden vivir en ticket o comentario; si comentario_id es null, se tratan como publicos.
- Si existe auditoria, registrar evento de adjunto creado.

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

