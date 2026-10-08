# 032 — Degradat a les cards del carrusel

**Stack**: css
**Estat**: acceptat
**Data**: 2026-10-08

## Context

Les 4 cards del carrusel d'«Especialització» (decisions 019 i 020) eren blanques planes. Es vol un degradat amb la paleta de la marca, sense passar de `lav-50`.

## Decisió

- **`bg-linear-to-br from-white to-lav-50`** a les 4 cards, en lloc de `bg-white`: diagonal de blanc a lavanda molt clara.
- **Token nou `--color-lav-50: #ece3ff`** a l'`@theme` d'`index.html`. És el punt mitjà entre blanc i `lav-100` (`#d9c7ff`). Es van descartar les variants amb `lav-300` i amb parada intermèdia a `lav-100`: el degradat era massa intens per a les targetes apilades.

## Conseqüències

- El degradat és molt subtil. Si es veu pla, s'enfosqueix el token `--color-lav-50` i canvia a totes les cards sense tocar l'HTML.
- **`lav-50` és un valor propi, no surt de la guia d'identitat visual.** Si existeix un valor oficial, cal substituir-lo.
- El token només és a `index.html`, l'única pàgina que l'usa: cada pàgina té el seu propi `@theme` copiat.
- No provat en un navegador, només comprovat el codi.
