# 012 — Efecto ripple en los CTA

**Stack**: css + js
**Estado**: aceptado
**Fecha**: 2026-10-06

## Contexto

Los botones de la web no tenían animación de hover. Se quiere el efecto «botón líquido» del ejemplo 4 de `03a_interaccion.html`: un círculo que nace donde entra el cursor y cubre el botón.

## Decisión

- **GSAP** se carga como script normal (`gsap@3`, jsDelivr) antes de los módulos, de modo que `gsap` existe como global cuando se ejecutan.
- **Marcado:** cada CTA es `relative overflow-hidden` y contiene un círculo (`data-ripple-fill`, `size-2.5`, `absolute top-0 left-0`) y el texto envuelto en `data-ripple-text` con `relative z-10`. El `<a>` lleva `data-ripple` y `data-ripple-text-color` con el color final del texto, como variable del tema.
- **`assets/js/modules/ripple.js`:** `initRipple` recorre los `[data-ripple]`; en `mouseenter` coloca el círculo en la posición del cursor relativa al botón y lo escala a 60 (`power2.out`), con el texto cambiando de color con retraso; en `mouseleave` lo encoge hacia el punto de salida.
- **No se usa `scale-0` de Tailwind:** v4 lo implementa con la propiedad `scale`, que se multiplica con el `transform` de GSAP y dejaría el círculo en escala 0. El estado inicial lo pone `gsap.set`.
- **Colores:** los primarios rellenan con `purple-300` y el texto pasa a `purple-700`; los secundarios rellenan con `purple-700` y el texto pasa a blanco; el de la card oscura rellena con `purple-500`.
- Aplicado a los 9 CTA: hero (2), header (2), menú móvil (1), card 3 (1), blog (1) y footer (2).
- Respeta `prefers-reduced-motion`: sin animación si el usuario la desactiva.

## Consecuencias

- Los tiempos del texto llevan retraso porque, con el círculo todavía pequeño, el texto ya cambiado quedaba morado sobre morado e invisible.
- `scale: 60` es una estimación para botones de unos 240 px; con una entrada por una esquina podría no cubrir.
- `purple-700` sobre `purple-300` tiene menos contraste que sobre blanco; sin medir.
- El efecto usa `mouseenter` y `mouseleave`: en táctil no se ve, aunque el botón sigue funcionando.
- No se ha podido ver la animación a velocidad real: la pestaña de pruebas no se pinta y GSAP avanza lento.
