# 037 — Fade del contenedor del carrusel de cards

**Stack**: js
**Estado**: aceptado
**Fecha**: 2026-10-08

## Contexto

La decisión 024 animaba la entrada de cada card del carrusel de «Especialització». Al salir de la sección y volver tras haber movido el carrusel con la rueda, las cards aparecían superpuestas y fantasmas hasta mover la rueda de nuevo.

## Decisión

- **Causa**: `render()` de `stack-carousel.js` calcula `y`, `scale`, `zIndex` y `opacity` de cada `<li>` según la card activa. El `from(cards, { opacity: 0 })` guardaba una sola vez las opacidades finales del estado inicial y, con `play reset play reset`, cada reproducción las forzaba aunque la card activa ya fuera otra.
- **Se anima el contenedor `<ol data-stack-list>`**, no cada card: `from(stack, { opacity: 0, duration: 1.4, ease: "power1.inOut", clearProps: "opacity" }, "-=0.4")`. `render` solo escribe en los `<li>` y la entrada solo en el `<ol>`, así que no comparten propiedades.
- Se quita el deslizamiento lateral alternado de las cards: la entrada es un fade puro.

## Consecuencias

- Se pierde el escalonado entre cards: entra la pila entera a la vez.
- Se resuelve el riesgo anotado en la decisión 024; la entrada de las cards de esa decisión queda sustituida por esta.
- No probado en un navegador: queda por comprobar mover el carrusel, salir de la sección y volver.
