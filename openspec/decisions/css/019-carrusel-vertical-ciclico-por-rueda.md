# 019 — Carrusel vertical cíclic per roda a «Especialització»

**Stack**: css + js
**Estat**: acceptat
**Data**: 2026-10-07

## Context

La secció 2 mostrava quatre targetes en cascada (decisió 009). Es vol un carrusel vertical: la targeta activa al davant i les veïnes darrere, més petites i tènues, en bucle infinit.

## Decisió

- **El carrusel només existeix des de `lg` i sense `prefers-reduced-motion`**, mitjançant `gsap.matchMedia`. Per sota, el grid normal (`md:grid-cols-2`) queda intacte.
- **Tailwind col·loca, JS mou.** Les classes `in-data-[stack=on]:*` apilen les targetes (`absolute`, `inset-x-0`, `top-1/2`) i fixen el contenidor (`max-w-5xl`, `h-136`, `min-h-88` per targeta), però només s'activen quan `stack-carousel.js` posa `data-stack="on"` a la secció. En sortir d'escriptori ho treu i esborra els estils de GSAP (`clearProps`).
- **Posició per distància cíclica.** `render(activa)` calcula `distancia = gsap.utils.wrap(-n/2, n/2, i - activa)` i col·loca cada targeta amb `yPercent: -50`, `y`, `scale`, `opacity` i `zIndex`. L'opacitat val `1 - (lluny / (n/2)) ** 2`, que arriba a 0 a la vora de la finestra: així el salt de la targeta que dona la volta no es veu.
- **Avanç amb la roda sobre la pila, sense `pin`.** Un `wheel` a `data-stack-stage` (amb `preventDefault`, listener no passiu) avança una targeta, animant un número `activa` amb GSAP (0,6 s, `power2.inOut`). Un bloqueig de 900 ms ignora la inèrcia del trackpad. Com que `activa` no està lligat al scroll, pot créixer sense límit i el bucle no té final.
- Es va descartar el `pin` amb `ScrollTrigger` (`scrub` + `snap`): necessita un principi i un final de scroll, que no encaixen en un bucle infinit, i segresta el scroll de tota la secció.

## Conseqüències

- Substitueix la cascada: la decisió 009 (atenuar amb `:has`) deixa d'aplicar-se a `lg`.
- Amb el cursor sobre la pila la pàgina no fa scroll, perquè la roda es bloqueja. Se'n surt movent el cursor fora de la pila.
- **No inclou** teclat, `aria-live` ni `inert` per a les targetes de darrere; la versió mínima prioritza la comprensió del codi. Pendent abans de donar la secció per accessible.
- **Tàctil resolt a la decisió 020**: per sota de `lg` el mateix `render` es mou arrossegant.
- Els noms de variables i els comentaris són en castellà i català, tal com es van anar construint a la sessió guiada.
