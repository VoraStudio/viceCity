# 024 — Animació d'entrada de la secció «Especialització»

**Stack**: js + css
**Estat**: acceptat
**Data**: 2026-10-08
**Parcialment substituïda per**: 030 (l'efecte caràcter a caràcter de la descripció queda substituït pel fade up per línies); 037 (l'animació d'entrada de les targetes queda substituïda pel fade del contenidor)

## Context

Continua les decisions 021-023. La descripció i les targetes del carrusel apilat (decisions 019 i 020) entren amb una timeline a `script.js`. Les targetes ja les posiciona `render` a `stack-carousel.js`.

## Decisió

- **Bloc `COMUNES` a `script.js`** amb `charsDepthReveal` (valors de l'efecte de profunditat a X) i `splitIntoChars` (`SplitText` en `words,chars` amb `inline-block` i `perspective`). El hero i aquesta secció els comparteixen, sense duplicar l'efecte.
- **Descripció** (`data-platform-description`): mateix efecte que el subtítol del hero.
- **Targetes**: fade i `stagger: 0.2`, amb direccions horitzontals oposades per índex (`x: (index) => index % 2 === 0 ? -80 : 80`), solapades amb `"-=0.4"` sobre la descripció.
- **Sense `clearProps` a les targetes.** `render` controla `yPercent`, `y`, `scale`, `opacity` i `zIndex`: `"transform"` esborraria la seva `y` i `scale`, i `"opacity"` la seva opacitat. `x` queda lliure perquè `render` no la toca.
- **`ScrollTrigger` amb `endTrigger: section` i `end: "bottom top"`**, perquè el `reset` no passi mentre el carrusel continuï en pantalla.
- **Fletxes de mòbil**: `mt-8` a `mt-2`, per acostar-les a la pila.

## Conseqüències

- **Fràgil**: el `from` d'`opacity` guarda les opacitats que `render` tenia en crear-lo. Si es gira el carrusel, se surt de la secció i es torna a entrar, el replay les deixa com estaven abans del gir i la roda següent dona un petit salt. S'arregla fent que `stack-carousel.js` exposi el seu estat.
- No provat en un navegador, només comprovat el codi.
