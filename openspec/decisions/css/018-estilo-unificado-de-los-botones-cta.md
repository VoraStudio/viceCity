# 018 — Estilo unificado de los botones CTA

**Stack**: css + js
**Estado**: aceptado
**Fecha**: 2026-10-07

## Contexto

Los botones con `data-ripple` (31 en las 5 páginas) mezclaban fondos, bordes y pesos de fuente. Al añadir un borde de 1-2 px aparecían fragmentos claros en las curvas, y a veces el texto se quedaba blanco tras pasar el ratón muy rápido.

## Decisión

- **Dos variantes, las del hero.**
  - Oscura: `bg-purple-700 text-white font-bold`; el ripple rellena en `purple-100` y el texto pasa a `purple-700`.
  - Clara: `bg-purple-100 text-purple-700 font-bold`; el ripple rellena en `purple-700` y el texto pasa a blanco.
  - La variante se elige por el fondo del botón; se conservan los extras de cada uno (anchos, `disabled`, `mt-auto`, `menu:inline-flex`). El de la tarjeta 3, antes `bg-paper`, pasa a la variante clara.
- **Contorno con `outline-2 -outline-offset-1 outline-purple-700`** en lugar de `border`. Un `border` se pinta sobre el fondo del propio botón y, en las curvas, deja ver ese fondo por antialiasing. El `outline` con offset negativo se pinta encima del relleno y se solapa 1 px, así que tapa la rendija entre el recorte del ripple y el contorno.
- **Sin `border-*`, `ring-*` ni `bg-clip-padding`** en los botones. Se probaron las tres y cada una reintroducía una rendija: `border` mezcla con el fondo, `ring` toca el recorte sin solapar y `bg-clip-padding` deja una costura entre fondo y borde.
- **`ripple.js`: `overwrite: true` en los tweens del texto.** Con `overwrite: 'auto'` un tween retardado (`delay: 0.15`) no se cancelaba al salir rápido y dejaba el texto en blanco con el relleno ya retirado.
- Se mantiene `rounded-full`; se probó `rounded-xl` y se descartó por estética.

## Consecuencias

- El ripple sigue siendo frágil frente a bordes: el recorte con `overflow-hidden` y cualquier contorno se tocan en el mismo píxel. Se descartó, por ahora, quitar el contorno o reescribir el efecto con `clip-path`.
- `-outline-offset-1` es imprescindible: sin él reaparece la rendija clara en el hover del botón claro.
- El contorno es del mismo color que el fondo en la variante oscura, así que solo aporta el solape; en la clara sí se ve como contorno.
- `font-bold` en vez de `font-medium` se aparta del manual (`tokens.md` fija `font-medium`); queda como ajuste local.
- No se ha probado en un dispositivo táctil real.
