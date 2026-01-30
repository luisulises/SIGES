# Retrospectiva por epic

Epic: 5 (Notificaciones de cambios)
Fecha: 2026-01-30
Participantes: (pendiente)

## Lo que salio bien
- Se implemento el esquema BD `notificaciones` con FKs a `usuarios` y `tickets` + indices utiles para campanita.
- Se centralizo la generacion de notificaciones en `TicketNotificacionService` y se integro a eventos clave del flujo (creacion, asignacion, cambio de estado, comentario publico, cierre/cancelacion).
- API simple y usable: listar notificaciones del usuario + marcar como leida.
- UI: componente de campanita integrado en `AuthenticatedLayout` con polling cada 60s y accion de “marcar leida”.
- QA en verde (revalidado): migraciones aplicadas, `php artisan test --compact` pasa y build de Vite pasa.

## Lo que salio mal
- El componente de campanita contiene texto con encoding incorrecto (mojibake) en strings como “leÃ­da”/“Â·”.
- Cobertura de tests enfocada en “creacion + leer” (falta asegurar por pruebas eventos restantes y exclusion de usuarios inactivos).
- El conteo `unread_count` no filtra por canal (hoy no afecta porque solo se emite `in_app`, pero es una deuda para cuando se active `email`).

## Bloqueos o riesgos detectados
- Riesgo de “notification storm” (muchos inserts por evento) si se agregan mas destinatarios/eventos sin control.
- Performance: campanita trae la primera pagina; si crece el volumen, se requerira paginacion/infinite scroll o politicas de retencion.
- Consistencia: se debe mantener la regla de NO notificar comentarios internos al solicitante (hoy esta ok; riesgo de regresion futura).

## Decisiones tomadas
- MVP: canal `in_app` como default; `email` queda preparado en esquema pero no se usa aun.
- Endpoints API: `GET /api/notificaciones` + `POST /api/notificaciones/{notificacion}/leer`.
- Polling UI: 60s (alineado con el resto del sistema).

## Acciones para el siguiente epic
- [ ] Agregar tests para eventos restantes (asignacion, estado, comentario publico, cierre/cancelacion) + exclusion de usuarios inactivos.
- [ ] Normalizar/asegurar encoding UTF-8 en `resources/js/Components/NotificationsBell.vue`.
- [ ] Decidir politica de retencion/paginacion visible en la UI si el volumen crece.
- [ ] Revisar si `unread_count` debe filtrar por `canal = in_app` cuando se habilite email.

## Estado en sprint-status.yaml
- epic-5-retrospective: done

