# Story 2.1: BD - Esquema de workflow y asignaciones

Status: done

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

- [x] Crear migraciones para prioridades y tipos_solicitud (AC: #1)
  - [x] prioridades: nombre (unique), orden, activo
  - [x] tipos_solicitud: nombre (unique), activo
- [x] Crear migracion para reglas_transicion_estado (AC: #1, #2)
  - [x] columnas: estado_origen_id, estado_destino_id, rol_id, requiere_responsable (bool)
  - [x] index unico (estado_origen_id, estado_destino_id, rol_id)
- [x] Crear migracion para asignaciones_ticket (AC: #1, #2)
  - [x] columnas: ticket_id, responsable_id, asignado_por_id, asignado_at, desasignado_at
  - [x] indices por ticket_id y responsable_id
- [x] Alterar tickets para agregar columnas operativas (AC: #3, #5)
  - [x] prioridad_id, tipo_solicitud_id, fecha_compromiso, fecha_entrega, resolucion, cerrado_at, cancelado_at
  - [x] agregar FKs e indices de prioridad_id y tipo_solicitud_id
- [x] Seed de estados_ticket faltantes (AC: #4)

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

Codex (GPT-5)

### Debug Log References

- `php artisan migrate`
- `php artisan db:seed --class=EstadoTicketSeeder`

### Completion Notes List

- Tablas base de workflow creadas: prioridades, tipos_solicitud, reglas_transicion_estado, asignaciones_ticket.
- Campos operativos agregados a tickets con FKs a prioridades y tipos_solicitud.
- Estados adicionales sembrados con `es_terminal` para Cerrado y Cancelado.
- Riesgo: faltan catalogos seed para prioridades/tipos_solicitud; definir valores iniciales antes de UI.

### File List

- database/migrations/2026_01_11_000006_create_prioridades_table.php
- database/migrations/2026_01_11_000007_create_tipos_solicitud_table.php
- database/migrations/2026_01_11_000008_create_reglas_transicion_estado_table.php
- database/migrations/2026_01_11_000009_create_asignaciones_ticket_table.php
- database/migrations/2026_01_11_000010_add_campos_operativos_to_tickets_table.php
- database/seeders/EstadoTicketSeeder.php

