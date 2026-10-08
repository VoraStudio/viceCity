# 047 — Animació d'entrada del blog

**Stack**: js + css
**Estat**: acceptat
**Data**: 2026-10-08

## Context

La pàgina del blog (decisió 031) va quedar amb els ganxos d'animació `data-blog-hero`, `data-blog-featured` i `data-blog-card` sense cablejar. Es vol que les cards entrin amb un fade up escalonat.

## Decisió

- **`initBlogAnimation` a `script.js`**, amb tres tweens de fade up (`y: 40`, 0,8 s, `power3.out`, `clearProps`):
  - **Capçalera del blog** (`data-blog-hero`): s'anima en carregar, sense `ScrollTrigger`, perquè ja és en pantalla.
  - **Article destacat** (`data-blog-featured`): amb `ScrollTrigger` a `top 85%`.
  - **Les 6 cards** (`data-blog-card`): fade up amb `stagger: 0.15`, amb `ScrollTrigger` al contenidor de les cards a `top 85%`.
- **`play reset play reset`** als dos tweens amb scroll, com a la resta de seccions.
- **`reveal.css` i `reveal()`**: els tres ganxos s'afegeixen a la llista d'elements ocults i es revelen a la mateixa funció (decisió 043).
- **El header i el footer del blog ja s'animaven**: la pàgina carrega `script.js` i els components generen `#inici nav` i `#peu`, que és el que busquen `initHeaderAnimation` i `initFooterAnimation`. No ha calgut res més.

## Conseqüències

- Les altres pàgines no tenen aquests ganxos, així que `initBlogAnimation` surt sense fer res.
- Els filtres de categories de la capçalera entren amb la capçalera, sense animació pròpia.
- No provat en un navegador, només comprovat el codi.
