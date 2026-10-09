# 011 — Configuració única del lloc: domini, titular i correu

**Stack**: arquitectura (php)
**Estat**: acceptat (fase 1; la fase 2 queda pendent)
**Data**: 2026-10-09

## Context

Vora Studio no tindrà accés a l'allotjament ni sap quin domini contractarà el client: el desplegament i la configuració els farà el client o el seu proveïdor. Fins ara el domini `www.vicity.cat` i les adreces de correu estaven escrits a mà en onze fitxers (`head.php`, `mail-config.php`, `recaptcha-config.php`, `robots.txt`, `sitemap.xml`, `index.html`, les tres pàgines legals i `site-footer.js`), i les dades del titular eren valors d'exemple repetits en cada lloc. Això obligava el client a buscar i substituir a mà, i permetia publicar sense adonar-se que quedaven dades pendents.

## Decisió

- **Un sol fitxer de configuració**: `includes/site-config.php` retorna `base_url`, `site_name`, `mail_recipient`, `mail_sender`, `mail_subject_prefix`, `blog_indexable` i `owner` (raó social, NIF, domicili, registre mercantil, correu i telèfon de contacte, DPD i autoritat de control). `includes/site.php` el carrega amb memòria cau estàtica i ofereix `siteConfig()`, `siteUrl()`, `sitePendingKeys()`, `siteIsConfigured()`, `siteMailConfigured()` i `siteIsPlaceholder()`.
- **Valors pendents**: tots els valors que depenen del client comencen per `PENDENT` (el domini és `https://PENDENT-DOMINI.tld`). `siteIsConfigured()` és fals mentre algun valor en contingui.
- **Substitucions locals**: `includes/site-config.local.php`, ignorat per git, se suma als valors per defecte amb `array_replace_recursive`. `includes/site-config.local.example.php` és la plantilla versionada, amb valors de `example.org`.
- **`hostname` de reCAPTCHA derivat** del host de `base_url`, de manera que no cal tocar `recaptcha-config.php` en canviar de domini.
- **Consumidors**: `head.php` (canonical, `og:url`, `og:image` absolut, `og:site_name`, enllaços de contacte del bloc `noscript`), `mail-config.php`, `recaptcha-config.php` i `contacto.php` (enllaç de correu del missatge d'error, que només es mostra si hi ha un correu configurat).
- **Esborrany visible**: mentre el lloc no estigui configurat, `head.php` mostra una franja fixa a dalt (`role="note"`, `sticky`, sense tapar el contingut: empeny el capçal) amb el text «Esborrany: hi ha dades pendents de configurar (includes/site-config.php)» i posa `noindex, nofollow`. Una vegada configurat desapareix la franja i el `meta robots` torna a la política de la decisió 006.
- **El blog no s'indexa per defecte**: `blog_indexable` és `false`. Amb el lloc configurat, el `meta robots` de les pàgines PHP és `noindex, follow`, igual que abans (decisió 006). Si el client el posa a `true`, el `meta robots` és `index, follow` i el sitemap inclou `blog.php` i totes les entrades amb `lastmod` igual a `published_at`.
- **`robots.php` i `sitemap.php`** substitueixen `robots.txt` i `sitemap.xml`. El `.htaccess` de l'arrel els serveix a les mateixes URL amb `RewriteRule ^robots\.txt$ robots.php [L]` i `RewriteRule ^sitemap\.xml$ sitemap.php [L]`, dins de `mod_rewrite`. El `robots.txt` diu `Disallow: /` mentre el lloc no estigui configurat i `Allow: /` quan ho està. El sitemap es genera amb `XMLWriter`, de manera que sempre és XML vàlid.
- **El formulari falla tancat**: si `mail_recipient` o `mail_sender` són `PENDENT`, `contacto.php` respon 503 amb «El formulari encara no està configurat.» abans de crear la sessió ni validar res, i no envia correu. El detall (`falten mail_recipient i mail_sender a includes/site-config.php`) va només a `error_log`, no a l'usuari. La resposta HTML de reserva té el seu propi bloc d'estat.

## Conseqüències

- Desplegar significa crear **un** fitxer (`site-config.local.php`) en lloc de substituir en onze. La franja d'esborrany i el `noindex` fan que un desplegament sense configurar no passi desapercebut ni s'indexi.
- Un servidor que no sigui Apache (o sense `mod_rewrite`) no servirà `/robots.txt` ni `/sitemap.xml`; llavors cal configurar l'equivalent al servidor o enllaçar `robots.php` i `sitemap.php` directament.
- El sitemap només inclou les pàgines legals amb l'extensió `.html` actual; en la fase 2, quan passin a PHP, caldrà actualitzar-ne les adreces.
- Amb `blog_indexable` a `true` el sitemap llista totes les entrades, incloses les de prova: cal netejar `data/posts.php` abans d'activar-ho.
- Hi ha un risc residual: el `meta robots` i la franja depenen de `siteIsConfigured()`, que només comprova cadenes que continguin `PENDENT`. Un valor sense sentit però sense aquest text es considerarà configurat.

## Verificat

- Còpia del projecte sota Apache (XAMPP), amb la regla del `.htaccess` real: `/robots.txt` i `/sitemap.xml` responen 200 amb el tipus de contingut correcte; `includes/site-config.php`, `includes/site.php` i `data/blog.sqlite` responen 403.
- Sense configurar: franja visible, `noindex, nofollow`, canonical i `og:*` amb `PENDENT-DOMINI.tld`, `Disallow: /` i el formulari amb 503 sense enviar. Amb la configuració de prova: sense franja, `noindex, follow`, canonical amb el domini de prova, `Allow: /` i el sitemap amb 4 adreces. Amb `blog_indexable` a `true`: `index, follow` i 13 adreces (blog i 8 entrades) amb `lastmod`, i el sitemap es carrega com a XML vàlid.
- Formulari amb receptor SMTP fals (sense correu real): amb la configuració de prova, 200 i un correu rebut amb `To`, `From` i `Reply-To` correctes; sense configurar, 503 i cap correu.
- Chrome real a 1280 i 390 px: la franja ocupa de 0 a 34 px (52 px en mòbil), el capçal comença just a sota, es manté visible en fer scroll i no hi ha desbordament horitzontal.

## Pendent per a la fase 2

- Passar `index.html` i les pàgines legals a PHP perquè llegeixin aquesta configuració (domini, titular, telèfon, adreça, `site-footer.js` inclòs).
- Mostrar la franja d'esborrany també en aquestes pàgines, amb la mateixa lògica.
- Actualitzar enllaços, sitemap i canonical, amb redirecció 301 dels `.html` antics.
- Incrustar el token CSRF a la portada, que tanca la ruta sense JS de la decisió 010.
- No s'ha provat un servidor de producció, el correu real ni cap navegador que no sigui Chrome.
