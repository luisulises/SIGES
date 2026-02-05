# Story 4.3: Backend - Relaciones, duplicados y referencia

Status: done

## Story

As a usuario,
I want relacionar tickets y marcar duplicados,
so that exista trazabilidad entre solicitudes.

## Acceptance Criteria

1. Usuario con acceso al ticket puede crear relacion entre tickets con tipo_relacion (relacionado|reabre). Para tipo_relacion=reabre, el ticket relacionado debe estar en estado "Cerrado" o "Cancelado". Marcar duplicado requiere rol coordinador/admin autorizado (coordinador del sistema o admin).
2. No se permite relacionar un ticket consigo mismo ni duplicar una relacion existente.
3. Marcar duplicado actualiza el ticket duplicado a estado "Cancelado" y crea la relacion duplicado_de hacia el ticket valido (que no puede estar en estado "Cancelado").
4. Se puede listar relaciones de un ticket, respetando visibilidad por rol.

## Tasks / Subtasks

- [x] Implementar endpoint para crear relacion (AC: #1-#3)
  - [x] Validar acceso al ticket origen y al relacionado
  - [x] Validar permiso de cancelacion para tipo_relacion=duplicado_de
  - [x] Evitar auto-relacion y duplicados (unique constraint)
- [x] Implementar logica de duplicado (AC: #3)
  - [x] Cambiar estado a "Cancelado" y set cancelado_at
- [x] Implementar endpoint para listar relaciones por ticket (AC: #4)
- [x] Pruebas de feature para crear/listar relaciones y duplicados (AC: #1-#4)

## Dev Notes

- Cuando se marca duplicado, mantener referencia al ticket valido con tipo_relacion=duplicado_de.
- El permiso para duplicados debe alinearse con reglas de cancelacion (Epic 2).
- Reapertura indirecta: crear nuevo ticket y relacion tipo reabre (no reabrir el ticket cerrado).
- Nota MVP: `POST /api/tickets` acepta `referencia_ticket_id` para crear automaticamente una relacion tipo `reabre` hacia un ticket Cerrado/Cancelado.
- Si existe auditoria, registrar evento de relacion creada y duplicado.

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

- Verificado (QA) 2026-01-29: `php artisan test --compact` (incluye `TicketRelacionTest`) y endpoints `GET|POST /api/tickets/{ticket}/relaciones`.

### File List

- app/Services/TicketRelacionService.php
- app/Http/Controllers/Api/TicketRelacionController.php
- app/Http/Requests/Api/StoreRelacionTicketRequest.php
- app/Http/Resources/RelacionTicketResource.php
- routes/api.php
- tests/Feature/Api/TicketRelacionTest.php
