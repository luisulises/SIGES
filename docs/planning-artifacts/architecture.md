---
stepsCompleted: [1, 2, 3, 4, 5, 6, 7, 8]
inputDocuments:
  - _bmad-output/planning-artifacts/prd.md
  - _bmad-output/planning-artifacts/product-brief-Sistema - copia-2026-01-01.md
workflowType: 'architecture'
project_name: 'Sistema - copia'
user_name: 'Alfal'
date: '2026-01-02'
lastStep: 8
status: 'complete'
completedAt: '2026-01-02'
---

# Architecture Decision Document

_This document builds collaboratively through step-by-step discovery. Sections are appended as we work through each architectural decision together._

## Project Context Analysis

### Requirements Overview

**Functional Requirements:**
- 54 FRs organizados en 15 areas: autenticacion/RBAC, tickets, estados/transiciones, asignacion/prioridad/fechas, comentarios/involucrados, adjuntos, relaciones/duplicados, registro de tiempo, auditoria/historial, busqueda/listados, notificaciones, administracion de catalogos, manejo de usuarios desactivados, reapertura indirecta y metricas operativas.

**Non-Functional Requirements:**
- Performance objetivo: listado <= 3 s; detalle y guardado <= 2 s (sin SLA formal).
- Seguridad: autenticacion, hash de contrasenas, HTTPS, RBAC y validacion en backend.
- Escalabilidad: baja (< 50 concurrentes), preparado para crecimiento moderado.
- Disponibilidad: horario laboral; mantenimientos fuera de horario.
- Accesibilidad: TBD / no aplica MVP.

**Scale & Complexity:**
- Primary domain: web_app (interna).
- Complexity level: low.
- Estimated architectural components: 8 (auth/RBAC, tickets, workflow/estados, comentarios/adjuntos, notificaciones, busqueda/listados, auditoria/historial, administracion/metrics).

### Technical Constraints & Dependencies

- Aplicacion interna sin SEO.
- Sin integraciones externas en MVP.
- Actualizacion frecuente de estados/comentarios (objetivo <= 60 s).
- Sin SLA formal en MVP.

### Cross-Cutting Concerns Identified

- Control de acceso y visibilidad por rol en todas las vistas.
- Auditoria e historial inmutable de cambios.
- Reglas de transicion de estado.
- Notificaciones por eventos clave.
- Manejo de usuarios desactivados sin perdida de trazabilidad.
- Seguridad: autenticacion y autorizacion consistente.

## High-Level Architecture (MVP)

- Boundary: aplicacion web interna para gestionar solicitudes/tickets; sin integraciones externas en MVP.
- Capas: UI web, servicios de aplicacion, dominio (reglas), persistencia (BD relacional), tareas asincronas (notificaciones, auditoria).
- Modulos: autenticacion/RBAC, tickets, workflow/estados, asignacion, comentarios, adjuntos, involucrados, relaciones/duplicados, registro de tiempo, historial/auditoria, busqueda/listados, administracion de catalogos.
- Control de acceso: validaciones por rol en cada lectura/escritura; visibilidad por tipo de comentario/adjunto.
- Actualizacion de estado: refresco periodico <= 60 s (sin tiempo real).
- Auditabilidad: cada cambio relevante genera un evento historico inmutable.

## Logical Data Model (MVP)

### Criterio de nomenclatura

- Columnas estandar del framework se mantienen (id, created_at, updated_at, deleted_at si aplica).
- Entidades y campos de dominio en espanol y snake_case.
- Priorizar claridad funcional sobre traduccion literal.

### Entidades

- Usuario: id, nombre, email, rol_id, activo, created_at, desactivado_at.
- Rol: id, nombre.
- Sistema: id, nombre, activo.
- SistemaCoordinador: sistema_id, usuario_id, created_at.
- TipoSolicitud: id, nombre, activo.
- Prioridad: id, nombre, orden, activo.
- EstadoTicket: id, nombre, es_terminal.
- ReglaTransicionEstado: id, estado_origen_id, estado_destino_id, rol_id, requiere_responsable.
- Ticket: id, asunto, descripcion, solicitante_id, sistema_id, tipo_solicitud_id, estado_id, prioridad_id, responsable_actual_id, interno, fecha_compromiso, created_at, updated_at, cerrado_at, cancelado_at.
- AsignacionTicket: id, ticket_id, responsable_id, asignado_por_id, asignado_at, desasignado_at.
- ComentarioTicket: id, ticket_id, autor_id, cuerpo, visibilidad(publico|interno), created_at.
- Adjunto: id, ticket_id, comentario_id (nullable), cargado_por_id, nombre_archivo, clave_almacenamiento, visibilidad, created_at.
- InvolucradoTicket: ticket_id, usuario_id, agregado_por_id, created_at.
- RelacionTicket: ticket_id, ticket_relacionado_id, tipo_relacion(relacionado|duplicado_de|reabre), creado_por_id, created_at.
- RegistroTiempoTicket: id, ticket_id, autor_id, minutos, nota (nullable), created_at.
- EventoAuditoriaTicket: id, ticket_id, actor_id, tipo_evento, valor_antes, valor_despues, metadatos, created_at.
- Notificacion: id, usuario_id, ticket_id, tipo_evento, canal(in_app|email), created_at, leido_at.

### Relaciones y reglas clave

- Ticket pertenece a Sistema, TipoSolicitud, Prioridad y EstadoTicket; solicitante y responsable_actual son Usuarios.
- Sistema tiene muchos coordinadores via SistemaCoordinador; coordinador accede a tickets del sistema.
- Ticket tiene muchos ComentarioTicket, Adjunto, AsignacionTicket, EventoAuditoriaTicket, InvolucradoTicket, RelacionTicket y RegistroTiempoTicket.
- InvolucradoTicket extiende visibilidad de lectura del ticket.
- Ticket interno es visible solo para roles internos; cliente interno no accede aunque sea involucrado.
- InvolucradoTicket es unico por (ticket_id, usuario_id).
- RelacionTicket es unico por (ticket_id, ticket_relacionado_id, tipo_relacion) y no permite auto-relacion.
- RegistroTiempoTicket es append-only (sin update/delete).
- EventoAuditoriaTicket es append-only.
- ReglaTransicionEstado gobierna cambios de estado; si requiere_responsable es true, el cambio exige responsable_actual_id.
- Actual vs historico: Ticket guarda estado/responsable_actual; el historial vive en AsignacionTicket y EventoAuditoriaTicket.
- En MVP solo se usa canal in_app; correo queda post-MVP.

## Starter Template Evaluation

### Primary Technology Domain

- Web application (interna).

### Starter Options Considered

- Laravel Breeze (Inertia + Vue) como starter por compatibilidad con el stack confirmado.

### Selected Starter: Laravel Breeze (Inertia + Vue)

**Rationale:** Acelera scaffolding de autenticacion y UI base con Inertia/Vue, alineado al stack definido.

**Initialization Command:** Ver `docs/flujo-proyecto.md` (setup con Breeze + Inertia + Vue).

**Architectural Decisions Provided by Starter:** Autenticacion base, rutas y vistas iniciales con Inertia/Vue.

## Core Architectural Decisions

### Decision Priority Analysis

**Critical Decisions (Block Implementation):**
- Tipo de aplicacion: web interna.
- Arquitectura: monolito modular con separacion por dominios.
- Datos: base relacional unica con integridad referencial.
- Seguridad: autenticacion obligatoria + RBAC + validacion en backend.
- Comunicacion: API REST/JSON interna para UI.
- Actualizacion de estado/comentarios: polling <= 60 s.
- Estilos UI: Tailwind CSS (version TBD).

**Important Decisions (Shape Architecture):**
- Auditoria e historiales append-only.
- Registro de tiempo append-only.
- Adjuntos con trazabilidad y control de visibilidad.
- Sin cache en MVP.

**Deferred Decisions (Post-MVP o fase tecnica):**
- CI/CD y hosting especifico.
- Observabilidad avanzada (tracing/metrics).

### Data Architecture

- Modelo relacional normalizado con claves foraneas y restricciones de unicidad.
- Migraciones para evolucion de esquema.
- Transacciones para cambios de estado/asignacion.
- Adjuntos almacenados fuera de BD con metadatos en BD.

### Authentication & Security

- Autenticacion por usuario/contrasena.
- RBAC por rol aplicado en backend.
- Validacion de permisos por operacion y por visibilidad (comentarios/adjuntos).
- Auditoria de cambios relevante por ticket.
- HTTPS obligatorio en produccion.

### API & Communication Patterns

- Endpoints REST/JSON para tickets, comentarios, adjuntos, catalogos y administracion.
- Errores y respuestas estandarizados (detalle en patrones de implementacion).
- Paginacion y filtros basicos en listados.

### Frontend Architecture

- UI web con rutas protegidas por rol.
- Vistas principales: listado, detalle, creacion, administracion de catalogos.
- Actualizacion periodica para reflejar cambios en <= 60 s.
- Estilos basados en utilidades (Tailwind CSS).

### Infrastructure & Deployment

- Despliegue en una instancia por ambiente (dev/stage/prod).
- Configuracion por variables de entorno.
- Backups programados de base de datos.
- Logs basicos de aplicacion y auditoria.

## Stack & Versions (confirmed)

- PHP: 8.2 (stable).
- Framework backend: Laravel 10 LTS.
- UI: Vue 3.x (3.4 recomendado).
- Inertia.js: 1.x.
- DB: PostgreSQL 15.
- Starter: Laravel Breeze (Inertia + Vue).

### Decision Impact Analysis

**Implementation Sequence:**
- Esquema de datos y RBAC.
- Workflow de tickets y reglas de transicion.
- Comentarios/adjuntos/involucrados/relaciones.
- Notificaciones y auditoria.

**Cross-Component Dependencies:**
- Reglas de transicion dependen de estado, rol y responsable_actual.
- Visibilidad de comentarios/adjuntos depende de rol e involucrados.

## Implementation Patterns & Consistency Rules

### Pattern Categories Defined

- Critical conflict points: naming, structure, formats, communication, process.

### Naming Patterns

**Database Naming Conventions:**
- Tables: plural, espanol, snake_case (ej: tickets, usuarios, comentarios_ticket).
- Columns: snake_case en espanol (ej: solicitante_id, fecha_compromiso).
- Foreign keys: <entidad>_id.
- Indexes: idx_<tabla>_<col>.

**API Naming Conventions:**
- Endpoints REST en plural (ej: /tickets, /usuarios).
- Path params: {id}.
- Query params: snake_case.

**Code Naming Conventions:**
- Clases: PascalCase (ej: Ticket, Usuario, ComentarioTicket).
- Archivos de clases: PascalCase.
- Variables/metodos: camelCase.
- Carpetas: snake_case.

### Structure Patterns

**MVC (capas claras):**
- Controllers: solo orquestan flujo HTTP, sin logica de negocio.
- Services: logica de negocio y reglas de transicion.
- Models: acceso a datos y relaciones.
- Requests: validacion de entrada.
- Policies: autorizacion RBAC.

**Ubicacion de pruebas:**
- tests/Unit, tests/Feature, tests/E2E (si aplica).

### Format Patterns

**API Response Formats:**
- Success: {data: ..., meta: ...}
- Error: {error: {code, message, details}}

**Data Formats:**
- JSON en snake_case.
- Fechas en ISO 8601 UTC.
- Booleanos true/false.

### Communication Patterns

- Eventos/auditoria: tipo_evento en snake_case (ej: ticket_creado, estado_cambiado).
- Notificaciones: in_app; correo post-MVP; sin canales externos en MVP.

### Process Patterns

- Validacion en backend como fuente de verdad.
- Manejo de errores centralizado y logueado.
- Cambios de estado siempre generan evento de auditoria.

### Enforcement Guidelines

**All AI Agents MUST:**
- Respetar naming y formatos definidos.
- Mantener controllers delgados; logica en services.
- Registrar auditoria en cada cambio relevante.

**Pattern Enforcement:**
- Revisar consistencia en PRs.
- Documentar desviaciones en comentarios de revision.

### Pattern Examples

**Good Examples:**
- Tabla: tickets, columna: solicitante_id.
- POST /tickets -> {data: {...}, meta: {...}}

**Anti-Patterns:**
- Columna TicketID o userId en BD.
- Respuestas JSON sin wrapper o en camelCase.

## Project Structure & Boundaries

### Complete Project Directory Structure

```
project-root/
  README.md
  composer.json
  package.json
  .env
  .env.example
  .gitignore
  app/
    Http/
      Controllers/
        Api/
          TicketsController.php
          ComentariosController.php
          AdjuntosController.php
          CatalogosController.php
          UsuariosController.php
        Web/
          DashboardController.php
      Middleware/
      Requests/
    Models/
      Ticket.php
      Usuario.php
      ComentarioTicket.php
      Adjunto.php
      Sistema.php
      TipoSolicitud.php
      Prioridad.php
      EstadoTicket.php
    Services/
      TicketService.php
      AuditoriaService.php
      NotificacionService.php
    Policies/
    Jobs/
    Notifications/
  routes/
    web.php
    api.php
  database/
    migrations/
    seeders/
  resources/
    views/
    js/
    css/
  public/
  storage/
    app/
      adjuntos/
  tests/
    Unit/
    Feature/
    E2E/
```

### Architectural Boundaries

**API Boundaries:**
- /api/* para operaciones JSON (tickets, comentarios, adjuntos, catalogos, usuarios).
- /web para vistas internas (dashboard y navegacion).

**Component Boundaries:**
- Controllers solo orquestan.
- Services concentran reglas de negocio.
- Models definen relaciones y persistencia.

**Data Boundaries:**
- BD relacional con migraciones.
- Adjuntos fuera de BD con metadatos en BD.

### Requirements to Structure Mapping

**Feature Mapping:**
- Tickets: Controllers/Api/TicketsController.php, Models/Ticket.php, Services/TicketService.php, Requests/Ticket*.php, Policies/TicketPolicy.php.
- Comentarios/Adjuntos: Controllers/Api/ComentariosController.php y AdjuntosController.php, Models/ComentarioTicket.php y Adjunto.php.
- Catalogos: Controllers/Api/CatalogosController.php, Models/Sistema.php, TipoSolicitud.php, Prioridad.php, EstadoTicket.php.
- Auditoria: Services/AuditoriaService.php, Models/EventoAuditoriaTicket.php.
- Notificaciones: Notifications/, Jobs/.

**Cross-Cutting Concerns:**
- Autenticacion/RBAC: Middleware/, Policies/.
- Visibilidad por rol: Policies/ + Services.

### Integration Points

**Internal Communication:**
- UI -> API -> Services -> Models -> BD.

**External Integrations:**
- Ninguna en MVP.

**Data Flow:**
- Creacion y cambios de estado generan auditoria y notificaciones.

### File Organization Patterns

**Configuration Files:**
- config/ y .env*

**Source Organization:**
- app/ por MVC y servicios

**Test Organization:**
- tests/Unit, tests/Feature, tests/E2E

**Asset Organization:**
- resources/js, resources/css, public/

### Development Workflow Integration

**Development Server Structure:**
- Separacion clara entre rutas web y api.

**Build Process Structure:**
- Assets desde resources/ hacia public/.

**Deployment Structure:**
- Configuracion por variables de entorno y storage persistente.

## Architecture Validation Results

### Coherence Validation

**Decision Compatibility:**
- Decisiones coherentes: MVC + servicios + RBAC + auditoria.

**Pattern Consistency:**
- Naming y formatos alineados entre BD, API y codigo.

**Structure Alignment:**
- Estructura MVC soporta los modulos definidos.

### Requirements Coverage Validation

**Functional Requirements Coverage:**
- FRs de tickets, estados, comentarios, adjuntos, involucrados, relaciones, tiempo, auditoria y notificaciones cubiertos por modelo y estructura.

**Non-Functional Requirements Coverage:**
- Seguridad, performance objetivo y trazabilidad soportados en decisiones y patrones.

### Implementation Readiness Validation

**Decision Completeness:**
- Decisiones core documentadas; stack y starter confirmados.

**Structure Completeness:**
- Arbol de proyecto definido con limites claros.

**Pattern Completeness:**
- Reglas para naming, formatos y procesos definidos.

### Gap Analysis Results

- Critical gaps: ninguno.
- Important gaps: CI/CD y hosting especifico.
- Nice-to-have gaps: observabilidad avanzada.

### Validation Issues Addressed

- Se explicita MVC y separacion de responsabilidades para evitar logica duplicada.

### Architecture Completeness Checklist

**Requirements Analysis**
- [x] Contexto del proyecto analizado
- [x] Restricciones identificadas
- [x] Preocupaciones transversales mapeadas

**Architectural Decisions**
- [x] Decisiones core documentadas
- [x] Seguridad y datos definidos
- [x] Comunicacion y UI definidos

**Implementation Patterns**
- [x] Naming y formatos definidos
- [x] Reglas de consistencia establecidas

**Project Structure**
- [x] Estructura completa definida
- [x] Limites y mapeo a requisitos

### Architecture Readiness Assessment

**Overall Status:** READY FOR IMPLEMENTATION

**Confidence Level:** medium (CI/CD y hosting diferidos).

**Key Strengths:**
- Modelo de datos robusto y trazable.
- Reglas de consistencia claras.

**Areas for Future Enhancement:**
- Definir CI/CD y hosting.
- Observabilidad avanzada.

### Implementation Handoff

**AI Agent Guidelines:**
- Seguir decisiones y patrones documentados.
- Respetar estructura MVC y limites.
- Mantener consistencia en naming y formatos.

**First Implementation Priority:**
- Preparar repo y setup base (Laravel 10 + Breeze Inertia + Vue + PostgreSQL).

## Architecture Completion Summary

### Workflow Completion

**Architecture Decision Workflow:** COMPLETED
**Total Steps Completed:** 8
**Date Completed:** 2026-01-02
**Document Location:** _bmad-output/planning-artifacts/architecture.md

### Final Architecture Deliverables

- Documento de arquitectura completo con decisiones core.
- Patrones de implementacion para consistencia.
- Estructura MVC con limites claros.
- Validacion de coherencia y cobertura de requisitos.

### Implementation Handoff

**For AI Agents:**
Este documento es la guia para implementar Sistema - copia. Seguir decisiones, patrones y estructura tal como se documenta.

**First Implementation Priority:**
Preparar repo y setup base (Laravel 10 + Breeze Inertia + Vue + PostgreSQL) antes de iniciar codigo.

### Quality Assurance Checklist

- [x] Coherencia arquitectonica validada
- [x] Requisitos cubiertos
- [x] Patrones listos
- [x] Estructura definida

**Architecture Status:** READY FOR IMPLEMENTATION

**Document Maintenance:** actualizar si se toman decisiones tecnicas nuevas.
