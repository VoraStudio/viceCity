# 011 — Obertura per clic de l'acordió en tauleta i mòbil

**Stack**: css + js
**Estat**: acceptat
**Data**: 2026-10-06

## Context

La decisió 010 obria les targetes de `#recursos` amb `group-hover`, que a Tailwind v4 va dins de `@media (hover: hover)`: en un mòbil o tauleta tàctil no s'aplica. Comprovat al CSS generat. A més, les targetes no eren enfocables, de manera que tocar-ne una no activava `group-focus-within`.

## Decisió

- **El títol passa a ser un `<button>`** dins de l'`<h2>`: `type="button"`, `aria-expanded="false"`, `aria-controls="recursos-panel-N"` i `data-accordion-toggle`. Cada panell porta l'`id` corresponent.
- **L'estat viu a ARIA.** L'envoltori plegable s'obre amb `group-has-aria-expanded:grid-rows-[1fr]`, que Tailwind genera com `:is(:where(.group):has([aria-expanded="true"]) *)`. No hi ha una classe `open` duplicada. Comprovat al navegador: passa de `0fr` a `1fr` en canviar l'atribut.
- **Es treuen `group-hover` i `group-focus-within` de l'envoltori.** Amb ells, el hover i el focus mantenien la targeta oberta encara que `aria-expanded` valgués `false`, i el segon clic no tancava. Per sota de `lg` queda una sola font de veritat.
- **JavaScript** a `assets/js/modules/acordeon.js`: `initAccordion` selecciona els botons amb `selectAll`, surt aviat si no n'hi ha cap i, en fer clic, llegeix `aria-expanded` com a text i escriu el contrari. Es carrega amb `<script type="module">` al costat de `nav.js`.
- A `lg` no canvia res: l'envoltori és `display: contents` i l'efecte horitzontal continua fent servir `lg:group-hover` i `lg:group-focus-within`.

## Conseqüències

- Per sota de `lg` el hover ja no obre les targetes: només el clic, el toc o el teclat (Enter o Espai). És el mateix comportament amb ratolí, dit i teclat.
- `return` no es pot fer servir a nivell superior d'un mòdul (`SyntaxError: Illegal return statement`, comprovat amb Node), per això l'early return viu dins d'`initAccordion`.
- Si el selector del JS no coincideix amb l'atribut de l'HTML, l'script falla en silenci, perquè la llista queda buida i surt per l'early return.
- **Pendent:** l'arxiu es diu `acordeon.js` i les convencions demanen noms en anglès (`accordion.js`).
- **Pendent a `lg`:** `lg:shrink-0` als `<h2>` i `lg:overflow-hidden` als `<article>` perquè el títol vertical ocupi tota l'alçada.
- No s'ha provat en un mòbil real.
