# 027 — Animació d'entrada de «Microsoft Azure», les cards de recursos i la franja del blog

**Stack**: js + css
**Estat**: acceptat
**Data**: 2026-10-08
**Parcialment substituïda per**: 030 (l'efecte caràcter a caràcter de la descripció queda substituït pel fade up per línies)

## Context

Continua les decisions 021-026. La secció `#azure` i el que ve a sota (les 3 cards de l'acordió de `#recursos` i la franja «Descobreix el nostre blog») entren amb una sola timeline a `script.js`, com ja es va fer amb les cards i el carrusel de logos (decisió 023).

## Decisió

- **Una timeline** (`data-azure*`, `data-resources-card` i `data-blog-strip` a l'HTML), amb `ScrollTrigger` a `top 70%` d'`#azure`, `endTrigger: "#recursos"` i `end: "bottom top"`. Les cards i la franja són en una altra `<section>`: sense l'`endTrigger`, el `reset` passaria en sortir `#azure` per dalt amb elles encara en pantalla.
- **Descripció**: efecte de profunditat a X amb `charsDepthReveal`.
- **Els 4 blocs** (Escalabilitat, Disponibilitat, Seguretat, Innovació): fade des de baix (`y: 40`, 0,8 s, `stagger: 0.2`, `"-=0.4"`).
- **Les 3 cards de l'acordió**: la primera amb fade des de l'esquerra (`x: -60`) i les altres dues des de baix (`y: 40`), en un sol tween amb valors per funció segons l'índex. Una mica més lentes (1,1 s, `stagger: 0.3`).
- **Franja del blog**: fade des de baix al final de la seqüència, amb `clearProps: "opacity"`. L'`<h2>` de la franja fa servir `data-title-reveal`, i `title-reveal.js` posa `perspective` al seu element pare, que és la pròpia franja: no s'ha comprovat si `clearProps: "transform"` també l'esborraria, així que s'evita.
- **`overflow-x-clip` a `#azure`**, pel desplaçament horitzontal dels caràcters.

## Conseqüències

- Les cards de l'acordió s'animen amb `transform` i `opacity` però la seva transició d'escriptori és de `flex`: no hi ha conflicte, tot i que convé provar el hover a `lg`.
- Les cards i la franja del blog poden fer el seu fade abans de veure's, perquè s'animen amb el trigger d'`#azure`.
- No provat en un navegador, només comprovat el codi.
