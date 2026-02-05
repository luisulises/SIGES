# Resumen del proyecto SIGES (handoff)

Este documento resume el estado actual del proyecto **SIGES** con foco en “handoff” (qué hay, cómo corre y qué falta).  
**Nota:** se omite el resumen del SQL (`docs/inserciones-base.sql`).

## 1) ¿Qué es SIGES?

SIGES es un sistema interno (greenfield) para centralizar y dar trazabilidad a solicitudes del área de sistemas (soporte, cambios y proyectos). Busca reemplazar / convivir con Notion/Excel/legacy como “registro confiable”.

Roles:
- **cliente interno** (solicitante)
- **soporte**
- **coordinador**
- **admin**

Stack:
- Laravel 10 + Breeze + Inertia (Vue 3) + Tailwind
- PostgreSQL 15 (Docker)
- Auth: Breeze (web) + Sanctum (API)

## 2) Estado actual (épicas / stories)

Fuente de tracking:
- `docs/implementation-artifacts/sprint-status.yaml`
- `docs/implementation-artifacts/*`
- `docs/flujo-proyecto.md`

Estado general: **todas las épicas 1–6 están completadas (“done”)**.

### Epic 1 (Done): Base funcional de tickets
- BD: roles, usuarios, sistemas, estados_ticket, tickets, involucrados_ticket.
- Backend: auth (Sanctum), CRUD mínimo de tickets + visibilidad por rol (Policies/Services) + tests.
- Frontend: listado/detalle/crear ticket (Inertia), polling <= 60s.

### Epic 2 (Done): Workflow y gestión operativa
- BD: workflow (prioridades, tipos_solicitud, reglas_transicion_estado), asignaciones_ticket, campos operativos en tickets.
- Backend: transiciones, cierre/cancelación, asignación y actualización operativa por rol + auditoría/notificaciones integrables + tests.
- Frontend: “Gestión operativa” en detalle del ticket (controles por rol) sin recargar la página.

### Epic 3 (Done): Seguimiento del ticket (comentarios/adjuntos/involucrados)
- Comentarios públicos e internos (visibilidad por rol; cliente interno solo “público”).
- Adjuntos: se suben **solo dentro de un comentario**; heredan visibilidad; **hay descarga** vía endpoint dedicado (ver `download_url`).
- Involucrados: gestión por coordinador/admin (soft delete y restauración).
- UI: sección “Seguimiento del ticket” (comentarios + adjuntos + involucrados) con refresco sin recarga completa.

### Epic 4 (Done): Trazabilidad (auditoría/historial, relaciones/duplicados, tiempo)
- Auditoría append-only de cambios relevantes (estado, asignación, prioridad/fechas/tipo/sistema, resolución, cierre/cancelación).
- Historial filtrado por rol (cliente interno solo eventos de estado/cierre/cancelación).
- Relaciones de tickets (relacionado/reabre) + duplicados (cancela ticket duplicado y referencia al válido).
- Reapertura indirecta: crear ticket referenciando uno Cerrado/Cancelado (sin reabrir el anterior).
- Registro de tiempo append-only (visible solo para roles internos autorizados).
- UI: secciones de historial, relaciones y tiempo en el detalle del ticket (con paginación/cargar más si aplica).

### Epic 5 (Done): Notificaciones in-app
- BD `notificaciones` + servicio de generación en eventos clave (creación, asignación, cambio de estado, comentario público, cierre/cancelación).
- Exclusión de usuarios inactivos.
- API para listar y marcar como leídas.
- UI: campanita con polling (60s) y “marcar leída”.

### Epic 6 (Done): Administración, búsqueda y métricas
- Admin: gestión de usuarios/roles (desactivación), catálogos (sistemas/prioridades/tipos_solicitud).
- Tickets internos: flag `tickets.interno` (oculta para cliente interno incluso si está involucrado).
- Coordinador/Admin: búsqueda por asunto/estado/sistema + métricas básicas (conteos por estado/prioridad).
- UI: pantallas Inertia para administración, búsqueda y métricas.

## 3) Modelo de datos (tablas clave)

Identidad y gobierno:
- `roles`, `usuarios` (incluye `activo`, `desactivado_at`)
- `sistemas`, `sistemas_coordinadores`

Tickets y operación:
- `tickets` (incluye `interno`, `solicitante_id`, `responsable_actual_id`, prioridad/tipo/fechas, `cerrado_at`, `cancelado_at`)
- `estados_ticket`, `reglas_transicion_estado`, `asignaciones_ticket`
- Catálogos: `prioridades`, `tipos_solicitud`

Colaboración y evidencias:
- `comentarios_ticket` (visibilidad `publico|interno`)
- `adjuntos` (hereda visibilidad del comentario)
- `involucrados_ticket` (soft delete)

Trazabilidad:
- `eventos_auditoria_ticket` (append-only)
- `relaciones_ticket`
- `registros_tiempo_ticket` (append-only)

Notificaciones:
- `notificaciones` (canal `in_app|email`, `leido_at`)

## 4) Endpoints importantes (API)

Formato (actual):
- Success (Resources): `{ data }` (en paginados: `{ data, links, meta }`)
- Error de validación: `422` con `{ message, errors }`
- Error de auth/permisos: `401/403` con `{ message }`

Auth:
- `POST /api/login`
- `POST /api/logout`

Tickets:
- `GET /api/tickets` (paginado; query param opcional `per_page` (1..200))
- `POST /api/tickets` (opcional: `referencia_ticket_id` para referenciar un ticket Cerrado/Cancelado)
- `GET /api/tickets/{ticket}`
- `PATCH /api/tickets/{ticket}/operativo`

Workflow:
- `POST /api/tickets/{ticket}/estado`
- `POST /api/tickets/{ticket}/cerrar`
- `POST /api/tickets/{ticket}/cancelar`

Colaboración:
- `GET|POST /api/tickets/{ticket}/comentarios`
- `GET /api/tickets/{ticket}/adjuntos`
- `POST /api/tickets/{ticket}/adjuntos`
- `GET /api/tickets/{ticket}/adjuntos/{adjunto}/download`
- `GET|POST /api/tickets/{ticket}/involucrados`
- `DELETE /api/tickets/{ticket}/involucrados/{usuario}`

Trazabilidad:
- `GET /api/tickets/{ticket}/historial`
- `GET|POST /api/tickets/{ticket}/relaciones`
- `GET|POST /api/tickets/{ticket}/tiempo`

Notificaciones:
- `GET /api/notificaciones`
- `POST /api/notificaciones/{notificacion}/leer`

Admin (solo admin):
- `GET /api/admin/roles`
- `GET|POST /api/admin/usuarios`
- `PATCH /api/admin/usuarios/{usuario}`
- `GET|POST /api/admin/catalogos/sistemas`
- `PATCH /api/admin/catalogos/sistemas/{sistema}`
- `GET|POST /api/admin/catalogos/prioridades`
- `PATCH /api/admin/catalogos/prioridades/{prioridad}`
- `GET|POST /api/admin/catalogos/tipos-solicitud`
- `PATCH /api/admin/catalogos/tipos-solicitud/{tipoSolicitud}`

Búsqueda/Métricas (solo admin/coordinador):
- `GET /api/tickets/busqueda`
- `GET /api/tickets/metricas`

## 5) UI (rutas web)

- Tickets:
  - `GET /tickets` (listado)
  - `GET /tickets/{ticket}` (detalle: gestión operativa + seguimiento + historial/relaciones/tiempo + toggle “interno” si admin)
- Admin (solo admin):
  - `GET /admin/usuarios`
  - `GET /admin/catalogos`
- Coordinador/Admin:
  - `GET /busqueda`
  - `GET /metricas`

## 6) Cómo iniciar el proyecto (Windows)

Requisitos:
- Docker Desktop (para Postgres)
- PHP/Composer
- Node.js/npm

Desde `c:\dev\SIGES`:

```powershell
# BD (Postgres expuesto en 127.0.0.1:5433 segun docker-compose.yml/.env)
docker compose up -d

# Dependencias
composer install
npm install

# Migraciones + seeders
php artisan migrate --seed

# Backend
php artisan serve --host=127.0.0.1 --port=8000

# Frontend (otra terminal)
npm.cmd run dev
```

Abrir:
- App: `http://127.0.0.1:8000`
- Vite (si aplica): usar `http://localhost:5173` (puede ligar a IPv6 y fallar en `127.0.0.1:5173`).

Notas frecuentes:
- Si falla conexión a DB: verificar Docker Desktop y el container `siges-postgres`.
- Si falla Vite en PowerShell por ExecutionPolicy: usar `npm.cmd` (no `npm`).
- Si aparece error de permisos en `storage/` o `bootstrap/cache/`: asegurar carpetas existentes y permisos de escritura.

## 7) Usuarios demo por rol

### Opción A (rápida)
1) Registrarse desde la UI (crea **cliente_interno** por defecto).
2) Crear el resto de roles con admin o tinker.

### Opción B (recomendado): tinker (crea/actualiza demo + catálogos mínimos)

```powershell
php artisan tinker
```

Pegar:

```php
use App\Models\Role;
use App\Models\Sistema;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

$roles = [Role::CLIENTE_INTERNO, Role::SOPORTE, Role::COORDINADOR, Role::ADMIN];
foreach ($roles as $rol) {
    Role::query()->firstOrCreate(['nombre' => $rol]);
}
$roleIds = Role::query()->pluck('id', 'nombre');

$users = [
    ['nombre' => 'Cliente Demo', 'email' => 'cliente@siges.test', 'rol' => Role::CLIENTE_INTERNO],
    ['nombre' => 'Soporte Demo', 'email' => 'soporte@siges.test', 'rol' => Role::SOPORTE],
    ['nombre' => 'Coordinador Demo', 'email' => 'coordinador@siges.test', 'rol' => Role::COORDINADOR],
    ['nombre' => 'Admin Demo', 'email' => 'admin@siges.test', 'rol' => Role::ADMIN],
];

foreach ($users as $data) {
    User::query()->updateOrCreate(['email' => $data['email']], [
        'nombre' => $data['nombre'],
        'password' => Hash::make('password'),
        'rol_id' => $roleIds[$data['rol']],
        'activo' => true,
        'email_verified_at' => now(),
        'remember_token' => Str::random(10),
    ]);
}

if (Sistema::query()->count() === 0) {
    Sistema::query()->create(['nombre' => 'SIGES', 'activo' => true]);
}

$coordinador = User::query()->where('email', 'coordinador@siges.test')->first();
if ($coordinador) {
    $sistemaIds = Sistema::query()->where('activo', true)->pluck('id')->all();
    $coordinador->sistemasCoordinados()->syncWithoutDetaching($sistemaIds);
}

if (DB::table('prioridades')->count() === 0) {
    $now = now();
    DB::table('prioridades')->insert([
        ['nombre' => 'Baja', 'orden' => 1, 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
        ['nombre' => 'Media', 'orden' => 2, 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
        ['nombre' => 'Alta', 'orden' => 3, 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
    ]);
}

if (DB::table('tipos_solicitud')->count() === 0) {
    $now = now();
    DB::table('tipos_solicitud')->insert([
        ['nombre' => 'Incidente', 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
        ['nombre' => 'Requerimiento', 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
    ]);
}
```

Credenciales (password: `password`):
- `cliente@siges.test`
- `soporte@siges.test`
- `coordinador@siges.test`
- `admin@siges.test`

## 8) Smoke checklist (rápido)

Comandos:
```powershell
docker compose up -d
php artisan migrate --seed
php artisan test --compact
npm.cmd -s run build
```

Manual (por rol, en `GET /tickets/{ticket}`):
- Cliente interno: crear ticket, comentar público, cerrar/cancelar cuando aplique, ver notificaciones.
- Soporte: comentar público/interno, adjuntar en comentario, registrar tiempo, mover estados según reglas, registrar resolución.
- Coordinador: asignar/reasignar responsable, cambiar prioridad/sistema/fecha compromiso, gestionar involucrados, ver métricas/búsqueda.
- Admin: administrar usuarios/roles (incluye desactivar), administrar catálogos, marcar ticket interno y validar visibilidad.

## 9) Limitaciones / post‑MVP (pendiente)

- Adjuntos: hay **descarga** pero no hay vista previa; definir hardening adicional (p. ej. antivirus/retención) si aplica.
- Notificaciones: canal `email` está en esquema pero MVP usa `in_app` (campanita).
- Gobierno/operación: definir retención y/o paginación visible en UI si crece el volumen (historial/tiempo/notificaciones).
- Roadmap típico: SLA/alertas, dashboards, integraciones, automatizaciones.
