# 018 — Estil unificat dels botons CTA

**Stack**: css + js
**Estat**: acceptat
**Data**: 2026-10-07

## Context

Els botons amb `data-ripple` (31 a les 5 pàgines) barrejaven fons, vores i pesos de font. En afegir una vora d'1-2 px apareixien fragments clars a les corbes, i de vegades el text es quedava blanc després de passar el ratolí molt ràpid.

## Decisió

- **Dues variants, les del hero.**
  - Fosca: `bg-purple-700 text-white font-bold`; el ripple emplena en `purple-100` i el text passa a `purple-700`.
  - Clara: `bg-purple-100 text-purple-700 font-bold`; el ripple emplena en `purple-700` i el text passa a blanc.
  - La variant es tria pel fons del botó; es conserven els extres de cadascun (amplades, `disabled`, `mt-auto`, `menu:inline-flex`). El de la targeta 3, abans `bg-paper`, passa a la variant clara.
- **Contorn amb `outline-2 -outline-offset-1 outline-purple-700`** en lloc de `border`. Un `border` es pinta sobre el fons del propi botó i, a les corbes, deixa veure aquest fons per antialiasing. L'`outline` amb offset negatiu es pinta damunt de l'emplenament i se solapa 1 px, així que tapa la ranura entre el retall del ripple i el contorn.
- **Sense `border-*`, `ring-*` ni `bg-clip-padding`** als botons. Es van provar les tres i cadascuna reintroduïa una ranura: `border` barreja amb el fons, `ring` toca el retall sense solapar i `bg-clip-padding` deixa una costura entre fons i vora.
- **`ripple.js`: `overwrite: true` als tweens del text.** Amb `overwrite: 'auto'` un tween retardat (`delay: 0.15`) no es cancel·lava en sortir ràpid i deixava el text en blanc amb l'emplenament ja retirat.
- Es manté `rounded-full`; es va provar `rounded-xl` i es va descartar per estètica.

## Conseqüències

- El ripple continua sent fràgil davant de vores: el retall amb `overflow-hidden` i qualsevol contorn es toquen al mateix píxel. Es va descartar, per ara, treure el contorn o reescriure l'efecte amb `clip-path`.
- `-outline-offset-1` és imprescindible: sense ell reapareix la ranura clara en el hover del botó clar.
- El contorn és del mateix color que el fons a la variant fosca, així que només aporta el solapament; a la clara sí que es veu com a contorn.
- `font-bold` en lloc de `font-medium` s'aparta del manual (`tokens.md` fixa `font-medium`); queda com a ajust local.
- No s'ha provat en un dispositiu tàctil real.
