# Story 4.1: BD - Esquema de auditoria, relaciones y tiempo

Status: done

## Story

As a administrador del sistema,
I want tablas de auditoria, relaciones y registro de tiempo,
so that la trazabilidad sea completa.

## Acceptance Criteria

1. Existen tablas eventos_auditoria_ticket, relaciones_ticket y registros_tiempo_ticket con columnas base y timestamps.
2. FKs definidas: eventos_auditoria_ticket.ticket_id -> tickets.id; eventos_auditoria_ticket.actor_id -> usuarios.id; relaciones_ticket.ticket_id -> tickets.id; relaciones_ticket.ticket_relacionado_id -> tickets.id; relaciones_ticket.creado_por_id -> usuarios.id; registros_tiempo_ticket.ticket_id -> tickets.id; registros_tiempo_ticket.autor_id -> usuarios.id.
3. eventos_auditoria_ticket es append-only (sin update/delete en app).
4. relaciones_ticket tiene indice unico (ticket_id, ticket_relacionado_id, tipo_relacion) y no permite auto-relacion.
5. registros_tiempo_ticket es append-only y almacena minutos acumulados por entrada.

## Tasks / Subtasks

- [x] Crear migracion para eventos_auditoria_ticket (AC: #1-#3)
  - [x] columnas: ticket_id, actor_id, tipo_evento, valor_antes, valor_despues, metadatos, created_at, updated_at
  - [x] indices por ticket_id y actor_id
- [x] Crear migracion para relaciones_ticket (AC: #1, #2, #4)
  - [x] columnas: ticket_id, ticket_relacionado_id, tipo_relacion, creado_por_id, created_at, updated_at
  - [x] indice unico y constraint para evitar auto-relacion
- [x] Crear migracion para registros_tiempo_ticket (AC: #1, #2, #5)
  - [x] columnas: ticket_id, autor_id, minutos, nota (nullable), created_at, updated_at
  - [x] indices por ticket_id y autor_id
- [x] Agregar FKs e indices necesarios (AC: #2, #4)

## Dev Notes

- relaciones_ticket soporta tipos: relacionado, duplicado_de, reabre (segun modelo logico).
- registros_tiempo_ticket es no editable en app; solo insert.
- Si se usa soft delete en tickets, considerar restricciones para relaciones.

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

- Verificado (QA) 2026-01-29: `docker compose up -d`, `php artisan migrate:status`, `php artisan test`, `npm.cmd -s run build`.

### File List

- database/migrations/2026_01_28_000001_create_eventos_auditoria_ticket_table.php
- database/migrations/2026_01_28_000002_create_relaciones_ticket_table.php
- database/migrations/2026_01_28_000003_create_registros_tiempo_ticket_table.php
- app/Models/EventoAuditoriaTicket.php
- app/Models/RelacionTicket.php
- app/Models/RegistroTiempoTicket.php
