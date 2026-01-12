# Story 1.2: Backend - Autenticacion y tickets basicos

Status: done

## Story

As a cliente interno,
I want iniciar sesion y crear tickets basicos,
so that pueda registrar solicitudes y ver su estado.

## Acceptance Criteria

1. Login con credenciales validas permite iniciar sesion y acceder a endpoints protegidos.
2. Crear ticket con asunto, sistema_id y descripcion crea un ticket en estado "Nuevo", sin responsable_actual y con solicitante_id del usuario autenticado.
3. Intentar modificar el asunto o la descripcion de un ticket es rechazado.
4. Listado y detalle aplican visibilidad por rol y relacion:
   - Cliente interno: solo sus tickets y nunca tickets internos.
   - Soporte: tickets asignados + tickets donde es involucrado.
   - Coordinador: tickets de sistemas donde es coordinador (sistemas_coordinadores.usuario_id = usuario.id) + tickets donde es involucrado.
   - Admin: todos.
   - Cualquier usuario involucrado puede ver el ticket aunque no sea solicitante/responsable (excepto cliente interno en tickets internos).
5. El listado ordena por updated_at descendente.

## Tasks / Subtasks

- [x] Implementar autenticacion y middleware de sesion (AC: #1)
  - [x] Endpoints de login/logout o flujo equivalente
  - [x] Proteccion de endpoints de tickets
- [x] Crear endpoints de tickets basicos (AC: #2, #5)
  - [x] POST /api/tickets (crear)
  - [x] GET /api/tickets (listar con orden por updated_at desc)
  - [x] GET /api/tickets/{id} (detalle)
- [x] Aplicar reglas de visibilidad por rol y por involucrados en listados/detalle (AC: #4)
- [x] Excluir tickets internos para cliente interno (AC: #4)
- [x] Bloquear cambios de asunto y descripcion en update (AC: #3)
- [x] Pruebas de feature minimas para login, create y visibility (AC: #1-#5)

## Dev Notes

- Respuestas JSON en snake_case; fechas ISO 8601 UTC.
- Controllers delgados; logica en app/Services; validacion en Requests; autorizacion en Policies.
- Estado inicial "Nuevo" debe resolverse por nombre en estados_ticket (seed previo de DB-1.1).
- No implementar cambios operativos (prioridad, fechas, estado) en esta historia.
- Coordinadores se resuelven via sistemas_coordinadores (many-to-many).
- Involucrados extienden visibilidad de lectura en listados/detalle.

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

- `php artisan test --filter TicketApiTest`

### Completion Notes List

- Auth API con tokens (login/logout) y endpoints CRUD base para tickets.
- Visibilidad por rol aplicada con `TicketVisibilityService` y `TicketPolicy`.
- Se agrego `involucrados_ticket` para soportar visibilidad por involucrados antes de Epic 3.
- `RoleSeeder` + `EstadoTicketSeeder` habilitan rol y estado inicial para pruebas.
- Riesgo: `involucrados_ticket` se adelanto a Epic 1; validar compatibilidad cuando se implemente Epic 3.

### File List

- app/Http/Controllers/Api/AuthController.php
- app/Http/Controllers/Api/TicketController.php
- app/Http/Requests/Api/LoginRequest.php
- app/Http/Requests/Api/StoreTicketRequest.php
- app/Http/Requests/Api/UpdateTicketRequest.php
- app/Http/Resources/TicketResource.php
- app/Models/EstadoTicket.php
- app/Models/Role.php
- app/Models/Sistema.php
- app/Models/Ticket.php
- app/Models/User.php
- app/Policies/TicketPolicy.php
- app/Providers/AuthServiceProvider.php
- app/Services/TicketService.php
- app/Services/TicketVisibilityService.php
- database/factories/UserFactory.php
- database/migrations/2026_01_11_000005_create_involucrados_ticket_table.php
- database/seeders/RoleSeeder.php
- database/seeders/DatabaseSeeder.php
- routes/api.php
- tests/Feature/Api/TicketApiTest.php
