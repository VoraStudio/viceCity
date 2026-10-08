# 033 — Carrusel en tablet con flechas y menos separación

**Stack**: css + js
**Estado**: aceptado
**Fecha**: 2026-10-08

## Contexto

La decisión 020 deja el carrusel de «Especialització» por debajo de `lg` con arrastre, y la decisión de las flechas de móvil las ocultaba desde `md`. En tablet (768 a 1023 px) no había flechas y la separación vertical entre cards era la de escritorio.

## Decisión

- **Flechas también en tablet**: el contenedor `data-stack-controls` pasa de `md:hidden` a `lg:hidden`. El JS ya las activaba por debajo de `lg` y reutiliza la animación suave de móvil (`mover`), así que no cambia su lógica.
- **Separación vertical de 64 px en tablet**, solo en ese tramo: un `gsap.matchMedia` con `(min-width: 48rem) and (max-width: 63.999rem)` pone `separacion = 64` (el 55 % de los 116 px de escritorio) y restaura 116 al salir. Móvil sigue en 70.

## Consecuencias

- Móvil (70) queda con más separación que tablet (64), algo contraintuitivo: si molesta, se iguala.
- El contenedor mantiene su altura fija (`h-136`): con las cards más juntas queda más aire arriba y abajo de la pila.
- Al cambiar de breakpoint la separación cambia pero la pila no se redibuja hasta mover una card (ya ocurría en móvil).
- No probado en un dispositivo real, solo comprobado el código.
