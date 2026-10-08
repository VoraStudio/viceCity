# 013 — Animació d'entrada dels títols de secció

**Stack**: css + js
**Estat**: acceptat
**Data**: 2026-10-06

## Context

Es vol l'animació «Skew» de l'exemple `20_examples_3d.html` als títols de secció, lligada al scroll: `rotateX: -90`, `skewX: 45`, `opacity: 0`, 1 segon, `power2.out`.

## Decisió

- **ScrollTrigger** es carrega com a script normal després de GSAP.
- **Ganxo:** els títols porten `data-title-reveal`: Gestiona avui, Especialització, Integració, Intel·ligència Artificial, Azure i el del blog. Es trien explícitament en lloc de seleccionar tots els `<h2>`.
- **`assets/js/modules/title-reveal.js`:** `gsap.from` sobre cada títol, amb `start: 'top 85%'` i `toggleActions: 'play reset play reset'`, de manera que l'animació es reprodueix en entrar i es reinicia en sortir, en les dues direccions del scroll.
- **Perspectiva al contenidor:** com l'exemple, que anima un `<span>` dins d'un pare amb `perspective: 1200px`, `title-reveal.js` fa `gsap.set(title.parentElement, { perspective: 1200 })`. Es va descartar `transformPerspective` al propi títol (vegeu Conseqüències).
- `force3D: true` es manté, tot i que no era la causa de l'estrebada final.
- Respecta `prefers-reduced-motion` i crida `ScrollTrigger.refresh()` després de la càrrega.

## Conseqüències

- **Queden fora** els `<h2>` de les targetes de recursos (fan servir `rotate-180` i `writing-mode`, que el `transform` de GSAP trepitjaria), l'`h1` del hero, el títol de la franja de confiança i els del footer.
- **Línia a línia (2026-10-07):** es fa servir `SplitText` 3.15.0 (script amb versió fixa i SRI) amb `type: "lines"`, `autoSplit` i `stagger: 0.12`. S'espera a `document.fonts.ready` abans de dividir, perquè el tall de línia depèn de la tipografia final, i la perspectiva passa al propi títol perquè totes les línies comparteixin el punt de fuga. El títol del blog porta una fletxa SVG, que `SplitText` trencaria, així que continua animant-se com a bloc.
- Mesurat al navegador: 6 títols enganxats i ocults fora de pantalla, sense scroll horitzontal, alçada de la pàgina estable (6304 px) i sense recàlculs de ScrollTrigger durant l'animació.
- **Estrebada al final, resolta (2026-10-07):** `transformPerspective` i `skewX` a la mateixa matriu de GSAP feien divergir el terme de perspectiva (`m43` passava de -0,0012 a -0,167 en els últims fotogrames) i després saltava a identitat. Mesurat amb `getComputedStyle(...).transform` per fotograma. `will-change` i `force3D` no ho arreglaven; sí que ho va fer la perspectiva al contenidor. Detall a `voraData/tips/gsap-skew-perspective-snap.md`.
- Compromís: el punt de fuga és el centre del contenidor, no del títol.
