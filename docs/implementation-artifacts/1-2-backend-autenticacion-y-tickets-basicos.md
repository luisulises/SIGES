# Story 1.2: Backend - Autenticacion y tickets basicos

Status: ready-for-dev

## Story

As a cliente interno,
I want iniciar sesion y crear tickets basicos,
so that pueda registrar solicitudes y ver su estado.

## Acceptance Criteria

1. Login con credenciales validas permite iniciar sesion y acceder a endpoints protegidos.
2. Crear ticket con asunto, sistema_id y descripcion crea un ticket en estado "Nuevo", sin responsable_actual y con solicitante_id del usuario autenticado.
3. Intentar modificar el asunto de un ticket es rechazado.
4. Listado y detalle aplican visibilidad por rol y relacion:
   - Cliente interno: solo sus tickets y nunca tickets internos.
   - Soporte: tickets asignados + tickets donde es involucrado.
   - Coordinador: tickets de sistemas donde es coordinador (sistemas.coordinador_id = usuario.id) + tickets donde es involucrado.
   - Admin: todos.
   - Cualquier usuario involucrado puede ver el ticket aunque no sea solicitante/responsable (excepto cliente interno en tickets internos).
5. El listado ordena por updated_at descendente.

## Tasks / Subtasks

- [ ] Implementar autenticacion y middleware de sesion (AC: #1)
  - [ ] Endpoints de login/logout o flujo equivalente
  - [ ] Proteccion de endpoints de tickets
- [ ] Crear endpoints de tickets basicos (AC: #2, #5)
  - [ ] POST /api/tickets (crear)
  - [ ] GET /api/tickets (listar con orden por updated_at desc)
  - [ ] GET /api/tickets/{id} (detalle)
- [ ] Aplicar reglas de visibilidad por rol y por involucrados en listados/detalle (AC: #4)
- [ ] Excluir tickets internos para cliente interno (AC: #4)
- [ ] Bloquear cambios de asunto en update (AC: #3)
- [ ] Pruebas de feature minimas para login, create y visibility (AC: #1-#5)

## Dev Notes

- Respuestas JSON en snake_case; fechas ISO 8601 UTC.
- Controllers delgados; logica en app/Services; validacion en Requests; autorizacion en Policies.
- Estado inicial "Nuevo" debe resolverse por nombre en estados_ticket (seed previo de DB-1.1).
- No implementar cambios operativos (prioridad, fechas, estado) en esta historia.
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

TBD

### Debug Log References

### Completion Notes List

### File List

