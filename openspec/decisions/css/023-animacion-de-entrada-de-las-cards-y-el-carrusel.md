# 023 — Animación de entrada de las cards y el carrusel de logos

**Stack**: js + css
**Estado**: aceptado
**Fecha**: 2026-10-08

## Contexto

Continúa las decisiones 021 y 022. Las 4 cards de «Gestiona avui» (`#solucions`) y la franja de logos de `#administracions` entran con una sola timeline en `script.js`.

## Decisión

- **Una timeline para las dos secciones**: la franja forma parte del bloque `SOLUCIONS` y no tiene bloque propio.
- **Cards**: entran con fade y `stagger: 0.2`, alternando el lado por índice (`x: (index) => index % 2 === 0 ? -80 : 80`, valor basado en función). Marcadas con `data-solutions-card`.
- **Franja de logos**: sube con fade (`y: 40`) después de las cards, con la posición `"-=0.9"`, marcada con `data-trust-strip`. Se anima la caja exterior y no el marquee, que ya usa `transform` en su `animate-marquee`.
- **`ScrollTrigger` con `endTrigger: "#administracions"` y `end: "bottom top"`.** La franja vive en otra `<section>` y el `reset` de `play reset play reset` no debe dispararse mientras siga en pantalla.
- **`overflow-x-clip` en `#solucions`**: las cards arrancan fuera del ancho (`x: ±80`) y sin él habría scroll horizontal durante la animación. `clip`, a diferencia de `hidden`, no crea un contenedor de scroll.
- **Padding de `#solucions` en `lg`**: de `lg:py-20` a `lg:pt-20 lg:pb-10`, para subir la franja. Se separó en `pt` y `pb` para no depender del orden del CSS generado.

## Consecuencias

- La franja se anima con el trigger de las cards: si queda por debajo del pantallazo, su fade puede ocurrir antes de verse.
- Se mantiene la sección `#administracions` separada en el HTML porque el menú enlaza a su id.
- No probado en un navegador, solo comprobado el código.
