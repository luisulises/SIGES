# Story 3.2: Backend - Comentarios publicos e internos

Status: done

## Story

As a usuario,
I want agregar comentarios publicos o internos,
so that pueda comunicar avances con el nivel correcto de visibilidad.

## Acceptance Criteria

1. Cliente interno puede crear comentarios publicos en sus tickets.
2. Soporte/coordinador/admin puede crear comentarios publicos en tickets que puede gestionar.
3. Comentarios internos solo pueden ser creados por soporte/coordinador/admin y solo son visibles para esos roles.
4. Comentarios publicos son visibles para solicitante, responsables y roles internos con acceso al ticket.
5. El comentario queda registrado con autor_id, visibilidad y timestamps.

## Tasks / Subtasks

- [x] Implementar endpoint para crear comentario (AC: #1-#5)
  - [x] Validar visibilidad permitida segun rol
  - [x] Validar acceso al ticket antes de crear
- [x] Implementar endpoint para listar comentarios por ticket (AC: #3, #4)
  - [x] Filtrar comentarios internos segun rol
- [x] Pruebas de feature por rol (AC: #1-#5)

## Dev Notes

- Usar visibilidad en comentarios_ticket: publico|interno.
- Rechazar creacion de comentario interno por cliente interno.
- Si existe auditoria, registrar evento de comentario creado.

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

- Cliente interno: solo puede comentar en sus tickets y solo con visibilidad `publico`.
- Roles internos (soporte/coordinador/admin): pueden crear comentarios `publico` e `interno` en tickets que pueden operar.
- Listado filtra `interno` para clientes; internos pueden ver todo.

### File List

- app/Http/Controllers/Api/TicketComentarioController.php
- app/Http/Requests/Api/StoreComentarioTicketRequest.php
- app/Http/Resources/ComentarioTicketResource.php
- app/Models/ComentarioTicket.php
- app/Services/TicketComentarioService.php
- routes/api.php
- tests/Feature/Api/TicketComentarioTest.php

