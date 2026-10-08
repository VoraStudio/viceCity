# 034 — Vídeo de la demo sota el hero

**Stack**: html + js
**Estat**: acceptat
**Data**: 2026-10-08

## Context

Sota el hero hi havia una maqueta de la plataforma feta en HTML (amb un comentari que demanava substituir-la per una captura real). Es disposa d'un vídeo de la demo.

## Decisió

- **El vídeo substitueix la maqueta**, dins del mateix marc animat (decisió 021 i marc de degradat cònic del hero). Original: 1920×1080, 39 s, 30 fps, 12,2 MB, sense àudio.
- **Compressió** amb ffmpeg a `assets/video/vicity-demo.mp4`: 1280×720, H.264 (CRF 28, preset slow), sense àudio i amb `faststart`: 2,0 MB. Es va provar WebM (VP9) i sortia més gran (2,3 MB), així que no s'inclou.
- **Pòster** `vicity-demo-poster.webp` (18 KB), tret del segon 5: el primer fotograma del vídeo és la portada amb el logo sobre blanc.
- **Atributs** `muted`, `loop`, `playsinline` i `preload="metadata"`, **sense `autoplay`**: `initHeroVideo` a `script.js` el reprodueix en entrar en pantalla i el pausa en sortir amb `ScrollTrigger`, per no gastar CPU fora de vista.
- Es retiren `role="img"` i l'`aria-label` de la maqueta; l'`aria-label` passa al propi vídeo.

## Conseqüències

- Amb `prefers-reduced-motion` el vídeo no arrenca i queda el pòster fix; sense JS passa el mateix.
- **Botó de pausa afegit a la decisió 044** (WCAG 2.2.2): un vídeo que es repeteix més de 5 s n'ha de tenir.
- No provat en un navegador, només comprovat el codi.
