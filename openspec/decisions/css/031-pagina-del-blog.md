# 031 — Pàgina del blog

**Stack**: html + css
**Estat**: acceptat
**Data**: 2026-10-08

## Context

`blog.html` era un placeholder de pàgina legal. Els dissenys són a `docs/Disseny web/Blog` (`vicity-blog-desktop.pdf`, `vicity-blog-ipad.pdf` i `vicity-blog-iphone.pdf`) i la pàgina es va construir amb un agent a partir d'ells. El menú, el footer i la franja de l'inici enllacen a `blog.html`, així que es reescriu al seu lloc.

## Decisió

- **Mobile first amb els tres dissenys**: base = iPhone, `md:` = iPad i `lg:` = escriptori. Sense media queries `max-width` ni `style=""`.
- **Tres seccions**: capçalera (pill «Blog», `h1`, subtítol i barra de categories en un `<nav aria-label>`), article destacat (card de dues columnes a escriptori) i últims articles (6 cards en 1, 2 o 3 columnes).
- **Reutilitza el que ja existeix**: `<site-header>`, `<site-footer>`, els tokens de l'`@theme` (sense tokens nous), el botó primari amb `data-ripple` i les classes de focus. Els mòduls es carreguen amb `site-components.js` primer.
- **Sense imatges reals**: les portades són blocs de color del disseny (blanc, `purple-700` i `ink`), amb `aspect-2/1`.
- **Accessibilitat**: un sol `h1`, ordre d'encapçalaments vàlid, `<time datetime>`, `aria-current` a la categoria activa i un text ocult amb el títol a cada enllaç «Llegir l'article» per distingir-los.
- **Ganxos d'animació sense cablejar**: `data-blog-hero`, `data-blog-featured` i `data-blog-card`. `script.js` no es toca.

## Conseqüències

- **Els 7 enllaços «Llegir l'article» són `href="#"`**: encara no hi ha pàgines d'article.
- **Els filtres enllacen a `blog.html?categoria=...` però no filtren.** Els dissenys no mostren paginació ni bloc de newsletter, així que no s'han afegit.
- Decisions de l'agent per confirmar: xip lavanda a les etiquetes de les cards de portada blanca (al PDF eren invisibles), `aspect-2/1` a les portades, títol mòbil a `text-3xl` (el disseny ronda els 32 px i cap token encaixa) i les dues taques difuminades copiades del hero.
- No provat en un navegador, només comprovat el codi.
