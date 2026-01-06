# Story 6.5: Frontend - UI de administracion, busqueda y metricas

Status: ready-for-dev

## Story

As a administrador o coordinador,
I want pantallas de administracion, busqueda y metricas,
so that pueda operar y gobernar el sistema.

## Acceptance Criteria

1. Admin ve pantallas de administracion de usuarios (incluye asignar rol) y catalogos.
2. Coordinador y admin pueden buscar tickets por asunto, estado y sistema.
3. Coordinador y admin ven metricas basicas (conteo por estado y prioridad).
4. Admin puede marcar/unmarcar un ticket como interno desde la UI.
5. La UI respeta visibilidad por rol en listados y resultados.

## Tasks / Subtasks

- [ ] Crear vistas de administracion de usuarios y catalogos (solo admin) (AC: #1)
  - [ ] Incluir selector de rol en formulario de usuario
- [ ] Crear vista de busqueda con filtros (asunto, estado, sistema) (AC: #2)
- [ ] Renderizar metricas basicas (AC: #3)
- [ ] Agregar control de ticket interno en detalle para admin (AC: #4)
- [ ] Manejar errores de validacion y refresco de datos (AC: #5)

## Dev Notes

- Reusar componentes de listado de tickets donde aplique.
- Mantener filtros en query params para compartir enlaces.

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

