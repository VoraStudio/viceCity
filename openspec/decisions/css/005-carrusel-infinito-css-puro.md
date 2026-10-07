# 005 — Carrusel infinito de logos en CSS puro

**Stack**: css
**Estado**: aceptado
**Fecha**: 2026-10-06

## Contexto

La franja de confianza («Administracions que ja confien...») mostraba cuatro cajas de placeholder en una cuadrícula. Se quiere un carrusel de logos que se mueva sin fin y se detenga al pasar el ratón por encima.

## Decisión

Marquee en CSS puro, sin JavaScript:

- La `<ul>` pasa a pista de una sola fila: `flex w-max items-center gap-12 pr-12`.
- Un `<div class="min-w-0 overflow-hidden">` la envuelve y recorta. El `overflow-hidden` va en el contenedor, no en la lista: una lista `w-max` mide lo que miden sus logos y no habría nada que recortar.
- Los logos se duplican (8 elementos, dos mitades idénticas). La segunda mitad lleva `aria-hidden="true"` para que los lectores de pantalla no repitan el logo.
- La animación vive en el `@theme`: `--animate-marquee: marquee 20s linear infinite`, con `@keyframes marquee { to { transform: translateX(-50%) } }`. Tailwind v4 genera la clase `animate-marquee`.
- Pausa en hover: `hover:[animation-play-state:paused]` sobre la lista.
- `pr-12` iguala el hueco final al `gap-12`. Sin él, cada mitad mediría 4 logos y 3,5 huecos y el bucle daría un tirón de medio hueco.
- Los logos se pasan a negro suave con `brightness-0 opacity-60`, porque `logoVora.png` es blanco roto sobre transparente y no se vería sobre `lav-100/40`.

## Consecuencias

- El número de elementos debe ser par y las dos mitades idénticas, o el desplazamiento del 50 % no cuadra.
- Los 8 elementos son hoy el mismo logo de Vora. Con logos reales de clientes de distinto ancho, el cálculo sigue valiendo mientras las dos mitades sean iguales.
- Queda sin resolver `prefers-reduced-motion`: la animación no se desactiva para quien lo pide (`motion-reduce:animate-none`).
- El `cursor-pointer` de los `<li>` promete un clic que no existe mientras los logos no sean enlaces.
