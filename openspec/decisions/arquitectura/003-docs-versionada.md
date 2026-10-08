# 003 — La carpeta docs/ es versiona

**Stack**: arquitectura
**Estat**: acceptat; publicació pendent
**Data**: 2026-10-05
**Substituïda per**: arquitectura/005 (la carpeta docs/ deixa de versionar-se i de ser pública)

## Context

La decisió 001 ignorava `docs/` (PDFs, branding, disseny d'origen). Pau vol que el material de disseny quedi al repo i ha tret `docs/` del `.gitignore`.

## Decisió

- `docs/` deixa de ser a `.gitignore` i es versiona (13 fitxers, 49 MB: manual d'estil, colors, tipografia, `ui.pdf`, moodboard i dissenys de Home i Blog).
- Substitueix la part de la decisió 001 que deia que `docs/` s'ignora.

## Conseqüències

- El repo `VoraStudio/viceCity` és **públic** (comprovat el 2026-10-05: `HTTP 200` sense credencials). Publicar `docs/` exposaria material de disseny del client, i la norma LOPD d'`AGENTS.md` diu que els brand assets de client es gestionen fora del repo.
- Per això el commit és local i **no es fa push** fins que es decideixi: fer el repo privat, o mantenir `docs/` fora de GitHub.
- Els logos i `index.html` ja es van publicar en un push anterior.
