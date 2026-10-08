# 035 — Borde animado al hover en las cards de «Gestiona avui»

**Stack**: css + js
**Estado**: aceptado
**Fecha**: 2026-10-08

## Contexto

Las 4 cards de `#solucions` deben mostrar, al pasar el ratón, el mismo borde animado con degradado cónico que el marco de la maqueta del hero, pero mucho más fino.

## Decisión

- **Utilidad `animated-border`** en el `<style type="text/tailwindcss">` de `index.html`: un `::before` con el degradado cónico de la marca (`purple-700`, `lav-300`, `purple-500`, `lav-100`) recortado con máscara (`mask-composite: exclude`) para que solo se vea el anillo, de 2 px. Cubre el borde de 1 px de la card (`inset: -1px`) y se muestra con `:hover` mediante un fundido de 0,3 s.
- **GSAP anima `--border-angle`** de `0deg` a `360deg` en 8 s, con `ease: "none"` y `repeat: -1`, como en el hero. Cada card tiene su tween pausado: `pointerenter` lo reproduce y `pointerleave` lo pausa, así el giro no se reinicia al volver a entrar.
- **Solo en dispositivos con hover**: `initSolutionsCardsBorder` sale si `(hover: hover)` no se cumple, porque en táctil el `:hover` queda pegado tras un toque.

## Consecuencias

- GSAP anima una variable CSS sin registrarla con `@property`: se espera que interpole bien el ángulo, pero no se ha comprobado visualmente.
- La utilidad está solo en `index.html`: cada página tiene su propio `<style>`.
- No probado en un navegador, solo comprobado el código.
