# 019 — Carrusel vertical cíclico por rueda en «Especialització»

**Stack**: css + js
**Estado**: aceptado
**Fecha**: 2026-10-07

## Contexto

La sección 2 mostraba cuatro tarjetas en cascada (decisión 009). Se quiere un carrusel vertical: la tarjeta activa al frente y las vecinas detrás, más pequeñas y tenues, en bucle infinito.

## Decisión

- **El carrusel solo existe desde `lg` y sin `prefers-reduced-motion`**, mediante `gsap.matchMedia`. Por debajo, el grid normal (`md:grid-cols-2`) queda intacto.
- **Tailwind coloca, JS mueve.** Las clases `in-data-[stack=on]:*` apilan las tarjetas (`absolute`, `inset-x-0`, `top-1/2`) y fijan el contenedor (`max-w-5xl`, `h-136`, `min-h-88` por tarjeta), pero solo se activan cuando `stack-carousel.js` pone `data-stack="on"` en la sección. Al salir de escritorio lo quita y borra los estilos de GSAP (`clearProps`).
- **Posición por distancia cíclica.** `render(activa)` calcula `distancia = gsap.utils.wrap(-n/2, n/2, i - activa)` y coloca cada tarjeta con `yPercent: -50`, `y`, `scale`, `opacity` y `zIndex`. La opacidad vale `1 - (lejos / (n/2)) ** 2`, que llega a 0 en el borde de la ventana: así el salto de la tarjeta que da la vuelta no se ve.
- **Avance con la rueda sobre la pila, sin `pin`.** Un `wheel` en `data-stack-stage` (con `preventDefault`, listener no pasivo) avanza una tarjeta, animando un número `activa` con GSAP (0,6 s, `power2.inOut`). Un bloqueo de 900 ms ignora la inercia del trackpad. Como `activa` no está atado al scroll, puede crecer sin límite y el bucle no tiene final.
- Se descartó el `pin` con `ScrollTrigger` (`scrub` + `snap`): necesita un principio y un final de scroll, que no encajan en un bucle infinito, y secuestra el scroll de toda la sección.

## Consecuencias

- Sustituye a la cascada: la decisión 009 (atenuar con `:has`) deja de aplicar en `lg`.
- Con el cursor sobre la pila la página no hace scroll, porque la rueda se bloquea. Se sale moviendo el cursor fuera de la pila.
- **No incluye** teclado, `aria-live` ni `inert` para las tarjetas de detrás; la versión mínima prioriza la comprensión del código. Pendiente antes de dar la sección por accesible.
- **Táctil resuelto en la decisión 020**: por debajo de `lg` el mismo `render` se mueve arrastrando.
- Los nombres de variables y los comentarios están en castellano y catalán, tal como se fueron construyendo en la sesión guiada.
