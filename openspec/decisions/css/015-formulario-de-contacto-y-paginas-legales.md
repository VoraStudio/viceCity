# 015 — Formulari de contacte i pàgines legals

**Stack**: html
**Estat**: acceptat (parcial: falta endpoint real i textos legals)
**Data**: 2026-10-07

## Context

Els enllaços del peu i de l'accés al blog eren `href="#"`, i `#contacte` / `#demo` apuntaven al peu i a una targeta de l'acordió, sense formulari. No hi ha backend.

## Decisió

- **Pàgines**: `avis-legal.html`, `privacitat.html`, `cookies.html` i `blog.html`, amb el mateix `@theme`, fonts, capçalera i peu que `index.html`. Es van generar a partir d'ells (els enllaços `#id` passen a `index.html#id`), un sol `<h1>` per pàgina i el mateix skip-link. Carreguen només `nav.js` i `ripple.js`.
- **Textos legals**: esborrany amb marcadors `[A COMPLETAR]` (element `<mark>`). No hi ha raó social, CIF ni adreça inventats. Es cita la normativa (LSSI-CE, RGPD) però no s'afirmen terminis, destinataris ni cookies concretes.
- **`blog.html`**: maqueta «Aviat» sense articles i amb `noindex, follow`; no és a `sitemap.xml` fins que hi hagi contingut.
- **Formulari** a `<section id="contacte">` amb `<form id="demo">`: la capçalera, el hero i el peu ja apuntaven a aquests dos ids. L'`id="demo"` va sortir de la targeta de l'acordió (el seu CTA ara porta al formulari) i l'`id` del peu va passar a `peu`.
  - Etiquetes associades, `required`, `autocomplete` (`name`, `organization`, `organization-title`, `email`, `tel`), casella de privacitat obligatòria enllaçada a `privacitat.html`.
  - `action="mailto:info@vicity.cat" method="post" enctype="text/plain"`, amb el correu que ja hi havia al peu. El text del formulari avisa que s'obrirà el programa de correu.
  - Camps amb les classes de `tokens.md` (`rounded-sm border`, `bg-white` per contrast amb el contenidor `bg-paper`), però amb `h-9` i el botó a `h-11` com demana la regla tàctil. Estats: hover, `focus-visible`, `active:scale-95`, `disabled:` (`bg-lav-100`), `user-invalid:border-purple-700`, tot amb `motion-reduce`.
  - L'emplenament del ripple dels components nous és `bg-lav-100`, no `bg-purple-300` (aquest color no és un token; vegeu `tokens.md`, secció 6).

## Conseqüències

- **`mailto:` no és un enviament fiable**: depèn del client de correu del visitant i no registra res. Cal substituir-lo per un endpoint real (i treure `enctype`) i retirar la frase del formulari que ho explica.
- El correu, el telèfon i l'adreça del peu semblen dades d'exemple; cal confirmar-los amb el client.
- Les pàgines dupliquen capçalera i peu: no hi ha build ni includes. Un canvi en aquests blocs es replica a mà a les quatre pàgines.
