# 020 — Arrossegament del carrusel en tauleta i mòbil

**Stack**: js
**Estat**: acceptat
**Data**: 2026-10-07

## Context

La decisió 019 deixa el carrusel d'«Especialització» només a escriptori, amb la roda. En tauleta i mòbil no hi ha roda i es volia el mateix apilat cíclic, manejat amb el dit.

## Decisió

- **Un segon bloc `gsap.matchMedia`** a `stack-carousel.js`, actiu per sota de `lg` (`max-width: 63.999rem`) i sense `prefers-reduced-motion`. El bloc d'escriptori no es toca.
- **`Draggable` 3.15.0** (script amb versió fixa i SRI) sobre un element invisible, amb `trigger: stage` i `type: "y"`. Mentre s'arrossega, `activa = inici - (y - yInici) / 116`, així que les targetes segueixen el dit; el `116` és la mateixa separació vertical que fa servir `render`. En deixar anar, `activa` s'anima a l'enter més proper (0,4 s, `power2.out`).
- **`render` i el bucle `wrap` es reutilitzen** tal qual: un únic lloc decideix la posició de cada targeta.
- **`Draggable` acumula la `y` entre arrossegaments**, per això es guarda `yInici` a `onPress` i es mesura el desplaçament des d'aquest punt. Sense això, el segon arrossegament començaria desplaçat.
- Marge superior del contenidor: `mt-16` a totes les mides (abans `mt-10` / `md:mt-14`), per separar la pila de la descripció.

## Conseqüències

- Amb el dit sobre la pila no es fa scroll de pàgina (`Draggable` captura el gest vertical); se'n surt tocant fora d'ella.
- Continua sense teclat, `aria-live` ni `inert` per a les targetes de darrere.
- **No provat en un dispositiu tàctil real**, només comprovat el codi. En mòbil el text de cada targeta és més llarg: `min-h-88` i `h-136` poden quedar-se curts.
- Els comentaris són en català i els noms de variables en castellà, com la resta del mòdul.
