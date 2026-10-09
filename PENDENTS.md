# Pendents abans de lliurar la web

Estat revisat el 2026-10-09 sobre el repositori i amb comprovacions a l'entorn local (XAMPP). Cada punt indica la prioritat, qui l'ha de fer i on és al codi.

**Prioritats:** `BLOQUEJANT` (no es pot publicar sense això) · `RECOMANAT` (s'hauria de fer abans de lliurar) · `OPCIONAL`.
**Responsable:** `Vora Studio` o `Client`.

## Veredicte

**No està llesta per publicar-se. Sí que està llesta per a una revisió amb el client.**

La funcionalitat (inici, blog, likes i visites, formulari amb CSRF, vídeos i toast) funciona i s'ha provat amb un servidor local i Chrome. El que impedeix publicar-la no és el codi, sinó les dades reals: les pàgines legals són un esborrany amb camps `[A COMPLETAR]`, les dades de contacte semblen d'exemple, hi ha una entrada de prova al blog i el correu del formulari no s'ha provat amb enviaments reals.

Abans de publicar també cal decidir què fer amb la protecció del formulari: sense captcha efectiu i amb el límit de freqüència per sessió, qualsevol bot pot enviar correu en massa (vegeu «Seguretat»).

Criteri: es considera llesta quan no quedi cap punt `BLOQUEJANT` marcat.

## Bloquejants abans de lliurar

- [ ] **Completar les pàgines legals** · `BLOQUEJANT` · Client (revisió jurídica) + Vora Studio
  On: `avis-legal.html`, `privacitat.html`, `cookies.html` (cerca `A COMPLETAR`). Falten: NIF/CIF, raó social, adreça, dades del registre mercantil, correu de contacte, delegat de protecció de dades, termini de conservació, encarregats del tractament i autoritat de control. Els tres textos s'identifiquen ells mateixos com a «esborrany pendent de revisió jurídica» (línia 145 de cada fitxer).
  Com: el client facilita les dades, es substitueixen les marques i s'elimina l'avís d'esborrany.
- [ ] **Confirmar les dades de contacte** · `BLOQUEJANT` · Client
  On: telèfon `+34 93 123 45 67` a `index.html:239`, `index.html:1513`, `assets/js/components/site-footer.js:134`, `avis-legal.html:129`, `privacitat.html:129`; adreça «Avinguda Diagonal, 456, Barcelona» a `index.html:1580` i `site-footer.js:142`; correu `info@vicity.cat` a `index.html:237`, `index.html:1543` i `site-footer.js:124`. El número i l'adreça tenen aspecte de dades d'exemple: a confirmar amb el client.
  Com: cercar i substituir pels valors reals, sense oblidar les dades estructurades (JSON-LD) de `index.html`.
- [ ] **Treure l'entrada de prova del blog** · `BLOQUEJANT` · Vora Studio
  On: `data/posts.php:132` (`entrada-de-prova-al-blog`).
  Com: esborrar aquest bloc de l'array. La resta d'entrades (vegeu «Contingut») s'han de confirmar amb el client.
- [ ] **Provar el formulari amb correu real i configurar el domini de correu** · `BLOQUEJANT` · Vora Studio + Client
  On: `includes/mail-config.php` (destinatari `info@vicity.cat`, remitent `noreply@vicity.cat`). Fins ara el correu només s'ha provat amb un receptor simulat, mai amb un enviament real.
  Com: confirmar que les dues bústies existeixen, configurar SPF, DKIM i DMARC al DNS de `vicity.cat` i enviar un missatge de prova des de la web publicada.
- [ ] **Prova manual mínima en navegadors i dispositius reals** · `BLOQUEJANT` · Vora Studio
  On: 36 decisions d'`openspec/decisions` acaben amb «No provat». S'ha comprovat amb Chrome automatitzat, però no amb Safari, Firefox, mòbil real ni lector de pantalla.
  Com: recórrer inici, blog, un article, formulari (èxit i error), vídeos i toast a Chrome, Firefox, Safari i un mòbil.
- [ ] **Allotjament i HTTPS** · `BLOQUEJANT` · Vora Studio + Client
  On: tot el lloc. Les cookies `Secure` només s'activen si hi ha HTTPS (`includes/contact-security.php:19` i `includes/blog.php`).
  Com: apuntar `www.vicity.cat` al servidor, instal·lar el certificat i redirigir HTTP a HTTPS (a confirmar amb el client si el domini sense `www` ha de redirigir).

## Configuració del servidor / DNS

- [ ] **PHP 8.1 o superior amb `pdo_sqlite`, `curl`, `mbstring` i sessions** · `BLOQUEJANT` · Vora Studio
  On: el tipus de retorn `never` obliga a PHP 8.1 (`includes/http.php:10`, `like.php:8`); `new PDO('sqlite:…')` a `includes/db.php`; `curl_init` a `includes/recaptcha.php`; `mb_*` a `includes/contact.php` i `includes/blog.php`.
  Com: comprovar-ho al panell de l'allotjament abans de pujar la web.
- [ ] **Permisos d'escriptura a `data/`** · `BLOQUEJANT` · Vora Studio
  On: SQLite crea `data/blog.sqlite` i els fitxers temporals de la base. Localment, `data/` i `data/blog.sqlite` són escrivibles.
  Com: donar permís d'escriptura a la carpeta `data/` a l'usuari del servidor web.
- [ ] **Comprovar que els `.htaccess` s'apliquen** · `BLOQUEJANT` · Vora Studio
  On: `.htaccess` a l'arrel, `data/.htaccess` i `includes/.htaccess`. Localment `data/blog.sqlite`, `data/posts.php`, `includes/db.php` i `README.md` responen 403. Si l'allotjament no és Apache amb `AllowOverride`, aquestes barreres no funcionen.
  Com: després de publicar, demanar aquestes rutes al domini real i comprovar que retornen 403.
- [ ] **SPF, DKIM i DMARC** · `RECOMANAT` · Client (DNS) + Vora Studio
  On: DNS de `vicity.cat`. Sense això, els correus del formulari poden acabar a brossa (`includes/mail-config.php` ho avisa).
  Com: publicar els tres registres que indiqui el proveïdor de correu.
- [ ] **`php.ini` de producció** · `RECOMANAT` · Vora Studio
  On: servidor. Localment la resposta inclou `X-Powered-By: PHP/8.2.12`.
  Com: `display_errors=Off`, `expose_php=Off` i, si s'activa reCAPTCHA, `curl.cainfo` apuntant al paquet de certificats.
- [ ] **Pàgines 404 i 500 pròpies** · `RECOMANAT` · Vora Studio
  On: no hi ha cap `ErrorDocument` a `.htaccess`; ara surt la pàgina per defecte d'Apache.
  Com: crear les pàgines amb el mateix cap i peu i declarar-les a `.htaccess`.

## Contingut i dades reals

- [ ] **Revisar el text i les dates de les entrades del blog** · `RECOMANAT` · Client
  On: `data/posts.php` (8 entrades, una és de prova). No consta que el client hagi validat els textos: a confirmar amb el client.
  Com: el client revisa o substitueix els articles i s'ajusten `published_at` i `is_featured`.
- [ ] **Imatge per compartir a xarxes** · `RECOMANAT` · Vora Studio
  On: `og:image` és el logotip (2156×671) a `index.html:32` i `includes/head.php:29`, amb `twitter:card` de tipus `summary`.
  Com: crear una imatge 1200×630 i canviar el meta i la targeta a `summary_large_image`.
- [ ] **Imatges i enllaços de les entrades** · `OPCIONAL` · Client
  On: totes les entrades tenen `'images' => []` i `'links' => []`.
  Com: vegeu «Com afegir una entrada al blog».
- [ ] **Alternativa textual als vídeos** · `RECOMANAT` · Vora Studio
  On: els tres vídeos de `index.html` només tenen `aria-label` (decisions 044 i 051); falta una transcripció o descripció completa (WCAG 1.2.1).
  Com: afegir un text equivalent o una transcripció accessible.
- [ ] **Confirmar les versions finals dels vídeos** · `OPCIONAL` · Client
  On: `assets/video/vicity-demo.mp4`, `vicity-animacio.mp4`, `vicity-conversa.mp4`.
  Com: el client confirma que són definitius; si no, es substitueixen mantenint el nom i es regenera el pòster.
- [ ] **Icones del lloc** · `OPCIONAL` · Vora Studio
  On: només hi ha una icona SVG (`includes/head.php:21`).
  Com: afegir `apple-touch-icon` i una versió PNG.

## Legal i RGPD

- [ ] **Inventari de cookies a `cookies.html`** · `RECOMANAT` · Vora Studio + Client
  On: `cookies.html` té tres marques «A COMPLETAR: detallar o indicar que no n'hi ha». Les cookies reals del lloc són `vc_l` (likes), `vc_v` (visites) i `vc_contact` (sessió tècnica del formulari). No s'ha trobat cap eina d'analítica (`gtag`, Matomo, etc.).
  Com: llistar-les amb finalitat, durada i titular. Si només hi ha cookies tècniques, el client confirma si cal bàner de consentiment.
- [ ] **Actualitzar la política de privacitat si s'activa reCAPTCHA** · `RECOMANAT` · Client
  On: `privacitat.html` no esmenta Google.
  Com: afegir Google com a tercer, la finalitat de seguretat i les transferències internacionals.
- [ ] **Termini de conservació i encarregats del tractament** · `RECOMANAT` · Client
  On: `privacitat.html` (marques «indicar termini» i «indicar encarregats del tractament»).
  Com: el client indica quant de temps conserva els correus rebuts i quin proveïdor d'allotjament i de correu intervé.
- [ ] **Registre d'activitats de tractament** · `OPCIONAL` · Client
  On: fora del repositori. A confirmar amb el client.
  Com: el client o el seu assessor el redacta.

## Seguretat

- [ ] **Activar reCAPTCHA v3** · `RECOMANAT` · Vora Studio + Client
  On: `includes/recaptcha-config.php:10` (`PENDENT_CLAU_DE_SITE`) i `includes/secrets.example.php:7`. Avui **no hi ha cap captcha efectiu**: el formulari es protegeix només amb token CSRF, límit de 10 s, honeypot i comprovació d'origen.
  Com: vegeu «Com activar reCAPTCHA».
- [ ] **Capçaleres HSTS i CSP** · `RECOMANAT` · Vora Studio
  On: `.htaccess:19-20` només posa `X-Content-Type-Options` i `Referrer-Policy`. Una CSP estricta no és compatible amb Tailwind en mode navegador (vegeu «SEO i rendiment»).
  Com: afegir `Strict-Transport-Security` un cop hi hagi HTTPS; la CSP, després de compilar el CSS.
- [ ] **Decidir si el repositori ha de ser públic** · `RECOMANAT` · Client
  On: segons les notes del projecte, `VoraStudio/viceCity` és públic: a confirmar. `docs/` no s'hi versiona (`.gitignore`).
  Com: si no ha de ser públic, passar-lo a privat. No s'han trobat claus ni credencials a l'arbre ni a l'historial (cerca amb `git grep`, amb patrons limitats).
- [ ] **Exigir les claus de reCAPTCHA a producció (que falli tancat)** · `RECOMANAT` (`BLOQUEJANT` si es publica sense captcha) · Vora Studio
  On: `includes/recaptcha.php:17-22` i `includes/recaptcha-config.php:10-11`. Si falta `secrets.local.php` o les claus són de relleno, `recaptchaEnabled()` és fals i `contacto.php` se salta la verificació sense avisar: el formulari queda només amb honeypot, CSRF i límit de freqüència.
  Com: afegir un mode `prod` (per exemple una variable d'entorn) que exigeixi les claus i, si no hi són, respongui amb error en lloc de desactivar el captcha.
- [ ] **Límit de freqüència del formulari per IP o amb captcha actiu** · `RECOMANAT` (`BLOQUEJANT` si es publica sense captcha) · Vora Studio
  On: `includes/contact-security.php:50-55`. El límit de 10 s es guarda a la sessió: un bot que no envia la cookie `vc_contact` obté una sessió nova a cada petició i pot enviar correu en massa a `info@vicity.cat`. Hi ha una trampa de temps de 3 s (decisió 010) que atura els bots que envien a l'instant, però no un bot que esperi o que canviï de sessió.
  Com: activar reCAPTCHA o afegir un límit per IP (fitxer o base de dades).
- [ ] **Nom de domini fix a la verificació de reCAPTCHA** · `OPCIONAL` · Vora Studio
  On: `includes/recaptcha-config.php:14` (`hostname` fixat a `www.vicity.cat`). Amb aquest valor el captcha no funcionarà en un entorn de proves o de preproducció.
  Com: llegir el domini esperat de la configuració per entorn.
- [ ] **Enviament sense JavaScript sense token CSRF** · `OPCIONAL` · Vora Studio
  On: `contacto.php:27` i decisió 010. La ruta sense JS només valida l'origen (`isSameOrigin()`) perquè una pàgina estàtica no pot dur el token. Límit conegut i de risc baix.
  Com: es resol convertint `index.html` en `index.php` (vegeu «Deute tècnic»).
- [ ] **Límits antiabús dels comptadors** · `OPCIONAL` · Vora Studio
  On: decisió 008. Els likes i les visites depenen de cookies i es poden inflar.
  Com: afegir límit per IP o captcha si el client ho demana. Mentrestant els comptadors són orientatius.

## SEO i rendiment

- [ ] **Decidir la indexació del blog** · `RECOMANAT` · Client + Vora Studio
  On: `includes/head.php:18` posa `noindex, follow` a totes les pàgines PHP i `sitemap.xml` només té 4 adreces (decisió 006).
  Com: si el blog ha de sortir a cercadors, treure el meta `noindex` i afegir `blog.php` i els articles al `sitemap.xml`.
- [ ] **Compilar el CSS de Tailwind per a producció** · `RECOMANAT` · Vora Studio
  On: `@tailwindcss/browser` es carrega des del CDN a `includes/head.php:41` i a `index.html`; genera els estils al navegador en cada visita.
  Com: generar un CSS estàtic i eliminar el script del CDN; això també permetria una CSP.
- [ ] **Dependències des de CDN** · `OPCIONAL` · Vora Studio
  On: Tailwind, GSAP, ScrollTrigger i SplitText des de `cdn.jsdelivr.net`, amb SRI (`includes/head.php:42-60`).
  Com: servir-les en local si es vol evitar que la IP del visitant arribi a un tercer o dependre del CDN.
- [ ] **Executar Lighthouse i una revisió d'accessibilitat** · `RECOMANAT` · Vora Studio
  On: no s'ha executat cap auditoria.
  Com: Lighthouse a inici, blog i article, i `axe` per a l'accessibilitat.

## Proves pendents

- [ ] **Safari, Firefox i mòbil** · `BLOQUEJANT` (vegeu «Bloquejants») · Vora Studio
  On: vegeu el punt de proves mínimes.
  Com: llista de comprovació: inici, blog amb filtre, article amb likes, formulari, vídeos i toast.
- [ ] **Lector de pantalla** · `RECOMANAT` · Vora Studio
  On: formulari, toast, botó de pausa dels vídeos, carrusel (decisions 040, 041, 044, 051, 052).
  Com: NVDA a Windows i VoiceOver a Mac o iPhone.
- [ ] **Avís sense JavaScript** · `RECOMANAT` · Vora Studio
  On: decisió 042, marcada com a «No provat».
  Com: desactivar JavaScript a Chrome i recarregar inici, blog i formulari.
- [ ] **Enllaços trencats** · `RECOMANAT` · Vora Studio
  On: no hi ha cap revisió automàtica.
  Com: passar un rastrejador d'enllaços sobre el domini publicat.
- [ ] **reCAPTCHA amb Google real** · `OPCIONAL` · Vora Studio
  On: només s'ha provat amb un simulador; vegeu «Com activar reCAPTCHA».
  Com: provar-ho quan hi hagi claus.

## Operativa i traspàs al client

- [ ] **Còpia de seguretat de `data/blog.sqlite`** · `RECOMANAT` · Vora Studio + Client
  On: el contingut es regenera des de `data/posts.php`, però els likes i les visites només viuen a la base.
  Com: còpia periòdica d'aquest fitxer (no es versiona).
- [ ] **No pujar material intern** · `RECOMANAT` · Vora Studio
  On: `docs/`, `.git`, `.atl` i `openspec/` no han d'arribar al servidor (el `.htaccess` ja els bloqueja, però millor no pujar-los).
  Com: desplegar excloent aquestes carpetes.
- [ ] **Crear `includes/secrets.local.php` al servidor** · `OPCIONAL` · Vora Studio
  On: el fitxer és a `.gitignore` i no es desplega amb el repositori.
  Com: només cal si s'activa reCAPTCHA.
- [ ] **Traspàs d'accessos i de manteniment** · `RECOMANAT` · Vora Studio + Client
  On: fora del repositori. A confirmar amb el client.
  Com: lliurar accessos a l'allotjament, al DNS i al correu, i explicar qui edita `data/posts.php`.

## Deute tècnic

Res d'això impedeix publicar; són millores de manteniment.

- [ ] **Validació d'imatges i enllaços duplicada** · `OPCIONAL` · Vora Studio
  On: `includes/db.php:215-272` (`isValidPostImage` i `isValidPostLink` repeteixen les regles de `validatePostImages` i `validatePostLinks`, amb els límits 200, 300 i 120 escrits dues vegades).
  Com: extreure els límits a constants o fer que `validate*` reutilitzi `isValid*`.
- [ ] **`initContactForm` fa massa coses** · `OPCIONAL` · Vora Studio
  On: `assets/js/script.js` (unes 135 línies: token i reCAPTCHA, errors per camp, estat del botó i enviament).
  Com: moure els errors per camp a un mòdul, com ja es va fer amb `toast.js`.
- [ ] **Acció de reCAPTCHA escrita a dos llocs** · `OPCIONAL` · Vora Studio
  On: `"contacte"` a `assets/js/script.js` i `'action' => 'contacte'` a `includes/recaptcha-config.php:13`. Si divergeixen, el servidor rebutja el token sense avisar.
  Com: que `token.php` lliuri l'acció juntament amb la clau de lloc.
- [ ] **Listeners `input` i `change` duplicats** · `OPCIONAL` · Vora Studio
  On: `assets/js/script.js` (`initContactForm`, mateix handler registrat dues vegades).
  Com: registrar-lo amb un bucle sobre els dos esdeveniments.
- [ ] **Passar `index.html` a `index.php`** · `OPCIONAL` · Vora Studio
  On: `index.html`, `assets/js/components/home-href.js`, `avis-legal.html`, `privacitat.html`, `cookies.html`, `includes/head.php`, `contacto.php` i `sitemap.xml` (referències a `index.html`).
  Com: generar el token CSRF al servidor i incrustar-lo a la pàgina (sense `token.php` ni espera del botó), compartir `head.php` i `foot.php` amb el blog i afegir una redirecció 301 de `index.html` a `index.php` a `.htaccess`.

## Com activar reCAPTCHA

reCAPTCHA queda desactivat mentre les claus siguin de relleno (`includes/recaptcha.php:12-21`).

1. Crear les claus reCAPTCHA v3 a la consola de Google per al domini `www.vicity.cat`.
2. Posar la clau de lloc a `includes/recaptcha-config.php` (`site_key`, línia 10).
3. Copiar `includes/secrets.example.php` a `includes/secrets.local.php` (ignorat per git) i posar-hi `recaptcha_secret`.
4. Comprovar `hostname` i `min_score` (0,5) a `includes/recaptcha-config.php`.
5. Comprovar que cURL té `curl.cainfo` configurat; si no, la verificació respondrà 503.
6. Actualitzar `privacitat.html` i `cookies.html` (Google com a tercer).
7. Provar el formulari: s'activa sol quan cap de les dues claus comença per `PENDENT`.

Atenció: amb reCAPTCHA actiu, l'enviament sense JavaScript queda bloquejat, perquè no pot aportar el token.

## Com afegir una entrada al blog

1. Obrir `data/posts.php` i afegir un bloc més a l'array amb els camps: `slug` (únic, minúscules i guions), `title`, `category` (una de `includes/categories.php`: `ia`, `recaptacio`, `integracio`, `casos-us`), `excerpt`, `body` (text pla; paràgrafs separats per una línia en blanc), `cover` (`lav`, `purple` o `ink`), `read_minutes`, `published_at` (`AAAA-MM-DD`), `is_featured` (només la destacada), `images` i `links`.
2. Si porta imatges, pujar-les a `assets/img/blog/<slug>/` (webp, jpg, jpeg, png o avif) i referenciar-les amb la ruta i un `alt` obligatori.
3. Desar i refrescar el blog: la base de dades es sincronitza sola en detectar el canvi del fitxer.
4. Per esborrar una entrada, treure el bloc de `data/posts.php`. Els likes i les visites de la resta no es perden.
