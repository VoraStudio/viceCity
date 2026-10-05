# 002 — Flux sdd-vd i base HTML intocable

**Stack**: arquitectura
**Estat**: acceptat
**Data**: 2026-10-05

## Context

El projecte parteix d'una plantilla (`vice-index.html`) i s'ha de treballar amb el flux `sdd-vd` de VoraData, que substitueix el flux anterior per a la fase INTAKE.

## Decisió

- El flux de treball és `sdd-vd` (INTAKE en 3 parts: tokens, components, seccions). BUILD i DELIVER segueixen el flux vigent de VoraData fins que existeixi `build.proposta.md`.
- `index.html` és la base de la web principal i **no es modifica**. El treball nou es construeix al voltant.
- L'entrada de disseny és la del propi projecte (`docs/Identitat Visual`, `docs/Disseny web`), no la zona de proves de `sdd-vd/sdd-local`.

## Conseqüències

- Qualsevol canvi que calgui fer a `index.html` (p. ex. el blau nit `#0B2530` davant de `#0D252F`) requereix decisió explícita.
- Un `@theme` de Tailwind v4 només funciona amb el script `@tailwindcss/browser@4`, que `index.html` no carrega: si es vol Tailwind, caldrà un segon HTML de treball o autoritzar el canvi.
