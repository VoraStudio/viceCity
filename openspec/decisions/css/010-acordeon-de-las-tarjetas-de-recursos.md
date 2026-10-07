# 010 — Acordeón de las tarjetas de recursos

**Stack**: css
**Estado**: aceptado (parcial)
**Fecha**: 2026-10-06

## Contexto

Las tres tarjetas de `#recursos` («Dades que impulsen decisions», «Pensat per a les administracions públiques», «El futur de la gestió tributària comença avui») se mostraban siempre desplegadas en un grid. Se quiere que solo se vea el título y que el resto aparezca al expandir.

## Decisión

**Escritorio (`lg`): acordeón horizontal.**
- El contenedor pasa de `grid` a `flex` (`flex-col` en la base, `lg:flex-row`). Sin `display: flex`, `flex-col` y `flex-row` no hacen nada.
- Cada tarjeta es `lg:flex-1` en reposo y `lg:hover:flex-3` en hover (la tercera `lg:hover:flex-4`). El reparto del espacio lo hace `flex-grow`; `flex-shrink` es solo la consecuencia. Con `min-w-0` para que no se bloquee con texto largo, y `transition-[flex] duration-1000`.
- Altura fija `lg:h-[32rem]` en las tres para romper la dependencia del contenido.
- En reposo el título se gira (`lg:[writing-mode:vertical-rl] lg:rotate-180`, con `lg:h-full`) y en hover vuelve a horizontal. El contenido se oculta con `lg:opacity-0` y aparece con `lg:group-hover:opacity-100` y `lg:group-focus-within:opacity-100`.
- Cada `<article>` es `group`: el hover ocurre en la tarjeta, pero el cambio se aplica a sus hijos.

**Tablet y móvil (por debajo de `lg`): acordeón vertical.**
- La base es columna: `flex-col`, con `lg:flex-row lg:hover:flex-col` solo desde `lg`.
- El contenido se envuelve en `grid grid-rows-[0fr]` que pasa a `group-hover:grid-rows-[1fr]` (y `group-focus-within`) con `transition-[grid-template-rows]`. Es el truco para animar una altura: `height: auto` no se anima, pero `0fr` a `1fr` sí.
- El hijo interior lleva `min-h-0 overflow-hidden lg:overflow-visible`, y el envoltorio `lg:contents`, de modo que en `lg` desaparece de la maquetación y el diseño de escritorio no cambia.

## Consecuencias

- **No hay apertura por clic o toque.** En Tailwind v4 `hover:` solo actúa en dispositivos con hover, así que en un móvil táctil las tarjetas no se abren. Falta un botón en el título con `aria-expanded` y `aria-controls`, y JavaScript que alterne el estado.
- **Escritorio sin cerrar.** Medido en el navegador a 1366 px, el título vertical se encogía a su palabra más larga (130–219 px) porque el contenido invisible tiene `min-height: auto` y no cede espacio. La corrección propuesta es `lg:shrink-0` en los `<h2>` y `lg:overflow-hidden` en las `<article>`. No está aplicada.
- `writing-mode` no se anima: el título cambia de orientación de golpe.
- El `gap-5` de la `<article>` deja 20 px entre el título y el contenido plegado.
- La tarjeta 2 usa `bg-lav-100/40` con texto, caso abierto desde la decisión 004.
- Animar `grid-template-rows` requiere Chrome 107, Firefox 66 o Safari 16; en navegadores anteriores el contenido abre de golpe.
- No se ha podido comprobar el comportamiento en tablet y móvil: la ventana del navegador de pruebas no baja de 1360 px.
