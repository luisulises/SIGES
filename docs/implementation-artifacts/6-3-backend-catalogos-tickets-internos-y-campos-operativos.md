# Story 6.3: Backend - Catalogos, tickets internos y campos operativos

Status: done

## Story

As a administrador,
I want gestionar catalogos e indicar tickets internos,
so that la operacion sea consistente.

## Acceptance Criteria

1. Admin puede crear/editar/desactivar catalogos (sistemas, prioridades, tipos_solicitud).
2. Admin puede marcar un ticket como interno (solo visible para roles internos).
3. Admin puede editar cualquier campo operativo de un ticket.
4. La visibilidad de tickets internos bloquea acceso a cliente interno (incluso si esta involucrado).

## Tasks / Subtasks

- [x] Implementar endpoints CRUD para catalogos (AC: #1)
  - [x] Validar unicidad de nombre y uso de activo
- [x] Implementar flag de ticket interno y su validacion (AC: #2, #4)
- [x] Implementar endpoints de actualizacion de campos operativos para admin (AC: #3)
- [x] Pruebas de feature para catalogos y tickets internos (AC: #1-#4)

## Dev Notes

- Si tickets ya tienen flujo operativo, reutilizar el mismo endpoint con permisos admin.
- En MVP, catalogos administrables: sistemas, prioridades y tipos_solicitud (estados quedan fijos).
- Usar tickets.interno (bool, default false) como indicador (definido en DB-1.1).
- Ticket interno afecta visibilidad en listados y detalle (coordinar con filtros de Epic 1).

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

- Catalogos admin CRUD (sin delete) + flag `tickets.interno` editable solo por admin via endpoint operativo.

### File List

- app/Models/Prioridad.php
- app/Models/TipoSolicitud.php
- app/Http/Controllers/Api/AdminSistemaCatalogoController.php
- app/Http/Controllers/Api/AdminPrioridadCatalogoController.php
- app/Http/Controllers/Api/AdminTipoSolicitudCatalogoController.php
- app/Http/Requests/Api/Admin/StoreSistemaCatalogoRequest.php
- app/Http/Requests/Api/Admin/UpdateSistemaCatalogoRequest.php
- app/Http/Requests/Api/Admin/StorePrioridadCatalogoRequest.php
- app/Http/Requests/Api/Admin/UpdatePrioridadCatalogoRequest.php
- app/Http/Requests/Api/Admin/StoreTipoSolicitudCatalogoRequest.php
- app/Http/Requests/Api/Admin/UpdateTipoSolicitudCatalogoRequest.php
- app/Http/Requests/Api/UpdateTicketOperativoRequest.php
- app/Services/TicketOperativoService.php
- tests/Feature/Api/AdminCatalogoTest.php
- tests/Feature/Api/TicketOperativoTest.php
