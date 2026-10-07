# 017 — Primera tarjeta de recursos desplegada por defecto

**Stack**: css + js
**Estado**: aceptado
**Fecha**: 2026-10-07

## Contexto

En `#recursos` las tres tarjetas nacían plegadas y solo se abrían con hover (`lg`) o con clic (móvil y tablet). La sección quedaba sin contenido visible al cargar, y las tarjetas plegadas eran demasiado anchas para un título vertical.

## Decisión

- **La tarjeta 1 nace desplegada en todos los breakpoints.** En `lg` pasa a `lg:flex-6`, `flex-col` y con el título horizontal. Se pliega solo cuando otra tarjeta recibe hover o foco, mediante `lg:group-has-[article:not(:first-child):hover]/list:*` y su variante `focus-within`. El contenedor lleva `group/list` para que la tarjeta 1 detecte a las demás.
- **Proporción 6/1/1 (8/1/1 en la tarjeta 3).** Las desplegadas suben de `flex-3`/`flex-4` a `flex-6`/`flex-8`, en lugar de bajar las plegadas por debajo de `1`: así no hay valores arbitrarios decimales y las plegadas quedan en ~12,5 % del ancho.
- **ARIA coherente con lo visible.** El botón de la tarjeta 1 nace con `aria-expanded="true"`. En `acordeon.js`, `syncAll` considera abierta la primera por defecto (siempre por debajo de `lg`; en `lg`, salvo que otra tarjeta esté activa), y los eventos `pointerenter`, `pointerleave`, `focusin` y `focusout` resincronizan todas las tarjetas, no solo la que recibe el evento.
- **Móvil y tablet:** título `text-xl` hasta `md` y un círculo con chevron (`lg:hidden`) dentro del mismo `<button>`, centrado en el extremo opuesto. El chevron gira con `group-has-aria-expanded:rotate-180` y respeta `motion-reduce`.
- **Texto de los paneles** de `text-base` (16 px) a `text-lg` (18 px), salvo los títulos. Las `article` llevan `overflow-hidden`.

## Consecuencias

- El estado ARIA se calcula en un único sitio (`syncAll`), así que el atributo siempre refleja lo que ve el usuario.
- `overflow-hidden` puede recortar el anillo de foco (`ring-4`) de los elementos pegados al borde de la tarjeta; si pasa, añadir margen interno o `ring-inset`.
- `text-lg` no es un token capturado del manual (el cuerpo es `text-body`, 22 px); queda como ajuste local hasta resolver la discrepancia 18/22 px.
- Sigue sin probarse en un dispositivo táctil real ni con lector de pantalla.
