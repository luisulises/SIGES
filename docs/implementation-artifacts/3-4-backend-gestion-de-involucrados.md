# Story 3.4: Backend - Gestion de involucrados

Status: done

## Story

As a coordinador,
I want agregar o remover involucrados,
so that las personas relevantes sigan el ticket.

## Acceptance Criteria

1. Coordinador o admin puede agregar involucrados a un ticket de sus sistemas; el involucrado queda registrado en involucrados_ticket.
2. Coordinador o admin puede remover involucrados; el registro se elimina (o marca inactivo si se decide soft delete).
3. No se permite duplicar involucrados (unico por ticket_id + usuario_id).
4. Los involucrados agregados pueden ver el ticket aunque no sean solicitante/responsable (excepto cliente interno en tickets internos).

## Tasks / Subtasks

- [x] Implementar endpoints para agregar y remover involucrados (AC: #1-#3)
  - [x] Validar visibilidad y rol antes de modificar
  - [x] Evitar duplicados (manejo de unique constraint)
- [x] Implementar endpoint para listar involucrados por ticket (AC: #4)
- [x] Pruebas de feature para agregar/remover/listar (AC: #1-#4)

## Dev Notes

- En MVP basta con hard delete del involucrado; si se requiere historial, migrar a soft delete.
- La visibilidad extendida aplica a listado/detalle de tickets (coordinar con endpoints de Epic 1/2).
- En tickets internos, clientes internos no deben tener visibilidad aunque esten involucrados.
- Si existe auditoria, registrar evento de alta/baja de involucrado.

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

- Se implemento soft delete para `involucrados_ticket` y un indice unico parcial para registros activos.
- `TicketVisibilityService` ignora involucrados con `deleted_at` (no dan visibilidad).
- Re-agregar un involucrado restaurara el registro soft-deleted.

### File List

- app/Http/Controllers/Api/TicketInvolucradoController.php
- app/Http/Requests/Api/StoreInvolucradoRequest.php
- app/Http/Resources/InvolucradoTicketResource.php
- app/Models/InvolucradoTicket.php
- app/Services/TicketInvolucradoService.php
- app/Services/TicketVisibilityService.php
- database/migrations/2026_01_21_000003_add_soft_deletes_to_involucrados_ticket_table.php
- routes/api.php
- tests/Feature/Api/TicketInvolucradoTest.php

