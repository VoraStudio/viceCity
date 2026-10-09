# 044 — Botó de pausa al vídeo del hero

**Stack**: html + js
**Estat**: acceptat
**Data**: 2026-10-08

## Context

El vídeo de la demo (decisió 034) es repeteix durant més de 5 s sense cap control: incompleix WCAG 2.2.2 (nivell A). Vicity ven a administracions públiques, que a Espanya han de complir WCAG 2.1 AA. A més, `video.play()` sense `.catch()` deixava un error a la consola quan el navegador bloquejava la reproducció.

## Decisió

- **Botó de pausa dins del marc del vídeo**, a baix a la dreta, amb `aria-pressed` i l'etiqueta fixa «Pausa el vídeo» (l'estat el porta `aria-pressed`, no el text). Mostra la icona de pausa o la de reproducció amb la variant `group-aria-pressed:`. Porta el mateix focus visible que la resta de la web.
- **`initHeroVideo` sincronitza el botó amb els esdeveniments `play` i `pause`** del vídeo, de manera que també reflecteix les pauses automàtiques.
- **L'elecció de l'usuari mana**: si l'usuari pausa el vídeo, en tornar a entrar a pantalla no es reprèn sol (`pausedByUser`). Només es reprodueix i es pausa per scroll mentre l'usuari no l'hagi pausat.
- **`prefers-reduced-motion`**: el vídeo no arrenca (queda el pòster) i el botó permet iniciar-lo. Per això `initHeroVideo` es crida fora del filtre de moviment reduït; només l'auto-reproducció per scroll hi queda condicionada.
- **`play()` amb `.catch(syncToggle)`**: si el navegador bloqueja la reproducció, el botó es torna a sincronitzar.

## Conseqüències

- Substitueix la nota «falta un botó de pausa» de la decisió 034.
- Segueix sense haver-hi una descripció textual equivalent del vídeo (WCAG 1.2.1); només porta `aria-label`.
- No provat en un navegador, només comprovat el codi.

**Nota (2026-10-09):** `initHeroVideo` s'ha generalitzat en `initVideoPlayers` (decisió 051), que gestiona tots els reproductors `[data-video-player]`; el comportament descrit aquí no ha canviat.
