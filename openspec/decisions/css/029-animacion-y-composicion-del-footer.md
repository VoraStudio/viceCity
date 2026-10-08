# 029 — Animació, crèdit i composició responsive del footer

**Stack**: html + css + js
**Estat**: acceptat
**Data**: 2026-10-08

## Context

Continua les decisions 021-028. El footer entra amb una timeline a `script.js`, rep el crèdit de Vora Studio i es recompon en mòbil i tauleta.

## Decisió

- **Animació dels 6 blocs** (`data-footer-item`), amb `stagger: 0.15` i barreja de direccions: logo i lema des de l'esquerra, CTAs des de la dreta, Enllaços i Contacte des de baix, Legal i la franja final amb fade simple. La direcció la dona el valor de l'atribut (`left`, `right`, `up`, `fade`) i un objecte `footerDirections` la tradueix a `x` i `y` mitjançant valors per funció en un únic `from`.
- **Paraula `vicity` final** (`data-footer-logo`): segon pas de la timeline, puja amb `yPercent: 100` (3,5 s, `power3.out`, `"-=1.6"`). Emergeix per la vora inferior perquè la seva caixa té `overflow-hidden`.
- **Crèdit «Desenvolupat per»** al costat del copyright, amb el logo de Vora Studio (`assets/img/clients/vora.png`, `h-4`, `brightness-0 opacity-60`) enllaçat a `https://vorastudio.cat` amb `target="_blank"` i `rel="noopener noreferrer"`. L'enllaç porta `aria-label` que avisa de la pestanya nova. El copyright passa de `<p>` a un `<div>` amb dos `<p>`, perquè un `<p>` no pot contenir-ne un altre. Text en català, com la resta de la web.
- **Composició en mòbil i tauleta** (fins a `lg`): Enllaços i Contacte en dues columnes i Legal a sota, a tota l'amplada (`col-span-2`, `order-last`) amb els seus enllaços en fila i `flex-wrap`. Des de `md` el grid torna a tres columnes, però Legal passa a columna (`md:flex-col`, `md:order-none`). `order` només canvia l'ordre visual: el de l'HTML i el del teclat es mantenen.
- **Paraula `vicity`** més gran fins a `lg` (`text-[38vw] lg:text-[18vw]`) i amb menys buit a sobre (`pb-2` i `mt-0`, fins a `lg:pb-10` i `lg:mt-4`).

## Conseqüències

- A 38vw la paraula pot quedar més ampla que la pantalla i retallar-se pels costats (`overflow-hidden`): és un recurs visual, no s'ha mesurat.
- Les dades de contacte del footer són placeholder i es dupliquen a la secció «Contacte» (decisió 028).
- No provat en un navegador ni en dispositius reals, només comprovat el codi.
