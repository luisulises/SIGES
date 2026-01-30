# Acciones de retrospectivas (Kaizen backlog)

Este documento centraliza las **acciones** que salen de las retrospectivas por épica para que:
- se conviertan en trabajo real (backlog),
- tengan **owner** y **criterio de “done”**,
- y se revisen en la siguiente retrospectiva antes de proponer acciones nuevas.

## Proceso (mínimo)

1) En cada retrospectiva:
   - Definir 1–3 acciones máximo (si hay más, agrupar o priorizar).
   - Asignar owner y fecha objetivo.
   - Definir evidencia para cerrar (tests/commands/validaciones).
2) Antes de cerrar la siguiente retrospectiva:
   - Revisar el estado de las acciones anteriores (done / bloqueado / pendiente).
   - Si hay acciones pendientes, priorizarlas antes de proponer nuevas.

## Acciones (estado actual)

| Epic | Acción | Severidad | Owner | Estado | Evidencia/DoD |
|---|---|---:|---|---|---|
| 3 | Documentar comandos Windows (`npm.cmd`, `localhost:5173`, Docker/Postgres 5433) + checklist pre-épica | High | TBD | Done | `docs/resumen.md` actualizado + smoke checklist |
| 3 | Alinear naming tablas Épica 4 en docs/migraciones/modelos | High | TBD | Done | Migraciones/tablas en plural (`eventos_auditoria_ticket`, `relaciones_ticket`, `registros_tiempo_ticket`) |
| 3 | Resolver discrepancia cierre soporte (seed vs permisos backend) | Medium | TBD | Done | Seeder sin cierre para soporte + tests en verde |
| 4 | Agregar paginación a historial y tiempo (API + UI) si el volumen lo requiere | Medium | TBD | Done | Endpoints soportan `page/per_page` + UI “Cargar más” |
| 4 | Definir índices adicionales según consultas reales (ej. `ticket_id + created_at`) | Medium | TBD | Done | Migración agrega índices compuestos (`2026_01_30_000001_*`), `php artisan migrate` OK |
| 5 | Agregar tests para eventos restantes + exclusión de usuarios inactivos | High | TBD | Done | `php artisan test --compact` en verde + cobertura (asignacion/estado/comentario/cierre/cancelacion/inactivos) |
| 5 | Revisar `unread_count` para filtrar canal `in_app` (cuando exista `email`) | Low | TBD | Done | `unread_count` filtra `canal = in_app` y tests pasan |
| 5 | Definir política de retención/paginación visible en la UI | Low | TBD | Done | Campanita soporta paginación y “Cargar más” |

## Cómo cerrar una acción

Una acción pasa a **Done** solo si:
- hay evidencia objetiva (tests/commands/manual steps),
- y queda documentado “qué cambió” (archivo(s) tocados + razón).
