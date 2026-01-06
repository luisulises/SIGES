# Story 2.1: BD - Esquema de workflow y asignaciones

Status: ready-for-dev

## Story

As a administrador del sistema,
I want estructuras para workflow, asignaciones, prioridades, tipos y fechas,
so that el ciclo del ticket se pueda gestionar.

## Acceptance Criteria

1. Existen tablas prioridades, tipos_solicitud, reglas_transicion_estado y asignaciones_ticket con columnas base y timestamps.
2. FKs definidas: reglas_transicion_estado.estado_origen_id/estado_destino_id -> estados_ticket.id; reglas_transicion_estado.rol_id -> roles.id; asignaciones_ticket.ticket_id -> tickets.id; asignaciones_ticket.responsable_id -> usuarios.id; asignaciones_ticket.asignado_por_id -> usuarios.id.
3. tickets agrega columnas prioridad_id, tipo_solicitud_id, fecha_compromiso, fecha_entrega, resolucion, cerrado_at y cancelado_at (todas nullable) y FKs a prioridades y tipos_solicitud.
4. estados_ticket incluye En analisis, Asignado, En progreso, Resuelto, Cerrado y Cancelado, con es_terminal = true para Cerrado y Cancelado.
5. Las migraciones no rompen tickets existentes (columnas nuevas con null permitido).

## Tasks / Subtasks

- [ ] Crear migraciones para prioridades y tipos_solicitud (AC: #1)
  - [ ] prioridades: nombre (unique), orden, activo
  - [ ] tipos_solicitud: nombre (unique), activo
- [ ] Crear migracion para reglas_transicion_estado (AC: #1, #2)
  - [ ] columnas: estado_origen_id, estado_destino_id, rol_id, requiere_responsable (bool)
  - [ ] index unico (estado_origen_id, estado_destino_id, rol_id)
- [ ] Crear migracion para asignaciones_ticket (AC: #1, #2)
  - [ ] columnas: ticket_id, responsable_id, asignado_por_id, asignado_at, desasignado_at
  - [ ] indices por ticket_id y responsable_id
- [ ] Alterar tickets para agregar columnas operativas (AC: #3, #5)
  - [ ] prioridad_id, tipo_solicitud_id, fecha_compromiso, fecha_entrega, resolucion, cerrado_at, cancelado_at
  - [ ] agregar FKs e indices de prioridad_id y tipo_solicitud_id
- [ ] Seed de estados_ticket faltantes (AC: #4)

## Dev Notes

- Campos operativos (prioridad, tipo, fechas, resolucion) viven en tickets; historico de asignaciones vive en asignaciones_ticket.
- reglas_transicion_estado modela permisos por rol y la bandera requiere_responsable para validar cambios a "En progreso".
- Las nuevas columnas de tickets deben permitir null para no romper datos previos.

### Project Structure Notes

- app/Http/Controllers/Api, app/Services, app/Models, app/Policies
- database/migrations, resources/js, resources/css

### References

- _bmad-output/planning-artifacts/epics.md#Epic-2
- _bmad-output/planning-artifacts/architecture.md#Implementation-Patterns-Consistency-Rules
- _bmad-output/planning-artifacts/architecture.md#Stack-Versions-confirmed

## Dev Agent Record

### Agent Model Used

TBD

### Debug Log References

### Completion Notes List

### File List

