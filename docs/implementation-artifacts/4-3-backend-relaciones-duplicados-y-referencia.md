# Story 4.3: Backend - Relaciones, duplicados y referencia

Status: ready-for-dev

## Story

As a usuario,
I want relacionar tickets y marcar duplicados,
so that exista trazabilidad entre solicitudes.

## Acceptance Criteria

1. Usuario con acceso al ticket puede crear relacion entre tickets con tipo_relacion (relacionado|reabre); marcar duplicado requiere rol con permiso de cancelacion.
2. No se permite relacionar un ticket consigo mismo ni duplicar una relacion existente.
3. Marcar duplicado actualiza el ticket duplicado a estado "Cancelado" y crea la relacion duplicado_de hacia el ticket valido.
4. Se puede listar relaciones de un ticket, respetando visibilidad por rol.

## Tasks / Subtasks

- [ ] Implementar endpoint para crear relacion (AC: #1-#3)
  - [ ] Validar acceso al ticket origen y al relacionado
  - [ ] Validar permiso de cancelacion para tipo_relacion=duplicado_de
  - [ ] Evitar auto-relacion y duplicados (unique constraint)
- [ ] Implementar logica de duplicado (AC: #3)
  - [ ] Cambiar estado a "Cancelado" y set cancelado_at
- [ ] Implementar endpoint para listar relaciones por ticket (AC: #4)
- [ ] Pruebas de feature para crear/listar relaciones y duplicados (AC: #1-#4)

## Dev Notes

- Cuando se marca duplicado, mantener referencia al ticket valido con tipo_relacion=duplicado_de.
- El permiso para duplicados debe alinearse con reglas de cancelacion (Epic 2).
- Reapertura indirecta: crear nuevo ticket y relacion tipo reabre (no reabrir el ticket cerrado).
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

TBD

### Debug Log References

### Completion Notes List

### File List

