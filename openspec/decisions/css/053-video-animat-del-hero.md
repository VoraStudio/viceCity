# 053 — Vídeo animat del hero

**Stack**: html + css + js (render) · ffmpeg
**Estat**: acceptat
**Data**: 2026-10-09

## Context

El vídeo del hero (`vicity-demo.mp4`, decisió 034) era una gravació de 39 s de la plataforma. Es vol una peça corporativa de 15 s, en català, amb transicions animades, que presenti la proposta de valor i els mòduls de Vicity amb temps per llegir cada missatge.

## Decisió

- **Composició en HTML** (`docs/video-hero/vicity-hero.html`) amb els tokens de `tokens.md` (colors, Red Hat Display i Quicksand locals), els logos SVG de `assets/img/logo/` i les captures de `assets/img/Iamtges_Vicity/`. Les animacions són Web Animations API amb temps absoluts; `window.seek(t)` posa la pàgina en qualsevol instant, de manera que el render és determinista.
- **Render** (`docs/video-hero/render.js`): Chrome headless amb `puppeteer-core`, 1280×720 a `deviceScaleFactor` 1,5 (1920×1080), 30 fps, 450 fotogrames, codificats amb ffmpeg: H.264, CRF 21, preset slow, `yuv420p`, sense àudio i amb `faststart`: **2,7 MB**. La versió 1280×720 feia 1,6 MB, però el text petit de les captures perdia nitidesa.
- **Guió (15 s)**:
  1. 0–2,4 s · Logo animat (isotip, estrella, logotip) i «La gestió tributària preparada per al que vindrà».
  2. 2,4–5,2 s · 01 Gestió tributària: «Impostos, taxes i preus públics» amb Menú principal i Contribuents.
  3. 5,2–7,9 s · 02 Recaptació: «Tot el cicle recaptatori, sota control» amb Rebuts, Gestió financera i KPI animats.
  4. 7,9–10,7 s · 03 Dades i IA: «Dades que impulsen decisions» amb Workflow i un assistent d'IA.
  5. 10,7–12,4 s · «Construït sobre la confiança de Microsoft Azure» i els quatre pilars.
  6. 12,4–15 s · Tancament: «El futur de la gestió tributària comença avui» i CTA.
- **Transicions**: revelat circular doble des de l'estrella, cortina doble, la finestra de Rebuts que creix fins a la pantalla següent i revelat des de baix. Corbes expo i *in-out*.
- **Loop**: comença i acaba sobre el fons blau nit; l'últim 0,45 s fa un fos del contingut perquè el `loop` del `<video>` no salti.
- **Pòster** `vicity-hero-poster.webp` (17 KB), tret del segon 2,2 (logo i lema complets).
- Al `index.html` només canvien `src`, `poster` i `aria-label`; el reproductor i el botó de pausa (decisió 044) es mantenen.

## Conseqüències

- Les xifres (1.630 rebuts, 132.470 €, 131.285,09 € pendents) surten de les captures de demostració, no són dades reals de clients.
- `vicity-demo.mp4` i el seu pòster queden sense ús a `assets/video/`; es poden esborrar.
- Per canviar textos o temps: editar l'HTML, `npm i puppeteer-core` i `node render.js <carpeta> 30`, i tornar a codificar amb ffmpeg.
- No provat en dispositius reals ni a Safari.
