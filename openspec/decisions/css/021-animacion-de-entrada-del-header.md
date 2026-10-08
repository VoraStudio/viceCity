# 021 — Animación de entrada del header

**Stack**: js
**Estado**: aceptado
**Fecha**: 2026-10-08

## Contexto

Se quiere animar la entrada de los elementos de cada sección con GSAP, ligada a una timeline por sección. Las animaciones viven en un único `assets/js/script.js`, separado por comentarios de sección. El header es la primera.

## Decisión

- **Un solo `script.js` general**, cargado como `type="module"` tras `title-reveal.js`. Usa `gsap` global, como el resto de módulos. Los únicos comentarios son los separadores de sección.
- **El header se anima al cargar, sin `ScrollTrigger`.** Está en pantalla con el scroll en 0, así que su `start` ya estaría superado y el `reset` de `play reset play reset` nunca se dispararía.
- **Una timeline de dos pasos**: el `nav` cae con fade (`y: -40`, 0,8 s, `power3.out`) y los elementos (logo, `li` del menú, CTAs y hamburguesa) entran con `stagger: 0.08`, solapados con `"-=0.6"`.
- **Selectores con `:scope`** para coger solo los hijos directos del `nav` y excluir el `ul` del menú móvil (`#menu-mobil`).
- **`clearProps: "transform,opacity"`** en los `defaults` de la timeline, para devolver los estilos a Tailwind y no romper los hover.
- **`prefers-reduced-motion`** corta la animación con un early return.

## Consecuencias

- Los elementos `display: none` según el breakpoint (los enlaces en móvil) entran en el `stagger` y retrasan a los siguientes.
- Puede verse un parpadeo antes de que GSAP oculte el header, porque los módulos cargan diferidos. Pendiente: ocultarlo con la variante `js:` y pasar a `fromTo`.
- No probado en navegador, solo comprobado el código.
- Las secciones siguientes añaden su bloque en el mismo `script.js`.
