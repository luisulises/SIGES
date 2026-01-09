# Flujo Git (solo)

## Objetivo
Definir un flujo simple y consistente para trabajar por story, hacer checkpoints con commits y fusionar a main.

## Convenciones

### Ramas
- Formato: `story/<id>-<slug>`
- Ejemplos:
  - `story/1.1-bd-base`
  - `story/1.2-backend-auth`
  - `story/1.3-frontend-ui`

### Commits
- Formato: `<id>: <mensaje corto>`
- Ejemplos:
  - `DB-1.1: crear esquema base`
  - `BE-1.2: endpoints CRUD tickets`
  - `FE-1.3: UI login y listado`

## Ciclo de trabajo por story

1) Actualizar main
```bash
git checkout main
git pull
```

2) Crear rama de la story
```bash
git checkout -b story/1.1-bd-base
```

3) Trabajar y hacer commits (checkpoints)
- Haz un commit cuando completes un bloque logico.
- Haz un commit antes de cambios riesgosos.
- Haz un commit al final del dia si el estado funciona.

4) Terminar la story
- Verifica que cumple los criterios de aceptacion de la story.
- Actualiza el estado en `docs/implementation-artifacts/sprint-status.yaml`.

5) Fusionar a main
```bash
git checkout main
git merge story/1.1-bd-base
```

6) Limpiar la rama
```bash
git branch -d story/1.1-bd-base
```

## Checkpoints (regla simple)
- Un commit por bloque logico terminado.
- Evita commits enormes sin contexto.
- Mensajes claros y accionables.

## Estado de trabajo
- Al iniciar una story: marcarla `in-progress`.
- Al terminar: marcarla `done`.
- Al iniciar el primer item del epic: poner el epic `in-progress`.

Archivo de estado:
- `docs/implementation-artifacts/sprint-status.yaml`

## Retroceder si algo falla

Ver historial:
```bash
git log --oneline
```

Deshacer un commit con un commit inverso (seguro):
```bash
git revert <hash>
```

Volver atras local (solo si no has compartido cambios):
```bash
git reset --hard <hash>
```

## Plantilla rapida por story
- [ ] Crear rama `story/<id>-<slug>`
- [ ] Implementar tareas
- [ ] Commits por checkpoints
- [ ] Validar criterios de aceptacion
- [ ] Actualizar `sprint-status.yaml`
- [ ] Merge a `main`
- [ ] Borrar rama
