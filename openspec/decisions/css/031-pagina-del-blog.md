# 031 — Página del blog

**Stack**: html + css
**Estado**: aceptado
**Fecha**: 2026-10-08

## Contexto

`blog.html` era un placeholder de página legal. Los diseños están en `docs/Disseny web/Blog` (`vicity-blog-desktop.pdf`, `vicity-blog-ipad.pdf` y `vicity-blog-iphone.pdf`) y la página se construyó con un agente a partir de ellos. El menú, el footer y la franja del inicio enlazan a `blog.html`, así que se reescribe en su sitio.

## Decisión

- **Mobile first con los tres diseños**: base = iPhone, `md:` = iPad y `lg:` = escritorio. Sin media queries `max-width` ni `style=""`.
- **Tres secciones**: cabecera (pill «Blog», `h1`, subtítulo y barra de categorías en un `<nav aria-label>`), artículo destacado (card de dos columnas en escritorio) y últimos artículos (6 cards en 1, 2 o 3 columnas).
- **Reutiliza lo existente**: `<site-header>`, `<site-footer>`, los tokens del `@theme` (sin tokens nuevos), el botón primario con `data-ripple` y las clases de foco. Los módulos se cargan con `site-components.js` primero.
- **Sin imágenes reales**: las portadas son bloques de color del diseño (blanco, `purple-700` y `ink`), con `aspect-2/1`.
- **Accesibilidad**: un solo `h1`, orden de encabezados válido, `<time datetime>`, `aria-current` en la categoría activa y un texto oculto con el título en cada enlace «Llegir l'article» para distinguirlos.
- **Ganchos de animación sin cablear**: `data-blog-hero`, `data-blog-featured` y `data-blog-card`. `script.js` no se toca.

## Consecuencias

- **Los 7 enlaces «Llegir l'article» son `href="#"`**: aún no hay páginas de artículo.
- **Los filtros enlazan a `blog.html?categoria=...` pero no filtran.** Los diseños no muestran paginación ni bloque de newsletter, así que no se han añadido.
- Decisiones del agente por confirmar: chip lavanda en las etiquetas de las cards de portada blanca (en el PDF eran invisibles), `aspect-2/1` en las portadas, título móvil en `text-3xl` (el diseño ronda 32 px y ningún token encaja) y las dos manchas difuminadas copiadas del hero.
- No probado en un navegador, solo comprobado el código.
