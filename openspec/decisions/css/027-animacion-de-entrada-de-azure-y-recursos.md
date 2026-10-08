# 027 — Animación de entrada de «Microsoft Azure», las cards de recursos y la franja del blog

**Stack**: js + css
**Estado**: aceptado
**Fecha**: 2026-10-08

## Contexto

Continúa las decisiones 021-026. La sección `#azure` y lo que viene debajo (las 3 cards del acordeón de `#recursos` y la franja «Descobreix el nostre blog») entran con una sola timeline en `script.js`, como ya se hizo con las cards y el carrusel de logos (decisión 023).

## Decisión

- **Una timeline** (`data-azure*`, `data-resources-card` y `data-blog-strip` en el HTML), con `ScrollTrigger` en `top 70%` de `#azure`, `endTrigger: "#recursos"` y `end: "bottom top"`. Las cards y la franja están en otra `<section>`: sin el `endTrigger`, el `reset` ocurriría al salir `#azure` por arriba con ellas aún en pantalla.
- **Descripción**: efecto de profundidad en X con `charsDepthReveal`.
- **Los 4 bloques** (Escalabilitat, Disponibilitat, Seguretat, Innovació): fade desde abajo (`y: 40`, 0,8 s, `stagger: 0.2`, `"-=0.4"`).
- **Las 3 cards del acordeón**: la primera con fade desde la izquierda (`x: -60`) y las otras dos desde abajo (`y: 40`), en un solo tween con valores por función según el índice. Algo más lentas (1,1 s, `stagger: 0.3`).
- **Franja del blog**: fade desde abajo al final de la secuencia, con `clearProps: "opacity"`. El `<h2>` de la franja usa `data-title-reveal`, y `title-reveal.js` pone `perspective` en su elemento padre, que es la propia franja: no se ha comprobado si `clearProps: "transform"` también lo borraría, así que se evita.
- **`overflow-x-clip` en `#azure`**, por el desplazamiento horizontal de los caracteres.

## Consecuencias

- Las cards del acordeón se animan con `transform` y `opacity` pero su transición de escritorio es de `flex`: no hay conflicto, aunque conviene probar el hover en `lg`.
- Las cards y la franja del blog pueden hacer su fade antes de verse, porque se animan con el trigger de `#azure`.
- No probado en un navegador, solo comprobado el código.
