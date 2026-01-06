# Story 6.4: Backend - Busqueda y metricas basicas

Status: ready-for-dev

## Story

As a coordinador,
I want buscar tickets y consultar metricas basicas,
so that pueda priorizar y explicar avances.

## Acceptance Criteria

1. Coordinador y admin pueden buscar tickets por asunto, estado y sistema.
2. La busqueda respeta visibilidad por rol.
3. Se exponen metricas basicas: conteo de tickets por estado y por prioridad.
4. Las metricas respetan visibilidad por rol.

## Tasks / Subtasks

- [ ] Implementar endpoint de busqueda de tickets (AC: #1, #2)
  - [ ] Filtros: asunto (parcial), estado_id, sistema_id
- [ ] Implementar endpoint de metricas basicas (AC: #3, #4)
  - [ ] Conteo por estado y por prioridad
- [ ] Pruebas de feature para busqueda y metricas (AC: #1-#4)

## Dev Notes

- Reusar filtros/visibilidad del listado base (Epic 1).
- Metricas basicas se calculan sobre tickets visibles.

### Project Structure Notes

- app/Http/Controllers/Api, app/Services, app/Models, app/Policies
- database/migrations, resources/js, resources/css

### References

- _bmad-output/planning-artifacts/epics.md#Epic-6
- _bmad-output/planning-artifacts/architecture.md#Implementation-Patterns-Consistency-Rules
- _bmad-output/planning-artifacts/architecture.md#Stack-Versions-confirmed

## Dev Agent Record

### Agent Model Used

TBD

### Debug Log References

### Completion Notes List

### File List

