# Story 5.1: BD - Esquema de notificaciones

Status: done

## Story

As a administrador del sistema,
I want una tabla de notificaciones,
so that el sistema pueda registrar avisos in_app y por correo.

## Acceptance Criteria

1. Existe tabla notificaciones con columnas base y timestamps.
2. FKs definidas: notificaciones.usuario_id -> usuarios.id; notificaciones.ticket_id -> tickets.id.
3. notificaciones incluye tipo_evento, canal (in_app|email) y leido_at (nullable).
4. Existen indices por usuario_id y leido_at para consultas de campanita.

## Tasks / Subtasks

- [x] Crear migracion para notificaciones (AC: #1-#4)
  - [x] columnas: usuario_id, ticket_id, tipo_evento, canal, leido_at, created_at, updated_at
  - [x] indices por usuario_id, leido_at y ticket_id
- [x] Agregar FKs e indices necesarios (AC: #2, #4)

## Dev Notes

- tipo_evento debe cubrir eventos clave del MVP (creacion, asignacion, cambio_estado, comentario_publico, cierre/cancelacion).
- canal se limita a in_app y email en MVP.

### Project Structure Notes

- app/Http/Controllers/Api, app/Services, app/Models, app/Policies
- database/migrations, resources/js, resources/css

### References

- _bmad-output/planning-artifacts/epics.md#Epic-5
- _bmad-output/planning-artifacts/architecture.md#Implementation-Patterns-Consistency-Rules
- _bmad-output/planning-artifacts/architecture.md#Stack-Versions-confirmed

## Dev Agent Record

### Agent Model Used

GPT-5.2 (Codex CLI)

### Debug Log References

### Completion Notes List

- Verificado (QA) 2026-01-29: `php artisan migrate` aplica `2026_01_29_000001_create_notificaciones_table`.

### File List

- database/migrations/2026_01_29_000001_create_notificaciones_table.php
- app/Models/Notificacion.php
