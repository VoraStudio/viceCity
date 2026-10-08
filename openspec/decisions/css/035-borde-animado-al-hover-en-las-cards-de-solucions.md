# 035 — Vora animada al hover a les cards de «Gestiona avui»

**Stack**: css + js
**Estat**: acceptat
**Data**: 2026-10-08

## Context

Les 4 cards de `#solucions` han de mostrar, en passar el ratolí, la mateixa vora animada amb degradat cònic que el marc de la maqueta del hero, però molt més fina.

## Decisió

- **Utilitat `animated-border`** al `<style type="text/tailwindcss">` d'`index.html`: un `::before` amb el degradat cònic de la marca (`purple-700`, `lav-300`, `purple-500`, `lav-100`) retallat amb màscara (`mask-composite: exclude`) perquè només es vegi l'anell, de 2 px. Cobreix la vora d'1 px de la card (`inset: -1px`) i es mostra amb `:hover` mitjançant un esvaïment de 0,3 s.
- **GSAP anima `--border-angle`** de `0deg` a `360deg` en 8 s, amb `ease: "none"` i `repeat: -1`, com al hero. Cada card té el seu tween en pausa: `pointerenter` el reprodueix i `pointerleave` el pausa, així el gir no es reinicia en tornar a entrar.
- **Només en dispositius amb hover**: `initSolutionsCardsBorder` surt si `(hover: hover)` no es compleix, perquè en tàctil el `:hover` queda enganxat després d'un toc.

## Conseqüències

- GSAP anima una variable CSS sense registrar-la amb `@property`: s'espera que interpoli bé l'angle, però no s'ha comprovat visualment.
- La utilitat és només a `index.html`: cada pàgina té el seu propi `<style>`.
- No provat en un navegador, només comprovat el codi.
