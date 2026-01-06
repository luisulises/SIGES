# Story 1.1: BD - Esquema base de usuarios, roles y tickets

Status: ready-for-dev

## Story

As a administrador del sistema,
I want disponer del esquema base de usuarios, roles, sistemas, estados y tickets,
so that los usuarios puedan autenticarse y registrar solicitudes.

## Acceptance Criteria

1. Existen tablas roles, usuarios, sistemas, estados_ticket y tickets con columnas base y timestamps.
2. FKs definidas: usuarios.rol_id -> roles.id; sistemas.coordinador_id -> usuarios.id (nullable); tickets.solicitante_id -> usuarios.id; tickets.responsable_actual_id -> usuarios.id (nullable); tickets.sistema_id -> sistemas.id; tickets.estado_id -> estados_ticket.id.
3. Existe el estado "Nuevo" en estados_ticket y tickets.responsable_actual_id permite null.
4. Se puede insertar un ticket con estado "Nuevo" y responsable_actual null sin violar restricciones.

## Tasks / Subtasks

- [ ] Crear migraciones para roles, usuarios, sistemas, estados_ticket y tickets (AC: #1)
  - [ ] roles: id, nombre (unique)
  - [ ] usuarios: nombre, email (unique), rol_id, activo, created_at, updated_at, desactivado_at
  - [ ] sistemas: nombre (unique), activo, coordinador_id (nullable)
  - [ ] estados_ticket: nombre (unique), es_terminal
  - [ ] tickets: codigo, asunto, descripcion, solicitante_id, sistema_id, estado_id, responsable_actual_id, created_at, updated_at
- [ ] Agregar FKs e indices necesarios (AC: #2)
- [ ] Seed de estado "Nuevo" en estados_ticket (AC: #3)
- [ ] Validar inserts minimos con responsable_actual null (AC: #4)

## Dev Notes

- Nombres en espanol y snake_case para tablas y columnas de dominio.
- MVP asume 1 coordinador por sistema (sistemas.coordinador_id).
- No crear tablas de prioridad o tipo_solicitud en esta historia; se agregan en DB-2.1.
- codigo puede quedar nullable hasta definir generacion en backend.

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

