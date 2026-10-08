# 034 — Vídeo de la demo bajo el hero

**Stack**: html + js
**Estado**: aceptado
**Fecha**: 2026-10-08

## Contexto

Bajo el hero había una maqueta de la plataforma hecha en HTML (con un comentario que pedía sustituirla por una captura real). Se dispone de un vídeo de la demo.

## Decisión

- **El vídeo sustituye a la maqueta**, dentro del mismo marco animado (decisión 021 y marco de degradado cónico del hero). Original: 1920×1080, 39 s, 30 fps, 12,2 MB, sin audio.
- **Compresión** con ffmpeg a `assets/video/vicity-demo.mp4`: 1280×720, H.264 (CRF 28, preset slow), sin audio y con `faststart`: 2,0 MB. Se probó WebM (VP9) y salía más grande (2,3 MB), así que no se incluye.
- **Póster** `vicity-demo-poster.webp` (18 KB), sacado del segundo 5: el primer fotograma del vídeo es la portada con el logo sobre blanco.
- **Atributos** `muted`, `loop`, `playsinline` y `preload="metadata"`, **sin `autoplay`**: `initHeroVideo` en `script.js` lo reproduce al entrar en pantalla y lo pausa al salir con `ScrollTrigger`, para no gastar CPU fuera de vista.
- Se retiran `role="img"` y el `aria-label` de la maqueta; el `aria-label` pasa al propio vídeo.

## Consecuencias

- Con `prefers-reduced-motion` el vídeo no arranca y queda el póster fijo; sin JS ocurre lo mismo.
- **Falta un botón de pausa**: un vídeo que se repite más de 5 s debería tenerlo (WCAG 2.2.2) y no está en el diseño.
- No probado en un navegador, solo comprobado el código.
