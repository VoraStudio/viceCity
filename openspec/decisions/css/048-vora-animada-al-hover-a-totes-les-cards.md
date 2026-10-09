# 048 — Vora animada al hover a totes les cards

**Stack**: css + html + js
**Estat**: acceptat
**Data**: 2026-10-09

## Context

La vora animada de la decisió 035 només era a les 4 cards de `#solucions`. Es vol el mateix efecte a la resta de cards de la web: les 4 del carrusel d'Especialització, les 3 de `#recursos` i les del blog (article destacat i cards).

## Decisió

- **La utilitat `animated-border` ja no força `position: relative`**: les cards del carrusel són `absolute` en mode stack (`in-data-[stack=on]:absolute`) i el `relative` de la utilitat ho hauria trepitjat. Cada card porta ara `relative` a la seva classe, i al carrusel el variant `in-data-[stack=on]:absolute` el sobreescriu.
- **`inset` parametritzat amb `--border-inset`** (per defecte `-3px`). Les cards amb `overflow-hidden` (`#recursos` i blog) fixen `[--border-inset:0px]`: amb un `inset` negatiu el `::before` quedaria retallat, i amb `0` l'anell de 4 px queda dins de la card.
- **`z-index: 1` al `::before`**: a les cards del blog la portada és `relative` i taparia l'anell de dalt.
- **Nou ganxo `data-hover-border`** a totes les cards. `initSolutionsCardsBorder` passa a `initCardsBorder` i selecciona `[data-hover-border]` en comptes de `[data-solutions-card]`, que es manté només per a l'animació d'entrada.
- **La utilitat també és a `includes/head.php`**, perquè el blog no comparteix `<style>` amb `index.html` (com a la decisió 035).

## Conseqüències

- La utilitat està duplicada a `index.html` i a `includes/head.php`; si es toca una, cal tocar l'altra.
- Al carrusel, `stack-carousel.js` fa `clearProps: "all"` a les cards: es perd l'angle del degradat en canviar de mode, sense efecte visible.
- No hi ha cap card nova a `articulo.php` (l'`<article>` és el text de l'article, no una card).
- Amb `prefers-reduced-motion` o sense hover, la vora apareix al hover però no gira (igual que a la 035).
- No provat en un navegador, només comprovat el codi.
