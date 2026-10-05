# 001 — Font dels tokens i escala tipogràfica

**Stack**: css
**Estat**: acceptat (Part 1 de l'INTAKE)
**Data**: 2026-10-05

## Context

Calia fixar d'on surten els tokens de disseny i resoldre una contradicció del manual d'estil sobre la mida de l'etiqueta.

## Decisió

- Colors: `docs/Identitat Visual/Colors_Vicity.pdf`. Noms de token: pàg. 12 del Manual d'estil (`lav-100`, `lav-300`, `purple-500`, `purple-700`, `ink`, `paper`).
- Tipografia: Red Hat Display per a titulars i Quicksand per a text de cos i interfície, amb els fitxers locals a `assets/fonts/`.
- Escala: Títol 1 60 px, Títol 2 40 px, cos 22 px, etiqueta **12 px en bold (700)**. El «Quicksand 15» de la mateixa fila del manual es descarta.
- Referència versionada: `tokens.md`.

## Conseqüències

- Pendents de Pau: nom del token del Gris `#B4B4B4`, blau nit `#0D252F` (manual) davant de `#0B2530` (`index.html`), cos 22 px davant de 18 px, regla «els tons lavanda no porten text», pesos de Quicksand.
- Parts 2 (components) i 3 (seccions) de l'INTAKE pendents. Per a la Part 2 cal l'exportació apaisada de `ui.pdf`, o marcar els components com a «estimat».
