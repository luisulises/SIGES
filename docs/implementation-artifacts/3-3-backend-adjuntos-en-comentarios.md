# Story 3.3: Backend - Adjuntos en comentarios

Status: done

## Story

As a usuario,
I want adjuntar archivos en comentarios,
so that pueda aportar evidencia.

## Acceptance Criteria

1. Usuario con acceso al ticket puede adjuntar archivos a un comentario del ticket (comentario_id requerido) segun su rol.
2. El adjunto queda registrado en adjuntos con ticket_id, comentario_id, cargado_por_id, nombre_archivo, clave_almacenamiento y visibilidad.
3. El adjunto hereda la visibilidad del comentario asociado.
4. Usuarios sin acceso al ticket no pueden adjuntar archivos.

## Tasks / Subtasks

- [x] Implementar endpoint para subir adjunto a un comentario del ticket (AC: #1-#4)
  - [x] Validar acceso al ticket y permiso de comentario segun rol
  - [x] Validar que comentario_id pertenece al ticket si se envia
  - [x] Guardar metadata en adjuntos con visibilidad heredada si aplica
- [x] Implementar endpoint para listar adjuntos en un ticket/comentario (AC: #2, #3)
  - [x] Filtrar adjuntos internos segun rol
- [x] Pruebas de feature por rol para adjuntar y listar (AC: #1-#4)

## Dev Notes

- Almacenamiento fisico fuera de BD; en BD solo metadatos (clave_almacenamiento).
- Validar tamano y tipo permitido en request (definir limites en config).
- Adjuntos se suben solo ligados a un comentario (comentario_id requerido en el request).
- Si existe auditoria, registrar evento de adjunto creado.

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

- Guardado fisico en `storage` (disk por `filesystems.default`), BD guarda solo metadatos y `clave_almacenamiento`.
- Validacion de archivo: max 10 MB, tipos `pdf,png,jpg,jpeg,docx,xlsx,txt`.
- El adjunto hereda la visibilidad del comentario.
- No se implemento descarga (solo listar y subir).

### File List

- app/Http/Controllers/Api/TicketAdjuntoController.php
- app/Http/Requests/Api/StoreAdjuntoRequest.php
- app/Http/Resources/AdjuntoResource.php
- app/Models/Adjunto.php
- app/Services/TicketAdjuntoService.php
- routes/api.php
- tests/Feature/Api/TicketAdjuntoTest.php
