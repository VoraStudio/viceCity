# 001 — Estructura de projecte: un repo per web

**Stack**: arquitectura
**Estat**: acceptat (amb un punt pendent: imatges i vídeos)
**Data**: 2026-10-05

## Context

Cada web necessita historial, remot i context d'Engram propis. Calia fixar on viu el projecte i com s'organitzen els fitxers, i què es versiona.

## Decisió

- Una carpeta exclusiva per web a `C:\xampp\htdocs\webs\<web>`, amb el seu propi repo (org `VoraStudio`). Aquest projecte: `webs\viceCity`, remot `VoraStudio/viceCity`.
- Engram deriva el projecte del remot git: aquí es detecta `vicecity` sense configuració addicional.
- Estructura:
  - `index.html`
  - `assets/css`, `assets/js/modules`, `assets/fonts`, `assets/img`, `assets/video`
  - `docs/` (ignorada): PDFs, branding, disseny d'origen
  - No s'usa cap carpeta `source/`.
- Es versiona el que cal per desplegar; el material d'origen i el sensible s'ignoren (`.gitignore`).
- **Logos: dins del repo** (decisió de Pau, 2026-10-05). Viuen a `assets/img/Logotips`.

## Conseqüències

- `docs/` només existeix en local: la còpia versionada dels tokens és `tokens.md`.
- Els `.gitkeep` mantenen les carpetes buides de `assets/` al repo.

## Pendent

Imatges i vídeos finals: dins del repo o ignorats i gestionats fora (la norma LOPD de VoraData diu que els brand assets de clients es gestionen fora, i els logos hi són per decisió expressa). Per ara `assets/img` i `assets/video` són versionables. Decideix l'equip.
