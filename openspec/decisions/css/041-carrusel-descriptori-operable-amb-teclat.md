# 041 — Carrusel d'escriptori operable amb teclat

**Stack**: html + js
**Estat**: acceptat
**Data**: 2026-10-08

## Context

A escriptori (≥ 64rem) el carrusel apilat d'«Especialització» només es movia amb la roda del ratolí: les fletxes només es mostraven per sota de `lg` i la regió `aria-live` (`data-stack-status`) no s'escrivia mai. Un usuari de teclat no podia fer-lo servir.

## Decisió

- **Les fletxes existeixen a tots els amples, però a escriptori no es veuen**: el contenidor `data-stack-controls` passa de `lg:hidden` a `lg:sr-only lg:focus-within:not-sr-only`. A tauleta i mòbil són visibles; a escriptori queden amagades visualment (no del lector de pantalla ni del teclat) i només es mostren mentre tenen el focus. Al bloc d'escriptori de `stack-carousel.js` es mostren els controls, `prev` i `next` criden `ir(-1)` i `ir(1)`, i la neteja treu els listeners i els amaga, com ja feia el bloc de tauleta i mòbil.
- **Anunci a la regió `aria-live`**: un helper `anunciar` escriu «Targeta N de M: títol» quan canvia l'índex enter (no a cada fotograma). Es crida des dels dos blocs.
- No canvien `render`, la roda, la separació ni `Draggable`.

## Conseqüències

- La roda continua bloquejant el scroll de la pàgina mentre el cursor és sobre la pila; ara hi ha alternativa de teclat.
- Les fletxes es van mostrar primer a escriptori; es va decidir que no s'havien de veure, i `sr-only` manté la via de teclat (WCAG 2.1.1) sense mostrar-les.
- Manté la regla de la decisió 033: visibles a tauleta i mòbil.
- No provat en un navegador, només comprovat el codi.
