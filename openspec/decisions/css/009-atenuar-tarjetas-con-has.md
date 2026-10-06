# 009 — Atenuar las demás tarjetas de «Especialització» con :has

**Stack**: css
**Estado**: aceptado
**Fecha**: 2026-10-06

## Contexto

La sección 2 muestra cuatro tarjetas en cascada. En `lg` se apilan en la misma celda del grid con desplazamientos, de modo que el `<ol>` es una caja grande con huecos vacíos. Se quiere que, al pasar el ratón por una tarjeta, las demás se atenúen.

## Decisión

- **Selector relacional en el padre:** al `<ol>` se le añade `[&:has(li:hover)>li:not(:hover)]:opacity-50`. Se lee: «si el `ol` contiene una `li` en hover, las `li` hijas que no están en hover pasan a `opacity-50`».
- Se elige `:has` frente a `group` + `group-hover`: `group` se activaría al pasar por los huecos vacíos del `<ol>` y atenuaría también la tarjeta señalada. Con `:has(li:hover)` solo se activa con el ratón sobre una tarjeta real.
- **Transición** en cada `<li>`: `transition-opacity duration-300 ease-out motion-reduce:transition-none`. Va en la `<li>`, que es quien cambia de opacidad, no en el `<ol>`.
- **Tarjeta al frente:** `lg:hover:z-50` en cada `<li>`, por encima de la cascada (`lg:z-10` a `lg:z-40`), para que una tarjeta parcialmente tapada salga entera en hover.
- **Alturas:** se quita `items-start` del `<ol>`. Las tarjetas de una misma fila de la rejilla pasan a igualar su altura (`items-stretch` por defecto).

## Consecuencias

- **No está hecho:** el fondo distinto en la tarjeta en hover. Hay que elegir color, y los fondos lavanda con texto siguen sin resolver desde la decisión 004.
- **No está hecho:** igualar la altura de todas las tarjetas entre filas distintas (`auto-rows-fr`). En móvil la tarjeta 04 sigue siendo más baja, porque no tiene la fila de la etiqueta.
- Con `opacity-50` las tarjetas que se solapan en `lg` dejan ver la que tienen debajo a través de ellas. No se ha comprobado visualmente.
- `z-index` no se anima: al soltar el hover vuelve de golpe al valor original.
- Varias listas de clases se han partido en dos líneas dentro del atributo `class`. Funciona, pero conviene volver a una sola línea.
