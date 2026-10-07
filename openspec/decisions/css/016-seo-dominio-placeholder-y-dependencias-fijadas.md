# 016 — SEO, dominio provisional y dependencias fijadas

**Stack**: html
**Estado**: aceptado (parcial: dominio y favicon pendientes)
**Fecha**: 2026-10-07

## Contexto

El `<head>` solo tenía título y descripción, los scripts de CDN usaban `@4` y `@3` (versión flotante, sin integridad) y los logos tenían espacios y acentos en la ruta.

## Decisión

- **Dominio provisional**: `https://EXAMPLE-DOMAIN.tld`, el mismo literal en `canonical`, Open Graph, Twitter, JSON-LD, `robots.txt` y `sitemap.xml`. Se sustituye con buscar y reemplazar en todo el proyecto (no hay build).
- **Metadatos**: `canonical`, `theme-color` (`#5e35b1`, es `purple-700`), Open Graph, Twitter card `summary` y JSON-LD `Organization` solo con nombre, URL, logo y descripción. No se añade `color-scheme`: la página es solo clara y no aporta nada.
- **Favicon**: `assets/img/logo/isotip/svg/sense-area-seguretat/1-isotip-fons-blanc.svg` como `rel="icon"` SVG. No es cuadrado (283 × 232) y no hay `.ico` ni `apple-touch-icon` PNG.
- **Imagen social**: el imagotipo PNG (2156 × 671). No hay una imagen 1200 × 630 pensada para redes.
- **Dependencias fijadas con SRI** (hash SHA-384 calculado descargando el fichero): `@tailwindcss/browser@4.3.3`, `gsap@3.15.0` y `ScrollTrigger@3.15.0`, con `integrity` y `crossorigin="anonymous"`; jsDelivr envía `access-control-allow-origin: *`. GSAP carga con `defer` en el `<head>`: los scripts `defer` y los módulos entran en la misma cola y se ejecutan en orden de documento, así que `gsap` ya existe cuando corren `ripple.js` y `title-reveal.js`. El script de Tailwind sigue siendo síncrono para no mostrar contenido sin estilos.
- **Rutas de logos**: `assets/img/Logotips/...` pasa a `assets/img/logo/{imagotip,isotip,logotip}/{pdf,png,svg}/[sense-area-seguretat/]N-nombre-en-minusculas.ext`. Se quitó el sufijo de captura (`12.19.49`) de los PNG del isotip. `logoVora.png` pasa a `assets/img/clients/vora.png`.

## Consecuencias

- Al actualizar una versión hay que recalcular el hash (`curl -sL <url> | openssl dgst -sha384 -binary | openssl base64 -A`). Un hash desfasado bloquea el script y la página queda sin estilos.
- El JSON-LD es un bloque de datos (`type="application/ld+json"`), no código ejecutable; es la única etiqueta `<script>` con contenido en línea.
- Los nombres numerados de los logos se conservan (`1-…` a `6-…`) porque identifican la variante de fondo.
