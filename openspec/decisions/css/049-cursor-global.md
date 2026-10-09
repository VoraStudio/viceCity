# 049 — Cursor global amb punt i anell

**Stack**: js + tailwind
**Estat**: acceptat
**Data**: 2026-10-09

## Context

Es vol a tota la web el cursor de la secció «Interacció» de GSAP Academy (`https://onikuza00.github.io/gsapAcademy/html/Sections/Mouse/03c_interaccion.html`, bloc «GLOBAL CURSOR»): un punt que segueix el ratolí a l'instant i un anell que el segueix amb retard i creix en passar per elements interactius.

## Decisió

- **`initCursor` a `assets/js/script.js`**, un únic punt d'entrada. Crea el punt i l'anell des de JS (`aria-hidden="true"`, `pointer-events-none`, `fixed`, `z-[100]`, per sobre del header `z-50`), així no cal copiar marcatge a cap pàgina. Totes les pàgines (`index.html`, `blog.php`, `articulo.php`, `contacto.php` i les legals) ja carreguen `script.js` com a mòdul després de GSAP.
- **Moviment**: el punt es posiciona amb `gsap.set`; l'anell amb `gsap.quickTo` (`x`/`y`, 0,15 s, `power3`, `overwrite: "auto"`), com a la referència.
- **Hover**: l'anell fa `scale: 1.5` (equival als 40 → 60 px de la referència sense tocar el layout), canvia de `border-purple-700` a `border-purple-500` amb `data-[hover=true]:` i el punt escala a 0. Es fa amb delegació d'esdeveniments (`pointerover`/`pointerout` + `closest()`), no amb un listener per element, de manera que cobreix els components web (header i footer) i qualsevol element dinàmic.
- **Què és «interactiu»**: `a[href], button, summary, label, input, select, textarea, [role='button'], [data-hover-border]`. Així entren els enllaços, els botons, els camps del formulari i les cards amb vora animada (decisió 048).
- **Només tokens existents**: el punt és `bg-ink`, l'anell `border-purple-700` i `border-purple-500` en hover. No hi ha cap color, variable ni hex nous, ni CSS propi: són classes de Tailwind generades en afegir els elements al DOM.
- **Cursor natiu conservat**: no hi ha `cursor: none`. El cursor és decoratiu; amagar el del sistema perjudica l'accessibilitat i la precisió.
- **Només amb `(hover: hover) and (pointer: fine)`**: en tàctil no es crea res.
- **`prefers-reduced-motion`**: l'anell segueix el ratolí sense retard (`duration: 0`), els canvis d'escala i opacitat són instantanis i la transició de color es desactiva (`motion-reduce:transition-none`). A diferència de les animacions d'entrada, `initCursor` s'executa també amb moviment reduït.
- **Visibilitat**: comença amb `opacity-0` i apareix al primer `mousemove`; s'amaga amb `mouseleave` del document.

## Conseqüències

- No intercepta esdeveniments (`pointer-events-none`), així que no afecta el drag del carrusel (Draggable) ni la vora animada.
- Sobre un `<input>` o `<textarea>` l'anell també creix; el cursor de text del sistema continua visible.
- Depèn que `@tailwindcss/browser` generi les classes de l'anell i del punt quan s'afegeixen al DOM (observa mutacions); si algun dia es passa a un CSS compilat, cal que aquestes classes hi siguin (safelist).
- No provat en un navegador, només comprovat el codi.
