# Story 4.5: Frontend - UI de historial, relaciones y tiempo

Status: done

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

- [x] Renderizar seccion de historial con eventos (AC: #1)
- [x] Renderizar seccion de relaciones y formulario para crear relacion (AC: #2, #4)
- [x] Renderizar seccion de tiempo y formulario para registrar tiempo (AC: #3, #4)
- [x] Manejar errores de validacion y refrescar datos sin recarga completa

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

GPT-5.2 (Codex CLI)

### Debug Log References

### Completion Notes List

- Verificado (QA) 2026-01-29: `npm.cmd -s run build` OK; UI consume endpoints Epic 4 y refresca datos sin recarga completa.

### File List

- resources/js/Pages/Tickets/Show.vue
- app/Http/Controllers/TicketController.php
