# 046 — Ancoratge centrat del formulari i més espai a les cards de recursos

**Stack**: css
**Estat**: acceptat
**Data**: 2026-10-08

## Context

Dos ajustos d'espaiat sense relació entre ells. En saltar a `#contacte` o a `#demo`, el navegador alineava el formulari amb la vora superior de la finestra, i no quedava centrat. A mòbil i tauleta, les cards 2 i 3 de l'acordió de recursos («Pensat per a les administracions públiques» i «El futur de la gestió tributària comença avui») tenien els elements del contingut massa junts.

## Decisió

- **`scroll-margin-top` a la secció `#contacte` i al formulari** (`#demo`): `scroll-mt-20` a mòbil i tauleta i `lg:scroll-mt-[16vh]` a escriptori. El 16 % de l'alçada de la finestra aproxima el centrat d'un formulari d'uns 610 px en una finestra d'uns 910 px. Es fa servir `vh` perquè el centrat depèn de l'alçada de la finestra; en mòbil no, perquè el formulari és més alt que la finestra i el que importa és veure'n el principi.
- **Més espai dins de les cards 2 i 3 de recursos**: el contenidor del contingut passa de `gap-5` a `gap-8 lg:gap-5`: 2 rem a mòbil i tauleta i, des de `lg`, el valor d'abans, perquè a escriptori les cards són columnes que s'expandeixen amb el hover.
- **No es toca l'espai entre títol i contingut**: el dona el `gap-5` de l'`<article>` i s'aplica també amb la card plegada; pujar-lo afegiria espai buit a les cards tancades.

## Conseqüències

- El 16vh és una aproximació: si en una pantalla concreta el formulari queda alt o baix, s'ajusta aquest valor.
- La primera card de recursos continua amb `gap-5`; no es va demanar igualar-la.
- No provat en un navegador, només comprovat el codi.
