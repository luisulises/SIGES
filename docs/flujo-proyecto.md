# Flujo de trabajo para iniciar y completar SIGES (MVP)

## Objetivo
Definir un flujo end-to-end para iniciar, construir y cerrar el MVP de SIGES usando la informacion de la carpeta docs.

## Fuentes
- docs/planning-artifacts/prd.md
- docs/planning-artifacts/epics.md
- docs/planning-artifacts/architecture.md
- docs/implementation-artifacts/tickets-tecnicos-mvp.md
- docs/implementation-artifacts/* (1-1 a 6-5)
- indicaciones/README.md

## Principios y decisiones base
- Stack: Laravel 10 LTS (PHP 8.2), Vue 3 + Inertia, Tailwind CSS, PostgreSQL 15.
- Starter: Laravel Breeze (Inertia + Vue).
- API REST/JSON en snake_case y fechas ISO 8601 UTC.
- Respuestas: success {data, meta}; error {error:{code, message, details}}.
- RBAC aplicado en backend y visibilidad por rol en UI.
- Auditoria y registro de tiempo append-only.
- Adjuntos fuera de BD con metadatos en BD.
- Actualizacion por polling <= 60 s.

## Estructura de trabajo (macro)
1. Preparacion tecnica y repositorio.
2. Construccion del MVP por epics (DB -> Backend -> Frontend).
3. QA, datos base y cierre del MVP.

## Fase 0: Preparacion tecnica
- Confirmar stack, versiones y starter (ver docs/planning-artifacts/architecture.md).
- Crear el proyecto Laravel con Breeze + Inertia + Vue y configurar Tailwind/Vite.
- Configurar PostgreSQL, .env y storage para adjuntos.
- Aplicar estructura MVC/Services/Policies definida en arquitectura.
- Flujo Git: rama story/1.1-bd-base, commit al terminar la story, merge a main solo con aprobacion.

## Fase 1: Epic 1 - Registro y consulta basica
- DB-1.1: docs/implementation-artifacts/1-1-bd-esquema-base-de-usuarios-roles-y-tickets.md
- BE-1.2: docs/implementation-artifacts/1-2-backend-autenticacion-y-tickets-basicos.md
- FE-1.3: docs/implementation-artifacts/1-3-frontend-ui-de-login-y-tickets-basicos.md
- Resultado esperado: login funcional; creacion/listado/detalle de tickets basicos; estado inicial "Nuevo" y responsable_actual null.

## Fase 2: Epic 2 - Workflow operativo y asignacion
- DB-2.1: docs/implementation-artifacts/2-1-bd-esquema-de-workflow-y-asignaciones.md
- BE-2.2: docs/implementation-artifacts/2-2-backend-reglas-de-transicion-y-cierre-cancelacion.md
- BE-2.3: docs/implementation-artifacts/2-3-backend-asignacion-y-campos-operativos.md
- FE-2.4: docs/implementation-artifacts/2-4-frontend-ui-de-gestion-operativa.md
- Resultado esperado: reglas de transicion, asignaciones, prioridades, fechas y cierre/cancelacion operativos.

## Fase 3: Epic 3 - Colaboracion y evidencias
- DB-3.1: docs/implementation-artifacts/3-1-bd-esquema-de-comentarios-adjuntos-e-involucrados.md
- BE-3.2: docs/implementation-artifacts/3-2-backend-comentarios-publicos-e-internos.md
- BE-3.3: docs/implementation-artifacts/3-3-backend-adjuntos-en-comentarios.md
- BE-3.4: docs/implementation-artifacts/3-4-backend-gestion-de-involucrados.md
- FE-3.5: docs/implementation-artifacts/3-5-frontend-ui-de-comentarios-adjuntos-e-involucrados.md
- Resultado esperado: comentarios publicos/internos, adjuntos y gestion de involucrados con visibilidad correcta.

## Fase 4: Epic 4 - Trazabilidad, historial y relaciones
- DB-4.1: docs/implementation-artifacts/4-1-bd-esquema-de-auditoria-relaciones-y-tiempo.md
- BE-4.2: docs/implementation-artifacts/4-2-backend-auditoria-e-historial.md
- BE-4.3: docs/implementation-artifacts/4-3-backend-relaciones-duplicados-y-referencia.md
- BE-4.4: docs/implementation-artifacts/4-4-backend-registro-de-tiempo.md
- FE-4.5: docs/implementation-artifacts/4-5-frontend-ui-de-historial-relaciones-y-tiempo.md
- Resultado esperado: auditoria e historial, relaciones/duplicados y registro de tiempo operativos.

## Fase 5: Epic 5 - Notificaciones
- DB-5.1: docs/implementation-artifacts/5-1-bd-esquema-de-notificaciones.md
- BE-5.2: docs/implementation-artifacts/5-2-backend-generacion-de-notificaciones.md
- FE-5.3: docs/implementation-artifacts/5-3-frontend-campanita-de-notificaciones.md
- Resultado esperado: notificaciones in_app con campanita y estados de lectura.

## Fase 6: Epic 6 - Administracion y gobierno
- DB-6.1: docs/implementation-artifacts/6-1-bd-catalogos-y-campos-administrativos.md
- BE-6.2: docs/implementation-artifacts/6-2-backend-gestion-de-usuarios-y-roles.md
- BE-6.3: docs/implementation-artifacts/6-3-backend-catalogos-tickets-internos-y-campos-operativos.md
- BE-6.4: docs/implementation-artifacts/6-4-backend-busqueda-y-metricas-basicas.md
- FE-6.5: docs/implementation-artifacts/6-5-frontend-ui-de-administracion-busqueda-y-metricas.md
- Resultado esperado: catalogos, usuarios/roles, tickets internos, busqueda y metricas basicas.

## QA y cierre del MVP
- Ejecutar migraciones y seeders base (estado "Nuevo" y catalogos minimos).
- Validar criterios de aceptacion por story (ver docs/implementation-artifacts/*).
- Verificar objetivos de performance basicos (listado <= 3 s; detalle/guardado <= 2 s).
- Revisar seguridad: RBAC y visibilidad correcta en backend.
- Preparar deploy: variables de entorno, storage persistente y backups de BD.

## Dependencias y orden fino
- El orden detallado y predecesores estan en docs/implementation-artifacts/tickets-tecnicos-mvp.md.
- La estructura y patrones de codigo estan en docs/planning-artifacts/architecture.md.

## Checklist operativo por story (MVP) - fases de desarrollo
Orden sugerido: seguir el listado de arriba hacia abajo.

### Fases usadas
- Preparacion: leer la story y alinear criterios de aceptacion.
- Implementacion: completar Tasks/Subtasks de la story.
- Pruebas: validar criterios de aceptacion en local.
- Cierre: registrar notas, actualizar checklist y commit segun flujo Git.

### Definition of Done (DoD) por story
Aplicar esta plantilla a cada story antes de marcarla como completada.
- [ ] Preparacion: criterios de aceptacion entendidos y riesgos anotados.
- [ ] Implementacion: Tasks/Subtasks completadas segun la story.
- [ ] Implementacion: naming, formatos y estructura respetan arquitectura.
- [ ] Pruebas: validaciones locales cubren todos los AC.
- [ ] Pruebas: datos de prueba y seeders actualizados si aplica.
- [ ] Cierre: notas de implementacion y archivos tocados registrados.
- [ ] Cierre: checklist actualizado y commit realizado en la rama de la story.
- [ ] Cierre: actualizar estado en docs/implementation-artifacts/sprint-status.yaml.

### Fase 0 - Preparacion tecnica (setup)
- [x] Preparacion: confirmar stack, versiones y starter (docs/planning-artifacts/architecture.md).
- [x] Implementacion: crear proyecto Laravel con Breeze + Inertia + Vue.
- [x] Implementacion: configurar PostgreSQL, .env y storage para adjuntos.
- [x] Implementacion: aplicar estructura MVC/Services/Policies definida en arquitectura.
- [ ] Cierre: configurar rama story/1.1-bd-base y flujo de commits.

### Epic 1 - Registro y consulta basica
#### 1-1 BD - Esquema base de usuarios, roles y tickets
- [x] Preparacion: revisar docs/implementation-artifacts/1-1-bd-esquema-base-de-usuarios-roles-y-tickets.md.
- [x] Implementacion: completar Tasks/Subtasks de la story.
- [x] Pruebas: validar AC (migraciones y inserts minimos).
- [x] Cierre: registrar notas y actualizar checklist.
- [x] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [x] DoD: Tasks/Subtasks completadas segun la story.
- [x] DoD: naming, formatos y estructura respetan arquitectura.
- [x] DoD: validaciones locales cubren todos los AC.
- [x] DoD: datos de prueba y seeders actualizados si aplica.
- [x] DoD: notas de implementacion y archivos tocados registrados.
- [x] DoD: checklist actualizado y commit realizado en la rama de la story.

#### 1-2 Backend - Autenticacion y tickets basicos
- [x] Preparacion: revisar docs/implementation-artifacts/1-2-backend-autenticacion-y-tickets-basicos.md.
- [x] Implementacion: completar Tasks/Subtasks de la story.
- [x] Pruebas: validar AC (login, crear ticket y visibilidad).
- [x] Cierre: registrar notas y actualizar checklist.
- [x] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [x] DoD: Tasks/Subtasks completadas segun la story.
- [x] DoD: naming, formatos y estructura respetan arquitectura.
- [x] DoD: validaciones locales cubren todos los AC.
- [x] DoD: datos de prueba y seeders actualizados si aplica.
- [x] DoD: notas de implementacion y archivos tocados registrados.
- [x] DoD: checklist actualizado y commit realizado en la rama de la story.

#### 1-3 Frontend - UI de login y tickets basicos
- [x] Preparacion: revisar docs/implementation-artifacts/1-3-frontend-ui-de-login-y-tickets-basicos.md.
- [x] Implementacion: completar Tasks/Subtasks de la story.
- [x] Pruebas: validar AC (login, listado y detalle).
- [x] Cierre: registrar notas y actualizar checklist.
- [x] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [x] DoD: Tasks/Subtasks completadas segun la story.
- [x] DoD: naming, formatos y estructura respetan arquitectura.
- [x] DoD: validaciones locales cubren todos los AC.
- [x] DoD: datos de prueba y seeders actualizados si aplica.
- [x] DoD: notas de implementacion y archivos tocados registrados.
- [x] DoD: checklist actualizado y commit realizado en la rama de la story.

### Epic 2 - Workflow operativo y asignacion
#### 2-1 BD - Esquema de workflow y asignaciones
- [x] Preparacion: revisar docs/implementation-artifacts/2-1-bd-esquema-de-workflow-y-asignaciones.md.
- [x] Implementacion: completar Tasks/Subtasks de la story.
- [x] Pruebas: validar AC (nuevas tablas y columnas).
- [x] Cierre: registrar notas y actualizar checklist.
- [x] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [x] DoD: Tasks/Subtasks completadas segun la story.
- [x] DoD: naming, formatos y estructura respetan arquitectura.
- [x] DoD: validaciones locales cubren todos los AC.
- [x] DoD: datos de prueba y seeders actualizados si aplica.
- [x] DoD: notas de implementacion y archivos tocados registrados.
- [ ] DoD: checklist actualizado y commit realizado en la rama de la story.

#### 2-2 Backend - Reglas de transicion y cierre/cancelacion
- [x] Preparacion: revisar docs/implementation-artifacts/2-2-backend-reglas-de-transicion-y-cierre-cancelacion.md.
- [x] Implementacion: completar Tasks/Subtasks de la story.
- [x] Pruebas: validar AC (reglas de transicion y cierre/cancelacion).
- [x] Cierre: registrar notas y actualizar checklist.
- [x] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [x] DoD: Tasks/Subtasks completadas segun la story.
- [x] DoD: naming, formatos y estructura respetan arquitectura.
- [x] DoD: validaciones locales cubren todos los AC.
- [x] DoD: datos de prueba y seeders actualizados si aplica.
- [x] DoD: notas de implementacion y archivos tocados registrados.
- [ ] DoD: checklist actualizado y commit realizado en la rama de la story.

#### 2-3 Backend - Asignacion y campos operativos
- [x] Preparacion: revisar docs/implementation-artifacts/2-3-backend-asignacion-y-campos-operativos.md.
- [x] Implementacion: completar Tasks/Subtasks de la story.
- [x] Pruebas: validar AC (responsable, prioridad, fechas, tipo y sistema).
- [x] Cierre: registrar notas y actualizar checklist.
- [x] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [x] DoD: Tasks/Subtasks completadas segun la story.
- [x] DoD: naming, formatos y estructura respetan arquitectura.
- [x] DoD: validaciones locales cubren todos los AC.
- [x] DoD: datos de prueba y seeders actualizados si aplica.
- [x] DoD: notas de implementacion y archivos tocados registrados.
- [x] DoD: checklist actualizado y commit realizado en la rama de la story.

#### 2-4 Frontend - UI de gestion operativa
- [x] Preparacion: revisar docs/implementation-artifacts/2-4-frontend-ui-de-gestion-operativa.md.
- [x] Implementacion: completar Tasks/Subtasks de la story.
- [x] Pruebas: validar AC (controles segun rol).
- [x] Cierre: registrar notas y actualizar checklist.
- [x] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [x] DoD: Tasks/Subtasks completadas segun la story.
- [x] DoD: naming, formatos y estructura respetan arquitectura.
- [x] DoD: validaciones locales cubren todos los AC.
- [x] DoD: datos de prueba y seeders actualizados si aplica.
- [x] DoD: notas de implementacion y archivos tocados registrados.
- [ ] DoD: checklist actualizado y commit realizado en la rama de la story.

### Epic 3 - Colaboracion y evidencias
#### 3-1 BD - Esquema de comentarios, adjuntos e involucrados
- [ ] Preparacion: revisar docs/implementation-artifacts/3-1-bd-esquema-de-comentarios-adjuntos-e-involucrados.md.
- [ ] Implementacion: completar Tasks/Subtasks de la story.
- [ ] Pruebas: validar AC (tablas y claves).
- [ ] Cierre: registrar notas y actualizar checklist.
- [ ] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [ ] DoD: Tasks/Subtasks completadas segun la story.
- [ ] DoD: naming, formatos y estructura respetan arquitectura.
- [ ] DoD: validaciones locales cubren todos los AC.
- [ ] DoD: datos de prueba y seeders actualizados si aplica.
- [ ] DoD: notas de implementacion y archivos tocados registrados.
- [ ] DoD: checklist actualizado y commit realizado en la rama de la story.

#### 3-2 Backend - Comentarios publicos e internos
- [ ] Preparacion: revisar docs/implementation-artifacts/3-2-backend-comentarios-publicos-e-internos.md.
- [ ] Implementacion: completar Tasks/Subtasks de la story.
- [ ] Pruebas: validar AC (visibilidad por rol).
- [ ] Cierre: registrar notas y actualizar checklist.
- [ ] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [ ] DoD: Tasks/Subtasks completadas segun la story.
- [ ] DoD: naming, formatos y estructura respetan arquitectura.
- [ ] DoD: validaciones locales cubren todos los AC.
- [ ] DoD: datos de prueba y seeders actualizados si aplica.
- [ ] DoD: notas de implementacion y archivos tocados registrados.
- [ ] DoD: checklist actualizado y commit realizado en la rama de la story.

#### 3-3 Backend - Adjuntos en comentarios
- [ ] Preparacion: revisar docs/implementation-artifacts/3-3-backend-adjuntos-en-comentarios.md.
- [ ] Implementacion: completar Tasks/Subtasks de la story.
- [ ] Pruebas: validar AC (adjuntos y no eliminables).
- [ ] Cierre: registrar notas y actualizar checklist.
- [ ] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [ ] DoD: Tasks/Subtasks completadas segun la story.
- [ ] DoD: naming, formatos y estructura respetan arquitectura.
- [ ] DoD: validaciones locales cubren todos los AC.
- [ ] DoD: datos de prueba y seeders actualizados si aplica.
- [ ] DoD: notas de implementacion y archivos tocados registrados.
- [ ] DoD: checklist actualizado y commit realizado en la rama de la story.

#### 3-4 Backend - Gestion de involucrados
- [ ] Preparacion: revisar docs/implementation-artifacts/3-4-backend-gestion-de-involucrados.md.
- [ ] Implementacion: completar Tasks/Subtasks de la story.
- [ ] Pruebas: validar AC (agregar/remover y visibilidad).
- [ ] Cierre: registrar notas y actualizar checklist.
- [ ] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [ ] DoD: Tasks/Subtasks completadas segun la story.
- [ ] DoD: naming, formatos y estructura respetan arquitectura.
- [ ] DoD: validaciones locales cubren todos los AC.
- [ ] DoD: datos de prueba y seeders actualizados si aplica.
- [ ] DoD: notas de implementacion y archivos tocados registrados.
- [ ] DoD: checklist actualizado y commit realizado en la rama de la story.

#### 3-5 Frontend - UI de comentarios, adjuntos e involucrados
- [ ] Preparacion: revisar docs/implementation-artifacts/3-5-frontend-ui-de-comentarios-adjuntos-e-involucrados.md.
- [ ] Implementacion: completar Tasks/Subtasks de la story.
- [ ] Pruebas: validar AC (comentarios, adjuntos e involucrados).
- [ ] Cierre: registrar notas y actualizar checklist.
- [ ] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [ ] DoD: Tasks/Subtasks completadas segun la story.
- [ ] DoD: naming, formatos y estructura respetan arquitectura.
- [ ] DoD: validaciones locales cubren todos los AC.
- [ ] DoD: datos de prueba y seeders actualizados si aplica.
- [ ] DoD: notas de implementacion y archivos tocados registrados.
- [ ] DoD: checklist actualizado y commit realizado en la rama de la story.

### Epic 4 - Trazabilidad, historial y relaciones
#### 4-1 BD - Esquema de auditoria, relaciones y tiempo
- [ ] Preparacion: revisar docs/implementation-artifacts/4-1-bd-esquema-de-auditoria-relaciones-y-tiempo.md.
- [ ] Implementacion: completar Tasks/Subtasks de la story.
- [ ] Pruebas: validar AC (append-only y relaciones).
- [ ] Cierre: registrar notas y actualizar checklist.
- [ ] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [ ] DoD: Tasks/Subtasks completadas segun la story.
- [ ] DoD: naming, formatos y estructura respetan arquitectura.
- [ ] DoD: validaciones locales cubren todos los AC.
- [ ] DoD: datos de prueba y seeders actualizados si aplica.
- [ ] DoD: notas de implementacion y archivos tocados registrados.
- [ ] DoD: checklist actualizado y commit realizado en la rama de la story.

#### 4-2 Backend - Auditoria e historial
- [ ] Preparacion: revisar docs/implementation-artifacts/4-2-backend-auditoria-e-historial.md.
- [ ] Implementacion: completar Tasks/Subtasks de la story.
- [ ] Pruebas: validar AC (eventos y consulta).
- [ ] Cierre: registrar notas y actualizar checklist.
- [ ] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [ ] DoD: Tasks/Subtasks completadas segun la story.
- [ ] DoD: naming, formatos y estructura respetan arquitectura.
- [ ] DoD: validaciones locales cubren todos los AC.
- [ ] DoD: datos de prueba y seeders actualizados si aplica.
- [ ] DoD: notas de implementacion y archivos tocados registrados.
- [ ] DoD: checklist actualizado y commit realizado en la rama de la story.

#### 4-3 Backend - Relaciones, duplicados y referencia
- [ ] Preparacion: revisar docs/implementation-artifacts/4-3-backend-relaciones-duplicados-y-referencia.md.
- [ ] Implementacion: completar Tasks/Subtasks de la story.
- [ ] Pruebas: validar AC (relaciones y duplicados).
- [ ] Cierre: registrar notas y actualizar checklist.
- [ ] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [ ] DoD: Tasks/Subtasks completadas segun la story.
- [ ] DoD: naming, formatos y estructura respetan arquitectura.
- [ ] DoD: validaciones locales cubren todos los AC.
- [ ] DoD: datos de prueba y seeders actualizados si aplica.
- [ ] DoD: notas de implementacion y archivos tocados registrados.
- [ ] DoD: checklist actualizado y commit realizado en la rama de la story.

#### 4-4 Backend - Registro de tiempo
- [ ] Preparacion: revisar docs/implementation-artifacts/4-4-backend-registro-de-tiempo.md.
- [ ] Implementacion: completar Tasks/Subtasks de la story.
- [ ] Pruebas: validar AC (append-only y visibilidad).
- [ ] Cierre: registrar notas y actualizar checklist.
- [ ] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [ ] DoD: Tasks/Subtasks completadas segun la story.
- [ ] DoD: naming, formatos y estructura respetan arquitectura.
- [ ] DoD: validaciones locales cubren todos los AC.
- [ ] DoD: datos de prueba y seeders actualizados si aplica.
- [ ] DoD: notas de implementacion y archivos tocados registrados.
- [ ] DoD: checklist actualizado y commit realizado en la rama de la story.

#### 4-5 Frontend - UI de historial, relaciones y tiempo
- [ ] Preparacion: revisar docs/implementation-artifacts/4-5-frontend-ui-de-historial-relaciones-y-tiempo.md.
- [ ] Implementacion: completar Tasks/Subtasks de la story.
- [ ] Pruebas: validar AC (historial, relaciones y tiempo).
- [ ] Cierre: registrar notas y actualizar checklist.
- [ ] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [ ] DoD: Tasks/Subtasks completadas segun la story.
- [ ] DoD: naming, formatos y estructura respetan arquitectura.
- [ ] DoD: validaciones locales cubren todos los AC.
- [ ] DoD: datos de prueba y seeders actualizados si aplica.
- [ ] DoD: notas de implementacion y archivos tocados registrados.
- [ ] DoD: checklist actualizado y commit realizado en la rama de la story.

### Epic 5 - Notificaciones
#### 5-1 BD - Esquema de notificaciones
- [ ] Preparacion: revisar docs/implementation-artifacts/5-1-bd-esquema-de-notificaciones.md.
- [ ] Implementacion: completar Tasks/Subtasks de la story.
- [ ] Pruebas: validar AC (tabla y columnas).
- [ ] Cierre: registrar notas y actualizar checklist.
- [ ] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [ ] DoD: Tasks/Subtasks completadas segun la story.
- [ ] DoD: naming, formatos y estructura respetan arquitectura.
- [ ] DoD: validaciones locales cubren todos los AC.
- [ ] DoD: datos de prueba y seeders actualizados si aplica.
- [ ] DoD: notas de implementacion y archivos tocados registrados.
- [ ] DoD: checklist actualizado y commit realizado en la rama de la story.

#### 5-2 Backend - Generacion de notificaciones
- [ ] Preparacion: revisar docs/implementation-artifacts/5-2-backend-generacion-de-notificaciones.md.
- [ ] Implementacion: completar Tasks/Subtasks de la story.
- [ ] Pruebas: validar AC (eventos y exclusion).
- [ ] Cierre: registrar notas y actualizar checklist.
- [ ] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [ ] DoD: Tasks/Subtasks completadas segun la story.
- [ ] DoD: naming, formatos y estructura respetan arquitectura.
- [ ] DoD: validaciones locales cubren todos los AC.
- [ ] DoD: datos de prueba y seeders actualizados si aplica.
- [ ] DoD: notas de implementacion y archivos tocados registrados.
- [ ] DoD: checklist actualizado y commit realizado en la rama de la story.

#### 5-3 Frontend - Campanita de notificaciones
- [ ] Preparacion: revisar docs/implementation-artifacts/5-3-frontend-campanita-de-notificaciones.md.
- [ ] Implementacion: completar Tasks/Subtasks de la story.
- [ ] Pruebas: validar AC (contador y lectura).
- [ ] Cierre: registrar notas y actualizar checklist.
- [ ] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [ ] DoD: Tasks/Subtasks completadas segun la story.
- [ ] DoD: naming, formatos y estructura respetan arquitectura.
- [ ] DoD: validaciones locales cubren todos los AC.
- [ ] DoD: datos de prueba y seeders actualizados si aplica.
- [ ] DoD: notas de implementacion y archivos tocados registrados.
- [ ] DoD: checklist actualizado y commit realizado en la rama de la story.

### Epic 6 - Administracion y gobierno
#### 6-1 BD - Catalogos y campos administrativos
- [ ] Preparacion: revisar docs/implementation-artifacts/6-1-bd-catalogos-y-campos-administrativos.md.
- [ ] Implementacion: completar Tasks/Subtasks de la story.
- [ ] Pruebas: validar AC (catalogos y flags activo/interno).
- [ ] Cierre: registrar notas y actualizar checklist.
- [ ] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [ ] DoD: Tasks/Subtasks completadas segun la story.
- [ ] DoD: naming, formatos y estructura respetan arquitectura.
- [ ] DoD: validaciones locales cubren todos los AC.
- [ ] DoD: datos de prueba y seeders actualizados si aplica.
- [ ] DoD: notas de implementacion y archivos tocados registrados.
- [ ] DoD: checklist actualizado y commit realizado en la rama de la story.

#### 6-2 Backend - Gestion de usuarios y roles
- [ ] Preparacion: revisar docs/implementation-artifacts/6-2-backend-gestion-de-usuarios-y-roles.md.
- [ ] Implementacion: completar Tasks/Subtasks de la story.
- [ ] Pruebas: validar AC (desactivacion y reglas asociadas).
- [ ] Cierre: registrar notas y actualizar checklist.
- [ ] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [ ] DoD: Tasks/Subtasks completadas segun la story.
- [ ] DoD: naming, formatos y estructura respetan arquitectura.
- [ ] DoD: validaciones locales cubren todos los AC.
- [ ] DoD: datos de prueba y seeders actualizados si aplica.
- [ ] DoD: notas de implementacion y archivos tocados registrados.
- [ ] DoD: checklist actualizado y commit realizado en la rama de la story.

#### 6-3 Backend - Catalogos, tickets internos y campos operativos
- [ ] Preparacion: revisar docs/implementation-artifacts/6-3-backend-catalogos-tickets-internos-y-campos-operativos.md.
- [ ] Implementacion: completar Tasks/Subtasks de la story.
- [ ] Pruebas: validar AC (catalogos y tickets internos).
- [ ] Cierre: registrar notas y actualizar checklist.
- [ ] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [ ] DoD: Tasks/Subtasks completadas segun la story.
- [ ] DoD: naming, formatos y estructura respetan arquitectura.
- [ ] DoD: validaciones locales cubren todos los AC.
- [ ] DoD: datos de prueba y seeders actualizados si aplica.
- [ ] DoD: notas de implementacion y archivos tocados registrados.
- [ ] DoD: checklist actualizado y commit realizado en la rama de la story.

#### 6-4 Backend - Busqueda y metricas basicas
- [ ] Preparacion: revisar docs/implementation-artifacts/6-4-backend-busqueda-y-metricas-basicas.md.
- [ ] Implementacion: completar Tasks/Subtasks de la story.
- [ ] Pruebas: validar AC (busqueda y conteos).
- [ ] Cierre: registrar notas y actualizar checklist.
- [ ] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [ ] DoD: Tasks/Subtasks completadas segun la story.
- [ ] DoD: naming, formatos y estructura respetan arquitectura.
- [ ] DoD: validaciones locales cubren todos los AC.
- [ ] DoD: datos de prueba y seeders actualizados si aplica.
- [ ] DoD: notas de implementacion y archivos tocados registrados.
- [ ] DoD: checklist actualizado y commit realizado en la rama de la story.

#### 6-5 Frontend - UI de administracion, busqueda y metricas
- [ ] Preparacion: revisar docs/implementation-artifacts/6-5-frontend-ui-de-administracion-busqueda-y-metricas.md.
- [ ] Implementacion: completar Tasks/Subtasks de la story.
- [ ] Pruebas: validar AC (pantallas admin, busqueda y metricas).
- [ ] Cierre: registrar notas y actualizar checklist.
- [ ] DoD: criterios de aceptacion entendidos y riesgos anotados.
- [ ] DoD: Tasks/Subtasks completadas segun la story.
- [ ] DoD: naming, formatos y estructura respetan arquitectura.
- [ ] DoD: validaciones locales cubren todos los AC.
- [ ] DoD: datos de prueba y seeders actualizados si aplica.
- [ ] DoD: notas de implementacion y archivos tocados registrados.
- [ ] DoD: checklist actualizado y commit realizado en la rama de la story.

### QA y cierre del MVP (fase final)
- [ ] Pruebas: ejecutar migraciones y seeders base (estado "Nuevo" y catalogos minimos).
- [ ] Pruebas: validar criterios de aceptacion por story (docs/implementation-artifacts/*).
- [ ] Pruebas: verificar performance basico (listado <= 3 s; detalle/guardado <= 2 s).
- [ ] Cierre: revisar seguridad (RBAC y visibilidad correcta en backend).
- [ ] Cierre: preparar deploy (variables de entorno, storage persistente y backups de BD).

### Retrospectivas por epic (opcional)
- [ ] Epic 1: registrar aprendizajes y actualizar epic-1-retrospective en docs/implementation-artifacts/sprint-status.yaml.
- [ ] Epic 2: registrar aprendizajes y actualizar epic-2-retrospective en docs/implementation-artifacts/sprint-status.yaml.
- [ ] Epic 3: registrar aprendizajes y actualizar epic-3-retrospective en docs/implementation-artifacts/sprint-status.yaml.
- [ ] Epic 4: registrar aprendizajes y actualizar epic-4-retrospective en docs/implementation-artifacts/sprint-status.yaml.
- [ ] Epic 5: registrar aprendizajes y actualizar epic-5-retrospective en docs/implementation-artifacts/sprint-status.yaml.
- [ ] Epic 6: registrar aprendizajes y actualizar epic-6-retrospective en docs/implementation-artifacts/sprint-status.yaml.

