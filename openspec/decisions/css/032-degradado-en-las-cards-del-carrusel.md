# 032 — Degradado en las cards del carrusel

**Stack**: css
**Estado**: aceptado
**Fecha**: 2026-10-08

## Contexto

Las 4 cards del carrusel de «Especialització» (decisiones 019 y 020) eran blancas planas. Se quiere un degradado con la paleta de la marca, sin pasar de `lav-50`.

## Decisión

- **`bg-linear-to-br from-white to-lav-50`** en las 4 cards, en lugar de `bg-white`: diagonal de blanco a lavanda muy clara.
- **Token nuevo `--color-lav-50: #ece3ff`** en el `@theme` de `index.html`. Es el punto medio entre blanco y `lav-100` (`#d9c7ff`). Se descartaron las variantes con `lav-300` y con parada intermedia en `lav-100`: el degradado era demasiado intenso para las tarjetas apiladas.

## Consecuencias

- El degradado es muy sutil. Si se ve plano, se oscurece el token `--color-lav-50` y cambia en todas las cards sin tocar el HTML.
- **`lav-50` es un valor propio, no sale de la guía de identidad visual.** Si existe un valor oficial, hay que sustituirlo.
- El token solo está en `index.html`, la única página que lo usa: cada página tiene su propio `@theme` copiado.
- No probado en un navegador, solo comprobado el código.
