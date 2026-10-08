# 029 — Animación, crédito y composición responsive del footer

**Stack**: html + css + js
**Estado**: aceptado
**Fecha**: 2026-10-08

## Contexto

Continúa las decisiones 021-028. El footer entra con una timeline en `script.js`, recibe el crédito de Vora Studio y se recompone en móvil y tablet.

## Decisión

- **Animación de los 6 bloques** (`data-footer-item`), con `stagger: 0.15` y mezcla de direcciones: logo y lema desde la izquierda, CTAs desde la derecha, Enllaços y Contacte desde abajo, Legal y la franja final con fade simple. La dirección la da el valor del atributo (`left`, `right`, `up`, `fade`) y un objeto `footerDirections` la traduce a `x` e `y` mediante valores por función en un único `from`.
- **Palabra `vicity` final** (`data-footer-logo`): segundo paso de la timeline, sube con `yPercent: 100` (3,5 s, `power3.out`, `"-=1.6"`). Emerge por el borde inferior porque su caja tiene `overflow-hidden`.
- **Crédito «Desenvolupat per»** junto al copyright, con el logo de Vora Studio (`assets/img/clients/vora.png`, `h-4`, `brightness-0 opacity-60`) enlazado a `https://vorastudio.cat` con `target="_blank"` y `rel="noopener noreferrer"`. El enlace lleva `aria-label` que avisa de la pestaña nueva. El copyright pasa de `<p>` a un `<div>` con dos `<p>`, porque un `<p>` no puede contener otro. Texto en catalán, como el resto de la web.
- **Composición en móvil y tablet** (hasta `lg`): Enllaços y Contacte en dos columnas y Legal debajo, a todo el ancho (`col-span-2`, `order-last`) con sus enlaces en fila y `flex-wrap`. Desde `md` el grid vuelve a tres columnas, pero Legal pasa a columna (`md:flex-col`, `md:order-none`). `order` solo cambia el orden visual: el del HTML y el del teclado se mantienen.
- **Palabra `vicity`** más grande hasta `lg` (`text-[38vw] lg:text-[18vw]`) y con menos hueco sobre ella (`pb-2` y `mt-0`, hasta `lg:pb-10` y `lg:mt-4`).

## Consecuencias

- A 38vw la palabra puede quedar más ancha que la pantalla y recortarse por los lados (`overflow-hidden`): es un recurso visual, no se ha medido.
- Los datos de contacto del footer son placeholder y se duplican en la sección «Contacte» (decisión 028).
- No probado en un navegador ni en dispositivos reales, solo comprobado el código.
