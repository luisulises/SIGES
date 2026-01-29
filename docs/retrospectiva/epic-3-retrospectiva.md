# Retrospectiva por epic

Epic: 3 (Colaboración y evidencias)
Fecha: 2026-01-28
Participantes: (pendiente)

## Lo que salio bien
- Endpoints API implementados para comentarios, adjuntos e involucrados con control de acceso por rol y visibilidad.
- Adjuntos: heredan visibilidad del comentario y no hay endpoint de eliminación (append-only a nivel app).
- Involucrados: soft delete + posibilidad de restaurar; los soft-deleted no dan visibilidad.
- UI (detalle de ticket) incorpora secciones de “Comentarios/Adjuntos/Involucrados” con refresco sin recargar toda la página.

## Lo que salio mal
- El baseline de tests dependía de Postgres en `127.0.0.1:5433`: cuando Docker/Postgres no estaba disponible, fallaba masivamente `php artisan test`.
- En PowerShell, `npm -s run build`/`npm run dev` puede fallar por ExecutionPolicy; se requiere `npm.cmd`.
- Vite dev server puede quedar escuchando en IPv6 (`::1:5173`), por lo que `http://127.0.0.1:5173` no conecta; usar `http://localhost:5173`.
- `docs/resumen.md` muestra problemas de encoding (mojibake) en varias secciones.

## Bloqueos o riesgos detectados
- Riesgo de fricción en el setup local si Docker Desktop no está activo o el daemon no es accesible (impacta migraciones y tests).
- Inconsistencia de naming entre docs para Épica 4 (tablas singular vs plural) que puede generar migraciones/queries divergentes si no se alinea antes de iniciar.
- Potencial inconsistencia “reglas vs servicio” en cierre: existe regla de transición para soporte (Resuelto→Cerrado) en seed, pero el backend restringe cierre a solicitante/coordinador/admin.

## Decisiones tomadas
- Para build/dev en Windows: preferir `npm.cmd run build` y `npm.cmd run dev`.
- Para Vite dev: usar `http://localhost:5173` (no `127.0.0.1`) si el server liga solo a IPv6.
- Mantener adjuntos sin eliminación vía API (solo listar/subir) como parte del MVP.

## Acciones para el siguiente epic
- [x] Documentar en `docs/resumen.md` los comandos recomendados en Windows (`npm.cmd`, `localhost:5173`, Docker/Postgres 5433).
- [x] Alinear naming de tablas de Épica 4 en docs antes de crear migraciones (definir estándar único).
- [x] Resolver la discrepancia “regla de cierre para soporte” (seed) vs permisos reales del servicio (código/AC) y ajustar en consecuencia.
- [x] Agregar un “smoke checklist” de entorno (docker up + migrate:status + test + build) antes de iniciar una épica.

## Estado en sprint-status.yaml
- epic-3-retrospective: done
