# 022 — Animación de entrada del hero

**Stack**: js
**Estado**: aceptado
**Fecha**: 2026-10-08

## Contexto

Continúa la decisión 021. El hero (título, subtítulo, dos CTAs y maqueta del producto) entra en secuencia con una timeline en `script.js`.

## Decisión

- **El título reutiliza `title-reveal.js`** con `data-title-reveal` en el `<h1>`, sin duplicar su efecto.
- **El subtítulo, los CTAs y la maqueta van en una sola timeline** enganchada con `data-hero-subtitle`, `data-hero-ctas` y `data-hero-visual`.
- **Subtítulo**: `SplitText` con `words,chars`, ambos en `display: inline-block` (las transformaciones no actúan sobre elementos inline, y así las palabras no se parten). Efecto de profundidad en X: `x`, `z`, `rotateX`, `opacity`, `power4.out`, `transformOrigin: "50% 0% -50px"`, con `perspective: 1000` en el párrafo. Arranca en `0.6` para dejar entrar al título.
- **`stagger: { amount }` en vez de un valor fijo por carácter**: el reparto total no depende de la longitud del texto.
- **CTAs** con fade up (`y: 30`) y `stagger`, y **maqueta** con fade de opacidad. Ambos con `clearProps`.
- **`ScrollTrigger` con `endTrigger: visual` y `end: "bottom top"`.** Sin `end`, `play reset play reset` hacía `reset` cuando el subtítulo salía por arriba, y la maqueta, aún visible, desaparecía. La zona activa debe cubrir todo lo que anima la timeline.

## Consecuencias

- La timeline se dispara al cargar (el subtítulo ya está en pantalla), así que la maqueta, más abajo, puede hacer su fade fuera de la vista.
- El título tiene su propio trigger y el subtítulo arranca a `0.6` s: si el título pasa de dos líneas, puede hacer falta subir ese tiempo.
- No probado en un navegador, solo comprobado el código.
