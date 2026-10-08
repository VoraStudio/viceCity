# 045 — Separació del carrusel amb un sol `matchMedia`

**Stack**: js
**Estat**: acceptat
**Data**: 2026-10-08

## Context

La separació vertical entre cards (`separacion`) la escrivien tres instàncies independents de `gsap.matchMedia()` (mòbil, tauleta i escriptori), i cadascuna restaurava el valor d'escriptori (116) en revertir-se. En canviar de breakpoint (per exemple de tauleta a mòbil) l'ordre d'execució entre instàncies no està garantit: podia quedar 116 en mòbil. A més, `stack-carousel.js` fallava amb un `TypeError` si la pàgina no tenia `[data-stack-carousel]`.

## Decisió

- **Un sol `gsap.matchMedia().add({ mobil, tauleta, escriptori }, ({ conditions }) => ...)`**: la separació es calcula a partir de `conditions` (70 en mòbil, 64 en tauleta i 116 en escriptori) i no es muta des de diversos llocs. Les tres condicions cobreixen tots els amples, així que la funció s'executa a cada canvi.
- **Guarda `if (!section) return;`**: el codi passa a viure dins d'una funció `initStackCarousel`, com la resta de mòduls, i el mòdul no falla en pàgines sense carrusel.

## Conseqüències

- Els blocs de comportament d'escriptori i de tauleta/mòbil continuen sent instàncies pròpies de `matchMedia`; només la separació s'ha unificat.
- Al canviar de breakpoint, la separació canvia però la pila no es redibuixa fins a moure una card (ja passava).
- Corregeix la inconsistència de la decisió 033.
- No provat en un navegador, només comprovat el codi.
