# 021 — Animació d'entrada del header

**Stack**: js
**Estat**: acceptat
**Data**: 2026-10-08

## Context

Es vol animar l'entrada dels elements de cada secció amb GSAP, lligada a una timeline per secció. Les animacions viuen en un únic `assets/js/script.js`, separat per comentaris de secció. El header és la primera.

## Decisió

- **Un sol `script.js` general**, carregat com a `type="module"` després de `title-reveal.js`. Fa servir `gsap` global, com la resta de mòduls. Els únics comentaris són els separadors de secció.
- **El header s'anima en carregar, sense `ScrollTrigger`.** És en pantalla amb el scroll a 0, així que el seu `start` ja estaria superat i el `reset` de `play reset play reset` mai es dispararia.
- **Una timeline de dos passos**: el `nav` cau amb fade (`y: -40`, 0,8 s, `power3.out`) i els elements (logo, `li` del menú, CTAs i hamburguesa) entren amb `stagger: 0.08`, solapats amb `"-=0.6"`.
- **Selectors amb `:scope`** per agafar només els fills directes del `nav` i excloure l'`ul` del menú mòbil (`#menu-mobil`).
- **`clearProps: "transform,opacity"`** als `defaults` de la timeline, per tornar els estils a Tailwind i no trencar els hover.
- **`prefers-reduced-motion`** talla l'animació amb un early return.

## Conseqüències

- Els elements `display: none` segons el breakpoint (els enllaços en mòbil) entren a l'`stagger` i endarrereixen els següents.
- Es pot veure un parpelleig abans que GSAP amagui el header, perquè els mòduls carreguen diferits. Pendent: amagar-lo amb la variant `js:` i passar a `fromTo`.
- No provat al navegador, només comprovat el codi.
- Les seccions següents afegeixen el seu bloc al mateix `script.js`.
