---
stepsCompleted: [1, 2, 3, 4]
inputDocuments: ["_bmad-output/planning-artifacts/prd.md", "_bmad-output/planning-artifacts/architecture.md", "_bmad-output/planning-artifacts/product-brief-Sistema - copia-2026-01-01.md"]
---

# Sistema - copia - Epic Breakdown

## Overview

This document provides the complete epic and story breakdown for Sistema - copia, decomposing the requirements from the PRD, UX Design if it exists, and Architecture requirements into implementable stories.

## Requirements Inventory

### Functional Requirements

- FR1: Usuarios pueden iniciar sesion con credenciales validas segun su rol.
- FR2: El sistema aplica visibilidad por rol a la lista de tickets (cliente interno solo los suyos; soporte solo asignados; coordinador solo sus proyectos; admin todos; involucrados pueden ver tickets donde fueron agregados).
- FR3: El sistema aplica visibilidad por rol a comentarios y adjuntos (publicos vs internos).
- FR4: Administrador puede marcar tickets como internos; quedan ocultos para clientes internos (aunque esten involucrados).
- FR5: Usuarios autorizados pueden crear tickets con asunto, proyecto/sistema y descripcion.
- FR6: El asunto y la descripcion del ticket no son editables despues de creado.
- FR7: Tickets nuevos inician en estado Nuevo y sin responsable asignado.
- FR8: Usuarios pueden ver el detalle completo de un ticket segun su rol.
- FR9: Soporte/coordinador/administrador pueden cambiar estado de tickets que gestionan.
- FR10: El sistema valida transiciones por reglas y rol (p. ej., no En progreso sin responsable; Cerrado solo desde Resuelto; no cierre sin resolucion).
- FR11: Cliente interno puede cerrar su propio ticket.
- FR12: Cliente interno puede cancelar su propio ticket.
- FR13: Coordinador/administrador pueden cancelar tickets.
- FR14: El cierre/cancelacion queda registrado en historial.
- FR15: Coordinador puede asignar responsable principal.
- FR16: Coordinador puede reasignar responsable principal.
- FR17: Coordinador puede definir/cambiar prioridad.
- FR18: Coordinador puede definir/modificar fecha compromiso.
- FR19: Soporte puede registrar fecha de entrega.
- FR20: Soporte puede cambiar tipo de solicitud.
- FR21: Coordinador puede cambiar proyecto/sistema.
- FR22: Administrador puede modificar campos operativos del ticket.
- FR23: Usuarios pueden agregar comentarios publicos.
- FR24: Soporte puede agregar comentarios internos.
- FR25: Coordinador puede ver comentarios internos de sus proyectos.
- FR26: Soporte puede registrar resolucion del ticket.
- FR27: Coordinador puede agregar o remover involucrados en un ticket.
- FR28: Usuarios pueden visualizar involucrados del ticket.
- FR29: Involucrados reciben notificaciones por cambios de estado.
- FR30: Usuarios pueden adjuntar archivos en tickets o comentarios.
- FR31: Adjuntos heredan visibilidad del comentario si aplica.
- FR32: Adjuntos no se pueden eliminar.
- FR33: Usuarios pueden relacionar un ticket con uno o mas tickets.
- FR34: Coordinador/administrador pueden marcar ticket duplicado, cancelarlo y referenciar el valido.
- FR35: Soporte puede registrar tiempo invertido de forma acumulativa y no editable.
- FR36: El tiempo registrado es visible en el ticket para roles autorizados.
- FR37: El sistema registra cambios de estado, responsable, prioridad, fechas, tipo, proyecto/sistema, cierre/cancelacion, comentarios y adjuntos.
- FR38: Usuarios autorizados pueden consultar el historial del ticket.
- FR39: Usuarios pueden listar tickets accesibles segun su rol.
- FR40: El listado ordena por ultima actualizacion descendente por defecto.
- FR41: Coordinador y administrador pueden buscar por asunto, estado o proyecto/sistema.
- FR42: El sistema notifica por creacion, asignacion, cambio de estado, comentarios publicos y cierre.
- FR43: Canales disponibles: campanita interna (correo post-MVP).
- FR44: Administrador puede gestionar usuarios y roles.
- FR45: Administrador puede gestionar proyectos/sistemas.
- FR46: Administrador puede gestionar tipos de solicitud.
- FR47: Administrador puede gestionar etiquetas (post-MVP).
- FR48: Administrador puede gestionar estados (post-MVP).
- FR49: Administrador puede gestionar areas/departamentos (post-MVP).
- FR50: Si un responsable es desactivado, el ticket queda sin responsable hasta reasignacion.
- FR51: Involucrados desactivados permanecen en historial pero no reciben notificaciones.
- FR52: El sistema permite crear un nuevo ticket referenciando uno cerrado/cancelado (sin reapertura directa).
- FR53: Administrador puede consultar metricas basicas por estado y prioridad.
- FR54: Coordinador puede consultar metricas basicas por estado y prioridad.

### NonFunctional Requirements

- NFR1: Listado de tickets objetivo <= 3 s.
- NFR2: Apertura de detalle de ticket objetivo <= 2 s.
- NFR3: Guardado de cambios (estado, comentarios, asignacion) objetivo <= 2 s.
- NFR4: Valores de performance son objetivos internos, no SLA.
- NFR5: Autenticacion obligatoria con usuario y contrasena.
- NFR6: Contrasenas almacenadas con hash seguro.
- NFR7: Cifrado en transito (HTTPS).
- NFR8: Control de acceso basado en roles definidos.
- NFR9: Validacion de permisos en backend (no solo en UI).
- NFR10: Uso esperado bajo (< 50 usuarios concurrentes).
- NFR11: Diseno preparado para crecimiento moderado sin cambios estructurales.
- NFR12: Accesibilidad TBD / no aplica en MVP.
- NFR13: Ventana de operacion en horario laboral.
- NFR14: Mantenimientos planificados fuera de horario laboral.
- NFR15: Disponibilidad no critica 24/7.

### Additional Requirements

- Aplicacion interna sin SEO; sin integraciones externas en MVP.
- Actualizacion de estados/comentarios con latencia objetivo <= 60 s (polling).
- Arquitectura MVC con controllers delgados y logica en services.
- API REST/JSON con JSON en snake_case y fechas ISO 8601 UTC.
- Auditoria e historiales append-only.
- Registro de tiempo append-only.
- Adjuntos almacenados fuera de BD con metadatos en BD.
- Notificaciones in_app (correo post-MVP).
- Estilos UI con Tailwind CSS (version TBD).
- Adopcion gradual (no obligatoria desde el dia uno).
- Equipo pequeno (2-3 personas).

### FR Coverage Map

FR1: Epic 1 - Autenticacion y acceso
FR2: Epic 1 - Visibilidad por rol en listados
FR3: Epic 3 - Visibilidad en comentarios y adjuntos
FR4: Epic 6 - Tickets internos
FR5: Epic 1 - Creacion de tickets
FR6: Epic 1 - Asunto y descripcion no editables
FR7: Epic 1 - Estado inicial
FR8: Epic 1 - Detalle segun rol
FR9: Epic 2 - Cambio de estado por soporte/coordinador/admin
FR10: Epic 2 - Reglas de transicion
FR11: Epic 2 - Cierre por solicitante
FR12: Epic 2 - Cancelacion por solicitante
FR13: Epic 2 - Cancelacion por coordinador/admin
FR14: Epic 4 - Historial de cierre/cancelacion
FR15: Epic 2 - Asignar responsable
FR16: Epic 2 - Reasignar responsable
FR17: Epic 2 - Prioridad
FR18: Epic 2 - Fecha compromiso
FR19: Epic 2 - Fecha entrega
FR20: Epic 2 - Cambio tipo de solicitud
FR21: Epic 2 - Cambio proyecto/sistema
FR22: Epic 6 - Modificar campos operativos
FR23: Epic 3 - Comentarios publicos
FR24: Epic 3 - Comentarios internos
FR25: Epic 3 - Visibilidad comentarios internos
FR26: Epic 2 - Registro de resolucion
FR27: Epic 3 - Gestion de involucrados
FR28: Epic 3 - Visualizar involucrados
FR29: Epic 5 - Notificar involucrados
FR30: Epic 3 - Adjuntos en tickets/comentarios
FR31: Epic 3 - Visibilidad de adjuntos
FR32: Epic 3 - Adjuntos no eliminables
FR33: Epic 4 - Relacion de tickets
FR34: Epic 4 - Duplicados
FR35: Epic 4 - Registro de tiempo
FR36: Epic 4 - Visibilidad del tiempo
FR37: Epic 4 - Auditoria de cambios
FR38: Epic 4 - Consulta de historial
FR39: Epic 1 - Listado segun rol
FR40: Epic 1 - Orden por ultima actualizacion
FR41: Epic 6 - Busqueda por asunto/estado/proyecto
FR42: Epic 5 - Notificaciones por eventos
FR43: Epic 5 - Canales campanita (correo post-MVP)
FR44: Epic 6 - Gestion usuarios/roles
FR45: Epic 6 - Gestion proyectos/sistemas
FR46: Epic 6 - Gestion tipos de solicitud
FR47: Post-MVP (etiquetas)
FR48: Post-MVP (estados)
FR49: Post-MVP (areas/departamentos)
FR50: Epic 6 - Manejo de responsables desactivados
FR51: Epic 6 - Manejo involucrados desactivados
FR52: Epic 4 - Nuevo ticket referenciando cerrado/cancelado
FR53: Epic 6 - Metricas por estado/prioridad
FR54: Epic 6 - Metricas por estado/prioridad

## Epic List

### Epic 1: Registro y consulta basica de tickets
Los usuarios inician sesion, crean tickets minimos y consultan lista/detalle segun rol.
**FRs covered:** FR1, FR2, FR5, FR6, FR7, FR8, FR39, FR40

### Epic 2: Workflow operativo y asignacion
Soporte y coordinacion gestionan estados, asignaciones y campos operativos con reglas.
**FRs covered:** FR9, FR10, FR11, FR12, FR13, FR15, FR16, FR17, FR18, FR19, FR20, FR21, FR26

### Epic 3: Colaboracion y evidencias
Comentarios publicos/internos, adjuntos e involucrados con visibilidad correcta.
**FRs covered:** FR3, FR23, FR24, FR25, FR27, FR28, FR30, FR31, FR32

### Epic 4: Trazabilidad, historial y relaciones
Auditoria, historial, relaciones/duplicados y registro de tiempo.
**FRs covered:** FR14, FR33, FR34, FR35, FR36, FR37, FR38, FR52

### Epic 5: Notificaciones de cambios
Notificaciones por eventos clave via campanita in_app (correo post-MVP).
**FRs covered:** FR29, FR42, FR43

### Epic 6: Administracion y gobierno
Catalogos, usuarios/roles, tickets internos, busqueda y metricas.
**FRs covered:** FR4, FR22, FR41, FR44, FR45, FR46, FR50, FR51, FR53, FR54

<!-- Repeat for each epic in epics_list (N = 1, 2, 3...) -->

## Epic 1: Registro y consulta basica de tickets

Los usuarios inician sesion, crean tickets minimos y consultan lista/detalle segun rol.

### Story 1.1: BD - Esquema base de usuarios, roles y tickets

As a administrador del sistema,
I want disponer del esquema base de usuarios, roles, sistemas, estados y tickets,
So that los usuarios puedan autenticarse y registrar solicitudes.

**Acceptance Criteria:**

**Given** las migraciones ejecutadas
**When** se crea un usuario, un sistema y un ticket
**Then** existen tablas usuarios, roles, sistemas, estados_ticket y tickets con claves foraneas validas
**And** el ticket inicia en estado "Nuevo" y responsable_actual es null

### Story 1.2: Backend - Autenticacion y tickets basicos

As a cliente interno,
I want iniciar sesion y crear tickets basicos,
So that pueda registrar solicitudes y ver su estado.

**Acceptance Criteria:**

**Given** credenciales validas
**When** el usuario inicia sesion y crea un ticket con asunto, sistema y descripcion
**Then** el sistema crea el ticket con estado "Nuevo" y sin responsable
**And** registra al solicitante como propietario

**Given** un ticket creado
**When** intenta editar el asunto o la descripcion
**Then** el sistema rechaza el cambio
**And** mantiene el asunto y la descripcion originales

**Given** usuarios con distintos roles
**When** consultan listado y detalle de tickets
**Then** solo ven los tickets permitidos por rol o donde son involucrados
**And** cliente interno no ve tickets internos aunque este involucrado
**And** el listado ordena por ultima actualizacion descendente

### Story 1.3: Frontend - UI de login y tickets basicos

As a cliente interno,
I want una interfaz para crear y consultar tickets,
So that pueda enviar solicitudes sin usar otros canales.

**Acceptance Criteria:**

**Given** usuario autenticado
**When** ingresa al formulario y guarda un ticket
**Then** ve confirmacion y el ticket aparece en su listado
**And** puede abrir el detalle con asunto, descripcion y estado (solo lectura)

**Given** el usuario permanece en la lista o detalle
**When** pasa el intervalo de actualizacion
**Then** la vista se refresca automaticamente en <= 60 s

## Epic 2: Workflow operativo y asignacion

Soporte y coordinacion gestionan estados, asignaciones y campos operativos con reglas.

### Story 2.1: BD - Esquema de workflow y asignaciones

As a administrador del sistema,
I want estructuras para workflow, asignaciones, prioridades, tipos y fechas,
So that el ciclo del ticket se pueda gestionar.

**Acceptance Criteria:**

**Given** migraciones ejecutadas
**When** se aplican
**Then** existen tablas prioridades, tipos_solicitud, reglas_transicion_estado y asignaciones_ticket
**And** tickets incluye columnas prioridad_id, tipo_solicitud_id, fecha_compromiso, fecha_entrega, resolucion, cerrado_at y cancelado_at
**And** estados_ticket incluye En analisis, Asignado, En progreso, Resuelto, Cerrado y Cancelado

### Story 2.2: Backend - Reglas de transicion y cierre/cancelacion

As a soporte,
I want cambiar estados con reglas,
So that el flujo sea consistente.

**Acceptance Criteria:**

**Given** un ticket sin responsable
**When** un rol autorizado intenta pasar a "En progreso"
**Then** el sistema rechaza la transicion
**And** mantiene el estado actual

**Given** un ticket en "Resuelto" con resolucion registrada
**When** el cliente interno cierra el ticket
**Then** el estado pasa a "Cerrado"
**And** el cierre queda registrado en el historial

**Given** cliente interno o coordinador/admin solicita cancelacion
**When** cumple las reglas de rol
**Then** el estado pasa a "Cancelado"
**And** el ticket queda marcado como cancelado

**Given** soporte/coordinador/admin
**When** intenta cambiar estado de un ticket que no gestiona
**Then** el sistema rechaza la operacion

### Story 2.3: Backend - Asignacion y campos operativos

As a coordinador,
I want asignar responsable, prioridad y fechas,
So that el trabajo quede ordenado.

**Acceptance Criteria:**

**Given** un ticket existente
**When** el coordinador asigna o reasigna responsable
**Then** responsable_actual se actualiza y se registra la asignacion

**Given** el coordinador cambia prioridad y fecha_compromiso
**When** guarda cambios
**Then** los campos se actualizan en el ticket

**Given** soporte cambia tipo_solicitud
**When** guarda cambios
**Then** el tipo de solicitud se actualiza

**Given** coordinador cambia proyecto/sistema
**When** guarda cambios
**Then** el proyecto/sistema se actualiza

**Given** soporte registra fecha_entrega y resolucion
**When** guarda
**Then** esos valores quedan disponibles en el ticket

### Story 2.4: Frontend - UI de gestion operativa

As a coordinador,
I want una interfaz para gestionar estado y campos operativos,
So that pueda operar tickets sin friccion.

**Acceptance Criteria:**

**Given** usuario con rol soporte o coordinador
**When** abre el detalle del ticket
**Then** ve controles de estado, asignacion, prioridad, fechas, tipo y sistema segun su rol

**Given** cliente interno
**When** abre el detalle
**Then** solo ve acciones de cerrar o cancelar

**When** se actualiza estado o campos
**Then** la UI refleja el nuevo estado y valores

## Epic 3: Colaboracion y evidencias

Comentarios publicos/internos, adjuntos e involucrados con visibilidad correcta.

### Story 3.1: BD - Esquema de comentarios, adjuntos e involucrados

As a administrador del sistema,
I want tablas para comentarios, adjuntos e involucrados,
So that la colaboracion quede registrada.

**Acceptance Criteria:**

**Given** migraciones ejecutadas
**When** se aplican
**Then** existen tablas comentarios_ticket, adjuntos e involucrados_ticket con claves foraneas
**And** comentarios_ticket incluye visibilidad (publico|interno)
**And** adjuntos referencia ticket y comentario

### Story 3.2: Backend - Comentarios publicos e internos

As a usuario,
I want agregar comentarios publicos o internos,
So that pueda comunicar avances con el nivel correcto de visibilidad.

**Acceptance Criteria:**

**Given** usuario autenticado
**When** agrega comentario publico
**Then** el comentario es visible para roles autorizados segun ticket

**Given** soporte agrega comentario interno
**When** el coordinador del proyecto consulta el ticket
**Then** puede ver el comentario interno
**And** el cliente interno no lo ve

### Story 3.3: Backend - Adjuntos en tickets y comentarios

As a usuario,
I want adjuntar archivos en tickets o comentarios,
So that pueda aportar evidencia.

**Acceptance Criteria:**

**Given** ticket o comentario existente
**When** adjunta un archivo
**Then** el adjunto se guarda y queda asociado al ticket
**And** hereda la visibilidad del comentario si aplica

**Given** un adjunto existente
**When** intenta eliminarlo
**Then** el sistema lo rechaza

### Story 3.4: Backend - Gestion de involucrados

As a coordinador,
I want agregar o remover involucrados,
So that las personas relevantes sigan el ticket.

**Acceptance Criteria:**

**Given** coordinador autorizado
**When** agrega un involucrado
**Then** el usuario aparece en la lista de involucrados del ticket

**Given** coordinador remueve un involucrado
**When** se actualiza la lista
**Then** el involucrado deja de aparecer como activo

### Story 3.5: Frontend - UI de comentarios, adjuntos e involucrados

As a usuario,
I want ver comentarios, adjuntos e involucrados en el ticket,
So that tenga contexto completo.

**Acceptance Criteria:**

**Given** ticket abierto
**When** navega a la seccion de comentarios
**Then** ve comentarios publicos y, si tiene permiso, los internos

**Given** ticket abierto
**When** agrega un comentario o adjunto (al ticket o comentario)
**Then** la lista se actualiza y muestra el nuevo contenido

**Given** coordinador
**When** gestiona involucrados
**Then** ve controles para agregar y remover usuarios

## Epic 4: Trazabilidad, historial y relaciones

Auditoria, historial, relaciones/duplicados y registro de tiempo.

### Story 4.1: BD - Esquema de auditoria, relaciones y tiempo

As a administrador del sistema,
I want tablas de auditoria, relaciones y registro de tiempo,
So that la trazabilidad sea completa.

**Acceptance Criteria:**

**Given** migraciones ejecutadas
**When** se aplican
**Then** existen tablas evento_auditoria_ticket, relacion_ticket y registro_tiempo_ticket con claves foraneas
**And** registro_tiempo_ticket es append-only

### Story 4.2: Backend - Auditoria e historial

As a coordinador,
I want historial de cambios del ticket,
So that pueda explicar decisiones y avances.

**Acceptance Criteria:**

**Given** se cambia estado, responsable, prioridad, fechas, tipo, sistema, cierre, comentarios o adjuntos
**When** se guarda el cambio
**Then** se genera un evento de auditoria con actor, fecha y detalle

**Given** un usuario autorizado
**When** consulta el historial del ticket
**Then** recibe la lista ordenada de eventos

### Story 4.3: Backend - Relaciones, duplicados y referencia

As a usuario,
I want relacionar tickets y marcar duplicados,
So that exista trazabilidad entre solicitudes.

**Acceptance Criteria:**

**Given** dos tickets existentes
**When** el usuario crea una relacion
**Then** ambos tickets quedan vinculados

**Given** coordinador/admin marca un ticket como duplicado
**When** selecciona el ticket valido
**Then** el ticket duplicado queda cancelado y referenciado

**Given** un ticket cerrado o cancelado
**When** se crea un nuevo ticket referenciandolo
**Then** se crea un nuevo ticket sin reabrir el anterior

### Story 4.4: Backend - Registro de tiempo

As a soporte,
I want registrar tiempo invertido de forma acumulativa,
So that quede evidencia del esfuerzo.

**Acceptance Criteria:**

**Given** ticket existente
**When** soporte agrega minutos trabajados
**Then** se crea una nueva entrada de tiempo no editable

**Given** usuario autorizado
**When** consulta el ticket
**Then** ve el tiempo acumulado registrado

### Story 4.5: Frontend - UI de historial, relaciones y tiempo

As a usuario autorizado,
I want ver historial, relaciones y tiempo en el detalle,
So that tenga trazabilidad completa.

**Acceptance Criteria:**

**Given** ticket abierto
**When** accede a la seccion de historial
**Then** ve la linea de tiempo de eventos

**Given** ticket abierto
**When** accede a relaciones
**Then** ve tickets relacionados y duplicados si aplica

**Given** rol autorizado
**When** ve el ticket
**Then** ve el tiempo registrado y puede agregar entradas si es soporte

## Epic 5: Notificaciones de cambios

Notificaciones por eventos clave via campanita in_app (correo post-MVP).

### Story 5.1: BD - Esquema de notificaciones

As a administrador del sistema,
I want una tabla de notificaciones,
So that el sistema pueda registrar avisos in_app (correo post-MVP).

**Acceptance Criteria:**

**Given** migraciones ejecutadas
**When** se aplican
**Then** existe tabla notificaciones con usuario_id, ticket_id, tipo_evento, canal y estado de lectura

### Story 5.2: Backend - Generacion de notificaciones

As a usuario,
I want recibir notificaciones por eventos clave,
So that este informado sin perseguir a nadie.

**Acceptance Criteria:**

**Given** se crea, asigna, cambia estado, comenta publico o se cierra un ticket
**When** ocurre el evento
**Then** se genera una notificacion in_app para solicitante e involucrados activos
**And** se registra campanita interna

**Given** usuario desactivado
**When** ocurre un evento
**Then** no se le generan notificaciones

### Story 5.3: Frontend - Campanita de notificaciones

As a usuario,
I want una campanita de notificaciones,
So that vea eventos recientes del ticket.

**Acceptance Criteria:**

**Given** usuario autenticado
**When** hay notificaciones no leidas
**Then** la campanita muestra el contador

**Given** el usuario abre la lista
**When** marca una notificacion como leida
**Then** el estado se actualiza en la vista

## Epic 6: Administracion y gobierno

Catalogos, usuarios/roles, tickets internos, busqueda y metricas.

### Story 6.1: BD - Catalogos y campos administrativos

As a administrador del sistema,
I want catalogos y campos administrativos adicionales,
So that pueda gobernar el sistema.

**Acceptance Criteria:**

**Given** migraciones ejecutadas
**When** se aplican
**Then** existen catalogos de sistemas, prioridades y tipos_solicitud con campo activo
**And** usuarios incluye campo activo
**And** tickets incluye campo interno

### Story 6.2: Backend - Gestion de usuarios y roles

As a administrador,
I want gestionar usuarios y roles y desactivarlos,
So that el acceso quede controlado.

**Acceptance Criteria:**

**Given** administrador autorizado
**When** crea, edita o desactiva un usuario
**Then** los cambios se guardan y el usuario queda activo/inactivo

**Given** un responsable es desactivado
**When** el sistema procesa el cambio
**Then** los tickets asignados quedan sin responsable

**Given** un involucrado es desactivado
**When** ocurren eventos
**Then** permanece en historial pero no recibe notificaciones

### Story 6.3: Backend - Catalogos, tickets internos y campos operativos

As a administrador,
I want gestionar catalogos e indicar tickets internos,
So that la operacion sea consistente.

**Acceptance Criteria:**

**Given** administrador autorizado
**When** gestiona sistemas, tipos_solicitud y prioridades
**Then** los catalogos se crean, editan o desactivan

**Given** un ticket
**When** el administrador marca el ticket como interno
**Then** el ticket queda oculto para cliente interno (aunque este involucrado)

**Given** administrador
**When** modifica campos operativos del ticket
**Then** los cambios se guardan y quedan visibles

### Story 6.4: Backend - Busqueda y metricas basicas

As a coordinador,
I want buscar tickets y consultar metricas basicas,
So that pueda priorizar y explicar avances.

**Acceptance Criteria:**

**Given** coordinador/admin
**When** busca por asunto, estado o sistema
**Then** obtiene resultados filtrados segun su rol

**Given** coordinador/admin
**When** consulta metricas basicas
**Then** obtiene conteos por estado y prioridad

### Story 6.5: Frontend - UI de administracion, busqueda y metricas

As a administrador o coordinador,
I want pantallas de administracion, busqueda y metricas,
So that pueda operar y gobernar el sistema.

**Acceptance Criteria:**

**Given** administrador
**When** ingresa al area de administracion
**Then** puede gestionar usuarios, roles, catalogos y tickets internos

**Given** coordinador/admin
**When** usa la busqueda
**Then** puede filtrar por asunto, estado y sistema

**Given** coordinador/admin
**When** abre metricas
**Then** ve indicadores basicos por estado y prioridad

