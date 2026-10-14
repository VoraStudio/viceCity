# 013 — Configuració només per entorn, amb `.env` de reserva

**Stack**: arquitectura (php)
**Estat**: acceptat
**Data**: 2026-10-14

## Context

La decisió [011](011-configuracio-unica-del-lloc.md) va concentrar la configuració a `includes/site-config.php` amb valors literals `PENDENT` i la va fer substituïble amb `includes/site-config.local.php`. En una primera versió d'aquesta decisió, les variables d'entorn eren només una capa superposada a aquests literals. Això deixava dades del client (nom, prefix del correu, marcadors `PENDENT`) escrites al codi i obligava a crear fitxers PHP al servidor per als secrets. Es vol que no hi hagi res escrit al codi: la configuració surt de l'entorn. Vora Studio no té accés a l'allotjament, així que el mecanisme ha de ser simple per a qui desplegui, també en un hosting compartit on potser no hi ha variables al panell.

## Decisió

- **Només entorn**: `includes/site-config.php` no té cap valor literal; construeix l'array llegint variables amb `siteEnvValue()`. Ja no hi ha valors per defecte `PENDENT`, ni nom de client, ni dominis, ni correus al codi.
- **`.env` de reserva** (`includes/env.php`): format `CLAU=VALOR`, comentaris amb `#`, valors opcionalment entre cometes simples o dobles, `export ` opcional. No fa interpolació (`${...}` queda literal) ni executa res; les línies invàlides (o amb cometa sense tancar) s'ignoren. Es carrega una sola vegada.
- **Ubicació**: `<arrel del projecte>/.env` i, si no hi és, un nivell per sobre (fora del docroot). Si hi ha els dos, guanya el de l'arrel.
- **Precedència**: entorn real (`getenv()`, `$_SERVER`, `$_ENV`, també amb prefix `REDIRECT_` del `SetEnv` d'Apache) > `.env`. El `.env` no toca l'entorn (no fa `putenv`): només rellena el que l'entorn real no té. Una variable buida compta com a no definida a tots dos nivells.
- **Semàntica «pendent» = buit**: ja no es busca el text `PENDENT`. Un valor és pendent si és buit o només espais. `mail_subject_prefix` és opcional: si falta es deriva de `site_name` com `[<site_name>]`, i no compta com a pendent. `site_name` és nou (`VICITY_SITE_NAME`) i sí compta com a pendent.
- **Noms amb prefix `VICITY_`**: `VICITY_BASE_URL`, `VICITY_SITE_NAME`, `VICITY_MAIL_RECIPIENT`, `VICITY_MAIL_SENDER`, `VICITY_MAIL_SUBJECT_PREFIX`, `VICITY_BLOG_INDEXABLE`, vuit `VICITY_OWNER_*`, `VICITY_RECAPTCHA_SITE_KEY` i `VICITY_RECAPTCHA_SECRET`. `.env.example` les conté totes, buides.
- **Booleà**: `VICITY_BLOG_INDEXABLE` val `true` amb `1`, `true`, `on` o `yes` (sense distingir majúscules); qualsevol altre valor, o absent, és `false`.
- **reCAPTCHA**: `site_key` i `secret_key` surten només de les variables; `recaptchaEnabled()` és cert si totes dues són no buides. `min_score`, `action` i `verify_url` són constants tècniques. El `hostname` es deriva del host de `base_url`.
- **Sense `base_url`**: no es generen URL absolutes trencades. `head.php` omet `canonical`, `og:url` i `og:image`; `robots.php` no emet la línia `Sitemap:`; `sitemap.php` retorna un `urlset` buit.
- **Retirats**: `includes/site-config.local.php` (i el seu carregador a `site.php`), `site-config.local.example.php`, `secrets.local.php`, `secrets.example.php`, `siteEnvMap()`/`siteApplyEnv()` i `isRecaptchaPlaceholder()`.
- **Seguretat**: `.env` és a `.gitignore` (`.env.example` no) i el `.htaccess` de l'arrel denega qualsevol fitxer que comenci per punt. Els secrets no van a `SetEnv` del `.htaccess`, que és versionat.

## Conseqüències

- Desplegar vol dir crear un `.env` (o definir variables al panell); no es toca cap fitxer PHP.
- Qui depura ha de saber que l'entorn real pisa el `.env` en silenci.
- La memòria cau estàtica fa que els canvis només s'apliquin a la petició següent; a PHP-FPM, les variables definides pel servidor poden requerir reiniciar el procés.
- Si el proveïdor deixa el `.env` dins del docroot, depèn del `.htaccess`; per això es recomana ubicar-lo un nivell per sobre quan es pugui.
- Les pàgines estàtiques (`index.html` i legals) no llegeixen la configuració, com a la 011.
- Un valor sense sentit però no buit es considerarà configurat: la validació només comprova que no sigui buit.
- `php -S` local: cal un `.env` a l'arrel o exportar les variables abans.

## Verificat

- Còpia de `includes/` fora del projecte, amb `php` CLI i un entorn controlat per execució (script de proves escrit abans de la implementació i vist fallar): sense entorn ni `.env`, 12 claus pendents, `siteIsConfigured()` fals, cap avís amb `E_ALL`; l'entorn real pisa el `.env`; el `.env` rellena el que falta, també un nivell per sobre, i el de l'arrel guanya; valors buits o en blanc són pendents; parser amb comentaris, cometes, espais, `export`, línies invàlides i `${...}` sense interpolar; booleans; `hostname` derivat de `VICITY_BASE_URL`; amb tot definit `siteIsConfigured()` i `recaptchaEnabled()` són certs; prefix derivat de `site_name`; cap literal de client ni domini als fitxers de configuració.
- `php -S` amb còpia del web: sense variables, franja d'esborrany, `noindex, nofollow`, sense `canonical` ni `og:url`, formulari 503, `Disallow: /` i sitemap buit; amb `.env` complet, sense franja, `canonical` i `og:*` amb el domini, `Allow: /` i sitemap amb adreces; el formulari ja no respon 503.
- `php -l` sense errors als fitxers PHP tocats.

## Pendent

- **A confirmar amb cdmon** si l'allotjament permet definir variables d'entorn per a PHP o ubicar fitxers un nivell per sobre del web; no s'ha verificat.
- No s'ha provat sota Apache real (`SetEnv`, `REDIRECT_`, bloqueig de `.env` pel `.htaccess`); només amb CLI i `php -S`.
