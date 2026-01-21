-- SIGES: inserciones base (Postgres)
-- Objetivo: dejar la BD lista para operar el flujo completo (tickets + workflow + gestion operativa)
-- Nota: este script asume que ya corriste migraciones.
--
-- Ejecucion (si usas Docker Compose del repo):
--   PowerShell:
--     Get-Content -Raw .\\docs\\inserciones-base.sql | docker exec -i siges-postgres psql -U siges -d siges
--   CMD:
--     type docs\\inserciones-base.sql | docker exec -i siges-postgres psql -U siges -d siges

BEGIN;

-- 1) Roles (requeridos para auth/visibilidad)
INSERT INTO roles (nombre, created_at, updated_at)
VALUES
  ('cliente_interno', now(), now()),
  ('soporte', now(), now()),
  ('coordinador', now(), now()),
  ('admin', now(), now())
ON CONFLICT (nombre) DO UPDATE
SET updated_at = now();

-- 2) Estados de ticket (workflow)
INSERT INTO estados_ticket (nombre, es_terminal, created_at, updated_at)
VALUES
  ('Nuevo', false, now(), now()),
  ('En analisis', false, now(), now()),
  ('Asignado', false, now(), now()),
  ('En progreso', false, now(), now()),
  ('Resuelto', false, now(), now()),
  ('Cerrado', true, now(), now()),
  ('Cancelado', true, now(), now())
ON CONFLICT (nombre) DO UPDATE
SET es_terminal = EXCLUDED.es_terminal,
    updated_at = now();

-- 3) Sistemas (necesarios para crear tickets desde UI)
INSERT INTO sistemas (nombre, activo, created_at, updated_at)
VALUES
  ('SIGES', true, now(), now()),
  ('ServicioApoyoLegacy', true, now(), now()),
  ('Reclutamiento', true, now(), now()),
  ('Aura', true, now(), now()),
  ('Mira', true, now(), now()),
  ('LISET', true, now(), now()),
  ('Facturacion', true, now(), now())
ON CONFLICT (nombre) DO UPDATE
SET activo = EXCLUDED.activo,
    updated_at = now();

-- 4) Catalogos operativos (UI gestion operativa)
INSERT INTO prioridades (nombre, orden, activo, created_at, updated_at)
VALUES
  ('Baja', 1, true, now(), now()),
  ('Media', 2, true, now(), now()),
  ('Alta', 3, true, now(), now())
ON CONFLICT (nombre) DO UPDATE
SET orden = EXCLUDED.orden,
    activo = EXCLUDED.activo,
    updated_at = now();

INSERT INTO tipos_solicitud (nombre, activo, created_at, updated_at)
VALUES
  ('Incidente', true, now(), now()),
  ('Requerimiento', true, now(), now())
ON CONFLICT (nombre) DO UPDATE
SET activo = EXCLUDED.activo,
    updated_at = now();

-- 5) Usuarios demo (para probar el checklist por rol)
-- Password (bcrypt) = "password"
-- Hash fijo usado comunmente en ejemplos de Laravel para "password".
WITH role_ids AS (
  SELECT id, nombre
  FROM roles
  WHERE nombre IN ('cliente_interno', 'soporte', 'coordinador', 'admin')
)
INSERT INTO usuarios (
  nombre,
  email,
  password,
  rol_id,
  activo,
  email_verified_at,
  remember_token,
  created_at,
  updated_at
)
VALUES
  (
    'Cliente Demo',
    'cliente@siges.test',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    (SELECT id FROM role_ids WHERE nombre = 'cliente_interno'),
    true,
    now(),
    'seed-cliente',
    now(),
    now()
  ),
  (
    'Soporte Demo',
    'soporte@siges.test',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    (SELECT id FROM role_ids WHERE nombre = 'soporte'),
    true,
    now(),
    'seed-soporte',
    now(),
    now()
  ),
  (
    'Coordinador Demo',
    'coordinador@siges.test',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    (SELECT id FROM role_ids WHERE nombre = 'coordinador'),
    true,
    now(),
    'seed-coordinador',
    now(),
    now()
  ),
  (
    'Admin Demo',
    'admin@siges.test',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    (SELECT id FROM role_ids WHERE nombre = 'admin'),
    true,
    now(),
    'seed-admin',
    now(),
    now()
  )
ON CONFLICT (email) DO UPDATE
SET nombre = EXCLUDED.nombre,
    password = EXCLUDED.password,
    rol_id = EXCLUDED.rol_id,
    activo = EXCLUDED.activo,
    email_verified_at = EXCLUDED.email_verified_at,
    updated_at = now();

-- 6) Asignar el coordinador a los sistemas (visibilidad/operacion)
INSERT INTO sistemas_coordinadores (sistema_id, usuario_id, created_at, updated_at)
SELECT
  s.id,
  u.id,
  now(),
  now()
FROM sistemas s
JOIN usuarios u ON u.email = 'coordinador@siges.test'
WHERE s.activo = true
ON CONFLICT (sistema_id, usuario_id) DO NOTHING;

-- 7) Reglas de transicion (workflow)
WITH transiciones(origen, destino, rol, requiere_responsable) AS (
  VALUES
    -- Operativas (roles: soporte/coordinador/admin)
    ('Nuevo', 'En analisis', 'soporte', false),
    ('Nuevo', 'En analisis', 'coordinador', false),
    ('Nuevo', 'En analisis', 'admin', false),
    ('En analisis', 'Asignado', 'soporte', false),
    ('En analisis', 'Asignado', 'coordinador', false),
    ('En analisis', 'Asignado', 'admin', false),
    ('Asignado', 'En progreso', 'soporte', true),
    ('Asignado', 'En progreso', 'coordinador', true),
    ('Asignado', 'En progreso', 'admin', true),
    ('En progreso', 'Resuelto', 'soporte', false),
    ('En progreso', 'Resuelto', 'coordinador', false),
    ('En progreso', 'Resuelto', 'admin', false),

    -- Cierre (Resuelto -> Cerrado) (roles: cliente/soporte/coordinador/admin)
    ('Resuelto', 'Cerrado', 'cliente_interno', false),
    ('Resuelto', 'Cerrado', 'soporte', false),
    ('Resuelto', 'Cerrado', 'coordinador', false),
    ('Resuelto', 'Cerrado', 'admin', false),

    -- Cancelacion (roles: cliente/coordinador/admin)
    ('Nuevo', 'Cancelado', 'cliente_interno', false),
    ('Nuevo', 'Cancelado', 'coordinador', false),
    ('Nuevo', 'Cancelado', 'admin', false),
    ('En analisis', 'Cancelado', 'cliente_interno', false),
    ('En analisis', 'Cancelado', 'coordinador', false),
    ('En analisis', 'Cancelado', 'admin', false),
    ('Asignado', 'Cancelado', 'cliente_interno', false),
    ('Asignado', 'Cancelado', 'coordinador', false),
    ('Asignado', 'Cancelado', 'admin', false),
    ('En progreso', 'Cancelado', 'cliente_interno', false),
    ('En progreso', 'Cancelado', 'coordinador', false),
    ('En progreso', 'Cancelado', 'admin', false),
    ('Resuelto', 'Cancelado', 'cliente_interno', false),
    ('Resuelto', 'Cancelado', 'coordinador', false),
    ('Resuelto', 'Cancelado', 'admin', false)
)
INSERT INTO reglas_transicion_estado (
  estado_origen_id,
  estado_destino_id,
  rol_id,
  requiere_responsable,
  created_at,
  updated_at
)
SELECT
  eo.id,
  ed.id,
  r.id,
  t.requiere_responsable,
  now(),
  now()
FROM transiciones t
JOIN estados_ticket eo ON eo.nombre = t.origen
JOIN estados_ticket ed ON ed.nombre = t.destino
JOIN roles r ON r.nombre = t.rol
ON CONFLICT (estado_origen_id, estado_destino_id, rol_id) DO UPDATE
SET requiere_responsable = EXCLUDED.requiere_responsable,
    updated_at = now();

-- 8) Tickets demo (opcionales, para probar UI/roles)
-- Se insertan solo si no existen ya para el cliente demo.
WITH refs AS (
  SELECT
    (SELECT id FROM usuarios WHERE email = 'cliente@siges.test') AS cliente_id,
    (SELECT id FROM usuarios WHERE email = 'soporte@siges.test') AS soporte_id,
    (SELECT id FROM usuarios WHERE email = 'coordinador@siges.test') AS coordinador_id,
    (SELECT id FROM sistemas WHERE nombre = 'SIGES') AS sistema_siges_id,
    (SELECT id FROM sistemas WHERE nombre = 'Infraestructura') AS sistema_infra_id,
    (SELECT id FROM estados_ticket WHERE nombre = 'Nuevo') AS estado_nuevo_id,
    (SELECT id FROM estados_ticket WHERE nombre = 'Resuelto') AS estado_resuelto_id
)
INSERT INTO tickets (
  asunto,
  descripcion,
  solicitante_id,
  sistema_id,
  estado_id,
  responsable_actual_id,
  interno,
  resolucion,
  created_at,
  updated_at
)
SELECT
  'Demo: Ticket nuevo (sin asignar)',
  'Ticket demo para probar asignacion y workflow.',
  refs.cliente_id,
  refs.sistema_siges_id,
  refs.estado_nuevo_id,
  NULL,
  false,
  NULL,
  now(),
  now()
FROM refs
WHERE refs.cliente_id IS NOT NULL
  AND refs.sistema_siges_id IS NOT NULL
  AND refs.estado_nuevo_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1
    FROM tickets t
    WHERE t.asunto = 'Demo: Ticket nuevo (sin asignar)'
      AND t.solicitante_id = refs.cliente_id
  );

WITH refs AS (
  SELECT
    (SELECT id FROM usuarios WHERE email = 'cliente@siges.test') AS cliente_id,
    (SELECT id FROM usuarios WHERE email = 'soporte@siges.test') AS soporte_id,
    (SELECT id FROM usuarios WHERE email = 'coordinador@siges.test') AS coordinador_id,
    (SELECT id FROM sistemas WHERE nombre = 'Infraestructura') AS sistema_infra_id,
    (SELECT id FROM estados_ticket WHERE nombre = 'Nuevo') AS estado_nuevo_id
)
INSERT INTO tickets (
  asunto,
  descripcion,
  solicitante_id,
  sistema_id,
  estado_id,
  responsable_actual_id,
  interno,
  created_at,
  updated_at
)
SELECT
  'Demo: Ticket nuevo (asignado a soporte)',
  'Ticket demo para probar campos operativos de soporte.',
  refs.cliente_id,
  refs.sistema_infra_id,
  refs.estado_nuevo_id,
  refs.soporte_id,
  false,
  now(),
  now()
FROM refs
WHERE refs.cliente_id IS NOT NULL
  AND refs.sistema_infra_id IS NOT NULL
  AND refs.estado_nuevo_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1
    FROM tickets t
    WHERE t.asunto = 'Demo: Ticket nuevo (asignado a soporte)'
      AND t.solicitante_id = refs.cliente_id
  );

WITH refs AS (
  SELECT
    (SELECT id FROM usuarios WHERE email = 'cliente@siges.test') AS cliente_id,
    (SELECT id FROM usuarios WHERE email = 'soporte@siges.test') AS soporte_id,
    (SELECT id FROM usuarios WHERE email = 'coordinador@siges.test') AS coordinador_id,
    (SELECT id FROM sistemas WHERE nombre = 'SIGES') AS sistema_siges_id,
    (SELECT id FROM estados_ticket WHERE nombre = 'Resuelto') AS estado_resuelto_id
)
INSERT INTO tickets (
  asunto,
  descripcion,
  solicitante_id,
  sistema_id,
  estado_id,
  responsable_actual_id,
  interno,
  resolucion,
  created_at,
  updated_at
)
SELECT
  'Demo: Ticket resuelto (para cerrar)',
  'Ticket demo en Resuelto con resolucion para probar cierre por cliente.',
  refs.cliente_id,
  refs.sistema_siges_id,
  refs.estado_resuelto_id,
  refs.soporte_id,
  false,
  'Resolucion de demo (seed).',
  now(),
  now()
FROM refs
WHERE refs.cliente_id IS NOT NULL
  AND refs.sistema_siges_id IS NOT NULL
  AND refs.estado_resuelto_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1
    FROM tickets t
    WHERE t.asunto = 'Demo: Ticket resuelto (para cerrar)'
      AND t.solicitante_id = refs.cliente_id
  );

-- Asignacion historica para el ticket asignado (si no existe)
WITH refs AS (
  SELECT
    (SELECT id FROM usuarios WHERE email = 'soporte@siges.test') AS soporte_id,
    (SELECT id FROM usuarios WHERE email = 'coordinador@siges.test') AS coordinador_id,
    (SELECT id FROM usuarios WHERE email = 'cliente@siges.test') AS cliente_id
),
ticket_asignado AS (
  SELECT t.id AS ticket_id
  FROM tickets t
  JOIN refs ON refs.cliente_id = t.solicitante_id
  WHERE t.asunto = 'Demo: Ticket nuevo (asignado a soporte)'
  LIMIT 1
)
INSERT INTO asignaciones_ticket (
  ticket_id,
  responsable_id,
  asignado_por_id,
  asignado_at,
  desasignado_at,
  created_at,
  updated_at
)
SELECT
  ticket_asignado.ticket_id,
  refs.soporte_id,
  refs.coordinador_id,
  now(),
  NULL,
  now(),
  now()
FROM refs
JOIN ticket_asignado ON true
WHERE refs.soporte_id IS NOT NULL
  AND refs.coordinador_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1
    FROM asignaciones_ticket a
    WHERE a.ticket_id = ticket_asignado.ticket_id
      AND a.desasignado_at IS NULL
  );

COMMIT;
