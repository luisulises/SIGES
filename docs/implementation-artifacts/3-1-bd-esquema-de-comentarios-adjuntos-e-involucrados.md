# Story 3.1: BD - Esquema de comentarios, adjuntos e involucrados

Status: done

## Story

As a administrador del sistema,
I want tablas para comentarios, adjuntos e involucrados,
so that la colaboracion quede registrada.

## Acceptance Criteria

1. Existen tablas comentarios_ticket, adjuntos e involucrados_ticket con columnas base y timestamps.
2. FKs definidas: comentarios_ticket.ticket_id -> tickets.id; comentarios_ticket.autor_id -> usuarios.id; adjuntos.ticket_id -> tickets.id; adjuntos.comentario_id -> comentarios_ticket.id (nullable); adjuntos.cargado_por_id -> usuarios.id; involucrados_ticket.ticket_id -> tickets.id; involucrados_ticket.usuario_id -> usuarios.id; involucrados_ticket.agregado_por_id -> usuarios.id.
3. comentarios_ticket incluye visibilidad (publico|interno) y adjuntos incluye visibilidad.
4. involucrados_ticket tiene indice unico (ticket_id, usuario_id).

## Tasks / Subtasks

- [x] Crear migracion para comentarios_ticket (AC: #1, #2, #3)
  - [x] columnas: ticket_id, autor_id, cuerpo, visibilidad, created_at, updated_at
  - [x] indices por ticket_id y autor_id
- [x] Crear migracion para adjuntos (AC: #1, #2, #3)
  - [x] columnas: ticket_id, comentario_id (nullable), cargado_por_id, nombre_archivo, clave_almacenamiento, visibilidad, created_at, updated_at
  - [x] indices por ticket_id y comentario_id
- [x] Crear migracion para involucrados_ticket (AC: #1, #2, #4)
  - [x] columnas: ticket_id, usuario_id, agregado_por_id, created_at, updated_at
  - [x] indice unico (ticket_id, usuario_id) e indices por ticket_id
- [x] Agregar FKs e indices necesarios (AC: #2, #4)

## Dev Notes

- Comentarios internos se filtran por rol en backend; el esquema solo guarda visibilidad.
- Si adjuntos.comentario_id existe, debe pertenecer al mismo ticket (validar en aplicacion).
- Involucrados_ticket soporta notificaciones y visibilidad adicional sin usar solo solicitante/responsable.

### Project Structure Notes

- app/Http/Controllers/Api, app/Services, app/Models, app/Policies
- database/migrations, resources/js, resources/css

### References

- _bmad-output/planning-artifacts/epics.md#Epic-3
- _bmad-output/planning-artifacts/architecture.md#Implementation-Patterns-Consistency-Rules
- _bmad-output/planning-artifacts/architecture.md#Stack-Versions-confirmed

## Dev Agent Record

### Agent Model Used

GPT-5.2

### Debug Log References

- `php artisan test` (Postgres)

### Completion Notes List

- Tablas nuevas: `comentarios_ticket`, `adjuntos` (visibilidad `publico|interno`).
- `involucrados_ticket` ya existia desde Epic 1; se extendio con soft delete y un indice unico solo para registros activos.

### File List

- database/migrations/2026_01_21_000001_create_comentarios_ticket_table.php
- database/migrations/2026_01_21_000002_create_adjuntos_table.php
- database/migrations/2026_01_21_000003_add_soft_deletes_to_involucrados_ticket_table.php
- database/migrations/2026_01_11_000005_create_involucrados_ticket_table.php

