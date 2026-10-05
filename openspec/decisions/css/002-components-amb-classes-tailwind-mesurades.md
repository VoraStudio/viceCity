# 002 — Components amb classes Tailwind mesurades

**Stack**: css
**Estat**: acceptat (Part 2 de l'INTAKE)
**Data**: 2026-10-05

## Context

El `ui.pdf` del projecte és un A4 vertical amb el contingut girat i `ui_metrics.py` només hi detecta 4 components. El mateix disseny en format apaïsat 1440×810 (còpia de `sdd-vd/sdd-local`) se'n mesura 23. La nota del PDF i la mesura donaven valors diferents per al radi de les targetes (30 px / 23 px) i per a la vora dels camps (1,5 px / 0,5 px).

## Decisió

- Els components es defineixen amb les classes de Tailwind que surten de la mesura de `ui_metrics.py` sobre el PDF apaïsat.
- Regla de Pau: **sempre mesures de Tailwind**, mai valors arbitraris. Quan nota i mesura difereixen, guanya la classe Tailwind mesurada: targeta `rounded-3xl`, camp amb `border`.
- Els colors `SENSE_TOKEN` d'etiquetes es resolen amb el token més pròxim (`ink`). El Gris `#B4B4B4` passa a ser el token `gray`.
- Detall a `tokens.md`.

## Conseqüències

- Estats hover, active i disabled sense especificar; no s'inventen.
- Pendents sense token: `#8C8C8C`, `#898989` i `#D8D8D8`.
- La mesura prové d'una còpia fora del projecte. Si es reexporta el `ui.pdf` apaïsat, caldria repetir-la.
