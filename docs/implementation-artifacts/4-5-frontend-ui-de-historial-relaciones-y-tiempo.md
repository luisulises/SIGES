# Story 4.5: Frontend - UI de historial, relaciones y tiempo

Status: ready-for-dev

## Story

As a usuario autorizado,
I want ver historial, relaciones y tiempo en el detalle,
so that tenga trazabilidad completa.

## Acceptance Criteria

1. En el detalle del ticket se muestra el historial de cambios segun visibilidad del rol.
2. Se muestran relaciones del ticket (relacionado, duplicado_de, reabre).
3. Se muestran registros de tiempo en orden cronologico; solo roles internos los ven.
4. Usuario con permisos puede crear relacion y registrar tiempo desde la UI.

## Tasks / Subtasks

- [ ] Renderizar seccion de historial con eventos (AC: #1)
- [ ] Renderizar seccion de relaciones y formulario para crear relacion (AC: #2, #4)
- [ ] Renderizar seccion de tiempo y formulario para registrar tiempo (AC: #3, #4)
- [ ] Manejar errores de validacion y refrescar datos sin recarga completa

## Dev Notes

- Cliente interno solo ve eventos de estado/cierre/cancelacion.
- La UI debe consumir endpoints de Epic 4 (historial, relaciones, tiempo).
- Reusar polling/refresco del detalle si ya existe.

### Project Structure Notes

- app/Http/Controllers/Api, app/Services, app/Models, app/Policies
- database/migrations, resources/js, resources/css

### References

- _bmad-output/planning-artifacts/epics.md#Epic-4
- _bmad-output/planning-artifacts/architecture.md#Implementation-Patterns-Consistency-Rules
- _bmad-output/planning-artifacts/architecture.md#Stack-Versions-confirmed

## Dev Agent Record

### Agent Model Used

TBD

### Debug Log References

### Completion Notes List

### File List

