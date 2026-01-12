# Story 1.3: Frontend - UI de login y tickets basicos

Status: done

## Story

As a cliente interno,
I want una interfaz para crear y consultar tickets,
so that pueda enviar solicitudes sin usar otros canales.

## Acceptance Criteria

1. Existe UI de login y permite acceder al sistema con credenciales validas.
2. Existe listado de tickets con orden por ultima actualizacion descendente.
3. Existe formulario de creacion de ticket (asunto, sistema, descripcion).
4. El detalle de ticket muestra asunto, descripcion y estado.
5. La vista se refresca automaticamente en <= 60 s para reflejar cambios.
6. El detalle no permite editar asunto ni descripcion (solo lectura).

## Tasks / Subtasks

- [x] Construir pantalla de login (AC: #1)
  - [x] Validar error de credenciales
- [x] Construir listado de tickets con orden y filtros basicos (AC: #2)
- [x] Construir formulario de creacion de ticket (AC: #3)
- [x] Construir detalle de ticket con campos base (AC: #4)
- [x] Asegurar que asunto y descripcion sean solo lectura en detalle (AC: #6)
- [x] Implementar polling para listado/detalle (AC: #5)

## Dev Notes

- UI en resources/js con Inertia + Vue; estilos Tailwind.
- Respetar visibilidad por rol (la API ya filtra, pero UI no debe exponer acciones no permitidas).
- Errores del API deben mostrarse de forma clara en el formulario de ticket.

### Project Structure Notes

- app/Http/Controllers/Api, app/Services, app/Models, app/Policies
- database/migrations, resources/js, resources/css

### References

- _bmad-output/planning-artifacts/epics.md#Epic-1
- _bmad-output/planning-artifacts/architecture.md#Implementation-Patterns-Consistency-Rules
- _bmad-output/planning-artifacts/architecture.md#Stack-Versions-confirmed

## Dev Agent Record

### Agent Model Used

Codex (GPT-5)

### Debug Log References

- Validacion manual en UI: login, listado, detalle y polling (60s)

### Completion Notes List

- Vista de tickets con formulario de creacion, listado ordenado y detalle solo lectura.
- Polling cada 60s en listado y detalle.
- Ajustes de navegacion y estilos para legibilidad en inputs.
- Riesgo: sin sistemas activos el formulario no puede crear tickets; requiere seed inicial.

### File List

- app/Http/Controllers/TicketController.php
- app/Http/Controllers/Auth/RegisteredUserController.php
- app/Http/Requests/ProfileUpdateRequest.php
- app/Providers/RouteServiceProvider.php
- resources/js/Layouts/AuthenticatedLayout.vue
- resources/js/Pages/Auth/Register.vue
- resources/js/Pages/Profile/Partials/UpdateProfileInformationForm.vue
- resources/js/Pages/Tickets/Index.vue
- resources/js/Pages/Tickets/Show.vue
- resources/js/Pages/Welcome.vue
- resources/js/Components/TextInput.vue
- routes/web.php
