# Story 6.1: BD - Catalogos y campos administrativos

Status: done

## Story

As a administrador del sistema,
I want catalogos y campos administrativos adicionales,
so that pueda gobernar el sistema.

## Acceptance Criteria

1. Existen tablas catalogos para proyectos (sistemas), prioridades y tipos_solicitud con campo activo.
2. usuarios incluye campos administrativos: activo (bool) y desactivado_at (nullable).
3. tickets incluye campos administrativos: cancelado_at y cerrado_at (si no existen ya en migraciones previas).
4. Los catalogos permiten marcar registros como inactivos sin eliminarlos.

## Tasks / Subtasks

- [x] Verificar migraciones previas de usuarios y tickets; agregar campos faltantes (AC: #2, #3)
- [x] Asegurar que sistemas, prioridades y tipos_solicitud tengan campo activo (AC: #1, #4)
- [x] Agregar indices por activo en catalogos (AC: #4)

## Dev Notes

- sistemas/prioridades/tipos_solicitud ya se crearon en historias BD previas; esta historia solo completa campos administrativos faltantes.
- Evitar duplicar columnas ya existentes; ajustar migracion de alter si aplica.

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

- AC verificados: catalogos y campos administrativos ya existian por migraciones previas; se agregaron indices por `activo`.

### File List

- database/migrations/2026_01_30_000002_add_activo_indexes_to_catalogos.php

