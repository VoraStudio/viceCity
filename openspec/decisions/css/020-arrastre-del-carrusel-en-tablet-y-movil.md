# 020 — Arrastre del carrusel en tablet y móvil

**Stack**: js
**Estado**: aceptado
**Fecha**: 2026-10-07

## Contexto

La decisión 019 deja el carrusel de «Especialització» solo en escritorio, con la rueda. En tablet y móvil no existe la rueda y se quería el mismo apilado cíclico, manejado con el dedo.

## Decisión

- **Un segundo bloque `gsap.matchMedia`** en `stack-carousel.js`, activo por debajo de `lg` (`max-width: 63.999rem`) y sin `prefers-reduced-motion`. El bloque de escritorio no se toca.
- **`Draggable` 3.15.0** (script con versión fija y SRI) sobre un elemento invisible, con `trigger: stage` y `type: "y"`. Mientras se arrastra, `activa = inicio - (y - yInicio) / 116`, así que las tarjetas siguen al dedo; el `116` es la misma separación vertical que usa `render`. Al soltar, `activa` se anima al entero más cercano (0,4 s, `power2.out`).
- **`render` y el bucle `wrap` se reutilizan** tal cual: un único sitio decide la posición de cada tarjeta.
- **`Draggable` acumula la `y` entre arrastres**, por eso se guarda `yInicio` en `onPress` y se mide el desplazamiento desde ese punto. Sin ello, el segundo arrastre empezaría desplazado.
- Margen superior del contenedor: `mt-16` en todos los tamaños (antes `mt-10` / `md:mt-14`), para separar la pila de la descripción.

## Consecuencias

- Con el dedo sobre la pila no se hace scroll de página (`Draggable` captura el gesto vertical); se sale tocando fuera de ella.
- Sigue sin teclado, `aria-live` ni `inert` para las tarjetas de detrás.
- **No probado en un dispositivo táctil real**, solo comprobado el código. En móvil el texto de cada tarjeta es más largo: `min-h-88` y `h-136` pueden quedarse cortos.
- Los comentarios están en catalán y los nombres de variables en castellano, como el resto del módulo.
