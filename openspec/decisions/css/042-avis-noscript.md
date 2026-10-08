# 042 — Avís `<noscript>`

**Stack**: html + css
**Estat**: acceptat
**Data**: 2026-10-08

## Context

Sense JavaScript no hi ha estils (Tailwind va al navegador), ni header ni footer (Web Components, decisió d'arquitectura 004). Un usuari sense JS, o un rastrejador que no l'executa, veia contingut sense format i sense cap forma de navegar.

## Decisió

- **Bloc `<noscript>` a les 5 pàgines**, just abans de `<site-header>`: un missatge («Aquesta web necessita JavaScript per mostrar-se correctament.»), enllaços a Inici, Blog, Avís legal, Privacitat i Cookies, i el correu i el telèfon.
- **CSS tradicional en un fitxer a part**, `assets/css/noscript.css`, enllaçat des de dins del propi `<noscript>`: només es descarrega quan cal. Fa servir variables a `:root` i és mobile first. No es pot fer servir Tailwind perquè sense JS no es genera cap classe, i així es compleix la regla de no embeure CSS.
- El telèfon porta `&nbsp;` entre els grups de xifres perquè no es parteixi.

## Conseqüències

- Només mitiga el problema: la resta de la pàgina continua sense estils. La solució de fons seria precompilar Tailwind amb el seu CLI, però **es descarta**: l'allotjament és bàsic i no hi ha compilador, així que es manté el mode navegador amb CDN.
- El bloc està copiat a les 5 pàgines, perquè sense JS no es pot reutilitzar amb un component. És petit (14 línies).
- Es prova a Chrome: `F12`, `Ctrl+Shift+P`, «Disable JavaScript» i recarregar. No provat.
