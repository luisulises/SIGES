# Story 2.2: Backend - Reglas de transicion y cierre/cancelacion

Status: done

## Story

As a soporte,
I want cambiar estados con reglas,
so that el flujo sea consistente.

## Acceptance Criteria

1. Un usuario autorizado puede cambiar el estado de un ticket solo si existe una regla en reglas_transicion_estado para su rol.
2. Si la regla requiere responsable, no se permite transicion a "En progreso" cuando responsable_actual_id es null.
3. Cerrar ticket solo es posible si el estado actual es "Resuelto" y existe resolucion; solicitante (propietario), coordinador o admin pueden cerrar.
4. Cancelar ticket solo es posible para solicitante (propietario) o coordinador/admin; se registra cancelado_at y estado "Cancelado".
5. Soporte solo puede cambiar estado de tickets asignados; coordinador solo de tickets en sus sistemas; admin puede operar cualquier ticket.

## Tasks / Subtasks

- [x] Definir endpoint/accion para cambio de estado (AC: #1-#5)
  - [x] Validar rol y visibilidad del ticket antes de aplicar cambios
  - [x] Resolver estados por nombre desde estados_ticket
- [x] Implementar validacion de reglas_transicion_estado (AC: #1, #2)
  - [x] Verificar regla por rol y par estado_origen/estado_destino
  - [x] Exigir responsable_actual_id cuando requiere_responsable = true
- [x] Implementar cierre de ticket (AC: #3)
  - [x] Verificar estado "Resuelto" y resolucion no null
  - [x] Validar rol permitido (solicitante/coordinador/admin)
  - [x] Set estado "Cerrado" y cerrado_at
- [x] Implementar cancelacion de ticket (AC: #4)
  - [x] Verificar rol permitido (solicitante/coordinador/admin)
  - [x] Set estado "Cancelado" y cancelado_at
- [x] Seed de reglas_transicion_estado basicas para MVP (AC: #1)
  - [x] Transiciones minimas para flujo: Nuevo -> En analisis -> Asignado -> En progreso -> Resuelto -> Cerrado
  - [x] Cancelado permitido segun rol
- [x] Pruebas de feature para casos validos e invalidos (AC: #1-#5)

## Dev Notes

- Reglas de transicion viven en reglas_transicion_estado; no hardcodear reglas en controllers.
- Cierre requiere resolucion registrada (tickets.resolucion).
- Cancelacion y cierre deben actualizar updated_at y timestamps cerrado_at/cancelado_at.
- Si existe servicio de auditoria (Epic 4), emitir evento de cambio de estado.

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

- `php artisan db:seed --class=ReglaTransicionEstadoSeeder`
- `php artisan test --filter TicketWorkflowTest`

### Completion Notes List

- Servicio `TicketWorkflowService` valida reglas por rol y requisitos de responsable.
- Endpoints separados para transicion, cierre y cancelacion con control de rol y visibilidad.
- Seeder de reglas MVP para flujo base y cancelacion por rol.
- Pruebas de workflow ejecutadas con exito.

### File List

- app/Http/Controllers/Api/TicketWorkflowController.php
- app/Http/Requests/Api/ChangeTicketEstadoRequest.php
- app/Models/EstadoTicket.php
- app/Models/Ticket.php
- app/Services/TicketWorkflowService.php
- database/seeders/ReglaTransicionEstadoSeeder.php
- database/seeders/DatabaseSeeder.php
- routes/api.php
- tests/Feature/Api/TicketWorkflowTest.php
