# 007 — Paraula «vicity» amb degradat al peu del footer

**Stack**: css
**Estat**: acceptat
**Data**: 2026-10-06

## Context

El peu del footer mostrava la paraula «vicity» en `text-lav-100` pla, retallada per una caixa d'alçada fixa (`h-24 md:h-32 lg:h-36`) i amb el copyright a l'esquerra. La referència demana una paraula gran que s'esvaeix cap avall, amb el copyright centrat a sobre.

## Decisió

- **Degradat al text:** `bg-linear-to-b from-lav-100 to-transparent bg-clip-text text-transparent`. El degradat és un fons retallat amb la forma de les lletres; el color del text es fa transparent perquè es vegi.
- **Mida proporcional a l'amplada:** `text-[18vw]` en lloc de `text-9xl`. Mesurat al navegador, la paraula ocupa 2,36 vegades la mida de la font d'amplada: a `18vw` omple el 48 % del contenidor de 1200 px, a `27vw` el 73 % i a `37vw` el 100 %.
- **Sense alçada fixa:** el contenidor perd `h-24 md:h-32 lg:h-36` i conserva `relative overflow-hidden`. La caixa mesura el que mesura el text, de manera que el degradat recorre tota l'alçada de les lletres i desapareix cap avall sense tall sec.
- **Centrat:** `text-center` a la paraula i `absolute inset-x-0 bottom-4 text-center` al copyright. Un element estirat amb `inset-x-0` centra el seu contingut sense calcular desplaçaments.

## Conseqüències

- `text-[18vw]` és un valor arbitrari i trenca la regla «només mesures de Tailwind» de `tokens.md`. És inevitable: Tailwind no té escala en `vw`. Es registra com a excepció.
- El text continua marcat `aria-hidden="true"`: és decoratiu, i el copyright és el contingut accessible.
- A amplades molt petites la paraula pot desbordar per la dreta; `overflow-hidden` la retalla en lloc de provocar scroll horitzontal.
- No s'ha comprovat visualment en mòbil.
