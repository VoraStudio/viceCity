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
  - **Les 6 cards** (`data-blog-card`): fade up amb `stagger: 0.15`, amb `ScrollTrigger` a la llista de cards (`<ul>`) a `top 85%`. Vegeu la correcció de 2026-10-09: la versió original apuntava per error al `<li>` de la primera card.
- **`play reset play reset`** als dos tweens amb scroll, com a la resta de seccions.
- **`reveal.css` i `reveal()`**: els tres ganxos s'afegeixen a la llista d'elements ocults i es revelen a la mateixa funció (decisió 043).
- **El header i el footer del blog ja s'animaven**: la pàgina carrega `script.js` i els components generen `#inici nav` i `#peu`, que és el que busquen `initHeaderAnimation` i `initFooterAnimation`. No ha calgut res més.

## Conseqüències

- Les altres pàgines no tenen aquests ganxos, així que `initBlogAnimation` surt sense fer res.
- Els filtres de categories de la capçalera entren amb la capçalera, sense animació pròpia.
- No provat en un navegador, només comprovat el codi.

## Correcció (2026-10-09)

Dos errors de `initBlogAnimation` deixaven el blog en blanc:

- **Cards amagades sense destacada**: es cridava `reveal(hero, featured ?? [], cards)`. Amb un array buit pel mig, GSAP no revelava les cards i es quedaven amb `visibility: hidden` (decisió 043) quan la categoria filtrada no tenia article destacat; per exemple `blog.php?categoria=ia` es veia buit. Ara és `reveal(...[hero, featured, ...cards].filter(Boolean))`.
- **Pantalla en blanc abans del footer**: el `ScrollTrigger` de les cards usava `cards[0].parentElement`, que és el `<li>` de la primera card i no la llista. En sortir la primera fila per dalt, el `reset` de `play reset play reset` amagava totes les cards, també les files que encara eren a la pantalla, fins que apareixia el footer. Ara el trigger és `cards[0].closest("ul")`.

Comprovat en Chrome real recorrent l'scroll de dalt a baix (0–2700 px, de 300 en 300): cap card queda amagada o a mitges mentre és a la pantalla. Només a l'extrem inferior, una card amb 1–2 px visibles es reinicia, i això és el comportament esperat de `reset`.
