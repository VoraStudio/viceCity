# 010 — Enviament del formulari de contacte per fetch, amb CSRF i límit de freqüència

**Stack**: arquitectura (php + js)
**Estat**: acceptat
**Data**: 2026-10-09

## Context

El formulari de contacte (decisió 007) enviava amb un POST clàssic a `contacto.php`, que responia una pàgina HTML completa: l'usuari sortia de `index.html`, no es repoblava el formulari i no hi havia cap protecció contra enviaments repetits ni contra peticions d'altres orígens.

Es van valorar els mecanismes habituals en aquest tipus de formulari: enviament amb `fetch` i JSON, token CSRF per sessió, límit de freqüència i reCAPTCHA v3. També es van identificar errors freqüents que **s'eviten**: la capçalera `X-Mailer` sense `\r\n` (el `Content-Type` queda enganxat), l'assumpte sense codificar, `htmlspecialchars` en un correu de text pla, `error_reporting(0)` i `@mail`, la comparació del CSRF sense `hash_equals`, el missatge del servidor posat amb `innerHTML`, un honeypot que respon error i delata el bot, `CURLOPT_SSL_VERIFYPEER=false`, claus al codi, rutes `/php/...` escrites a mà i un límit de freqüència que llegeix l'hora de l'últim enviament però **no l'escriu mai**.

## Decisió

- **Enviament amb `fetch`** (`FormData`, `Accept: application/json`, `credentials: same-origin`) des de `initContactForm` a `assets/js/script.js`, sense recarregar la pàgina. El botó passa a «Enviant…» i queda desactivat, i es reactiva sempre en un `finally`.
- **Resposta JSON** de `contacto.php` quan la petició demana JSON (`wantsJson()`): `{ok, message}` a l'èxit i `{ok:false, message, errors:{camp: missatge}, list:[…]}` al 422. Codis: 200, 403, 405, 422, 429, 500 i 503.
- **Mateixa ruta per a HTML i JSON**: si la petició **no** demana JSON, `contacto.php` serveix la pàgina HTML de la decisió 007 (ara també amb 403 i 429). Sense JS el formulari funciona com abans.
- **Token CSRF per sessió** (`token.php`, només GET, `Cache-Control: no-store`). La sessió `vc_contact` porta la cookie `HttpOnly`, `SameSite=Lax`, `Secure` només sota HTTPS i el camí de `cookieBasePath()`. El servidor valida amb `hash_equals` **abans** de fer cap feina costosa i el token **es rota** després d'un enviament correcte.
- **Sense condició de cursa**: el botó queda desactivat fins que el token és carregat. Si no es pot obtenir, es mostra l'error i el botó es reactiva; el `submit` torna a demanar el token abans d'enviar, així que es pot reintentar.
- **`isSameOrigin()`** (de `includes/blog.php`) es manté com a segona defensa a `contacto.php` i a `token.php`.
- **Límit de freqüència**: mínim 10 s entre enviaments per sessió. `last_submit_time` s'**escriu** només després d'un enviament correcte (`markContactSubmitted`) i es comprova abans de validar. Resposta 429 amb `Retry-After`.
- **Errors per camp**: `validateContact` retorna també `fieldErrors` (camp → missatge). El JS els pinta als `<p id="err-*">` existents (decisió 040), marca `aria-invalid="true"`, porta el focus al primer camp invàlid en l'ordre del DOM i afegeix al text del toast els errors de camps sense `err-*` (càrrec, telèfon, missatge). Es netegen en escriure al camp.
- **Missatges globals accessibles**: toast fix a baix a la dreta (decisió 052), amb dues regions persistents `role="status"` (èxit) i `role="alert"` (error). Els textos es posen amb `textContent`, mai amb `innerHTML`. A l'èxit es reinicia el formulari. Abans d'aquesta decisió eren dues regions dins del formulari.
- **Rutes relatives**: l'URL del token viu a `data-token-url="token.php"` i l'enviament usa `form.action`.
- **Estructura**: `includes/http.php` (`wantsJson`, `respondJson`, abans a `like.php`), `includes/contact-security.php` (sessió, CSRF, límit), `includes/recaptcha.php` i `includes/recaptcha-config.php`.

## Política de CSRF, límit i enviament sense JS

| Camí | Origen | Token CSRF | Límit 10 s | reCAPTCHA (si és actiu) |
|---|---|---|---|---|
| `fetch` amb JSON | obligatori | **obligatori** | sí | obligatori |
| POST clàssic sense JS | obligatori | no (no hi ha cap manera de dur-lo en una pàgina estàtica) | sí | obligatori, així que sense JS no es pot enviar |

El POST clàssic es manté perquè el formulari ha de funcionar sense JS. Això vol dir que el token CSRF **no impedeix** que un client programat enviï el formulari ometent la capçalera `Accept: application/json`: la protecció real d'aquest camí és l'origen, el límit de freqüència i el honeypot. El token protegeix el camí JSON i, sobretot, evita que un altre lloc faci enviar el formulari a un navegador amb sessió.

## Què s'ha adoptat i què s'ha descartat

| Mecanisme | Decisió |
|---|---|
| `fetch` + JSON i botó «Enviant…» | **Adoptat**, amb un toast accessible amb regions `role="status"` / `role="alert"` (decisió 052) |
| Token CSRF per sessió amb endpoint | **Adoptat**, amb `hash_equals`, rotació i validació abans de res més |
| Límit de freqüència de 10 s | **Adoptat**, escrivint realment la hora |
| reCAPTCHA v3 | **Implementat, però desactivat** (vegeu més avall) |
| `.env` propi i `error_reporting(0)` | Descartat: es manté `includes/mail-config.php` sense secrets |
| Honeypot que respon error | Descartat: es manté `website` amb èxit silenciós |
| Toasts amb `innerHTML` | Descartat: `textContent` i regions accessibles |
| Validació del servidor minsa | Descartat: es manté `validateContact` estricta i l'anti-injecció de capçaleres |
| Rutes `/php/...` absolutes | Descartat: rutes relatives |

## reCAPTCHA v3: implementat, a falta de claus reals

**Avui NO hi ha cap captcha efectiu.** No hi ha claus: la de lloc és `PENDENT_CLAU_DE_SITE` i la secreta no existeix. Mentre siguin un valor de reompliment, reCAPTCHA queda **desactivat de manera segura**: `token.php` retorna `recaptcha: null`, el navegador no carrega cap script de Google, el servidor no exigeix cap token i el formulari queda protegit només per **CSRF + límit de freqüència + honeypot + `isSameOrigin()`**.

Quan hi hagi claus reals s'activa sol:

- El client carrega `api.js` amb la clau de lloc i obté un token amb l'acció `contacte` just abans d'enviar.
- El servidor verifica amb `siteverify` (cURL amb **verificació SSL activada**, 3 s de connexió i 5 s en total) i comprova `success`, `action === 'contacte'`, `hostname === 'www.vicity.cat'` i `score >= 0.5` (`min_score`).
- Si la verificació falla, respon 403 amb un missatge genèric. Si Google no respon, **falla tancat**: 503 amb un missatge clar. No es salta mai la verificació per `localhost`.

**Per activar-lo**:

1. Crear les claus reCAPTCHA v3 per al domini `www.vicity.cat`.
2. A `includes/recaptcha-config.php`, substituir `'PENDENT_CLAU_DE_SITE'` per la clau de lloc.
3. Copiar `includes/secrets.example.php` a `includes/secrets.local.php` (ignorat per git) i posar la clau secreta a `recaptcha_secret`. **La clau secreta no s'ha de pujar mai al repositori, que és públic.**
4. Comprovar que PHP té cURL i que `curl.cainfo` apunta a un paquet de CA (si no, la verificació SSL fallarà i el formulari respondrà 503).
5. Comprovar que `token.php` retorna la clau a `recaptcha` i fer un enviament de prova.

## Conseqüències

- Sense JS el formulari funciona, però no es pot enviar si reCAPTCHA s'activa.
- La sessió per al límit exigeix cookies: un client sense cookies se salta el límit, però no el CSRF del camí JSON.
- Un atacant pot esgotar el límit d'una sessió pròpia, però no el d'altres usuaris, perquè és per sessió i no global.
- `mail()` no s'ha tocat: continua pendent SPF/DKIM (decisió 007).
- La nota obsoleta de la 007 sobre `mailto:` i la resposta sense redirecció queda actualitzada.

## Comprovat i pendent

**Comprovat** (còpia del projecte amb `php -S`, receptor SMTP fals i stub de `siteverify`, cap correu real; Chrome real per CDP):

- `php -l` de tot el que s'ha tocat i `node --check` de `script.js`.
- `token.php`: JSON, cookie `HttpOnly; SameSite=Lax`, `no-store`; POST respon 405.
- `contacto.php`: JSON sense token 403, token invàlid 403, origen creuat 403, token i dades vàlids 200 amb un correu rebut, segon enviament immediat 429, dades invàlides 422 amb `errors` per camp, `\r\nBcc:` rebutjat al nom i al correu, honeypot 200 sense correu, GET 405, POST clàssic amb pàgina HTML i 429 també en HTML. El correu rebut porta assumpte codificat, `Reply-To` correcte i cap `Bcc`.
- reCAPTCHA activat amb el stub: sense token, puntuació baixa, acció o `hostname` incorrectes i `success` fals donen 403; Google caigut dóna 503; correcte dóna 200. Tornant a les claus de reompliment queda desactivat.
- Navegador: el formulari s'envia sense cap navegació de pàgina (`window` es manté), els camps buits mostren els `err-*` natius i el focus va a «Nom i cognoms», un nom només d'espais dóna l'error del servidor al camp amb `aria-invalid` i el focus, l'èxit mostra el missatge a la regió `status`, el 429 i la manca de xarxa mostren l'error i el botó es reactiva.

**No provat**: reCAPTCHA amb Google de veritat, l'enviament real de correu, `Secure` sota HTTPS, el comportament en mòbil i amb lector de pantalla, i Apache amb el `.htaccess`.
