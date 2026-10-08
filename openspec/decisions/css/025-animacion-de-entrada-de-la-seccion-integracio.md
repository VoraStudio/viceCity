# 025 — Animació d'entrada de la secció «Integració»

**Stack**: js + css
**Estat**: acceptat
**Data**: 2026-10-08
**Parcialment substituïda per**: 030 (l'efecte caràcter a caràcter de la descripció queda substituït pel fade up per línies)

## Context

Continua les decisions 021-024. La secció «Integració sense barreres» (imatge SVG a l'esquerra, text a la dreta, card lila final) entra amb una timeline a `script.js`. El títol ja l'anima `title-reveal.js`.

## Decisió

- **Una timeline** (`data-integration*` a l'HTML), amb `ScrollTrigger` a `top 70%` i `end: "bottom top"` de la secció: tot el que s'anima és a dins seu.
- **Subtítol**: fade des del costat (`x: 60`), entra des de la dreta, on és la seva columna.
- **Descripció** (dos paràgrafs): efecte de profunditat a X (`charsDepthReveal`), amb un únic `stagger` repartit entre els caràcters d'ambdós.
- **Imatge**: revelació de cortina horitzontal d'esquerra a dreta amb `clip-path`, de `inset(0% 100% 0% 0%)` a `inset(0% 0% 0% 0%)` (1,5 s, `power2.inOut`), solapada amb la descripció (`"-=1.6"`). Es fa servir `fromTo` perquè un `clip-path` sense definir val `none` i GSAP no pot interpolar des d'allà.
- **Card lila final**: fade up (`y: 30`), al final de la seqüència.
- **`overflow-x-clip` a la secció**: l'efecte de caràcters desplaça `x: 100` i en mòbil crearia scroll horitzontal.

## Conseqüències

- L'ordre de la seqüència és subtítol, descripció, imatge i card; canvia movent la posició de cada pas.
- No provat en un navegador, només comprovat el codi.
