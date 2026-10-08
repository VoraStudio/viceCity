# 033 — Carrusel en tauleta amb fletxes i menys separació

**Stack**: css + js
**Estat**: acceptat
**Data**: 2026-10-08

## Context

La decisió 020 deixa el carrusel d'«Especialització» per sota de `lg` amb arrossegament, i la decisió de les fletxes de mòbil les amagava des de `md`. En tauleta (768 a 1023 px) no hi havia fletxes i la separació vertical entre cards era la d'escriptori.

## Decisió

- **Fletxes també en tauleta**: el contenidor `data-stack-controls` passa de `md:hidden` a `lg:hidden`. El JS ja les activava per sota de `lg` i reutilitza l'animació suau de mòbil (`mover`), així que no canvia la seva lògica.
- **Separació vertical de 64 px en tauleta**, només en aquest tram: un `gsap.matchMedia` amb `(min-width: 48rem) and (max-width: 63.999rem)` posa `separacion = 64` (el 55 % dels 116 px d'escriptori) i restaura 116 en sortir. Mòbil continua a 70.

## Conseqüències

- Mòbil (70) queda amb més separació que tauleta (64), una mica contraintuïtiu: si molesta, s'iguala.
- El contenidor manté la seva alçada fixa (`h-136`): amb les cards més juntes queda més aire a dalt i a baix de la pila.
- En canviar de breakpoint la separació canvia però la pila no es redibuixa fins a moure una card (ja passava en mòbil).
- No provat en un dispositiu real, només comprovat el codi.
