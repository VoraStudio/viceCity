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
- **Perspectiva en el contenedor:** como el ejemplo, que anima un `<span>` dentro de un padre con `perspective: 1200px`, `title-reveal.js` hace `gsap.set(title.parentElement, { perspective: 1200 })`. Se descartó `transformPerspective` en el propio título (ver Consecuencias).
- `force3D: true` se mantiene, aunque no era la causa del tirón final.
- Respeta `prefers-reduced-motion` y llama a `ScrollTrigger.refresh()` tras la carga.

## Consecuencias

- **Quedan fuera** los `<h2>` de las tarjetas de recursos (usan `rotate-180` y `writing-mode`, que el `transform` de GSAP pisaría), el `h1` del hero, el título de la franja de confianza y los del footer.
- La animación se aplica al título entero, no línea a línea como el ejemplo; para eso haría falta SplitText.
- Medido en el navegador: 6 títulos enganchados y ocultos fuera de pantalla, sin scroll horizontal, altura de la página estable (6304 px) y sin recálculos de ScrollTrigger durante la animación.
- **Tirón al final, resuelto (2026-10-07):** `transformPerspective` y `skewX` en la misma matriz de GSAP hacían divergir el término de perspectiva (`m43` pasaba de -0,0012 a -0,167 en los últimos fotogramas) y luego saltaba a identidad. Medido con `getComputedStyle(...).transform` por fotograma. `will-change` y `force3D` no lo arreglaban; sí lo hizo la perspectiva en el contenedor. Detalle en `voraData/tips/gsap-skew-perspective-snap.md`.
- Compromiso: el punto de fuga es el centro del contenedor, no del título.
