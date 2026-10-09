# 052 — Toast de resultat del formulari de contacte

**Stack**: html + js
**Estat**: acceptat
**Data**: 2026-10-09

## Context

Des de la decisió 010 el resultat de l'enviament del formulari de contacte es mostrava en dues regions fixes dins del formulari (`role="status"` i `role="alert"`), sota el botó. En mòbil, amb el formulari llarg, l'avís podia quedar fora de la pantalla. Un toast fix a la cantonada resol la visibilitat, però ha de ser accessible: amb regions `role` i `aria-live`, i amb temps de lectura suficient.

## Decisió

- **Mòdul `assets/js/modules/toast.js`** (ESM, importat per `script.js`) amb `initToasts()` i `showToast({ type, message })`. `type` és `success` o `error`.
- **Contenidor fix** a baix a la dreta (`right-4 bottom-4`, `md:right-6 md:bottom-6`), `z-[90]`: per sobre de la capçalera (`z-50`) i per sota del cursor global (`z-[100]`). El contenidor és `pointer-events-none` i cada toast `pointer-events-auto`, de manera que no intercepta res fora del propi avís.
- **Dues regions persistents i buides**, `role="status"` (èxit) i `role="alert"` (error), creades a la càrrega amb `initToasts()`. Cada toast s'hi insereix amb `aria-atomic="true"`, perquè els lectors de pantalla anunciïn el missatge sencer. Si la regió es crea en el moment de mostrar el primer toast, la inserció s'ajorna 150 ms perquè es registri.
- **Contingut**: icona SVG `aria-hidden` (marca de verificació o avís), text amb `textContent` (mai `innerHTML`) i botó de tancar amb `aria-label="Tanca l'avís"` i el mateix focus visible que la resta de la web.
- **Temps**: èxit 6 s i error 8 s. Els errors es llegeixen i s'han de poder corregir, i per això duren més; no es queden fins que es tanquen perquè no tapin el formulari. El temps **es pausa** amb el ratolí o el focus dins del toast (WCAG 2.2.1) i en reprendre'l queden com a mínim 1,5 s. `Escape` tanca el toast si el focus hi és dins, i el focus torna a l'element anterior.
- **Apilat**: màxim 3 toasts; en afegir-ne un quart es tanca el més antic. L'espai de 12 px entre la regió d'èxit i la d'error només hi és quan la regió següent té contingut (`[&:has(+div:not(:empty))]:mb-3`), perquè un toast sol quedi a la mateixa distància de la vora tant si és d'èxit com d'error.
- **Animació amb GSAP**: entrada des de la dreta (`x: 48` i fade, 0,4 s) i sortida inversa (0,3 s). Amb `prefers-reduced-motion` apareix i desapareix a l'instant; es comprova a cada crida.
- **Formulari**: el toast només comunica el resultat de l'enviament (èxit, 403, 429, 422, 500, sense xarxa, 503 del captcha). Els errors per camp continuen als `err-*` amb `aria-invalid` i el focus va al primer camp invàlid. A un 422 el toast diu «Revisa els camps marcats.», i hi afegeix els errors de camps sense `err-*` (càrrec, telèfon, missatge). S'eliminen les dues regions del formulari (`data-form-success` i `data-form-error`) i, a l'èxit, el focus ja no es mou a cap missatge: es reinicia el formulari i l'avís s'anuncia per la regió.
- **Estils**: només tokens del theme: `border-success` / `text-success` i `border-error` / `text-error`, fons `bg-white` i ombra com la de les cards.
- **Sense JS** no canvia res: el POST clàssic continua portant a la pàgina de resultat de `contacto.php`.

## Conseqüències

- Verificat a Chrome real (CDP) amb servidor local i un receptor de correu fals: el toast d'èxit i el d'error surten a 24 px de la vora inferior dreta a 1280 px, i a 16 px de les vores a 390 px (358 px d'ample, sense scroll horitzontal). Cauen a les regions `status` i `alert`, es tanquen sols als 6 s i als 8 s, es pausen amb el ratolí, es tanquen amb el botó i amb `Escape`, i es limiten a 3 sense solapar-se. Amb la xarxa tallada surt l'error de connexió i el botó es reactiva. Amb `prefers-reduced-motion` no hi ha animació. Sense errors de consola.
- Un avís que desapareix sol es pot perdre si l'usuari triga a llegir-lo. Es mitiga amb la pausa per ratolí i focus, el botó de tancar i la durada més llarga dels errors.
- `:has()` demana navegadors moderns; en un que no el suporti només es perd la separació entre un toast d'èxit i un d'error alhora.
- `showToast` només funciona amb JavaScript. La pàgina de resultat de `contacto.php` (sense JS) no el fa servir.
- No provat amb un lector de pantalla real, ni a Safari o Firefox, ni en dispositius reals.
