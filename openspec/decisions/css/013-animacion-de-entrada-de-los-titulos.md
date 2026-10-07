# 013 — Animación de entrada de los títulos de sección

**Stack**: css + js
**Estado**: aceptado
**Fecha**: 2026-10-06

## Contexto

Se quiere la animación «Skew» del ejemplo `20_examples_3d.html` en los títulos de sección, ligada al scroll: `rotateX: -90`, `skewX: 45`, `opacity: 0`, 1 segundo, `power2.out`.

## Decisión

- **ScrollTrigger** se carga como script normal después de GSAP.
- **Gancho:** los títulos llevan `data-title-reveal`: Gestiona avui, Especialització, Integració, Intel·ligència Artificial, Azure y el del blog. Se eligen explícitamente en lugar de seleccionar todos los `<h2>`.
- **`assets/js/modules/title-reveal.js`:** `gsap.from` sobre cada título, con `start: 'top 85%'` y `toggleActions: 'play reset play reset'`, de modo que la animación se reproduce al entrar y se reinicia al salir, en las dos direcciones del scroll.
- **Perspectiva:** el ejemplo anima un `<span>` dentro de un padre con `perspective: 1200px`. Aquí se usa `transformPerspective: 1200` en el propio título, sin tocar HTML ni CSS.
- `force3D: true`: GSAP, por defecto, retira la aceleración 3D al terminar, lo que fuerza un repintado del texto. Es una hipótesis para el tirón al final de la animación.
- Respeta `prefers-reduced-motion` y llama a `ScrollTrigger.refresh()` tras la carga.

## Consecuencias

- **Quedan fuera** los `<h2>` de las tarjetas de recursos (usan `rotate-180` y `writing-mode`, que el `transform` de GSAP pisaría), el `h1` del hero, el título de la franja de confianza y los del footer.
- La animación se aplica al título entero, no línea a línea como el ejemplo; para eso haría falta SplitText.
- Medido en el navegador: 6 títulos enganchados y ocultos fuera de pantalla, sin scroll horizontal, altura de la página estable (6304 px) y sin recálculos de ScrollTrigger durante la animación.
- **Pendiente de confirmar:** el tirón al final de la animación; no se ha podido reproducir. Si persiste, las sospechas son el `overflow-x-clip` de alguna sección y el coste de pintado de los blobs con `blur-3xl` del hero.
