# 028 — Datos de contacto y animación de entrada de «Contacte»

**Stack**: html + js
**Estado**: aceptado
**Fecha**: 2026-10-08

## Contexto

Continúa las decisiones 021-027. La sección `#contacte` tenía en la columna izquierda solo el título y un párrafo. Se añaden los datos de contacto y la sección entra con una timeline en `script.js`.

## Decisión

- **Lista de contacto en la columna izquierda**: teléfono, correo y dirección, en columna, cada uno con un icono SVG en un círculo (`aria-hidden`, `stroke="currentColor"`), una etiqueta y el dato. Los datos se copian del footer para que no haya dos versiones; el teléfono usa `&nbsp;` entre los grupos de cifras para que no se parta, y la dirección va en un `<address>` con `not-italic`.
- **Las dos columnas entran a la vez por lados opuestos**: la izquierda con fade desde la izquierda (`x: -80`) y el formulario desde la derecha (`x: 80`), ambas de 1 s con `power3.out`. El formulario arranca con la posición `"<"` (mismo inicio que el tween anterior).
- **`ScrollTrigger`** en `top 70%` y `end: "bottom top"` de la sección, con `data-contact`, `data-contact-info` y `data-contact-form`.
- **`overflow-x-clip` en `#contacte`**: las dos columnas arrancan fuera del ancho.
- **`clearProps: "transform,opacity"`** en ambas, para no dejar estilos inline sobre el formulario.

## Consecuencias

- Los datos son placeholder (copiados del footer): si cambian allí, hay que cambiarlos también aquí.
- Los CTAs que apuntan a `#demo` hacen scroll al formulario: si se pulsan mientras la animación está en curso, el scroll puede aterrizar algo desplazado hasta que termina.
- No probado en un navegador, solo comprobado el código.
