# 030 — Descripciones con fade up por líneas

**Stack**: js
**Estado**: aceptado
**Fecha**: 2026-10-08

## Contexto

Las descripciones del hero, Especialització, Integració, IA y Azure usaban el efecto de profundidad en X por caracteres (decisiones 022, 024, 025, 026 y 027). Solucions y Contacte no tenían efecto en su descripción. Se prueba primero en el hero y se extiende a todas.

## Decisión

- **Un único efecto**: fade up con `stagger` por líneas, con `SplitText` (`type: "lines"`). `linesFadeUp` (`y: 30`, 0,8 s, `power3.out`, `stagger: 0.15`) y `splitIntoLines` viven en el bloque `COMUNES` de `script.js`.
- **Se aplica a las 7 descripciones**: hero, Solucions (nueva), Especialització, Integració (2 párrafos), IA, Azure y Contacte (nueva). En Solucions y Contacte se añaden `data-solutions-description` y `data-contact-description` y la animación se coloca con posición absoluta (`0.4` y `0.3`) para no mover el resto de la timeline.
- **IA conserva su tempo más rápido** con un override local: `{ ...linesFadeUp, duration: 0.5, stagger: 0.1 }`.
- Se eliminan `charsDepthReveal` y `splitIntoChars`, que quedan sin uso. Los dos efectos siguen en el historial de git.
- Solucions y Contacte pasan a esperar `document.fonts.ready` antes de dividir en líneas, igual que el resto.

## Consecuencias

- **El split por líneas se calcula una sola vez**: al girar el dispositivo o redimensionar, las líneas no se recalculan. `title-reveal.js` lo resuelve con `autoSplit: true`; si se nota, se pueden pasar las secciones a `autoSplit`.
- Solucions tiene una descripción de una sola línea, así que no hay `stagger` que ver.
- Sustituye al efecto de caracteres de las decisiones 022, 024, 025, 026 y 027 en lo que se refiere a las descripciones.
- No probado en un navegador, solo comprobado el código.
