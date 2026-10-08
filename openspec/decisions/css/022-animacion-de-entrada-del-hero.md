# 022 — Animació d'entrada del hero

**Stack**: js
**Estat**: acceptat
**Data**: 2026-10-08
**Parcialment substituïda per**: 030 (l'efecte caràcter a caràcter fet servir per a les DESCRIPCIONS, aquí el subtítol, queda substituït pel fade up per línies)

## Context

Continua la decisió 021. El hero (títol, subtítol, dos CTAs i maqueta del producte) entra en seqüència amb una timeline a `script.js`.

## Decisió

- **El títol reutilitza `title-reveal.js`** amb `data-title-reveal` a l'`<h1>`, sense duplicar-ne l'efecte.
- **El subtítol, els CTAs i la maqueta van en una sola timeline** enganxada amb `data-hero-subtitle`, `data-hero-ctas` i `data-hero-visual`.
- **Subtítol**: `SplitText` amb `words,chars`, tots dos en `display: inline-block` (les transformacions no actuen sobre elements inline, i així les paraules no es parteixen). Efecte de profunditat a X: `x`, `z`, `rotateX`, `opacity`, `power4.out`, `transformOrigin: "50% 0% -50px"`, amb `perspective: 1000` al paràgraf. Arrenca a `0.6` per deixar entrar el títol.
- **`stagger: { amount }` en lloc d'un valor fix per caràcter**: el repartiment total no depèn de la longitud del text.
- **CTAs** amb fade up (`y: 30`) i `stagger`, i **maqueta** amb fade d'opacitat. Tots dos amb `clearProps`.
- **`ScrollTrigger` amb `endTrigger: visual` i `end: "bottom top"`.** Sense `end`, `play reset play reset` feia `reset` quan el subtítol sortia per dalt, i la maqueta, encara visible, desapareixia. La zona activa ha de cobrir tot el que anima la timeline.

## Conseqüències

- La timeline es dispara en carregar (el subtítol ja és en pantalla), així que la maqueta, més avall, pot fer el seu fade fora de la vista.
- El títol té el seu propi trigger i el subtítol arrenca a `0.6` s: si el títol passa de dues línies, pot caldre pujar aquest temps.
- No provat en un navegador, només comprovat el codi.
