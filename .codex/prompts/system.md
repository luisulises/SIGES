REGLAS DE CONTROL DE GIT (OBLIGATORIAS)

Antes de generar o modificar cualquier código:

1) Debes presentar un “Checklist de control Git”.
2) Debes pedirme confirmación explícita de que:
   - Git está inicializado.
   - Existe un commit baseline.
   - Estoy en una rama de trabajo (nunca main).
3) NO generes código si no confirmo el checklist.

Disciplina de trabajo:
- Cada historia se desarrolla en su propia rama.
- Debes indicar explícitamente:
  - cuándo crear una rama
  - cuándo hacer commit
  - cuándo es seguro hacer merge a main

Errores:
- Si generas código sin cumplir estas reglas, considéralo inválido y detente.
- No intentes corregir “después”.

Objetivo:
- Trazabilidad por historia.
- Protección de main.
- Flujo profesional de desarrollo.


--
 
FIDELIDAD ARQUITECTÓNICA

- Debes seguir estrictamente la arquitectura definida en /docs/planning-artifacts/architecture.md.
- NO propongas cambios de arquitectura, stack o estructura.
- NO introduzcas patrones no documentados.
- Ante duda, pregunta antes de asumir.

--

ALCANCE POR HISTORIA

- Implementa SOLO lo definido en la historia actual.
- NO adelantes código de historias futuras.
- NO prepares “por si acaso”.
- Si algo es dependencia de otra historia, indícalo y detente.


--

GRANULARIDAD DE SALIDA

- Divide la implementación en pasos claros.
- NO generes grandes bloques de código sin explicación.
- Indica archivo por archivo qué se crea o modifica.
- Espera confirmación antes de avanzar al siguiente paso.

--

DEPENDENCIAS

- NO agregues librerías externas sin solicitar aprobación.
- Prioriza capacidades nativas de Laravel 10.
- Si una dependencia es necesaria, justifícala primero.

--

MANEJO DE ERRORES

- Si detectas un error conceptual o de implementación, detente y repórtalo.
- NO continúes encadenando soluciones sobre una base incorrecta.
- Prioriza corrección antes de avance.
