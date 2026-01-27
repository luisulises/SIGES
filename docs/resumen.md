# Resumen del proyecto SIGES (handoff)

Este documento resume el estado actual del proyecto **SIGES** para que otra sesión de Codex pueda continuar con contexto completo.

## 1) ¿Qué es SIGES?

SIGES es un sistema interno (greenfield) para centralizar y dar trazabilidad a solicitudes del área de sistemas (soporte, cambios y proyectos). Reemplaza / convive con Notion/Excel/legacy, buscando ser el registro confiable. Roles: **cliente interno**, **soporte**, **coordinador**, **admin**.

Stack actual:
- Laravel 10 + Breeze + Inertia (Vue)
- Postgres 15 (en Docker)
- Auth: Breeze (web) + Sanctum (API)

## 2) Estado actual (épicas / stories)

Fuente de tracking:
- `docs/implementation-artifacts/sprint-status.yaml`
- `docs/implementation-artifacts/*`
- `docs/flujo-proyecto.md`

### Epic 1 (Done): Base funcional de tickets
- **1.1 BD (Done)**: roles, usuarios, sistemas, estados_ticket, sistemas_coordinadores, tickets (+ involucrados_ticket). Seed: estados base.
- **1.2 Backend (Done)**:
  - Auth API (Sanctum token)
  - Tickets CRUD básico (API)
  - Visibilidad por rol (`TicketVisibilityService`, `TicketPolicy`)
  - `TicketResource` + tests `TicketApiTest`
- **1.3 Frontend (Done)**:
  - Inertia pages para Tickets `Index` y `Show`
  - Form “Nuevo ticket”, listado, detalle readonly con polling (60s)
  - Ajustes UI: inputs legibles, flecha grande de regreso en detalle
  - Se validó manualmente: crear usuario, crear 3 tickets, ver listado/detalle

### Epic 2 (Done): Workflow y gestión operativa
- **2.1 BD (Done)**:
  - Tablas: `prioridades`, `tipos_solicitud`, `reglas_transicion_estado`, `asignaciones_ticket`
  - Campos operativos en `tickets`: prioridad/tipo/fechas/resolucion/cerrado_at/cancelado_at
  - Seed: estados workflow (Nuevo, En analisis, Asignado, En progreso, Resuelto, Cerrado, Cancelado)
- **2.2 Backend (Done)**:
  - `TicketWorkflowService` (transición/cerrar/cancelar con validaciones)
  - Endpoints:
    - `POST /api/tickets/{ticket}/estado`
    - `POST /api/tickets/{ticket}/cerrar`
    - `POST /api/tickets/{ticket}/cancelar`
  - Seeder reglas transición: `ReglaTransicionEstadoSeeder`
  - Tests: `TicketWorkflowTest`
- **2.3 Backend (Done)**:
  - Endpoint operativo: `PATCH /api/tickets/{ticket}/operativo`
  - `TicketOperativoService`: validación por rol y catálogo activo, trazabilidad en `asignaciones_ticket`
  - Tests: `TicketOperativoTest`
- **2.4 Frontend (Done)**:
  - En detalle del ticket se agregó sección **“Gestión operativa”**
  - Controles por rol:
    - Coordinador/Admin: responsable, prioridad, sistema, fecha compromiso
    - Soporte asignado/Admin: tipo solicitud, fecha entrega, resolución
    - Cliente interno: cerrar/cancelar (si aplica por reglas)
  - UI consume endpoints de Epic 2 y refleja cambios sin recargar toda la página
  - Backend web (`TicketController@show`) expone `catalogs`, `transiciones`, `permissions` para la UI

## 3) Modelo de datos (tablas clave)

- `roles` (cliente_interno|soporte|coordinador|admin)
- `usuarios` (custom table name para User)
- `sistemas`
- `sistemas_coordinadores` (pivot sistema<->coordinador)
- `estados_ticket`
- `tickets` (incluye campos operativos + responsable_actual_id)
- `asignaciones_ticket` (histórico de asignaciones)
- `reglas_transicion_estado` (origen/destino/rol + requiere_responsable)
- `prioridades`, `tipos_solicitud` (catálogos)
- `involucrados_ticket` (visibilidad extra por involucrados)

## 4) Endpoints importantes (API)

Auth:
- `POST /api/login`
- `POST /api/logout` (auth:sanctum)

Tickets:
- `GET /api/tickets`
- `POST /api/tickets`
- `GET /api/tickets/{ticket}`
- `PATCH /api/tickets/{ticket}` (placeholder “Sin cambios”)

Workflow:
- `POST /api/tickets/{ticket}/estado` (body: `{ estado: "En analisis" }`, etc.)
- `POST /api/tickets/{ticket}/cerrar`
- `POST /api/tickets/{ticket}/cancelar`

Operativo:
- `PATCH /api/tickets/{ticket}/operativo` (body parcial; valida rol + catálogo activo)

## 5) Cómo iniciar el proyecto (Windows)

Requisitos:
- Docker Desktop (para Postgres)
- PHP/Composer
- Node.js/npm

Desde `c:\dev\SIGES`:

```powershell
# BD
docker compose up -d

# Dependencias (si aplica)
composer install
npm install

# Migraciones + seeders base
php artisan migrate --seed

# Backend
php artisan serve --host=127.0.0.1 --port=8000

# Frontend (otra terminal)
npm run dev
```

Abrir:
- `http://127.0.0.1:8000`

Notas frecuentes:
- Si falla conexión a DB: abrir Docker Desktop y verificar que el container `siges-postgres` esté en `Running`.
- Si aparece error de `storage/framework/sessions` o `storage/logs`: crear carpetas faltantes y/o asegurar permisos de escritura en `storage/` y `bootstrap/cache/`.

## 6) Usuarios demo por rol (para probar el checklist 2.4)

### Opción A (rápida): UI para cliente + cambios manuales
1) Registrar un usuario desde la pantalla de registro (esto crea **cliente_interno** por defecto).
2) Crear usuarios de soporte/coordinador/admin directamente en BD (o usando tinker).

### Opción B (recomendado): Tinker (crea/actualiza todo)

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

## 7) Checklist manual mínimo para validar 2.4 (UI)

Como coordinador:
- Abrir ticket del sistema coordinado y verificar “Gestión operativa”.
- Asignar responsable, cambiar prioridad/sistema/fecha compromiso y guardar (refleja sin recargar).
- Cambiar estado usando transiciones permitidas.

Como soporte asignado:
- Abrir ticket asignado a ti.
- Cambiar tipo solicitud/fecha entrega/resolución y guardar.

Como cliente interno:
- Abrir tu ticket y probar cerrar/cancelar cuando aplique por reglas.

## 8) Qué sigue (siguiente épica recomendada)

Epic 3 (Colaboración y evidencias) ya está implementada:
- Comentarios públicos/internos
- Adjuntos en comentarios (solo listar + subir; sin descarga)
- Involucrados (soft delete)

La siguiente en el backlog es **Epic 4 (Trazabilidad, historial y relaciones)**:
- 4.1 BD: auditoría, relaciones y tiempo
- 4.2 Backend: auditoría e historial
- 4.3 Backend: relaciones/duplicados y referencia
- 4.4 Backend: registro de tiempo
- 4.5 Frontend: UI de historial/relaciones/tiempo
