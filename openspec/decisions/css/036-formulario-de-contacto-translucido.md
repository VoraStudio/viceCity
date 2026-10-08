# 036 — Formulario de contacto translúcido

**Stack**: css
**Estado**: aceptado
**Fecha**: 2026-10-08

## Contexto

El formulario de `#contacte` tenía `bg-paper` (`#f7f8f6`), el mismo color que la parte alta del degradado de la sección (decisión 029), así que arriba casi no se distinguía del fondo.

## Decisión

- `bg-paper` pasa a **`bg-white/40`**: blanco puro al 40 % de opacidad. Deja ver el degradado de la sección: casi blanco arriba y lavanda clara abajo. Se mantiene el borde `border-lav-100`.

## Consecuencias

- El contraste del texto de los campos depende ahora del fondo de la sección; hay que comprobarlo con los `input` y `textarea` en el navegador.
- No probado en un navegador, solo comprobado el código.
