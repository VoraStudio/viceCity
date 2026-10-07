# 011 — Apertura por clic del acordeón en tablet y móvil

**Stack**: css + js
**Estado**: aceptado
**Fecha**: 2026-10-06

## Contexto

La decisión 010 abría las tarjetas de `#recursos` con `group-hover`, que en Tailwind v4 va dentro de `@media (hover: hover)`: en un móvil o tablet táctil no se aplica. Comprobado en el CSS generado. Además, las tarjetas no eran enfocables, así que tocar una no activaba `group-focus-within`.

## Decisión

- **El título pasa a ser un `<button>`** dentro del `<h2>`: `type="button"`, `aria-expanded="false"`, `aria-controls="recursos-panel-N"` y `data-accordion-toggle`. Cada panel lleva el `id` correspondiente.
- **El estado vive en ARIA.** El envoltorio plegable se abre con `group-has-aria-expanded:grid-rows-[1fr]`, que Tailwind genera como `:is(:where(.group):has([aria-expanded="true"]) *)`. No hay una clase `open` duplicada. Comprobado en el navegador: pasa de `0fr` a `1fr` al cambiar el atributo.
- **Se quitan `group-hover` y `group-focus-within` del envoltorio.** Con ellos, el hover y el foco mantenían la tarjeta abierta aunque `aria-expanded` valiera `false`, y el segundo clic no cerraba. Por debajo de `lg` queda una sola fuente de verdad.
- **JavaScript** en `assets/js/modules/acordeon.js`: `initAccordion` selecciona los botones con `selectAll`, sale pronto si no hay ninguno y, al hacer clic, lee `aria-expanded` como texto y escribe el contrario. Se carga con `<script type="module">` junto a `nav.js`.
- En `lg` no cambia nada: el envoltorio es `display: contents` y el efecto horizontal sigue usando `lg:group-hover` y `lg:group-focus-within`.

## Consecuencias

- Por debajo de `lg` el hover ya no abre las tarjetas: solo el clic, el toque o el teclado (Enter o Espacio). Es el mismo comportamiento con ratón, dedo y teclado.
- `return` no puede usarse a nivel superior de un módulo (`SyntaxError: Illegal return statement`, comprobado con Node), por eso el early return vive dentro de `initAccordion`.
- Si el selector del JS no coincide con el atributo del HTML, el script falla en silencio, porque la lista queda vacía y sale por el early return.
- **Pendiente:** el archivo se llama `acordeon.js` y las convenciones piden nombres en inglés (`accordion.js`).
- **Pendiente en `lg`:** `lg:shrink-0` en los `<h2>` y `lg:overflow-hidden` en las `<article>` para que el título vertical ocupe todo el alto.
- No se ha probado en un móvil real.
