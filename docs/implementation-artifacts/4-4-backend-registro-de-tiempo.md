# Story 4.4: Backend - Registro de tiempo

Status: ready-for-dev

## Story

As a soporte,
I want registrar tiempo invertido de forma acumulativa,
so that quede evidencia del esfuerzo.

## Acceptance Criteria

1. Soporte (asignado) o coordinador/admin puede registrar tiempo en un ticket; cada entrada crea un registro en registros_tiempo_ticket.
2. Los registros son append-only (no se permite editar ni eliminar via API).
3. Se puede listar el tiempo registrado de un ticket en orden cronologico.
4. Usuarios sin acceso al ticket no pueden registrar ni ver tiempo.

## Tasks / Subtasks

- [ ] Implementar endpoint para registrar tiempo en ticket (AC: #1, #2)
  - [ ] Validar acceso al ticket y rol
  - [ ] Validar minutos > 0
- [ ] Implementar endpoint para listar registros de tiempo por ticket (AC: #3, #4)
- [ ] Pruebas de feature para registrar/listar y bloquear edicion (AC: #1-#4)

## Dev Notes

- Cada registro representa tiempo incremental; no recalcular acumulados en backend.
- Si existe auditoria, registrar evento de tiempo registrado.

### Project Structure Notes

- app/Http/Controllers/Api, app/Services, app/Models, app/Policies
- database/migrations, resources/js, resources/css

### References

- _bmad-output/planning-artifacts/epics.md#Epic-4
- _bmad-output/planning-artifacts/architecture.md#Implementation-Patterns-Consistency-Rules
- _bmad-output/planning-artifacts/architecture.md#Stack-Versions-confirmed

## Dev Agent Record

### Agent Model Used

TBD

### Debug Log References

### Completion Notes List

### File List

