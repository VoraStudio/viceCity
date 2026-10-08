# 014 — Acordió accessible i degradació sense JavaScript

**Stack**: css + js
**Estat**: acceptat
**Data**: 2026-10-07

## Context

L'acordió de `#recursos` (decisions 010 i 011) arrencava amb `aria-expanded="false"` escrit a mà i les targetes col·lapsades per CSS. Dos problemes: si el mòdul JS no carregava, el contingut quedava plegat sense manera d'obrir-lo, i `aria-expanded` només canviava amb el clic, tot i que a escriptori les targetes s'obren amb hover o focus.

## Decisió

- **`assets/js/js-flag.js`**: script extern, síncron i mínim al `<head>` que afegeix la classe `js` a `<html>`. No és un `<script>` inline, així que respecta la regla; i en executar-se abans del primer pintat evita veure el contingut desplegat i replegant-se.
- **`@custom-variant js (&:where(.js *))`** al bloc `text/tailwindcss`. Només l'estat plegat en depèn: `js:grid-rows-[0fr]` i `js:lg:opacity-0`. Sense la classe `js`, el contingut es veu. El `:where()` deixa l'especificitat a zero, de manera que `group-has-aria-expanded:grid-rows-[1fr]`, `lg:group-hover:opacity-100` i `lg:group-focus-within:opacity-100` continuen guanyant sense dependre de l'ordre al CSS generat.
- **`aria-expanded` ja no és a l'HTML**: l'escriu `acordeon.js` en arrencar, perquè sense JS el botó no obre res i declarar-lo «tancat» seria fals. `aria-controls` sí que es queda a l'HTML.
- **`acordeon.js`** sincronitza `aria-expanded` amb el que es veu: per sota de `lg` alterna amb el clic; des de `lg` (`min-width: 64rem`) segueix hover i focus (`pointerenter/leave`, `focusin/out`) i el clic no fa res, per no contradir el CSS, que ja manté la targeta oberta amb el cursor a sobre. En canviar de breakpoint es ressincronitza.
- **`inert`** al panell plegat per sota de `lg`: l'enllaç del CTA de la tercera targeta ja no és enfocable ni es llegeix mentre està ocult.
- **Títols verticals**: `<h2>` amb `<button>` a dins. El nom accessible és el text del botó, i `writing-mode` + `rotate-180` no canvien l'ordre del DOM, de manera que els lectors de pantalla el llegeixen en ordre normal. No ha calgut tocar l'HTML.

## Conseqüències

- Sense JavaScript no es carrega Tailwind (és l'script de navegador), així que la pàgina sencera queda sense estils i el contingut continua llegible. La classe `js` cobreix el cas intermedi: Tailwind carrega però el mòdul no.
- A escriptori un lector de pantalla veu `aria-expanded` canviar amb hover o focus, no amb Enter. És el comportament visual real.
- Els panells continuen a l'arbre d'accessibilitat a escriptori encara que tinguin `opacity-0` (és intencionat: el focus els obre).
