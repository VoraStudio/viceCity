# 028 — Dades de contacte i animació d'entrada de «Contacte»

**Stack**: html + js
**Estat**: acceptat
**Data**: 2026-10-08

## Context

Continua les decisions 021-027. La secció `#contacte` tenia a la columna esquerra només el títol i un paràgraf. S'afegeixen les dades de contacte i la secció entra amb una timeline a `script.js`.

## Decisió

- **Llista de contacte a la columna esquerra**: telèfon, correu i adreça, en columna, cadascun amb una icona SVG en un cercle (`aria-hidden`, `stroke="currentColor"`), una etiqueta i la dada. Les dades es copien del footer perquè no hi hagi dues versions; el telèfon fa servir `&nbsp;` entre els grups de xifres perquè no es parteixi, i l'adreça va en un `<address>` amb `not-italic`.
- **Les dues columnes entren alhora per costats oposats**: l'esquerra amb fade des de l'esquerra (`x: -80`) i el formulari des de la dreta (`x: 80`), totes dues d'1 s amb `power3.out`. El formulari arrenca amb la posició `"<"` (mateix inici que el tween anterior).
- **`ScrollTrigger`** a `top 70%` i `end: "bottom top"` de la secció, amb `data-contact`, `data-contact-info` i `data-contact-form`.
- **`overflow-x-clip` a `#contacte`**: les dues columnes arrenquen fora de l'amplada.
- **`clearProps: "transform,opacity"`** a totes dues, per no deixar estils inline sobre el formulari.

## Conseqüències

- Les dades són placeholder (copiades del footer): si canvien allà, cal canviar-les també aquí.
- Els CTAs que apunten a `#demo` fan scroll al formulari: si es premen mentre l'animació és en curs, el scroll pot aterrar una mica desplaçat fins que acaba.
- No provat en un navegador, només comprovat el codi.
