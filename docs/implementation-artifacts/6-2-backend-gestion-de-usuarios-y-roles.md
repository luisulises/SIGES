# Story 6.2: Backend - Gestion de usuarios y roles

Status: done

## Story

As a administrador,
I want gestionar usuarios y roles y desactivarlos,
so that el acceso quede controlado.

## Acceptance Criteria

1. Admin puede crear, editar y desactivar usuarios.
2. Admin puede asignar rol a un usuario.
3. Usuario desactivado no puede iniciar sesion ni operar tickets.
4. Al desactivar un responsable, el ticket queda sin responsable_actual_id y la asignacion activa se cierra.
5. Al desactivar un involucrado, permanece en historial pero no recibe notificaciones.
6. Los cambios de rol o estado activo quedan reflejados en el usuario.

## Tasks / Subtasks

- [x] Implementar endpoints CRUD basicos de usuarios (AC: #1, #6)
  - [x] Crear/editar datos base (nombre, email)
  - [x] Desactivar usuario (activo=false, desactivado_at)
- [x] Implementar asignacion de rol a usuario (AC: #2)
- [x] Bloquear acceso a usuarios desactivados (AC: #3)
- [x] Al desactivar responsable, limpiar responsable_actual_id y cerrar asignacion activa (AC: #4)
- [x] Al desactivar involucrado, excluirlo de notificaciones (AC: #5)
- [x] Pruebas de feature para admin (AC: #1-#6)

## Dev Notes

- No permitir borrar usuarios; solo desactivar.
- Manejo de tickets con usuarios desactivados afecta asignaciones e involucrados.
- Al desactivar responsable, cerrar asignacion activa con desasignado_at.

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

- Endpoints admin (roles/usuarios) + bloqueo de usuario inactivo (web + api) + limpieza de asignaciones al desactivar.

### File List

- app/Http/Middleware/EnsureUserIsActive.php
- app/Http/Middleware/EnsureUserHasRole.php
- app/Http/Kernel.php
- app/Http/Requests/Auth/LoginRequest.php
- routes/web.php
- routes/api.php
- app/Services/AdminUsuarioService.php
- app/Http/Controllers/Api/AdminUsuarioController.php
- app/Http/Controllers/Api/AdminRoleController.php
- app/Http/Requests/Api/Admin/StoreUsuarioRequest.php
- app/Http/Requests/Api/Admin/UpdateUsuarioRequest.php
- app/Http/Resources/UsuarioResource.php
- tests/Feature/Api/AdminUsuarioTest.php

