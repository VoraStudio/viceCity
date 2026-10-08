# 026 — Animación de entrada de la sección «Intel·ligència Artificial»

**Stack**: js + css
**Estado**: aceptado
**Fecha**: 2026-10-08

## Contexto

Continúa las decisiones 021-025. La sección `#ia` (texto a la izquierda, card con el chat a la derecha) entra con una timeline en `script.js`, en espejo de «Integració» (decisión 025) y con los mismos tiempos. El título lo sigue animando `title-reveal.js`.

## Decisión

- **Una timeline** (`data-ia*` en el HTML), con `ScrollTrigger` en `top 70%` y `end: "bottom top"` de la sección.
- **Subtítulo**: fade desde abajo (`y: 40`, 0,8 s).
- **Los 3 textos de la lista**: fade lateral desde la izquierda (`x: -60`, `stagger: 0.2`), solapados con `"-=0.4"`.
- **Descripción**: efecto de profundidad en X con `charsDepthReveal`, más rápido que en otras secciones (`duration: 0.4`, `stagger.amount: 0.5`) mediante spread, y solapada con `"-=0.8"`.
- **Imagen** (la card blanca del chat): cortina horizontal que empieza por la derecha, de `inset(0% 0% 0% 100%)` a `inset(0% 0% 0% 0%)` (1,5 s, `power2.inOut`, `"-=1.6"`). `fromTo` porque un `clip-path` sin definir vale `none` y no se puede interpolar.
- **`overflow-x-clip` en `#ia`**, por el desplazamiento horizontal de los caracteres y de la lista.

## Consecuencias

- Los tiempos de la descripción son locales a esta sección: el efecto compartido no cambia.
- Se probó una constante propia para la descripción (`aiDescriptionReveal`) y se descartó a favor del spread inline.
- No probado en un navegador, solo comprobado el código.
