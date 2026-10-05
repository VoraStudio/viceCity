# 003 — Fonts en WOFF2 variable

**Stack**: css
**Estat**: acceptat
**Data**: 2026-10-05

## Context

Les fonts de marca (Red Hat Display i Quicksand) són locals a `assets/fonts/` i es distribuïen com a TTF variable: 126 624 B i 97 112 B. El pes de les fonts afecta el LCP (objectiu < 2,5 s) i el pes de lliurament.

## Decisió

- La web carrega només els dos fitxers **WOFF2 variables**, sense subconjunt de glifos: `Quicksand-VariableFont_wght.woff2` (53 584 B, `wght` 300–700) i `RedHatDisplay-VariableFont_wght.woff2` (40 356 B, `wght` 300–900).
- Conversió només de format amb `fonttools` i `brotli`, sense canviar noms ni glifos. Es manté la llicència OFL.
- Els `.ttf` d'origen, els estàtics i la cursiva es conserven al repo i no es carreguen.
- `@font-face` dins del mateix bloc `<style type="text/tailwindcss">` de `index.html`, al costat de l'`@theme`, amb `font-display: swap` i `preload` dels dos fitxers. No hi ha cap fitxer CSS apart: una petició menys, i els tokens `--font-head` i `--font-body` queden junt a la definició de les fonts.

## Conseqüències

- Lectura àmplia de l'excepció d'`AGENTS.md` (que només esmenta l'`@theme`): decisió de Pau, 2026-10-05.
- Reducció del 58 % del pes de les fonts respecte al TTF.
- Tots els navegadors del lliurament (Chrome, Firefox, Safari, Edge) admeten WOFF2; no cal fallback a TTF.
- `fonttools` i `brotli` s'han instal·lat només com a eina local de conversió.
