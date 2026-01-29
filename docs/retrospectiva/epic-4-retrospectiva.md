# Retrospectiva por epic

Epic: 4 (Trazabilidad, historial y relaciones)
Fecha: 2026-01-28
Participantes: (pendiente)

## Lo que salio bien
- Se implemento la trazabilidad end-to-end: BD + endpoints + UI en el detalle del ticket.
- Auditoria: cambios operativos y workflow generan eventos append-only y se pueden consultar por ticket.
- Relaciones: se pueden crear/listar; duplicados alineados a permisos de cancelacion (y cancela el ticket duplicado).
- Tiempo: registro append-only por ticket y visible solo para roles internos autorizados.
- Baseline de calidad en verde: tests y build pasan.

## Lo que salio mal
- No hay paginacion aun para historial/relaciones/tiempo (riesgo si crecen mucho).

## Bloqueos o riesgos detectados
- Crecimiento de tablas append-only (historial/tiempo): requiere estrategia de paginacion/indices si el volumen aumenta.
- Visibilidad por rol: mantener el filtro (cliente interno solo ve estado/cierre/cancelacion; tiempo solo roles internos).

## Decisiones tomadas
- Endpoints API Epic 4:
  - `GET /api/tickets/{ticket}/historial`
  - `GET|POST /api/tickets/{ticket}/relaciones`
  - `GET|POST /api/tickets/{ticket}/tiempo`
- Naming de tablas (plural): `eventos_auditoria_ticket`, `relaciones_ticket`, `registros_tiempo_ticket`.

## Acciones para el siguiente epic
- [ ] Agregar paginacion a historial y tiempo (API + UI) si el volumen lo requiere.
- [ ] Definir indices adicionales segun consultas reales (ej. por ticket_id + created_at).

## Estado en sprint-status.yaml
- epic-4-retrospective: done

