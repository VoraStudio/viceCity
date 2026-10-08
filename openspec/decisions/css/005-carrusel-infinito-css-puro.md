# 005 — Carrusel infinit de logos en CSS pur

**Stack**: css
**Estat**: acceptat
**Data**: 2026-10-06

## Context

La franja de confiança («Administracions que ja confien...») mostrava quatre caixes de placeholder en una quadrícula. Es vol un carrusel de logos que es mogui sense fi i s'aturi en passar-hi el ratolí per sobre.

## Decisió

Marquee en CSS pur, sense JavaScript:

- La `<ul>` passa a pista d'una sola fila: `flex w-max items-center gap-12 pr-12`.
- Un `<div class="min-w-0 overflow-hidden">` l'envolta i la retalla. L'`overflow-hidden` va al contenidor, no a la llista: una llista `w-max` mesura el que mesuren els seus logos i no hi hauria res a retallar.
- Els logos es dupliquen (8 elements, dues meitats idèntiques). La segona meitat porta `aria-hidden="true"` perquè els lectors de pantalla no repeteixin el logo.
- L'animació viu a l'`@theme`: `--animate-marquee: marquee 20s linear infinite`, amb `@keyframes marquee { to { transform: translateX(-50%) } }`. Tailwind v4 genera la classe `animate-marquee`.
- Pausa en hover: `hover:[animation-play-state:paused]` sobre la llista.
- `pr-12` iguala el buit final al `gap-12`. Sense ell, cada meitat mesuraria 4 logos i 3,5 buits i el bucle donaria una estrebada de mig buit.
- Els logos es passen a negre suau amb `brightness-0 opacity-60`, perquè `logoVora.png` és blanc trencat sobre transparent i no es veuria sobre `lav-100/40`.

## Conseqüències

- El nombre d'elements ha de ser parell i les dues meitats idèntiques, o el desplaçament del 50 % no quadra.
- Els 8 elements són avui el mateix logo de Vora. Amb logos reals de clients d'amplada diferent, el càlcul continua valent mentre les dues meitats siguin iguals.
- Queda sense resoldre `prefers-reduced-motion`: l'animació no es desactiva per a qui ho demana (`motion-reduce:animate-none`).
- El `cursor-pointer` dels `<li>` promet un clic que no existeix mentre els logos no siguin enllaços.
