---
stepsCompleted: [1, 2, 3, 4, 5]
inputDocuments:
  - docs/product.md
  - docs/user-stories.md
  - docs/idea/respuestas.md
date: 2026-01-01
author: Alfal
---

# Product Brief: {{project_name}}

<!-- Content will be appended sequentially through collaborative workflow steps -->

## Executive Summary

SIGES es un sistema interno para centralizar y dar trazabilidad a solicitudes del area de sistemas (soporte, cambios y proyectos). Hoy la gestion es dispersa (Notion/Excel/legacy) con registros inconsistentes, tareas ambiguas y visibilidad dependiente de enlaces, lo que genera urgencias repetidas, trabajo duplicado y falta de metricas. El MVP prioriza registro rapido con campos minimos, roles claros, estados y seguimiento, sin reemplazar los canales de comunicacion existentes. Supuestos a validar: adopcion real del registro por todos los roles y mantenimiento consistente de catalogos de proyectos/sistemas.

---

## Core Vision

### Problem Statement

El area de sistemas no cuenta con un registro unico y confiable de solicitudes y actividades, lo que impide orden, seguimiento y control operativo.

### Problem Impact

El desorden provoca solicitudes urgentes sin traza, perdida de contexto, dificultad para priorizar y ausencia de metricas claras sobre quien pidio que y cuando.

### Why Existing Solutions Fall Short

Las soluciones actuales (Notion/Excel/legacy) no aseguran un registro constante, dependen de disciplina manual y enlaces compartidos, y no ofrecen estados, responsables y trazabilidad consistentes.

### Proposed Solution

Un sistema interno simple para registrar, asignar, dar seguimiento y cerrar tickets con roles definidos, estados, comentarios publicos/internos, adjuntos, historial automatico y notificaciones basicas, manteniendo campos minimos y sin sustituir la comunicacion existente.

### Key Differentiators

Producto interno: no se busca diferenciacion externa; el valor se centra en alineacion al proceso del area, registro rapido con campos minimos y alta trazabilidad operativa.

---

## Target Users

### Primary Users

**Laura Martinez (Analista administrativa / cliente interno)**
- Contexto: usuaria interna que solicita soporte o cambios para continuar su trabajo sin bloqueos.
- Objetivos: pedir ayuda sin friccion, dar seguimiento y cerrar cuando queda conforme.
- Problema actual: depende de correos/mensajes, no sabe si su solicitud fue recibida, no tiene fechas ni responsables claros y debe insistir.
- Exito: crear un ticket en minutos, ver estado sin preguntar, recibir notificaciones claras, saber quien lo atiende, cerrar el ticket cuando esta conforme.

### Secondary Users

**Carlos Gomez (Soporte / Programador)**
- Contexto: atiende multiples solicitudes en paralelo mientras desarrolla y da soporte correctivo.
- Objetivos: trabajar solo en lo asignado, con contexto claro y sin interrupciones.
- Problema actual: solicitudes desordenadas, cambios por distintos canales, decisiones perdidas en chats, dificultad para planear el dia.
- Exito: ver solo tickets asignados, tener contexto completo en un lugar, registrar avances sin repetirlos, cerrar sin reprocesos.

**Mariana Lopez (Coordinadora)**
- Contexto: coordina proyectos/sistemas y es responsable de flujo, prioridades y resultados.
- Objetivos: mantener el trabajo ordenado, priorizado y con responsables claros.
- Problema actual: no saber quien atiende que, tickets mal clasificados o sin prioridad, retrasos sin visibilidad, dificultad para justificar decisiones.
- Exito: asignar/reasignar facil, definir prioridades y fechas compromiso, tener trazabilidad para explicar avances y bloqueos, cerrar ciclos sin ambiguedad.

**Andres Ruiz (Administrador)**
- Contexto: responsable del sistema y del gobierno operativo.
- Objetivos: que el sistema funcione, sea consistente y refleje la realidad operativa.
- Problema actual: datos incompletos, poca visibilidad global, roles mal configurados, dificil auditar decisiones pasadas.
- Exito: visibilidad total, reglas claras, auditoria sin depender de personas, sistema autoexplicativo.

### User Journey

N/A (pendiente de definir)

---

## Success Metrics

**Metricas de usuario y operacion**
- Porcentaje de tickets con responsable asignado en < 24 h.
- Tiempo promedio a primera respuesta.
- Tiempo promedio a resolucion.
- Porcentaje de tickets cerrados por el solicitante.
- Porcentaje de tickets con historial completo (estado + comentarios).

### Business Objectives

**A 3 meses**
- 70% de solicitudes registradas en el sistema.
- 80% de tickets con responsable asignado.
- Reduccion clara de tickets sin estado.

**A 12 meses**
- 90% de solicitudes registradas.
- Historial completo en 95% de tickets.
- Tendencia estable o decreciente en tiempos de resolucion.
- Uso activo de metricas por coordinacion/administracion.

### Key Performance Indicators

- Tiempo promedio a primera respuesta: meta < 24 h (plazo 3 meses).
- Porcentaje de tickets con responsable asignado en < 24 h: meta 85% (plazo 3 meses).
- Tiempo promedio a resolucion por tipo de solicitud: linea base + mejora continua (plazo 6-12 meses).
- Porcentaje de tickets cerrados por el solicitante: meta 30% (plazo 6 meses).
- Porcentaje de tickets con comentarios y estados usados correctamente: meta 90% (plazo 3-6 meses).

---

## MVP Scope

### Core Features

- Creacion de tickets (asunto, proyecto/sistema, descripcion; comentario inicial no editable).
- Roles claros con visibilidad acotada (Cliente interno, Soporte/Programador, Coordinador, Administrador).
- Estados del ticket y flujo basico (Nuevo, En analisis, Asignado, En progreso, Resuelto, Cerrado, Cancelado).
- Asignacion de responsable principal (requisito para avanzar el trabajo).
- Comentarios publicos e internos (visibilidad controlada y notificaciones).
- Historial y auditoria automatica (estados, responsables, fechas, prioridad, cierre/cancelacion).
- Notificaciones basicas (creacion, asignacion, cambio de estado, comentarios publicos, cierre).
- Busqueda simple (por asunto, estado y proyecto/sistema).
- Adjuntos con trazabilidad (heredan visibilidad, no se eliminan).
- Cierre y cancelacion con reglas claras (incluye cierre por solicitante).

### Out of Scope for MVP

- SLA automaticos.
- Dashboards ejecutivos avanzados.
- Reportes exportables.
- Automatizaciones complejas.
- Subtareas jerarquicas.
- Integraciones externas (WhatsApp, Jira, etc.).
- Reglas de permisos granulares.
- Reapertura directa de tickets.
- Encuestas de satisfaccion.
- IA / priorizacion automatica.

### MVP Success Criteria

- 70% de solicitudes internas registradas como tickets.
- 80% de tickets con responsable asignado en < 24 h.
- Tiempo promedio a primera respuesta < 24 h.
- 30% de tickets cerrados por el solicitante.
- Historial completo en 90% de los tickets.

### Future Vision

- SLA y alertas por incumplimiento.
- Dashboards operativos y ejecutivos (carga, cuellos de botella, tendencias).
- Automatizacion ligera (asignaciones sugeridas, reglas simples).
- Integraciones con herramientas existentes (correo, mensajeria interna, herramientas de desarrollo).
- Mejora de experiencia del solicitante (feedback estructurado, comunicacion mas clara).
