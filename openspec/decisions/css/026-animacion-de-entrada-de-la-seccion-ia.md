# 026 — Animació d'entrada de la secció «Intel·ligència Artificial»

**Stack**: js + css
**Estat**: acceptat
**Data**: 2026-10-08
**Parcialment substituïda per**: 030 (l'efecte caràcter a caràcter de la descripció queda substituït pel fade up per línies)

## Context

Continua les decisions 021-025. La secció `#ia` (text a l'esquerra, card amb el xat a la dreta) entra amb una timeline a `script.js`, en mirall d'«Integració» (decisió 025) i amb els mateixos temps. El títol continua animant-lo `title-reveal.js`.

## Decisió

- **Una timeline** (`data-ia*` a l'HTML), amb `ScrollTrigger` a `top 70%` i `end: "bottom top"` de la secció.
- **Subtítol**: fade des de baix (`y: 40`, 0,8 s).
- **Els 3 textos de la llista**: fade lateral des de l'esquerra (`x: -60`, `stagger: 0.2`), solapats amb `"-=0.4"`.
- **Descripció**: efecte de profunditat a X amb `charsDepthReveal`, més ràpid que en altres seccions (`duration: 0.4`, `stagger.amount: 0.5`) mitjançant spread, i solapada amb `"-=0.8"`.
- **Imatge** (la card blanca del xat): cortina horitzontal que comença per la dreta, de `inset(0% 0% 0% 100%)` a `inset(0% 0% 0% 0%)` (1,5 s, `power2.inOut`, `"-=1.6"`). `fromTo` perquè un `clip-path` sense definir val `none` i no es pot interpolar.
- **`overflow-x-clip` a `#ia`**, pel desplaçament horitzontal dels caràcters i de la llista.

## Conseqüències

- Els temps de la descripció són locals a aquesta secció: l'efecte compartit no canvia.
- Es va provar una constant pròpia per a la descripció (`aiDescriptionReveal`) i es va descartar a favor de l'spread inline.
- No provat en un navegador, només comprovat el codi.
