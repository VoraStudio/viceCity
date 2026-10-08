# 043 — Sense parpelleig a les animacions d'entrada

**Stack**: css + js
**Estat**: acceptat
**Data**: 2026-10-08

## Context

Totes les animacions d'entrada creaven els seus `gsap.from(...)` només després de `document.fonts.ready` i de l'execució dels mòduls. Fins llavors els elements es pintaven visibles i després saltaven a `opacity: 0` per entrar animats: un parpelleig (FOUC) a totes les seccions. `js-flag.js` ja afegeix la classe `js` a `<html>` de manera síncrona.

## Decisió

- **`assets/css/reveal.css`** (CSS tradicional, enllaçat al `<head>` de les 5 pàgines): sota `.js` i dins de `@media (prefers-reduced-motion: no-preference)` s'amaguen amb **`visibility: hidden`** tots els elements que anima el JS, escollits amb els ganxos `data-*` que ja existien. Es fa servir `visibility` i no `opacity` ni `display`: no canvia la maquetació i `from()` llegeix l'opacitat calculada, que ha de continuar sent 1.
- **`reveal()` a `script.js`**: `gsap.set(targets, { visibility: "visible" })`, cridat a cada `init…` **després** de crear el tween que posa l'estat inicial a `opacity: 0`, dins del mateix tick síncron: no hi ha cap pintat entre els dos. `title-reveal.js` fa el mateix amb els títols.
- **Failsafe de 3 s**: `animation: reveal-failsafe 0s 3s forwards` torna visible l'element si el JS falla o si s'oblida algun element. Els usuaris amb `prefers-reduced-motion` mai tenen elements ocults.
- **Elements niuats**: un fill amb el seu propi `visibility: hidden` no hereta la visibilitat d'un pare ja revelat, així que cada objectiu niuat (la descripció de Contacte, el títol de la franja del blog...) es revela explícitament.
- Creuament verificat: tot selector ocult per CSS es revela al JS i tot objectiu animat és ocult per CSS.

## Conseqüències

- Qui afegeixi una animació nova ha d'afegir el seu selector a `reveal.css` i cridar `reveal()`; si no, l'element apareix als 3 s sense animació.
- Si una secció té un element obligatori que falta, no es revela res d'aquella secció fins al failsafe.
- Amb una càrrega de fonts molt lenta, el contingut apareix als 3 s sense animació.
- No provat en un navegador, només comprovat el codi.
