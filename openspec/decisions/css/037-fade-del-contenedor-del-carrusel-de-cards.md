# 037 — Fade del contenidor del carrusel de cards

**Stack**: js
**Estat**: acceptat
**Data**: 2026-10-08

## Context

La decisió 024 animava l'entrada de cada card del carrusel d'«Especialització». En sortir de la secció i tornar-hi després d'haver mogut el carrusel amb la roda, les cards apareixien superposades i fantasma fins a moure la roda de nou.

## Decisió

- **Causa**: `render()` de `stack-carousel.js` calcula `y`, `scale`, `zIndex` i `opacity` de cada `<li>` segons la card activa. El `from(cards, { opacity: 0 })` guardava una sola vegada les opacitats finals de l'estat inicial i, amb `play reset play reset`, cada reproducció les forçava encara que la card activa ja fos una altra.
- **S'anima el contenidor `<ol data-stack-list>`**, no cada card: `from(stack, { opacity: 0, duration: 1.4, ease: "power1.inOut", clearProps: "opacity" }, "-=0.4")`. `render` només escriu als `<li>` i l'entrada només a l'`<ol>`, així que no comparteixen propietats.
- Es treu el lliscament lateral alternat de les cards: l'entrada és un fade pur.

## Conseqüències

- Es perd l'escalonat entre cards: entra la pila sencera alhora.
- Es resol el risc anotat a la decisió 024; l'entrada de les cards d'aquesta decisió queda substituïda per aquesta.
- No provat en un navegador: queda per comprovar moure el carrusel, sortir de la secció i tornar-hi.
