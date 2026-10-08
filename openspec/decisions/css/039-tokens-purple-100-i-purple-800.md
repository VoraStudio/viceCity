# 039 — Tokens `purple-100` i `purple-800` a l'`@theme`

**Stack**: css
**Estat**: acceptat
**Data**: 2026-10-08

## Context

La web feia servir `bg-purple-100` (reble del ripple i fons dels botons de fletxa, 7 usos) i `purple-800/20` (vora de les cards, 8 usos), però cap dels dos tokens era a l'`@theme`: es renderitzaven amb la paleta per defecte de Tailwind i no amb la de la marca. `tokens.md` només captura `lav-100`, `lav-300`, `purple-500` i `purple-700`.

## Decisió

- **S'afegeixen a l'`@theme` de les 5 pàgines**: `--color-purple-100: #efe7ff` i `--color-purple-800: #432184`.
- **Els valors són derivats de la paleta, no capturats del manual de marca**: un to més clar de `lav-100` i un to més fosc de `purple-700`.

## Conseqüències

- L'aspecte canvia lleugerament respecte als colors per defecte de Tailwind que es feien servir.
- Si el manual de marca defineix aquests tons, cal substituir els valors i actualitzar `tokens.md`.
- L'`@theme` continua copiat a cada pàgina (el mateix canvi s'ha fet 5 vegades).
- No provat en un navegador, només comprovat el codi.
