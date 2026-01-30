# Plantilla de retrospectiva por epic

Epic: 4 (Trazabilidad, historial y relaciones)
Fecha: 2026-01-28
Participantes: (pendiente)

## Lo que salio bien
- Infra en verde: `siges-postgres` (Postgres 15) arriba y healthy en `0.0.0.0:5433->5432`.
- BD consistente: `php artisan migrate:status` OK (migraciones aplicadas).
- Baseline de calidad OK: `php artisan test` OK (56 passed), cubriendo Épicas 1–3.
- Build de frontend OK: `npm.cmd -s run build` OK.

## Lo que salio mal
- Cuando Docker/Postgres no está disponible, `php artisan test` y `php artisan migrate:status` fallan por conexión a `127.0.0.1:5433`.
- En Windows/PowerShell, `npm` puede fallar por ExecutionPolicy; se requiere usar `npm.cmd`.
- Vite dev server puede escuchar en IPv6 (`::1:5173`), por lo que `http://127.0.0.1:5173` no conecta; usar `http://localhost:5173`.

## Bloqueos o riesgos detectados
- Inconsistencia de naming en docs de Épica 4 (tablas singular vs plural). Si no se alinea antes de migrar, habrá drift entre BD/código/docs.
- “Append-only” (auditoría/tiempo): riesgo de performance (historial grande) y de fuga de información si no se filtra por rol (cliente interno vs roles internos).
- Relaciones/duplicados: riesgo de inconsistencias si no se define manejo de simetría (A↔B) y reglas de permisos (duplicado vs cancelar).

## Decisiones tomadas
- Usar comandos recomendados en Windows: `npm.cmd run dev` y `npm.cmd -s run build`.
- Para acceso al dev server: preferir `http://localhost:5173` cuando Vite ligue a IPv6.
- Antes de iniciar 4.1 (DB), alinear el naming definitivo de tablas de Épica 4 en docs y migraciones.

## Acciones para el siguiente epic
- [x] Definir estándar único de nombres para: auditoría, relaciones y tiempo (docs + migraciones + modelos).
- [x] Definir contrato de endpoints de Épica 4 (historial, relaciones, tiempo) y su matriz de permisos por rol.
- [x] Decidir estrategia de paginación/índices para historial y tiempo (evitar cargas completas).
- [x] Documentar checklist de entorno “pre-épica” (docker up + migrate:status + test + build) en `docs/resumen.md`.

Ver también: `docs/retrospectiva/epic-4-preflight-fixes-2026-01-28.md`.

## Estado en sprint-status.yaml
- epic-4-retrospective: optional (pre-flight documentado; retrospectiva final al cierre de Épica 4)
