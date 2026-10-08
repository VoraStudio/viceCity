# 004 — Header y footer como Web Components

**Stack**: arquitectura (html + js)
**Estado**: aceptado
**Fecha**: 2026-10-08

## Contexto

El header (~177 líneas) y el footer (~173) estaban copiados a mano en las 5 páginas (`index`, `avis-legal`, `privacitat`, `cookies`, `blog`). Ya se habían desincronizado: las páginas internas no tenían el crédito de Vora Studio ni la composición móvil del footer, y no cargaban `script.js`, así que no se animaban.

## Decisión

- **Dos custom elements** vanilla, `<site-header>` y `<site-footer>`, en `assets/js/components/` (`site-header.js`, `site-footer.js`, `site-components.js` como punto de entrada y `home-href.js` como helper).
- **Light DOM, sin Shadow DOM**: los estilos son clases de Tailwind de una hoja global, que el Shadow DOM no vería. El markup se pinta con `innerHTML` en `connectedCallback`, con guarda contra doble render.
- **Enlaces de ancla resueltos al pintar**: en la página de inicio son `#solucions`, `#contacte`... y en las demás `index.html#solucions`, mediante `isHome`, `home` y `logoHref` de `home-href.js`.
- **El módulo de componentes se carga el primero** en cada página, antes de `nav.js`, `ripple.js`, etc.: esos scripts consultan el DOM del header y del footer al arrancar. Definir el elemento mejora las etiquetas ya parseadas de forma síncrona.
- **Las 4 páginas internas cargan también SplitText y `script.js`**, así su header y su footer se animan igual que en el inicio. `script.js` no hace nada cuando faltan los elementos de una sección.
- **`display: block`** para `site-header` y `site-footer` en el CSS de cada página, para limitar el salto de maquetación.
- Las plantillas son idénticas, salvo los enlaces, a los bloques que sustituyen (comprobado línea a línea contra git).

## Consecuencias

- Header y footer viven en un único sitio: un cambio se propaga a las 5 páginas.
- **Sin JS desaparecen** el header y el footer, con la navegación.
- Antes de pintarse, las etiquetas no tienen altura: puede haber un pequeño salto al cargar.
- No probado en un navegador, solo comprobado el código.
