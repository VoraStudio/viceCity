# 008 — Efecto hover de los enlaces del menú hamburguesa

**Stack**: css
**Estado**: aceptado
**Fecha**: 2026-10-06

## Contexto

El menú de escritorio marca el enlace con hover mediante texto en `purple-700` y una rayita de 16 × 4 px bajo la palabra. El menú hamburguesa, visible por debajo de 1360 px (también en portátiles con ratón y ventanas estrechas), no tenía ese efecto.

## Decisión

- Los cinco enlaces de `#menu-mobil` reutilizan las clases del escritorio: `relative transition-colors hover:text-purple-700 after:absolute after:bottom-0.5 after:left-1/2 after:h-1 after:w-4 after:-translate-x-1/2 after:rounded-full after:bg-purple-700 after:opacity-0 hover:after:opacity-100 motion-reduce:transition-none`.
- `relative` es necesario: la rayita es un `::after` y se posiciona respecto al enlace.
- `w-fit` hace que el enlace mida lo que su texto más el `px-4`. Sin él, el enlace es un bloque a todo el ancho y `left-1/2` centra la rayita en la fila, no bajo la palabra.
- El botón «Parla amb un especialista» del final del panel no cambia: no es un enlace de navegación.

## Consecuencias

- El área clicable de cada enlace pasa de la fila entera al texto más 16 px por lado. La altura (`py-3`) no cambia.
- En Tailwind v4 `hover:` solo se aplica en dispositivos con hover (`@media (hover: hover)`). En un móvil táctil el efecto no aparece; para feedback al tocar haría falta `active:`.
- No se ha podido comprobar visualmente: la ventana del navegador de pruebas no baja de 1360 px y el panel móvil queda oculto.
