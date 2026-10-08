# 012 — Efecte ripple als CTA

**Stack**: css + js
**Estat**: acceptat
**Data**: 2026-10-06

## Context

Els botons de la web no tenien animació de hover. Es vol l'efecte «botó líquid» de l'exemple 4 de `03a_interaccion.html`: un cercle que neix on entra el cursor i cobreix el botó.

## Decisió

- **GSAP** es carrega com a script normal (`gsap@3`, jsDelivr) abans dels mòduls, de manera que `gsap` existeix com a global quan s'executen.
- **Marcatge:** cada CTA és `relative overflow-hidden` i conté un cercle (`data-ripple-fill`, `size-2.5`, `absolute top-0 left-0`) i el text envoltat en `data-ripple-text` amb `relative z-10`. L'`<a>` porta `data-ripple` i `data-ripple-text-color` amb el color final del text, com a variable del tema.
- **`assets/js/modules/ripple.js`:** `initRipple` recorre els `[data-ripple]`; a `mouseenter` col·loca el cercle a la posició del cursor relativa al botó i l'escala a 60 (`power2.out`), amb el text canviant de color amb retard; a `mouseleave` l'encongeix cap al punt de sortida.
- **No es fa servir `scale-0` de Tailwind:** v4 l'implementa amb la propietat `scale`, que es multiplica amb el `transform` de GSAP i deixaria el cercle a escala 0. L'estat inicial el posa `gsap.set`.
- **Colors:** els primaris emplenen amb `purple-300` i el text passa a `purple-700`; els secundaris emplenen amb `purple-700` i el text passa a blanc; el de la card fosca emplena amb `purple-500`.
- Aplicat als 9 CTA: hero (2), header (2), menú mòbil (1), card 3 (1), blog (1) i footer (2).
- Respecta `prefers-reduced-motion`: sense animació si l'usuari la desactiva.

## Conseqüències

- Els temps del text porten retard perquè, amb el cercle encara petit, el text ja canviat quedava lila sobre lila i invisible.
- `scale: 60` és una estimació per a botons d'uns 240 px; amb una entrada per una cantonada podria no cobrir.
- `purple-700` sobre `purple-300` té menys contrast que sobre blanc; sense mesurar.
- L'efecte fa servir `mouseenter` i `mouseleave`: en tàctil no es veu, tot i que el botó continua funcionant.
- No s'ha pogut veure l'animació a velocitat real: la pestanya de proves no es pinta i GSAP avança lent.
