# 010 — Acordió de les targetes de recursos

**Stack**: css
**Estat**: acceptat (parcial)
**Data**: 2026-10-06

## Context

Les tres targetes de `#recursos` («Dades que impulsen decisions», «Pensat per a les administracions públiques», «El futur de la gestió tributària comença avui») es mostraven sempre desplegades en un grid. Es vol que només es vegi el títol i que la resta aparegui en expandir.

## Decisió

**Escriptori (`lg`): acordió horitzontal.**
- El contenidor passa de `grid` a `flex` (`flex-col` a la base, `lg:flex-row`). Sense `display: flex`, `flex-col` i `flex-row` no fan res.
- Cada targeta és `lg:flex-1` en repòs i `lg:hover:flex-3` en hover (la tercera `lg:hover:flex-4`). El repartiment de l'espai el fa `flex-grow`; `flex-shrink` n'és només la conseqüència. Amb `min-w-0` perquè no es bloquegi amb text llarg, i `transition-[flex] duration-1000`.
- Alçada fixa `lg:h-[32rem]` a les tres per trencar la dependència del contingut.
- En repòs el títol es gira (`lg:[writing-mode:vertical-rl] lg:rotate-180`, amb `lg:h-full`) i en hover torna a horitzontal. El contingut s'amaga amb `lg:opacity-0` i apareix amb `lg:group-hover:opacity-100` i `lg:group-focus-within:opacity-100`.
- Cada `<article>` és `group`: el hover passa a la targeta, però el canvi s'aplica als seus fills.

**Tauleta i mòbil (per sota de `lg`): acordió vertical.**
- La base és columna: `flex-col`, amb `lg:flex-row lg:hover:flex-col` només des de `lg`.
- El contingut s'envolta en `grid grid-rows-[0fr]` que passa a `group-hover:grid-rows-[1fr]` (i `group-focus-within`) amb `transition-[grid-template-rows]`. És el truc per animar una alçada: `height: auto` no s'anima, però `0fr` a `1fr` sí.
- El fill interior porta `min-h-0 overflow-hidden lg:overflow-visible`, i l'envoltori `lg:contents`, de manera que a `lg` desapareix de la maquetació i el disseny d'escriptori no canvia.

## Conseqüències

- **No hi ha obertura per clic o toc.** A Tailwind v4 `hover:` només actua en dispositius amb hover, de manera que en un mòbil tàctil les targetes no s'obren. Falta un botó al títol amb `aria-expanded` i `aria-controls`, i JavaScript que alterni l'estat.
- **Escriptori sense tancar.** Mesurat al navegador a 1366 px, el títol vertical s'encongia a la seva paraula més llarga (130–219 px) perquè el contingut invisible té `min-height: auto` i no cedeix espai. La correcció proposada és `lg:shrink-0` als `<h2>` i `lg:overflow-hidden` als `<article>`. No està aplicada.
- `writing-mode` no s'anima: el títol canvia d'orientació de cop.
- El `gap-5` de l'`<article>` deixa 20 px entre el títol i el contingut plegat.
- La targeta 2 fa servir `bg-lav-100/40` amb text, cas obert des de la decisió 004.
- Animar `grid-template-rows` requereix Chrome 107, Firefox 66 o Safari 16; en navegadors anteriors el contingut s'obre de cop.
- No s'ha pogut comprovar el comportament en tauleta i mòbil: la finestra del navegador de proves no baixa de 1360 px.
