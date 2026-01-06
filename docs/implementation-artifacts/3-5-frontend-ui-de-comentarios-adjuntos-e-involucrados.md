# Story 3.5: Frontend - UI de comentarios, adjuntos e involucrados

Status: ready-for-dev

## Story

As a usuario,
I want ver comentarios, adjuntos e involucrados en el ticket,
so that tenga contexto completo.

## Acceptance Criteria

1. En el detalle del ticket se muestran comentarios publicos visibles para el usuario.
2. Soporte/coordinador/admin pueden alternar vista de comentarios internos.
3. Los adjuntos se muestran junto al comentario o en una lista de adjuntos, respetando visibilidad.
4. Se listan involucrados actuales del ticket; coordinador/admin puede agregarlos o removerlos.
5. Al agregar comentario o adjunto, la UI refleja el nuevo elemento sin recargar toda la pagina.

## Tasks / Subtasks

- [ ] Renderizar seccion de comentarios con filtro por visibilidad (AC: #1, #2)
- [ ] Agregar formulario para nuevo comentario (publico/interno segun rol) (AC: #1, #2)
- [ ] Agregar carga de adjuntos asociada a comentario o al ticket (AC: #3)
- [ ] Mostrar lista de involucrados y controles de alta/baja (AC: #4)
- [ ] Manejar errores de validacion del backend y refrescar la vista (AC: #5)

## Dev Notes

- Comentarios internos solo visibles para roles internos.
- Adjuntos heredan visibilidad del comentario asociado.
- Reusar polling/refresco del detalle si ya existe.

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

