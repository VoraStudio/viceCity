# 051 — Vídeos a «Integració sense barreres» i a la secció d'IA

**Stack**: html + js
**Estat**: acceptat
**Data**: 2026-10-09

## Context

La secció d'integració tenia una il·lustració SVG estàtica i la d'IA una maqueta de xat en HTML. Pau ha aportat dos vídeos que les substitueixen: `assets/video/vicity-animacio.mp4` (1922×1386, 7 s, 747 KB, 60 fps, H.264) i `assets/video/vicity-conversa.mp4` (1932×1446, 12 s, 455 KB, 30 fps, H.264). El de conversa és la targeta blanca sencera (títol, etiquetes i xat).

## Decisió

- **Integració**: el vídeo omple el marc `data-integration-image` (`overflow-hidden rounded-3xl bg-lav-50`, sense `padding`, perquè el fons del vídeo ja és lavanda). L'`aria-label` de l'SVG passa al `<video>`.
- **IA**: el vídeo substitueix tota la targeta `data-ia-image` (`overflow-hidden rounded-3xl bg-white`). Com que el títol i les etiquetes ara són dins del vídeo, es mantenen en el DOM com a `sr-only` (`h3` i llista) i l'`aria-label` del vídeo descriu la conversa.
- **Pòsters** generats amb ffmpeg a 1200 px: `vicity-animacio-poster.webp` (fotograma de 5 s, 19 KB) i `vicity-conversa-poster.webp` (fotograma d'11,5 s, 33 KB). No s'usa el primer fotograma perquè és gairebé buit.
- **Proporció**: `aspect-[1922/1386]` i `aspect-[1932/1446]` amb `object-cover`, així el marc reserva l'espai abans de carregar i no hi ha salt de maquetació.
- **Atributs** `muted`, `loop`, `playsinline`, `preload="metadata"`, sense `autoplay`, com al hero (decisió 034).
- **Botó de pausa** (WCAG 2.2.2, duren més de 5 s en bucle) amb el mateix disseny que el del hero (decisió 044). A la secció d'IA va a dalt a la dreta, perquè a baix a la dreta tapava el camp de text del xat.
- **Refactor**: `initHeroVideo` passa a `initVideoPlayers`, que recorre `[data-video-player]` amb `[data-video]` i `[data-video-toggle]`. El hero, la integració i la IA comparteixen el mateix codi: reproducció en entrar a pantalla, pausa en sortir, i l'elecció de l'usuari mana (`pausedByUser`). Els hooks d'animació d'entrada `data-integration-image` i `data-ia-image` no canvien.

## Conseqüències

- Amb `prefers-reduced-motion` els vídeos no arrenquen sols (queda el pòster) i el botó permet iniciar-los. Sense JS queda el pòster fix.
- Els vídeos pesen 0,7 MB i 0,45 MB: no s'han tornat a comprimir.
- El text del xat només és al vídeo i a la seva `aria-label`; no hi ha una transcripció completa (WCAG 1.2.1).
- Verificat en Chrome real (CDP): els 3 reproductors no s'executen fora de pantalla, reprodueixen en entrar, el botó alterna `aria-pressed`, la pausa manual es manté en tornar a entrar, amb moviment reduït no arrenquen sols i no hi ha errors de consola. Captures a 1280 i 390 px revisades.
- No provat amb lector de pantalla ni en dispositius reals.
