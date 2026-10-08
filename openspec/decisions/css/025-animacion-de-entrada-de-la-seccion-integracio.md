# 025 — Animación de entrada de la sección «Integració»

**Stack**: js + css
**Estado**: aceptado
**Fecha**: 2026-10-08

## Contexto

Continúa las decisiones 021-024. La sección «Integració sense barreres» (imagen SVG a la izquierda, texto a la derecha, card morada final) entra con una timeline en `script.js`. El título ya lo anima `title-reveal.js`.

## Decisión

- **Una timeline** (`data-integration*` en el HTML), con `ScrollTrigger` en `top 70%` y `end: "bottom top"` de la sección: todo lo que se anima está dentro de ella.
- **Subtítulo**: fade desde el lado (`x: 60`), entra desde la derecha, donde está su columna.
- **Descripción** (dos párrafos): efecto de profundidad en X (`charsDepthReveal`), con un único `stagger` repartido entre los caracteres de ambos.
- **Imagen**: revelación de cortina horizontal de izquierda a derecha con `clip-path`, de `inset(0% 100% 0% 0%)` a `inset(0% 0% 0% 0%)` (1,5 s, `power2.inOut`), solapada con la descripción (`"-=1.6"`). Se usa `fromTo` porque un `clip-path` sin definir vale `none` y GSAP no puede interpolar desde ahí.
- **Card morada final**: fade up (`y: 30`), al final de la secuencia.
- **`overflow-x-clip` en la sección**: el efecto de caracteres desplaza `x: 100` y en móvil crearía scroll horizontal.

## Consecuencias

- El orden de la secuencia es subtítulo, descripción, imagen y card; cambia moviendo la posición de cada paso.
- No probado en un navegador, solo comprobado el código.
