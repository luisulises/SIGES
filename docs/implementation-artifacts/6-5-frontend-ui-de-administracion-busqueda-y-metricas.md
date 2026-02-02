# Story 6.5: Frontend - UI de administracion, busqueda y metricas

Status: done

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

- [x] Crear vistas de administracion de usuarios y catalogos (solo admin) (AC: #1)
  - [x] Incluir selector de rol en formulario de usuario
- [x] Crear vista de busqueda con filtros (asunto, estado, sistema) (AC: #2)
- [x] Renderizar metricas basicas (AC: #3)
- [x] Agregar control de ticket interno en detalle para admin (AC: #4)
- [x] Manejar errores de validacion y refresco de datos (AC: #5)

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

GPT-5.2 (Codex CLI)

### Debug Log References

### Completion Notes List

- Nuevas pantallas Inertia (admin usuarios/catalogos, busqueda, metricas) + links de navegacion por rol + toggle de ticket interno en detalle.

### File List

- app/Http/Middleware/HandleInertiaRequests.php
- app/Http/Controllers/AdminController.php
- app/Http/Controllers/BusquedaController.php
- app/Http/Controllers/MetricasController.php
- routes/web.php
- resources/js/Layouts/AuthenticatedLayout.vue
- resources/js/Pages/Admin/Users.vue
- resources/js/Pages/Admin/Catalogs.vue
- resources/js/Pages/Search.vue
- resources/js/Pages/Metrics.vue
- app/Http/Controllers/TicketController.php
- resources/js/Pages/Tickets/Show.vue

