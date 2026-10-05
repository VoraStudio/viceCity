# 004 — Components sempre amb els tokens capturats

**Stack**: css
**Estat**: acceptat
**Data**: 2026-10-05

## Context

La primera versió de `index.html` en Tailwind afegia estils propis que no són a `ui.pdf` ni a `tokens.md`: botons en Red Hat bold, un degradat a la targeta fosca, etiquetes amb dos estils, targetes amb vora `lav-100` i focus només amb contorn.

## Decisió

Regla de Pau: **tot component s'implementa amb els tokens capturats**, sense estils inventats.

- Botons: Quicksand de pes mitjà (`font-body font-medium`).
- Targeta fosca: `bg-purple-700` pla, sense degradat. Botó sobre ella: `bg-paper text-purple-700`.
- Etiquetes: `font-body text-label font-bold`, sense majúscules, un sol estil.
- Targetes de pàgina: el token estàndard `rounded-3xl border-2 border-purple-700`.
- Focus: contorn `purple-500` amb halo suau (`ring-4 ring-purple-500/30`), com diu la nota del manual.

## Conseqüències

- Queden sense resoldre, perquè `ui.pdf` no ho defineix: les superfícies lavanda amb text (`bg-lav-100/40`) davant de la regla «els tons lavanda no porten text», i els dos botons primaris a la primera pantalla.
- El nou focus no s'ha pogut veure renderitzat.
