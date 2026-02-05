---
stepsCompleted: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
inputDocuments:
  - _bmad-output/planning-artifacts/product-brief-Sistema - copia-2026-01-01.md
  - docs/user-stories.md
documentCounts:
  briefs: 1
  research: 0
  brainstorming: 0
  projectDocs: 0
workflowType: 'prd'
lastStep: 0
---

# Product Requirements Document - Sistema - copia

**Author:** Alfal
**Date:** 2026-01-02

## Executive Summary

SIGES es un sistema interno greenfield para centralizar y dar trazabilidad a solicitudes del area de sistemas (soporte, cambios y proyectos). Convive con Notion/Excel/legacy como apoyo historico, pero busca ser el registro confiable. Atiende a cliente interno (primario) y a roles de sistemas (soporte, coordinacion y administracion) con foco en visibilidad, orden operativo y cierre claro.

### What Makes This Special

Producto interno: no se busca diferenciacion externa. El valor esta en el registro rapido con campos minimos, roles claros, estados consistentes y trazabilidad completa, sin reemplazar canales de comunicacion existentes.

## Project Classification

**Technical Type:** web_app  
**Domain:** general  
**Complexity:** low  
**Project Context:** Greenfield - new project

Clasificacion basada en señales del brief: sistema interno/intranet, gestion de tickets, roles y seguimiento.

## Success Criteria

### User Success

- Cliente interno puede registrar su solicitud rapido, ver estado sin perseguir y cerrar el ciclo cuando se resuelve.
- Sistemas trabaja con backlog visible y priorizado, con menos interrupciones y retrabajo.
- Historial completo y trazable en cada ticket para explicar decisiones.

### Business Success

**A 3 meses**
- 70% de solicitudes registradas en el sistema.
- 80% de tickets con responsable asignado.
- Reduccion clara de tickets sin estado.

**A 12 meses**
- 90% de solicitudes registradas.
- Historial completo en 95% de tickets.
- Tendencia estable o decreciente en tiempos de resolucion.
- Uso activo de metricas por coordinacion/administracion.

### Technical Success

- Operacion interna sin SLA formal (MVP).
- Mantenimiento y operacion manejables por equipo pequeno.
- Integridad del historial y auditoria (sin perdida de eventos).

### Measurable Outcomes

- Tiempo promedio a primera respuesta: meta < 24 h (plazo 3 meses).
- % tickets con responsable asignado en < 24 h: meta 85% (plazo 3 meses).
- Tiempo promedio a resolucion por tipo de solicitud: linea base + mejora continua (plazo 6-12 meses).
- % tickets cerrados por el solicitante: meta 30% (plazo 6 meses).
- % tickets con comentarios y estados usados correctamente: meta 90% (plazo 3-6 meses).

## Product Scope

### MVP - Minimum Viable Product

- Creacion de tickets (asunto, proyecto/sistema, descripcion; comentario inicial no editable).
- Roles claros con visibilidad acotada.
- Estados del ticket y flujo basico.
- Asignacion de responsable principal.
- Comentarios publicos e internos.
- Historial y auditoria automatica.
- Notificaciones basicas.
- Busqueda simple.
- Adjuntos con trazabilidad.
- Cierre y cancelacion con reglas claras (incluye cierre por solicitante).

### Growth Features (Post-MVP)

- SLA y alertas por incumplimiento.
- Dashboards operativos y ejecutivos.
- Automatizacion ligera (asignaciones sugeridas, reglas simples).
- Integraciones con herramientas existentes.
- Mejora de experiencia del solicitante (feedback estructurado, comunicacion mas clara).

### Vision (Future)

Evolucionar a una plataforma interna con trazabilidad completa y gestion proactiva, basada en metricas y automatizaciones graduales.

## User Journeys

**Journey 1: Laura Martinez - Solicitud resuelta sin perseguir**
Laura, analista administrativa, detecta un problema que bloquea su trabajo. Entra a SIGES, crea un ticket con asunto, proyecto/sistema y descripcion, y adjunta evidencia. Ve el estado "Nuevo" y recibe notificacion cuando se asigna responsable. A medida que el responsable avanza, Laura recibe comentarios publicos y el estado cambia hasta "Resuelto". Verifica la solucion y cierra el ticket. El historial deja registrada la secuencia completa y Laura no tuvo que perseguir a nadie.

**Journey 2: Laura Martinez - Cancelacion sin friccion**
Laura crea un ticket, pero el problema deja de ser relevante. Al ver que aun no hay responsable asignado, decide cancelar la solicitud desde el ticket. El estado cambia a "Cancelado" y queda registrado en el historial. Si el problema reaparece, crea un nuevo ticket (sin reapertura directa).

**Journey 3: Carlos Gomez - Trabajo ordenado y visible**
Carlos inicia sesion y ve los tickets asignados (y aquellos donde es involucrado). Abre uno, revisa contexto y cambia el estado a "En progreso". Agrega un comentario interno con decisiones y un comentario publico para el solicitante. Adjunta evidencia cuando es necesario y mantiene el estado actualizado. Al completar el trabajo, marca el ticket como "Resuelto", dejando todo registrado para auditoria.

**Journey 4: Andres Ruiz - Gobierno del sistema**
Andres administra el sistema: configura usuarios y roles, mantiene el catalogo de proyectos/sistemas y tipos de solicitud, y asegura prioridades consistentes y estados predefinidos. Revisa auditoria e historial para verificar que cambios y cierres sean trazables. Cuando detecta incoherencias (p. ej., tickets sin responsable), coordina ajustes y garantiza que el sistema refleje la operacion real.

### Journey Requirements Summary

- Autenticacion y acceso por rol con visibilidad acotada.
- Creacion de tickets con campos obligatorios y adjuntos.
- Estados y transiciones con reglas (nuevo, en analisis, asignado, en progreso, resuelto, cerrado, cancelado).
- Asignacion/reasignacion de responsable principal.
- Comentarios publicos e internos con notificaciones basicas.
- Historial/auditoria de cambios y eventos.
- Busqueda simple por asunto, estado y proyecto/sistema.
- Cierre/cancelacion con reglas claras (incluye cierre por solicitante).
- Administracion de usuarios, roles, proyectos/sistemas, tipos y prioridades (estados predefinidos).

## Web App Specific Requirements

### Project-Type Overview

- Aplicacion web de uso interno para gestion de tickets.
- SPA/MPA indiferente (TBD).
- Requiere actualizacion frecuente de estados y comentarios (casi en tiempo real).

### Technical Architecture Considerations

- Control de acceso por rol y visibilidad acotada.
- Consistencia de historial y auditoria de cambios.
- Actualizacion de estados/comentarios con latencia objetivo <= 60s.
- Sin SLA formal en MVP.

### Browser Support Matrix

- Navegadores modernos requeridos: Chrome, Edge, Firefox y Safari (ultimas 2 versiones).
- Navegadores legacy (IE o similares): fuera de alcance.

### Responsive Design

- Uso principal en desktop/laptop.
- Responsive basico para consulta en pantallas pequenas.

### Performance Targets

- Tiempos de carga y respuesta: TBD (sin SLA formal en MVP).
- Actualizacion de estado/comentarios: <= 60s.

### SEO Strategy

- No requiere SEO (aplicacion interna).

### Accessibility Level

- TBD.

### Implementation Considerations

- Sin decisiones tecnicas en esta fase; requisitos funcionales y de uso primero.

## Project Scoping & Phased Development

### MVP Strategy & Philosophy

**MVP Approach:** Problem-Solving MVP  
**Resource Requirements:** Equipo pequeno (2-3 personas: PM/PO + dev full-stack + QA/ops parcial).

### MVP Feature Set (Phase 1)

**Core User Journeys Supported:**
- Laura (happy path: solicitud -> seguimiento -> cierre).
- Laura (cancelacion sin friccion).
- Carlos (gestion diaria y cambios de estado).
- Andres (gobierno del sistema).

**Must-Have Capabilities:**
- Creacion de tickets con campos obligatorios y adjuntos.
- Roles y visibilidad acotada.
- Estados y transiciones basicas.
- Asignacion de responsable principal.
- Comentarios publicos/internos.
- Historial y auditoria.
- Notificaciones basicas.
- Busqueda simple.
- Cierre/cancelacion con reglas claras.

### Post-MVP Features

**Phase 2 (Post-MVP):**
- SLA y alertas por incumplimiento.
- Dashboards operativos/ejecutivos.
- Automatizacion ligera (asignaciones sugeridas, reglas simples).
- Integraciones con herramientas existentes.
- Feedback estructurado del solicitante.

**Phase 3 (Expansion):**
- Evolucion a plataforma con gestion proactiva basada en metricas y automatizaciones graduales.

### Risk Mitigation Strategy

**Technical Risks:**  
- Cambio cultural/adopcion.  
Mitigacion: onboarding simple, reglas claras de uso, champions internos y seguimiento de adopcion con metricas basicas.

**Market Risks:**  
- Uso irregular por parte de areas internas.  
Mitigacion: medicion semanal de adopcion y backlog visible.

**Resource Risks:**  
- Equipo mas pequeno de lo previsto.  
Mitigacion: recortar primero notificaciones, mantener core de tickets, estados e historial.

## Functional Requirements

### Autenticacion y Acceso por Rol
- FR1: Usuarios pueden iniciar sesion con credenciales validas segun su rol.
- FR2: El sistema aplica visibilidad por rol a la lista de tickets (cliente interno solo los suyos; soporte solo asignados; coordinador solo sus proyectos; admin todos; involucrados pueden ver tickets donde fueron agregados).
- FR3: El sistema aplica visibilidad por rol a comentarios y adjuntos (publicos vs internos).
- FR4: Administrador puede marcar tickets como internos; quedan ocultos para clientes internos (aunque esten involucrados).

### Creacion y Gestion de Tickets
- FR5: Usuarios autorizados pueden crear tickets con asunto, proyecto/sistema y descripcion.
- Nota MVP: Solo se permite crear tickets con un proyecto/sistema activo.
- FR6: El asunto y la descripcion del ticket no son editables despues de creado.
- FR7: Tickets nuevos inician en estado Nuevo y sin responsable asignado.
- FR8: Usuarios pueden ver el detalle completo de un ticket segun su rol y relacion (involucrados).

### Estados y Reglas de Transicion
- FR9: Soporte/coordinador/administrador pueden cambiar estado de tickets que gestionan.
- FR10: El sistema valida transiciones por reglas y rol (p. ej., no En progreso sin responsable; Cerrado solo desde Resuelto; no cierre sin resolucion).
- FR11: Cliente interno puede cerrar su propio ticket.
- FR12: Cliente interno puede cancelar su propio ticket.
- FR13: Coordinador/administrador pueden cancelar tickets.
- FR14: El cierre/cancelacion queda registrado en historial.

### Asignacion, Prioridad y Fechas
- FR15: Coordinador puede asignar responsable principal.
- FR16: Coordinador puede reasignar responsable principal.
- FR17: Coordinador puede definir/cambiar prioridad.
- FR18: Coordinador puede definir/modificar fecha compromiso.
- FR19: Soporte puede registrar fecha de entrega.
- FR20: Soporte puede cambiar tipo de solicitud.
- FR21: Coordinador puede cambiar proyecto/sistema.
- FR22: Administrador puede modificar campos operativos del ticket.

### Comentarios, Resolucion e Involucrados
- FR23: Usuarios pueden agregar comentarios publicos.
- FR24: Roles internos (soporte/coordinador/admin) pueden agregar comentarios internos en tickets que gestionan.
- FR25: Coordinador puede ver comentarios internos de sus proyectos.
- FR26: Soporte puede registrar resolucion del ticket.
- FR27: Coordinador puede agregar o remover involucrados en un ticket.
- FR28: Usuarios pueden visualizar involucrados del ticket.
- FR29: Involucrados reciben notificaciones por cambios de estado.

### Adjuntos
- FR30: Usuarios pueden adjuntar archivos en comentarios (no adjuntos directos al ticket en MVP).
- FR31: Adjuntos heredan visibilidad del comentario si aplica.
- FR32: Adjuntos no se pueden eliminar.
- Nota MVP: Se pueden subir y listar adjuntos; descarga/vista previa es post-MVP.

### Relaciones y Duplicados
- FR33: Usuarios pueden relacionar un ticket con uno o mas tickets.
- FR34: Coordinador/administrador pueden marcar ticket duplicado, cancelarlo y referenciar el valido.
- Nota MVP: El ticket valido no puede estar en estado Cancelado.

### Registro de Tiempo
- FR35: Roles internos autorizados pueden registrar tiempo invertido de forma acumulativa y no editable (soporte asignado; coordinador/admin para sus tickets).
- FR36: El tiempo registrado es visible en el ticket para roles autorizados.

### Historial y Auditoria
- FR37: El sistema registra cambios de estado, responsable, prioridad, fechas, tipo, proyecto/sistema, cierre/cancelacion, comentarios y adjuntos.
- FR38: Usuarios autorizados pueden consultar el historial del ticket.

### Busqueda y Listados
- FR39: Usuarios pueden listar tickets accesibles segun su rol y relacion (involucrados).
- FR40: El listado ordena por ultima actualizacion descendente por defecto.
- FR41: Coordinador y administrador pueden buscar por asunto, estado o proyecto/sistema.
- Nota MVP: La busqueda por asunto es parcial y no distingue mayusculas/minusculas.

### Notificaciones
- FR42: El sistema notifica por creacion, asignacion, cambio de estado, comentarios publicos y cierre.
- FR43: Canales disponibles: campanita interna (correo post-MVP).

### Administracion del Sistema
- FR44: Administrador puede gestionar usuarios y roles.
- FR45: Administrador puede gestionar proyectos/sistemas.
- FR46: Administrador puede gestionar tipos de solicitud.
- FR47: Administrador puede gestionar etiquetas (post-MVP).
- FR48: Administrador puede gestionar estados (post-MVP).
- FR49: Administrador puede gestionar areas/departamentos (post-MVP).

### Manejo de Usuarios Desactivados
- FR50: Si un responsable es desactivado, el ticket queda sin responsable hasta reasignacion.
- FR51: Involucrados desactivados permanecen en historial pero no reciben notificaciones.

### Reapertura Indirecta
- FR52: El sistema permite crear un nuevo ticket referenciando uno cerrado/cancelado (sin reapertura directa).
- Nota MVP: En la creacion de ticket se puede enviar `referencia_ticket_id` (Cerrado/Cancelado) para crear una relacion tipo `reabre`, sin reabrir el ticket anterior.

### Metricas Operativas
- FR53: Administrador puede consultar metricas basicas por estado y prioridad.
- FR54: Coordinador puede consultar metricas basicas por estado y prioridad.

## Non-Functional Requirements

### Performance

- Listado de tickets: objetivo <= 3 s.
- Apertura de detalle de ticket: objetivo <= 2 s.
- Guardado de cambios (estado, comentarios, asignacion): objetivo <= 2 s.
- Estos valores son objetivos internos, no SLA.

### Security

- Autenticacion obligatoria con usuario y contrasena.
- Contrasenas almacenadas con hash seguro.
- Cifrado en transito (HTTPS).
- Control de acceso basado en roles definidos.
- Validacion de permisos en backend (no solo en UI).

### Scalability

- Uso esperado bajo: < 50 usuarios concurrentes.
- Diseno preparado para crecimiento moderado sin cambios estructurales.

### Accessibility

- TBD / No aplica en MVP (sistema interno, sin requerimiento normativo formal).

### Availability

- Ventana de operacion: horario laboral.
- Mantenimientos planificados fuera de horario laboral.
- Disponibilidad no critica 24/7.
