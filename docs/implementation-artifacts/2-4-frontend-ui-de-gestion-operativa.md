# Story 2.4: Frontend - UI de gestion operativa

Status: ready-for-dev

## Story

As a coordinador,
I want una interfaz para gestionar estado y campos operativos,
so that pueda operar tickets sin friccion.

## Acceptance Criteria

1. En el detalle del ticket, soporte/coordinador/admin ven controles de estado, asignacion, prioridad, fechas, tipo y sistema segun su rol.
2. Cliente interno solo ve acciones de cerrar o cancelar su ticket (si aplica por estado).
3. Al actualizar estado o campos, la UI refleja el nuevo estado y valores sin recargar toda la pagina.
4. La UI muestra validaciones de reglas (ej: no permite pasar a "En progreso" sin responsable).
5. Las acciones respetan visibilidad por rol del ticket.

## Tasks / Subtasks

- [ ] Agregar seccion "Gestion operativa" en detalle del ticket (AC: #1, #2)
- [ ] Implementar controles segun rol:
  - [ ] Estado (select con transiciones permitidas)
  - [ ] Responsable (selector)
  - [ ] Prioridad (selector)
  - [ ] Fecha compromiso / fecha entrega (date inputs)
  - [ ] Tipo de solicitud y sistema (select)
  - [ ] Resolucion (textarea)
- [ ] Mostrar acciones de cerrar/cancelar para cliente interno (AC: #2)
- [ ] Manejar errores de validacion del backend y mostrarlos en UI (AC: #4)
- [ ] Actualizar detalle con resultados de API (AC: #3, #5)

## Dev Notes

- La UI debe consumir endpoints de Epic 2 (estado y campos operativos).
- Mostrar solo transiciones permitidas segun rol y estado actual (idealmente desde API).
- Respetar polling <= 60 s ya implementado en listado/detalle.

### Project Structure Notes

- app/Http/Controllers/Api, app/Services, app/Models, app/Policies
- database/migrations, resources/js, resources/css

### References

- _bmad-output/planning-artifacts/epics.md#Epic-2
- _bmad-output/planning-artifacts/architecture.md#Implementation-Patterns-Consistency-Rules
- _bmad-output/planning-artifacts/architecture.md#Stack-Versions-confirmed

## Dev Agent Record

### Agent Model Used

TBD

### Debug Log References

### Completion Notes List

### File List

