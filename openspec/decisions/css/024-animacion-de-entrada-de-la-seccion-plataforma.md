# 024 — Animación de entrada de la sección «Especialització»

**Stack**: js + css
**Estado**: aceptado
**Fecha**: 2026-10-08

## Contexto

Continúa las decisiones 021-023. La descripción y las tarjetas del carrusel apilado (decisiones 019 y 020) entran con una timeline en `script.js`. Las tarjetas ya las posiciona `render` en `stack-carousel.js`.

## Decisión

- **Bloque `COMUNES` en `script.js`** con `charsDepthReveal` (valores del efecto de profundidad en X) y `splitIntoChars` (`SplitText` en `words,chars` con `inline-block` y `perspective`). El hero y esta sección los comparten, sin duplicar el efecto.
- **Descripción** (`data-platform-description`): mismo efecto que el subtítulo del hero.
- **Tarjetas**: fade y `stagger: 0.2`, con direcciones horizontales opuestas por índice (`x: (index) => index % 2 === 0 ? -80 : 80`), solapadas con `"-=0.4"` sobre la descripción.
- **Sin `clearProps` en las tarjetas.** `render` controla `yPercent`, `y`, `scale`, `opacity` y `zIndex`: `"transform"` borraría su `y` y `scale`, y `"opacity"` su opacidad. `x` queda libre porque `render` no lo toca.
- **`ScrollTrigger` con `endTrigger: section` y `end: "bottom top"`**, para que el `reset` no ocurra mientras el carrusel siga en pantalla.
- **Flechas de móvil**: `mt-8` a `mt-2`, para acercarlas a la pila.

## Consecuencias

- **Frágil**: el `from` de `opacity` guarda las opacidades que `render` tenía al crearlo. Si se gira el carrusel, se sale de la sección y se vuelve a entrar, el replay las deja como estaban antes del giro y la siguiente rueda da un pequeño salto. Se arregla haciendo que `stack-carousel.js` exponga su estado.
- No probado en un navegador, solo comprobado el código.
