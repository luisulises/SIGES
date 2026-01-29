# Sistema - copia - Tickets tecnicos (MVP)

## Referencias

- _bmad-output/planning-artifacts/epics.md
- _bmad-output/planning-artifacts/prd.md
- _bmad-output/planning-artifacts/architecture.md

## Reglas globales (aplican a todos los tickets)

- API REST/JSON con snake_case y fechas ISO 8601 UTC.
- Respuesta success: {data, meta}; error: {error:{code, message, details}}.
- RBAC aplicado en backend y en visibilidad de UI.
- Auditoria append-only en cambios relevantes.
- Sin integraciones externas en MVP.
- Actualizacion de vistas por polling <= 60 s.

## Prioridad de entrega (MVP)

- P0.1 DB-1.1 Esquema base: usuarios, roles, sistemas, estados_ticket, sistemas_coordinadores y tickets (incluye ticket.interno).
- P0.2 BE-1.1 Autenticacion y sesiones con RBAC.
- P0.3 BE-1.2 CRUD minimo de tickets: crear con asunto/sistema/descripcion; asunto y descripcion no editables; estado inicial Nuevo.
- P0.4 BE-1.3 Listado y detalle por rol con orden por ultima actualizacion.
- P0.5 FE-1.1 UI de login.
- P0.6 FE-1.2 UI de listado y detalle de tickets con filtros basicos y orden por ultima actualizacion.
- P0.7 FE-1.3 UI de creacion de ticket (asunto, sistema, descripcion).

- P1.1 DB-2.1 Esquema de workflow: prioridades, tipos_solicitud, reglas_transicion_estado, asignaciones_ticket.
- P1.2 BE-2.1 Reglas de transicion de estado (incluye cierre desde Resuelto con resolucion).
- P1.3 BE-2.2 Asignacion/reasignacion de responsable y cambios operativos.
- P1.4 BE-2.3 Cancelacion por roles permitidos y registro en historial.
- P1.5 FE-2.1 Controles de cambio de estado segun rol.
- P1.6 FE-2.2 UI de asignacion, prioridad, fechas, tipo_solicitud y sistema segun rol.
- P1.7 FE-2.3 Acciones de cerrar/cancelar para cliente interno.

- P2.1 DB-3.1 Esquema de colaboracion: comentarios_ticket, adjuntos, involucrados_ticket.
- P2.2 BE-3.1 Comentarios publicos e internos con visibilidad por rol.
- P2.3 BE-3.2 Adjuntos en tickets/comentarios, herencia de visibilidad y no eliminables.
- P2.4 BE-3.3 Gestion de involucrados (agregar/remover) y consulta.
- P2.5 FE-3.1 UI de comentarios publicos/internos con visibilidad por rol.
- P2.6 FE-3.2 UI de adjuntos en tickets/comentarios.
- P2.7 FE-3.3 UI de gestion de involucrados (solo coordinador).

- P3.1 DB-4.1 Esquema de trazabilidad: eventos_auditoria_ticket, relaciones_ticket, registros_tiempo_ticket.
- P3.2 BE-4.1 Auditoria de cambios y consulta de historial.
- P3.3 BE-4.2 Relaciones de tickets, duplicados y referencia a tickets cerrados/cancelados.
- P3.4 BE-4.3 Registro de tiempo acumulativo (append-only) y visibilidad por rol.
- P3.5 FE-4.1 UI de historial/auditoria en detalle de ticket.
- P3.6 FE-4.2 UI de relaciones y duplicados.
- P3.7 FE-4.3 UI de registro y visualizacion de tiempo (segun rol).

- P4.1 DB-5.1 Esquema de notificaciones: notificaciones (usuario_id, ticket_id, tipo_evento, canal, leido_at).
- P4.2 BE-5.1 Generacion de notificaciones por eventos.
- P4.3 BE-5.2 Exclusion de usuarios desactivados en notificaciones.
- P4.4 FE-5.1 Campanita de notificaciones con contador y listado.

- P5.1 DB-6.1 Catalogos y campos administrativos: sistemas, prioridades, tipos_solicitud, usuario.activo.
- P5.2 BE-6.1 Gestion de usuarios y roles, con desactivacion y reglas asociadas.
- P5.3 BE-6.2 Gestion de catalogos (sistemas, tipos_solicitud, prioridades).
- P5.4 BE-6.3 Tickets internos y modificacion de campos operativos por admin.
- P5.5 BE-6.4 Busqueda por asunto/estado/sistema y metricas basicas (conteo por estado y prioridad).
- P5.6 FE-6.1 UI de administracion de usuarios/roles.
- P5.7 FE-6.2 UI de catalogos (sistemas, tipos_solicitud, prioridades).
- P5.8 FE-6.3 UI para marcar ticket interno y editar campos operativos (admin).
- P5.9 FE-6.4 UI de busqueda y metricas basicas.

## Dependencias (predecesores)

- DB-1.1: sin predecesores.
- BE-1.1: DB-1.1.
- BE-1.2: DB-1.1, BE-1.1.
- BE-1.3: BE-1.1, BE-1.2.
- FE-1.1: BE-1.1.
- FE-1.2: BE-1.3.
- FE-1.3: BE-1.2.

- DB-2.1: DB-1.1.
- BE-2.1: DB-2.1, BE-1.2.
- BE-2.2: DB-2.1, BE-1.2.
- BE-2.3: DB-2.1, BE-2.1.
- FE-2.1: BE-2.1.
- FE-2.2: BE-2.2.
- FE-2.3: BE-2.3.

- DB-3.1: DB-1.1.
- BE-3.1: DB-3.1, BE-1.3.
- BE-3.2: DB-3.1, BE-3.1.
- BE-3.3: DB-3.1, BE-1.3.
- FE-3.1: BE-3.1.
- FE-3.2: BE-3.2.
- FE-3.3: BE-3.3.

- DB-4.1: DB-1.1.
- BE-4.1: DB-4.1, BE-1.2.
- BE-4.2: DB-4.1, BE-1.2.
- BE-4.3: DB-4.1, BE-1.2.
- FE-4.1: BE-4.1.
- FE-4.2: BE-4.2.
- FE-4.3: BE-4.3.

- DB-5.1: DB-1.1.
- BE-5.1: DB-5.1, BE-1.2, BE-2.2, BE-3.1, BE-4.1.
- BE-5.2: DB-5.1, BE-6.1.
- FE-5.1: BE-5.1.

- DB-6.1: DB-1.1.
- BE-6.1: DB-6.1, BE-1.1.
- BE-6.2: DB-6.1.
- BE-6.3: DB-6.1, BE-1.2.
- BE-6.4: DB-6.1, BE-1.3.
- FE-6.1: BE-6.1.
- FE-6.2: BE-6.2.
- FE-6.3: BE-6.3.
- FE-6.4: BE-6.4.

## Revision de cobertura (MVP)

- FRs cubiertos por los tickets DB/BE/FE definidos.
- Visibilidad por rol y tickets internos cubierta en BE-1.2/BE-1.3 + BE-6.3 y FE-6.3.
- Comentarios internos visibles solo para roles permitidos en BE-3.2 y FE-3.1.
- Notificaciones por campanita (correo post-MVP) en BE-5.1 y FE-5.1.

## Base de Datos

### Epic 1 - Registro y consulta basica de tickets

- DB-1.1 Esquema base: usuarios, roles, sistemas, estados_ticket, sistemas_coordinadores y tickets.
  - Criterios: claves foraneas validas; estado inicial "Nuevo"; responsable_actual null; tickets.interno default false.

### Epic 2 - Workflow operativo y asignacion

- DB-2.1 Esquema de workflow: prioridades, tipos_solicitud, reglas_transicion_estado, asignaciones_ticket.
  - Criterios: columnas en tickets para prioridad_id, tipo_solicitud_id, responsable_actual_id, fecha_compromiso, fecha_entrega, resolucion.

### Epic 3 - Colaboracion y evidencias

- DB-3.1 Esquema de colaboracion: comentarios_ticket, adjuntos, involucrados_ticket.
  - Criterios: visibilidad en comentarios; adjuntos ligados a ticket y comentario.

### Epic 4 - Trazabilidad, historial y relaciones

- DB-4.1 Esquema de trazabilidad: eventos_auditoria_ticket, relaciones_ticket, registros_tiempo_ticket.
  - Criterios: registros_tiempo_ticket append-only; relaciones_ticket sin auto-relacion.

### Epic 5 - Notificaciones de cambios

- DB-5.1 Esquema de notificaciones: notificaciones (usuario_id, ticket_id, tipo_evento, canal, leido_at).

### Epic 6 - Administracion y gobierno

- DB-6.1 Catalogos y campos administrativos: sistemas, prioridades, tipos_solicitud, usuario.activo.
  - Criterios: catalogos con flag activo; usuarios activos/inactivos definidos.

## Backend

### Epic 1 - Registro y consulta basica de tickets

- BE-1.1 Autenticacion y sesiones con RBAC.
- BE-1.2 CRUD minimo de tickets: crear con asunto/sistema/descripcion; asunto y descripcion no editables; estado inicial Nuevo.
- BE-1.3 Listado y detalle por rol con orden por ultima actualizacion.

### Epic 2 - Workflow operativo y asignacion

- BE-2.1 Reglas de transicion de estado (incluye cierre desde Resuelto con resolucion).
- BE-2.2 Asignacion/reasignacion de responsable y cambios operativos (prioridad, fecha_compromiso, tipo_solicitud, sistema).
- BE-2.3 Cancelacion por roles permitidos y registro en historial.

### Epic 3 - Colaboracion y evidencias

- BE-3.1 Comentarios publicos e internos con visibilidad por rol.
- BE-3.2 Adjuntos en tickets/comentarios, herencia de visibilidad y no eliminables.
- BE-3.3 Gestion de involucrados (agregar/remover) y consulta.

### Epic 4 - Trazabilidad, historial y relaciones

- BE-4.1 Auditoria de cambios y consulta de historial.
- BE-4.2 Relaciones de tickets, duplicados y referencia a tickets cerrados/cancelados.
- BE-4.3 Registro de tiempo acumulativo (append-only) y visibilidad por rol.

### Epic 5 - Notificaciones de cambios

- BE-5.1 Generacion de notificaciones por eventos (creacion, asignacion, cambio de estado, comentario publico, cierre).
- BE-5.2 Exclusion de usuarios desactivados en notificaciones.

### Epic 6 - Administracion y gobierno

- BE-6.1 Gestion de usuarios y roles, con desactivacion y reglas de responsables/involucrados desactivados.
- BE-6.2 Gestion de catalogos (sistemas, tipos_solicitud, prioridades).
- BE-6.3 Tickets internos y modificacion de campos operativos por admin.
- BE-6.4 Busqueda por asunto/estado/sistema y metricas basicas (conteo por estado y prioridad).

## Frontend

### Epic 1 - Registro y consulta basica de tickets

- FE-1.1 UI de login.
- FE-1.2 UI de listado y detalle de tickets con filtros basicos y orden por ultima actualizacion.
- FE-1.3 UI de creacion de ticket (asunto, sistema, descripcion).

### Epic 2 - Workflow operativo y asignacion

- FE-2.1 Controles de cambio de estado segun rol.
- FE-2.2 UI de asignacion, prioridad, fechas, tipo_solicitud y sistema segun rol.
- FE-2.3 Acciones de cerrar/cancelar para cliente interno.

### Epic 3 - Colaboracion y evidencias

- FE-3.1 UI de comentarios publicos/internos con visibilidad por rol.
- FE-3.2 UI de adjuntos en tickets/comentarios.
- FE-3.3 UI de gestion de involucrados (solo coordinador).

### Epic 4 - Trazabilidad, historial y relaciones

- FE-4.1 UI de historial/auditoria en detalle de ticket.
- FE-4.2 UI de relaciones y duplicados.
- FE-4.3 UI de registro y visualizacion de tiempo (segun rol).

### Epic 5 - Notificaciones de cambios

- FE-5.1 Campanita de notificaciones con contador y listado.

### Epic 6 - Administracion y gobierno

- FE-6.1 UI de administracion de usuarios/roles.
- FE-6.2 UI de catalogos (sistemas, tipos_solicitud, prioridades).
- FE-6.3 UI para marcar ticket interno y editar campos operativos (admin).
- FE-6.4 UI de busqueda y metricas basicas.
