# 005 — La carpeta `docs/` deixa de ser pública

**Stack**: arquitectura
**Estat**: acceptat
**Data**: 2026-10-08

## Context

La decisió 003 versionava `docs/` (disseny, identitat visual i manual de marca, uns 50 MB en 22 fitxers). El repositori té un remot a GitHub (`VoraStudio/viceCity`) i aquest material és del client: no ha de ser públic. A més, el servidor local (XAMPP) servia `/docs/...`.

## Decisió

- **`docs/` s'afegeix a `.gitignore`** i es deixa de versionar (`git rm --cached`): els fitxers continuen al disc, però ja no són al repositori.
- **`docs/.htaccess` amb `Require all denied`** perquè Apache no serveixi la carpeta en local. Depèn que Apache permeti `.htaccess`; no s'ha comprovat.
- El desplegament a GitHub Pages ja exclou `docs` (`.github/workflows/pages.yml`).
- `tokens.md` i algunes decisions hi fan referència: ara és material només local.

## Conseqüències

- **`docs/` ja es va pujar a `origin/main`** (15 fitxers a l'última rama coneguda): deixar de versionar-lo **no l'esborra de l'historial de GitHub**. Si el repositori és públic, cal fer-lo privat o reescriure l'historial (`git filter-repo` amb force push). No s'ha fet cap push ni cap reescriptura.
- Qui clonï el repositori no tindrà els dissenys: s'han de compartir per un altre canal (emmagatzematge privat).
- **Les TTF d'origen s'han mogut** de `assets/fonts` a `docs/Identitat Visual/fonts-origen/` (només en local) i `tokens.md` s'ha actualitzat. A `assets/fonts` es queden els `.woff2` amb la seva llicència (`OFL.txt`), que la SIL OFL obliga a acompanyar.
- **`.atl/`** (registre d'skills amb rutes locals de Windows) s'afegeix també a `.gitignore` i es deixa de versionar.
