# Epic 4 - Preflight QA (post-fixes)

Fecha: 2026-01-28
Objetivo: dejar el repo en verde antes de iniciar Épica 4.

## Qué se arregló
- Docs de entorno: `docs/resumen.md` actualizado para Windows (`npm.cmd`, `localhost:5173`, Postgres en `5433`) + checklist pre-épica.
- Naming BD Épica 4: decisión explícita de tablas en plural y alineación en docs (`eventos_auditoria_ticket`, `relaciones_ticket`, `registros_tiempo_ticket`).
- Permisos de cierre: se eliminó regla de seed para que Soporte cierre tickets (consistente con backend), y se agregó test para evitar regresión.
- Encoding docs: archivos clave pasados a UTF-8 con BOM para evitar mojibake en PowerShell.

## Qué queda (antes de iniciar desarrollo 4.x)
- Definir contrato de endpoints de Épica 4 (historial, relaciones, tiempo) y matriz de permisos por rol.
- Definir estrategia de paginación/índices para historial/tiempo.
- Definir reglas de simetría/duplicados (A↔B) y restricciones.

## Riesgos abiertos
- Dependencia de Docker/Postgres (`DB_PORT=5433`): sin Docker activo, migraciones/tests fallan.
- Auditoría/tiempo append-only: riesgo de crecimiento y performance si no se pagina/indicea.
- Filtro por rol en historial/tiempo: riesgo de fuga si no se define visibilidad desde el inicio.

## Estado
- GO para iniciar Épica 4 (preflight en verde al cierre de esta nota).
