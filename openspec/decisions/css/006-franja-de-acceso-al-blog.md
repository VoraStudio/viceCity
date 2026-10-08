# 006 — Franja d'accés al blog abans del footer

**Stack**: css
**Estat**: acceptat
**Data**: 2026-10-06

## Context

Abans del footer, dins de `#recursos`, quedava una còpia buida de la franja lavanda de confiança, amb l'`id="confianca-titol"` repetit. Cal un accés al blog.

## Decisió

- La còpia es reutilitza com a franja d'accés: títol «Descobreix el nostre blog», fletxa SVG i un CTA «Visitar el blog».
- El títol fa servir l'escala dels `<h2>` de secció: `text-3xl md:text-title-2`.
- La fletxa és decorativa (`aria-hidden`) i hereta el color amb `currentColor`. En mòbil apunta cap avall (`rotate-90`) i des de `lg` cap a la dreta (`lg:rotate-0`), cap al botó.
- El CTA és el botó primari de `tokens.md`: `h-11 rounded-full bg-purple-700 font-body font-medium text-white`, amb el focus de la resta de botons.
- L'`id` del títol passa a `blog-titol`: un `id` repetit és HTML invàlid i trenca `aria-labelledby`.

## Conseqüències

- L'`href` és `#` perquè l'URL del blog encara no existeix. Cal substituir-lo quan es conegui.
- La franja queda dins de `<section id="recursos">`, l'`aria-label` de la qual descriu només les tres targetes. Convé decidir si ha de ser la seva pròpia secció.
- La fletxa mesura `size-6` davant d'un títol de 40 px; pot quedar petita.
- La superfície `bg-lav-100/40` porta text, cas ja obert a la decisió 004.
