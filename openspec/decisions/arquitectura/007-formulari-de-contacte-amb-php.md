# 007 — Formulari de contacte amb PHP

**Stack**: arquitectura
**Estat**: acceptat
**Data**: 2026-10-08

## Context

El formulari de `index.html` (`data-contact-form`, `id="demo"`) envia amb `action="mailto:info@vicity.cat"` i `enctype="text/plain"`: només obre el programa de correu de l'usuari i no garanteix que res arribi. El projecte de referència, Registrals, **només té el front preparat** i no té cap backend, així que no hi ha res per reutilitzar. Des de la decisió 006 l'allotjament executa PHP.

## Decisió

- **Backend des de zero** a `contacto.php`, que accepta **només POST**. Qualsevol altre mètode respon 405 amb `Allow: POST` i un enllaç de tornada a `index.html#contacte`.
- **Sense front controller `index.php`**: `index.html` ja és a l'arrel i un `index.php` o una reescriptura canviarien les URL actuals. `contacto.php` és un endpoint més, com `blog.php`.
- **Defensa en capes**: camp **honeypot** ocult (`website`; si ve omplert es respon com a èxit sense enviar res), **validació al servidor** de cada camp (`trim`, longituds màximes, `filter_var` per al correu, `motiu` dins la llista tancada del `<select>`, telèfon amb patró, privacitat obligatòria) i **protecció contra injecció de capçaleres** (es rebutgen `\r`, `\n` i bytes nuls en tots els camps d'una sola línia; l'assumpte només es construeix amb el prefix configurat i l'etiqueta del motiu, mai amb text lliure).
- **Enviament amb `mail()`**, amb `From` i `Reply-To` correctes, UTF-8 i assumpte codificat amb `mb_encode_mimeheader`. Destinatari, remitent i prefix a `includes/mail-config.php` (a `includes/`, protegit per `.htaccess`). Validació i construcció del correu són funcions pures a `includes/contact.php`, de manera que es poden provar per CLI.
- **Resposta HTML pròpia, sense redirecció**: com que `index.html` és estàtic no pot mostrar missatges. `contacto.php` reutilitza `includes/head.php` i `includes/foot.php` del blog (mateix aspecte, header i footer, `noindex`), amb un únic `<h1>`, `role="status"` a l'èxit i `role="alert"` als errors, i l'enllaç «Torna al formulari». Codis: 200 èxit, 422 errors de validació, 500 si `mail()` falla (missatge genèric, sense detalls). No es repobla el formulari.
- **`.htaccess` a l'arrel**: `Options -Indexes`, accés denegat a fitxers ocults, `*.md`, `*.sqlite`, i a `openspec/`, `docs/`, `.git/`, `.github/` i `.atl/` (amb `RedirectMatch 403`, perquè `<DirectoryMatch>` no és vàlid a `.htaccess`), i capçaleres bàsiques (`X-Content-Type-Options`, `Referrer-Policy`) dins `<IfModule mod_headers.c>`. Sense reescriptures ni `DirectoryIndex`.

## Conseqüències

- **Pendent (no fet per ordre de Pau)**: `index.html` encara té `action="mailto:info@vicity.cat"` i `enctype="text/plain"`. Cal canviar-ho a `action="contacto.php" method="post"` (sense `enctype`) i afegir el camp honeypot ocult (`name="website"`, `tabindex="-1"`, `autocomplete="off"`, fora de pantalla i amb `aria-hidden`). També cal revisar el text «s'obrirà el teu programa de correu», que deixarà de ser cert. Mentre no es faci, el formulari no usa el backend.
- Com el blog, `contacto.php` **no funciona a GitHub Pages**: necessita l'allotjament amb PHP.
- `mail()` depèn de la configuració de l'allotjament (sendmail/SMTP). Es recomana configurar **SPF i DKIM** del domini i usar com a remitent una adreça del mateix domini; sense això els correus poden acabar a brossa.
- Sense límit de freqüència ni captcha: el honeypot atura bots simples, però no un atac dirigit. Si apareix spam, valorar un límit per IP o un captcha.
- **Comprovat**: sintaxi (`php -l`), respostes HTTP amb `php -S` (405 amb `Allow`, 422 amb errors, honeypot, 500 genèric quan `mail()` falla, rebuig de `\r\nBcc:`) i les funcions de `includes/contact.php` per CLI.
- **No provat**: el resultat en un navegador, que Apache apliqui el `.htaccess` (`php -S` no el llegeix; cal `AllowOverride` actiu i `mod_headers`) ni l'enviament real de correu a l'allotjament.
