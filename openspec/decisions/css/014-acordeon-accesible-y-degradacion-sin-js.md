# 014 — Acordeón accesible y degradación sin JavaScript

**Stack**: css + js
**Estado**: aceptado
**Fecha**: 2026-10-07

## Contexto

El acordeón de `#recursos` (decisiones 010 y 011) arrancaba con `aria-expanded="false"` escrito a mano y las tarjetas colapsadas por CSS. Dos problemas: si el módulo JS no cargaba, el contenido quedaba plegado sin forma de abrirlo, y `aria-expanded` solo cambiaba con el clic, aunque en escritorio las tarjetas se abren con hover o foco.

## Decisión

- **`assets/js/js-flag.js`**: script externo, síncrono y mínimo en el `<head>` que añade la clase `js` a `<html>`. No es un `<script>` inline, así que respeta la regla; y al ejecutarse antes del primer pintado evita ver el contenido desplegado y replegándose.
- **`@custom-variant js (&:where(.js *))`** en el bloque `text/tailwindcss`. Solo el estado plegado depende de él: `js:grid-rows-[0fr]` y `js:lg:opacity-0`. Sin la clase `js`, el contenido se ve. El `:where()` deja la especificidad en cero, de modo que `group-has-aria-expanded:grid-rows-[1fr]`, `lg:group-hover:opacity-100` y `lg:group-focus-within:opacity-100` siguen ganando sin depender del orden en el CSS generado.
- **`aria-expanded` ya no está en el HTML**: lo escribe `acordeon.js` al arrancar, porque sin JS el botón no abre nada y declararlo «cerrado» sería falso. `aria-controls` sí se queda en el HTML.
- **`acordeon.js`** sincroniza `aria-expanded` con lo que se ve: por debajo de `lg` alterna con el clic; desde `lg` (`min-width: 64rem`) sigue a hover y foco (`pointerenter/leave`, `focusin/out`) y el clic no hace nada, para no contradecir al CSS, que ya mantiene la tarjeta abierta con el cursor encima. Al cambiar de breakpoint se resincroniza.
- **`inert`** en el panel plegado por debajo de `lg`: el enlace del CTA de la tercera tarjeta ya no es enfocable ni se lee mientras está oculto.
- **Títulos verticales**: `<h2>` con `<button>` dentro. El nombre accesible es el texto del botón, y `writing-mode` + `rotate-180` no cambian el orden del DOM, así que los lectores de pantalla lo leen en orden normal. No ha hecho falta tocar el HTML.

## Consecuencias

- Sin JavaScript no se carga Tailwind (es el script de navegador), así que la página entera queda sin estilos y el contenido sigue legible. La clase `js` cubre el caso intermedio: Tailwind carga pero el módulo no.
- En escritorio un lector de pantalla ve `aria-expanded` cambiar con hover o foco, no con Enter. Es el comportamiento visual real.
- Los paneles siguen en el árbol de accesibilidad en escritorio aunque tengan `opacity-0` (es intencionado: el foco los abre).
