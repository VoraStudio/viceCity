# 036 — Formulari de contacte translúcid

**Stack**: css
**Estat**: acceptat
**Data**: 2026-10-08

## Context

El formulari de `#contacte` tenia `bg-paper` (`#f7f8f6`), el mateix color que la part alta del degradat de la secció (decisió 029), així que a dalt gairebé no es distingia del fons.

## Decisió

- `bg-paper` passa a **`bg-white/40`**: blanc pur al 40 % d'opacitat. Deixa veure el degradat de la secció: gairebé blanc a dalt i lavanda clara a baix. Es manté la vora `border-lav-100`.

## Conseqüències

- El contrast del text dels camps depèn ara del fons de la secció; cal comprovar-ho amb els `input` i `textarea` al navegador.
- No provat en un navegador, només comprovat el codi.
