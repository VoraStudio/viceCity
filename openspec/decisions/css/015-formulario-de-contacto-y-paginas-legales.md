# 015 — Formulario de contacto y páginas legales

**Stack**: html
**Estado**: aceptado (parcial: falta endpoint real y textos legales)
**Fecha**: 2026-10-07

## Contexto

Los enlaces del pie y del acceso al blog eran `href="#"`, y `#contacte` / `#demo` apuntaban al pie y a una tarjeta del acordeón, sin formulario. No hay backend.

## Decisión

- **Páginas**: `avis-legal.html`, `privacitat.html`, `cookies.html` y `blog.html`, con el mismo `@theme`, fuentes, cabecera y pie que `index.html`. Se generaron a partir de ellos (los enlaces `#id` pasan a `index.html#id`), un solo `<h1>` por página y el mismo skip-link. Cargan solo `nav.js` y `ripple.js`.
- **Textos legales**: borrador con marcadores `[A COMPLETAR]` (elemento `<mark>`). No hay razón social, CIF ni dirección inventados. Se cita la normativa (LSSI-CE, RGPD) pero no se afirman plazos, destinatarios ni cookies concretas.
- **`blog.html`**: maqueta «Aviat» sin artículos y con `noindex, follow`; no está en `sitemap.xml` hasta que haya contenido.
- **Formulario** en `<section id="contacte">` con `<form id="demo">`: la cabecera, el hero y el pie ya apuntaban a esos dos ids. El `id="demo"` salió de la tarjeta del acordeón (su CTA ahora lleva al formulario) y el `id` del pie pasó a `peu`.
  - Etiquetas asociadas, `required`, `autocomplete` (`name`, `organization`, `organization-title`, `email`, `tel`), casilla de privacidad obligatoria enlazada a `privacitat.html`.
  - `action="mailto:info@vicity.cat" method="post" enctype="text/plain"`, con el correo que ya había en el pie. El texto del formulario avisa de que se abrirá el programa de correo.
  - Campos con las clases de `tokens.md` (`rounded-sm border`, `bg-white` por contraste con el contenedor `bg-paper`), pero con `h-9` y el botón en `h-11` como pide la regla táctil. Estados: hover, `focus-visible`, `active:scale-95`, `disabled:` (`bg-lav-100`), `user-invalid:border-purple-700`, todo con `motion-reduce`.
  - El relleno del ripple de los componentes nuevos es `bg-lav-100`, no `bg-purple-300` (ese color no es un token; ver `tokens.md`, sección 6).

## Consecuencias

- **`mailto:` no es un envío fiable**: depende del cliente de correo del visitante y no registra nada. Hay que sustituirlo por un endpoint real (y quitar `enctype`) y retirar la frase del formulario que lo explica.
- El correo, el teléfono y la dirección del pie parecen datos de ejemplo; hay que confirmarlos con el cliente.
- Las páginas duplican cabecera y pie: no hay build ni includes. Un cambio en esos bloques se replica a mano en las cuatro páginas.
