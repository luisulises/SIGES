# Story 1.1: BD - Esquema base de usuarios, roles y tickets

Status: done

## Story

As a administrador del sistema,
I want disponer del esquema base de usuarios, roles, sistemas, estados y tickets,
so that los usuarios puedan autenticarse y registrar solicitudes.

## Acceptance Criteria

1. Existen tablas roles, usuarios, sistemas, estados_ticket, sistemas_coordinadores y tickets con columnas base y timestamps.
2. FKs definidas: usuarios.rol_id -> roles.id; sistemas_coordinadores.sistema_id -> sistemas.id; sistemas_coordinadores.usuario_id -> usuarios.id; tickets.solicitante_id -> usuarios.id; tickets.responsable_actual_id -> usuarios.id (nullable); tickets.sistema_id -> sistemas.id; tickets.estado_id -> estados_ticket.id.
3. Existe el estado "Nuevo" en estados_ticket y tickets.responsable_actual_id permite null.
4. tickets incluye campo interno (bool) con default false.
5. Se puede insertar un ticket con estado "Nuevo" y responsable_actual null sin violar restricciones.

## Tasks / Subtasks

- [x] Crear migraciones para roles, usuarios, sistemas, estados_ticket, sistemas_coordinadores y tickets (AC: #1)
  - [x] roles: id, nombre (unique)
  - [x] usuarios: nombre, email (unique), rol_id, activo, created_at, updated_at, desactivado_at
  - [x] sistemas: nombre (unique), activo
  - [x] estados_ticket: nombre (unique), es_terminal
  - [x] sistemas_coordinadores: sistema_id, usuario_id, created_at, updated_at
  - [x] tickets: asunto, descripcion, solicitante_id, sistema_id, estado_id, responsable_actual_id, interno, created_at, updated_at
- [x] Agregar FKs e indices necesarios (AC: #2)
- [x] Agregar unique (sistema_id, usuario_id) en sistemas_coordinadores
- [x] Seed de estado "Nuevo" en estados_ticket (AC: #3)
- [x] Validar inserts minimos con responsable_actual null (AC: #4)

## Dev Notes

- Nombres en espanol y snake_case para tablas y columnas de dominio.
- MVP soporta multiples coordinadores por sistema via sistemas_coordinadores.
- No crear tablas de prioridad o tipo_solicitud en esta historia; se agregan en DB-2.1.
- tickets.interno debe ser bool con default false para soportar visibilidad por rol desde Epic 1.
- Identificador publico del ticket se definira mas adelante; usar id en MVP.

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

- `php artisan migrate:fresh --seed`
- Validacion de insert con `responsable_actual_id` null via `psql` (transaccion con rollback)

### Completion Notes List

- Migraciones base creadas con FKs y `tickets.interno` por defecto.
- Estado "Nuevo" sembrado en `estados_ticket`.
- Insert de ticket con responsable_actual null validado.
- Riesgo: catalogos de prioridad/tipo_solicitud se agregan en DB-2.1; revisar dependencias posteriores.

### File List

- database/migrations/2014_10_11_000000_create_roles_table.php
- database/migrations/2014_10_12_000000_create_users_table.php
- database/migrations/2026_01_11_000001_create_sistemas_table.php
- database/migrations/2026_01_11_000002_create_estados_ticket_table.php
- database/migrations/2026_01_11_000003_create_sistemas_coordinadores_table.php
- database/migrations/2026_01_11_000004_create_tickets_table.php
- database/seeders/EstadoTicketSeeder.php
- database/seeders/DatabaseSeeder.php
