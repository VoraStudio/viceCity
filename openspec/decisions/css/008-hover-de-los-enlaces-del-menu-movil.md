# 008 — Efecte hover dels enllaços del menú hamburguesa

**Stack**: css
**Estat**: acceptat
**Data**: 2026-10-06

## Context

El menú d'escriptori marca l'enllaç amb hover mitjançant text en `purple-700` i una ratlleta de 16 × 4 px sota la paraula. El menú hamburguesa, visible per sota de 1360 px (també en portàtils amb ratolí i finestres estretes), no tenia aquest efecte.

## Decisió

- Els cinc enllaços de `#menu-mobil` reutilitzen les classes de l'escriptori: `relative transition-colors hover:text-purple-700 after:absolute after:bottom-0.5 after:left-1/2 after:h-1 after:w-4 after:-translate-x-1/2 after:rounded-full after:bg-purple-700 after:opacity-0 hover:after:opacity-100 motion-reduce:transition-none`.
- `relative` és necessari: la ratlleta és un `::after` i es posiciona respecte a l'enllaç.
- `w-fit` fa que l'enllaç mesuri el que el seu text més el `px-4`. Sense ell, l'enllaç és un bloc a tota l'amplada i `left-1/2` centra la ratlleta a la fila, no sota la paraula.
- El botó «Parla amb un especialista» del final del panell no canvia: no és un enllaç de navegació.

## Conseqüències

- L'àrea clicable de cada enllaç passa de la fila sencera al text més 16 px per costat. L'alçada (`py-3`) no canvia.
- A Tailwind v4 `hover:` només s'aplica en dispositius amb hover (`@media (hover: hover)`). En un mòbil tàctil l'efecte no apareix; per a feedback en tocar caldria `active:`.
- No s'ha pogut comprovar visualment: la finestra del navegador de proves no baixa de 1360 px i el panell mòbil queda ocult.
