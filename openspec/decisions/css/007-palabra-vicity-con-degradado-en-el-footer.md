# 007 — Palabra «vicity» con degradado en el pie del footer

**Stack**: css
**Estado**: aceptado
**Fecha**: 2026-10-06

## Contexto

El pie del footer mostraba la palabra «vicity» en `text-lav-100` plano, recortada por una caja de altura fija (`h-24 md:h-32 lg:h-36`) y con el copyright a la izquierda. La referencia pide una palabra grande que se desvanece hacia abajo, con el copyright centrado encima.

## Decisión

- **Degradado en el texto:** `bg-linear-to-b from-lav-100 to-transparent bg-clip-text text-transparent`. El degradado es un fondo recortado con la forma de las letras; el color del texto se hace transparente para que se vea.
- **Tamaño proporcional al ancho:** `text-[18vw]` en lugar de `text-9xl`. Medido en el navegador, la palabra ocupa 2,36 veces el tamaño de fuente de ancho: a `18vw` llena el 48 % del contenedor de 1200 px, a `27vw` el 73 % y a `37vw` el 100 %.
- **Sin altura fija:** el contenedor pierde `h-24 md:h-32 lg:h-36` y conserva `relative overflow-hidden`. La caja mide lo que mide el texto, así que el degradado recorre toda la altura de las letras y desaparece abajo sin corte seco.
- **Centrado:** `text-center` en la palabra y `absolute inset-x-0 bottom-4 text-center` en el copyright. Un elemento estirado con `inset-x-0` centra su contenido sin calcular desplazamientos.

## Consecuencias

- `text-[18vw]` es un valor arbitrario y rompe la regla «solo medidas de Tailwind» de `tokens.md`. Es inevitable: Tailwind no tiene escala en `vw`. Se registra como excepción.
- El texto sigue marcado `aria-hidden="true"`: es decorativo, y el copyright es el contenido accesible.
- A anchos muy pequeños la palabra puede desbordar por la derecha; `overflow-hidden` la recorta en lugar de provocar scroll horizontal.
- No se ha comprobado visualmente en móvil.
