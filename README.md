# SIGES

SIGES es un sistema interno para centralizar y dar trazabilidad a solicitudes del área de sistemas (soporte, cambios y proyectos). Busca convivir/reemplazar Notion/Excel/legacy como “registro confiable”.

- Stack: Laravel 10 + Breeze + Inertia (Vue 3) + Tailwind + PostgreSQL 15 (Docker) + Sanctum.
- Roles: `cliente_interno`, `soporte`, `coordinador`, `admin`.

## Documentación

- Estado/handoff del proyecto (qué hay y cómo corre): `docs/resumen.md`
- Flujo de trabajo (épicas/stories): `docs/flujo-proyecto.md`
- Estado de tracking: `docs/implementation-artifacts/sprint-status.yaml`

## Quickstart (Windows)

Requiere: Docker Desktop, PHP 8.2+, Node.js.

```powershell
docker compose up -d

if (-not (Test-Path .env)) { Copy-Item .env.example .env }

php composer.phar install
npm install

php artisan key:generate
php artisan migrate --seed

# Backend
php artisan serve --host=127.0.0.1 --port=8000

# Frontend (otra terminal)
npm.cmd run dev
```

Abrir:
- App: `http://127.0.0.1:8000`

Notas rápidas:
- DB por defecto: `127.0.0.1:5433` (ver `docker-compose.yml` y `.env.example`).
- En PowerShell puede fallar `npm` por ExecutionPolicy; usar `npm.cmd`.
