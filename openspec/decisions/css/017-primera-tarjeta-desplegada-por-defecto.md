# 017 — Primera targeta de recursos desplegada per defecte

**Stack**: css + js
**Estat**: acceptat
**Data**: 2026-10-07

## Context

A `#recursos` les tres targetes naixien plegades i només s'obrien amb hover (`lg`) o amb clic (mòbil i tauleta). La secció quedava sense contingut visible en carregar, i les targetes plegades eren massa amples per a un títol vertical.

## Decisió

- **La targeta 1 neix desplegada a tots els breakpoints.** A `lg` passa a `lg:flex-6`, `flex-col` i amb el títol horitzontal. Es plega només quan una altra targeta rep hover o focus, mitjançant `lg:group-has-[article:not(:first-child):hover]/list:*` i la seva variant `focus-within`. El contenidor porta `group/list` perquè la targeta 1 detecti les altres.
- **Proporció 6/1/1 (8/1/1 a la targeta 3).** Les desplegades pugen de `flex-3`/`flex-4` a `flex-6`/`flex-8`, en lloc de baixar les plegades per sota d'`1`: així no hi ha valors arbitraris decimals i les plegades queden a ~12,5 % de l'amplada.
- **ARIA coherent amb el que es veu.** El botó de la targeta 1 neix amb `aria-expanded="true"`. A `acordeon.js`, `syncAll` considera oberta la primera per defecte (sempre per sota de `lg`; a `lg`, tret que una altra targeta estigui activa), i els esdeveniments `pointerenter`, `pointerleave`, `focusin` i `focusout` ressincronitzen totes les targetes, no només la que rep l'esdeveniment.
- **Mòbil i tauleta:** títol `text-xl` fins a `md` i un cercle amb xevró (`lg:hidden`) dins del mateix `<button>`, centrat a l'extrem oposat. El xevró gira amb `group-has-aria-expanded:rotate-180` i respecta `motion-reduce`.
- **Text dels panells** de `text-base` (16 px) a `text-lg` (18 px), tret dels títols. Els `article` porten `overflow-hidden`.

## Conseqüències

- L'estat ARIA es calcula en un únic lloc (`syncAll`), de manera que l'atribut sempre reflecteix el que veu l'usuari.
- `overflow-hidden` pot retallar l'anell de focus (`ring-4`) dels elements enganxats a la vora de la targeta; si passa, afegir marge intern o `ring-inset`.
- `text-lg` no és un token capturat del manual (el cos és `text-body`, 22 px); queda com a ajust local fins a resoldre la discrepància 18/22 px.
- Continua sense provar-se en un dispositiu tàctil real ni amb lector de pantalla.
