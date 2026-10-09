# Traspàs al client: pendents abans de publicar la web

Estat revisat el 2026-10-09 sobre el repositori i amb comprovacions a l'entorn local. Aquest document s'adreça al client i al seu proveïdor d'allotjament: Vora Studio lliura la web preparada, però **no té accés a l'allotjament, al domini ni al DNS**, i encara no se sap quin domini es contractarà. Tot el que depèn del servidor, del domini o de les dades reals és responsabilitat del client o del seu proveïdor.

## 0. Resum i veredicte

**La web està llesta per a una revisió amb el client (vista prèvia). No està llesta per publicar-se.**

El que impedeix publicar-la no és el codi, sinó el que només el client pot facilitar o comprovar: el domini i les adreces de correu, les dades del titular, els textos legals revisats, el contingut real del blog i les proves finals al servidor real. Mentre quedi cap valor `PENDENT`, la web es mostra com a esborrany (franja visible, `noindex` i formulari desactivat), de manera que un desplegament sense configurar no passa desapercebut ni s'indexa.

**Criteri:** es considera llesta per publicar quan no quedi cap punt `BLOQUEJANT` sense marcar.

### Entregat i verificat per Vora Studio

Provat amb un servidor local i Chrome automatitzat (no amb Safari, Firefox, mòbil real, lector de pantalla, correu real ni servidor de producció).

- [x] Inici amb animacions d'entrada, carrusel, vídeos amb botó de pausa (hero, integració i IA) i toast accessible.
- [x] Blog amb filtre per categoria, pàgina d'article, imatges i enllaços opcionals per entrada, i comptadors de «m'agrada» i visites.
- [x] Formulari de contacte per `fetch` amb token CSRF, límit de freqüència de 10 s, trampa de temps de 3 s, honeypot i comprovació d'origen, i protecció de les capçaleres del correu.
- [x] Configuració única del lloc (`includes/site-config.php`): domini, correu i dades del titular en un sol fitxer.
- [x] El formulari **no envia res** mentre el destinatari o el remitent siguin `PENDENT` (respon 503).
- [x] `noindex` i franja d'esborrany mentre hi hagi dades pendents; `robots.txt` i `sitemap.xml` es generen sols amb el domini configurat.
- [x] reCAPTCHA v3 implementat i verificat amb un simulador, però **desactivat** fins que hi hagi claus reals.
- [x] Cap clau ni credencial versionada (`includes/secrets.local.php` i `includes/site-config.local.php` són a `.gitignore`).

## 1. Responsables i com llegir aquest document

Cada punt indica la prioritat, el responsable i on és al codi.

**Prioritats:** `BLOQUEJANT` (no es pot publicar sense això) · `RECOMANAT` (s'hauria de fer abans de publicar) · `OPCIONAL`.

**Responsables:**

| Etiqueta | Qui és | Què li correspon |
|---|---|---|
| `Client / allotjament` | El client o el seu proveïdor | Servidor, domini, DNS, correu, HTTPS i claus de reCAPTCHA |
| `Client (dades)` | El client | Dades del titular, textos i contingut |
| `Client / assessor jurídic` | El client o el seu assessor | Revisió dels textos legals |
| `Vora Studio (millora de codi)` | Vora Studio, si el client ho encarrega | Millores al codi que no requereixen accés al servidor |

Quan un punt només es pot comprovar al servidor real, porta l'etiqueta **Prova final del client**: Vora Studio no el pot verificar.

## 2. Configuració en un sol lloc

El domini, el correu del formulari i les dades del titular es defineixen **només** a `includes/site-config.php`. Els valors pendents comencen per `PENDENT`.

- [ ] **Crear `includes/site-config.local.php` amb els valors reals** · `BLOQUEJANT` · `Client / allotjament` (amb les dades de `Client (dades)`)
  On: copiar `includes/site-config.local.example.php` a `includes/site-config.local.php` (fitxer ignorat per git) i substituir els valors. També es pot editar directament `includes/site-config.php`. Els valors locals se sumen als per defecte.
  Com: omplir `base_url` (amb `https` i sense barra final), `mail_recipient`, `mail_sender`, `mail_subject_prefix` i tots els camps d'`owner` (raó social, NIF/CIF, domicili, registre mercantil, correu i telèfon de contacte, contacte del DPD i autoritat de control).

Què passa mentre quedi algun valor `PENDENT`:

- Les pàgines PHP (`blog.php`, `articulo.php`, `contacto.php`) mostren una franja fixa d'esborrany a dalt i porten `noindex, nofollow`.
- `robots.txt` diu `Disallow: /`.
- El formulari respon 503 («El formulari encara no està configurat.») i no envia res.
- `blog_indexable` és `false`: el blog segueix amb `noindex` i fora del `sitemap.xml` fins que es posi a `true`. **Abans d'activar-ho cal netejar les entrades de prova** (vegeu «Contingut»).

Atenció: la detecció només comprova que el valor no contingui el text `PENDENT`. Un valor sense sentit però sense aquesta paraula es considerarà configurat.

## 3. Requisits del servidor i desplegament

- [ ] **PHP 8.1 o superior amb `pdo_sqlite`, `curl`, `mbstring` i sessions** · `BLOQUEJANT` · `Client / allotjament`
  On: el tipus de retorn `never` obliga a PHP 8.1 (`includes/http.php`, `like.php`); `new PDO('sqlite:…')` a `includes/db.php`; `curl_init` a `includes/recaptcha.php`; `mb_*` a `includes/contact.php` i `includes/blog.php`.
  Com: comprovar-ho al panell de l'allotjament abans de pujar la web.
- [ ] **Apache amb `mod_rewrite` i `AllowOverride`** · `BLOQUEJANT` · `Client / allotjament`
  On: `.htaccess` a l'arrel, `data/.htaccess` i `includes/.htaccess`. Protegeixen `data/` i `includes/` i serveixen `/robots.txt` i `/sitemap.xml` (que són `robots.php` i `sitemap.php`).
  Com: si el servidor no és Apache o no permet `.htaccess`, aquestes barreres no funcionen i cal configurar l'equivalent (bloquejar `data/`, `includes/`, `openspec/` i fitxers `.md`, `.sqlite` i ocults, i servir els dos fitxers generats). Sense `mod_rewrite` no es serveixen `/robots.txt` ni `/sitemap.xml` (es poden enllaçar `robots.php` i `sitemap.php` directament).
- [ ] **Permisos d'escriptura a `data/`** · `BLOQUEJANT` · `Client / allotjament`
  On: SQLite crea `data/blog.sqlite` i fitxers temporals a la mateixa carpeta.
  Com: donar permís d'escriptura a `data/` a l'usuari del servidor web.
- [ ] **HTTPS** · `BLOQUEJANT` · `Client / allotjament`
  On: tot el lloc. Les cookies `Secure` només s'activen si la petició és HTTPS (`includes/contact-security.php`, `includes/blog.php`).
  Com: instal·lar el certificat i redirigir HTTP a HTTPS; decidir si el domini sense `www` ha de redirigir (a confirmar amb el client).
- [ ] **Comprovar que les rutes protegides responen 403** · `BLOQUEJANT` · `Client / allotjament` · **Prova final del client**
  On: `data/blog.sqlite`, `data/posts.php`, `includes/site-config.php`, `includes/db.php` i `README.md`.
  Com: demanar-les al domini real; han de retornar 403. Localment ho fan; al servidor del client no s'ha pogut comprovar.
- [ ] **`php.ini` de producció** · `RECOMANAT` · `Client / allotjament`
  On: servidor. Localment la resposta inclou `X-Powered-By: PHP/8.2.12`.
  Com: `display_errors=Off`, `expose_php=Off` i, si s'activa reCAPTCHA, `curl.cainfo` apuntant al paquet de certificats.
- [ ] **Capçaleres HSTS i CSP** · `RECOMANAT` · `Client / allotjament`
  On: `.htaccess` només posa `X-Content-Type-Options` i `Referrer-Policy`. Una CSP estricta no és compatible amb Tailwind en mode navegador (vegeu «Deute tècnic»).
  Com: afegir `Strict-Transport-Security` quan hi hagi HTTPS; la CSP, després de compilar el CSS.
- [ ] **Pàgines 404 i 500 pròpies** · `RECOMANAT` · `Vora Studio (millora de codi)`
  On: no hi ha cap `ErrorDocument` a `.htaccess`; ara surt la pàgina per defecte del servidor.
  Com: crear les pàgines amb el mateix cap i peu i declarar-les a `.htaccess`.
- [ ] **Còpia de seguretat de `data/blog.sqlite`** · `RECOMANAT` · `Client / allotjament`
  On: el contingut es regenera des de `data/posts.php`, però els likes i les visites només viuen a la base (no es versiona).
  Com: còpia periòdica d'aquest fitxer.
- [ ] **No pujar material intern** · `RECOMANAT` · `Client / allotjament`
  On: `docs/`, `.git`, `.github`, `.atl` i `openspec/` no han d'arribar al servidor.
  Com: desplegar excloent aquestes carpetes (el `.htaccess` ja les bloqueja, però millor no pujar-les).

## 4. Domini, DNS i correu

- [ ] **Contractar el domini i fixar-lo a la configuració** · `BLOQUEJANT` · `Client / allotjament`
  On: `base_url` a `includes/site-config.php` o `site-config.local.php`. Encara no se sap quin domini es contractarà.
  Com: vegeu «Configuració en un sol lloc» i «Fitxers amb dades escrites a mà».
- [ ] **Crear les bústies de destí i de remitent** · `BLOQUEJANT` · `Client / allotjament`
  On: `mail_recipient` i `mail_sender` a la configuració. Fins que no es posin, el formulari respon 503.
  Com: el remitent ha de ser una adreça del mateix domini que el servidor.
- [ ] **SPF, DKIM i DMARC** · `RECOMANAT` · `Client / allotjament`
  On: DNS del domini contractat. Sense això, els correus del formulari poden acabar a brossa (`includes/mail-config.php` ho recorda).
  Com: publicar els tres registres que indiqui el proveïdor de correu.
- [ ] **Prova d'enviament real del formulari** · `BLOQUEJANT` · `Client / allotjament` · **Prova final del client**
  On: formulari de contacte de la web publicada. Fins ara el correu només s'ha provat amb un receptor simulat, mai amb un enviament real.
  Com: enviar un missatge de prova i comprovar que arriba a la bústia de destí, amb el remitent i el `Reply-To` correctes, i que no cau a brossa.
- [ ] **Confirmar que l'allotjament permet `mail()`** · `RECOMANAT` · `Client / allotjament`
  On: `includes/contact.php` envia amb la funció `mail()` de PHP, sense SMTP.
  Com: si l'allotjament no la permet o la bloqueja, caldrà un enviament per SMTP, que és un canvi de codi (`Vora Studio (millora de codi)`). A confirmar amb el proveïdor.

## 5. reCAPTCHA v3

**Avui NO hi ha cap captcha efectiu.** Mentre les claus siguin de relleno, reCAPTCHA queda desactivat: el formulari es protegeix només amb token CSRF, límit de 10 s per sessió, trampa de temps de 3 s, honeypot i comprovació d'origen. Això atura enviaments duplicats i bots simples, però **no un bot que usi el formulari com un navegador**.

- [ ] **Crear les claus de reCAPTCHA v3 i activar-lo** · `RECOMANAT` (`BLOQUEJANT` si es publica sense altra protecció) · `Client / allotjament`
  On: `includes/recaptcha-config.php` (`site_key`) i `includes/secrets.local.php` (`recaptcha_secret`).
  Com: vegeu «Com activar reCAPTCHA».
- [ ] **Provar reCAPTCHA amb Google real** · `RECOMANAT` · `Client / allotjament` · **Prova final del client**
  On: només s'ha provat amb un simulador.
  Com: un cop hi hagi claus, enviar el formulari i comprovar que s'accepta i que un enviament sense token és rebutjat.
- [ ] **Exigir les claus a producció (que falli tancat)** · `RECOMANAT` (`BLOQUEJANT` si es publica sense captcha) · `Vora Studio (millora de codi)`
  On: `includes/recaptcha.php` (`recaptchaEnabled()`) i `includes/recaptcha-config.php`. Si falta `secrets.local.php` o les claus són de relleno, el servidor se salta la verificació sense avisar.
  Com: afegir un mode `prod` que exigeixi les claus i, si no hi són, respongui amb error en lloc de desactivar el captcha.
- [ ] **Límit de freqüència per IP, o captcha actiu** · `RECOMANAT` (`BLOQUEJANT` si es publica sense captcha) · `Vora Studio (millora de codi)`
  On: `includes/contact-security.php`. El límit de 10 s es guarda a la sessió: un bot que no envia la cookie `vc_contact` obté una sessió nova a cada petició i podria enviar correu en massa a l'adreça de destí.
  Com: activar reCAPTCHA o afegir un límit per IP (fitxer o base de dades).
- [ ] **Actualitzar la política de privacitat si s'activa reCAPTCHA** · `RECOMANAT` · `Client / assessor jurídic`
  On: `privacitat.html` no esmenta Google.
  Com: afegir Google com a tercer, la finalitat de seguretat i les transferències internacionals.

### Com activar reCAPTCHA

reCAPTCHA s'activa sol quan cap de les dues claus comença per `PENDENT` (`includes/recaptcha.php`).

1. Crear les claus reCAPTCHA v3 a la consola de Google per al domini definitiu. El `hostname` que es verifica surt de `base_url` a la configuració del lloc, així que no cal tocar-lo.
2. A `includes/recaptcha-config.php`, posar la clau de lloc a `site_key` en lloc de `PENDENT_CLAU_DE_SITE`.
3. Copiar `includes/secrets.example.php` a `includes/secrets.local.php` (ignorat per git) i posar-hi la clau secreta a `recaptcha_secret`. **Mai no es puja al repositori.**
4. Comprovar que PHP té cURL i que `curl.cainfo` apunta a un paquet de certificats; si no, la verificació SSL fallarà i el formulari respondrà 503.
5. Comprovar `min_score` (0,5) a `includes/recaptcha-config.php`.
6. Actualitzar `privacitat.html` i `cookies.html` (Google com a tercer).
7. Fer un enviament de prova.

Atenció: amb reCAPTCHA actiu, l'enviament sense JavaScript queda bloquejat, perquè no pot aportar el token.

## 6. Dades legals

Hi ha dues coses diferents: les **dades que ha de facilitar el client** i el **text que ha de revisar un assessor jurídic**.

### Dades que ha de facilitar el client

- [ ] **Facilitar les dades del titular i del tractament** · `BLOQUEJANT` · `Client (dades)`

| Dada | Camp de la configuració | On apareix ara |
|---|---|---|
| Raó social | `owner.razon_social` | `avis-legal.html:148`, `privacitat.html:147` |
| NIF/CIF | `owner.nif` | `avis-legal.html:148`, `privacitat.html:147` |
| Domicili | `owner.domicilio` | `avis-legal.html:148`, `privacitat.html:147`, `index.html` (~1581), `assets/js/components/site-footer.js:142` |
| Registre mercantil (tom, foli i full) | `owner.registro_mercantil` | `avis-legal.html:148` |
| Correu de contacte | `owner.email_contacto` | `avis-legal.html:148` i `:129`, `privacitat.html:147` i `:129`, `cookies.html:129`, `index.html` (~238, ~1544), `site-footer.js:124` |
| Telèfon | `owner.telefono` | `index.html` (~240, ~1511), `site-footer.js:134`, les tres pàgines legals (`:129`) |
| Contacte del DPD | `owner.dpd_contacto` | `privacitat.html:147` |
| Autoritat de control (AEPD o Autoritat Catalana de Protecció de Dades) | `owner.autoridad_control` | `privacitat.html:160` |
| Titular dels continguts i fur aplicable | (només al text) | `avis-legal.html:154` i `:162` |
| Termini de conservació de les dades | (només al text) | `privacitat.html:157` |
| Encarregats del tractament (allotjament, correu) | (només al text) | `privacitat.html:155` |

Importa: el telèfon `+34 93 123 45 67` i l'adreça «Avinguda Diagonal, 456, Barcelona» **tenen aspecte de dades d'exemple**. A confirmar amb el client.

Mentre la portada i les pàgines legals siguin HTML estàtic, **aquestes dades no es llegeixen encara de la configuració**: s'han de canviar a mà (vegeu «Fitxers amb dades escrites a mà»).

### Text que ha de revisar l'assessor jurídic

- [ ] **Revisió jurídica dels tres textos legals** · `BLOQUEJANT` · `Client / assessor jurídic`
  On: `avis-legal.html`, `privacitat.html` i `cookies.html`. Els tres s'identifiquen ells mateixos com a «esborrany pendent de revisió jurídica» (línia 145 de cada fitxer). **No són assessorament legal.**
  Com: l'assessor revisa i valida el text, es substitueixen les marques `[A COMPLETAR]` i s'elimina l'avís d'esborrany.
- [ ] **Inventari real de cookies a `cookies.html`** · `RECOMANAT` · `Client / assessor jurídic`
  On: `cookies.html:149`, `:150` (tres marques) i `:152`. Les cookies reals del lloc són `vc_l` (likes), `vc_v` (visites) i `vc_contact` (sessió tècnica del formulari). No s'ha trobat cap eina d'analítica (`gtag`, Matomo…). Totes són tècniques.
  Com: llistar-les amb finalitat, durada i titular, i confirmar si cal bàner de consentiment. **Si s'afegeix analítica, cal actualitzar `cookies.html` i `privacitat.html` i habilitar un consentiment previ.**
- [ ] **Registre d'activitats de tractament** · `OPCIONAL` · `Client / assessor jurídic`
  On: fora del repositori. A confirmar amb el client.
  Com: el client o el seu assessor el redacta.

## 7. Fitxers amb dades escrites a mà

La fase que portaria la portada i les pàgines legals a la configuració única **està en pausa a propòsit** (vegeu «Vista prèvia a GitHub Pages»). Mentre duri, el domini, el telèfon, l'adreça i el correu s'han de **canviar a mà** als fitxers següents. Les línies de `index.html` són aproximades i poden desplaçar-se.

- [ ] **Substituir el domini, el correu, el telèfon i l'adreça** · `BLOQUEJANT` · `Client / allotjament` (amb les dades de `Client (dades)`)

| Fitxer | Línies | Què hi ha escrit |
|---|---|---|
| `index.html` | 11, 30, 33, 49, 57-58 | Domini a `canonical`, `og:url`, `og:image`, `twitter:image` i JSON-LD (`url` i `logo`) |
| `index.html` | ~238, ~1544, ~1547 | Correu `info@vicity.cat` |
| `index.html` | ~240, ~1511 | Telèfon `+34 93 123 45 67` |
| `index.html` | ~1581 | Adreça «Avinguda Diagonal, 456, Barcelona» |
| `assets/js/components/site-footer.js` | 124, 126 | Correu |
| `assets/js/components/site-footer.js` | 134, 136 | Telèfon |
| `assets/js/components/site-footer.js` | 142 | Adreça |
| `avis-legal.html` | 8, 17, 18, 129 | Domini (`canonical`, `og:url`, `og:image`) i contacte (correu i telèfon) |
| `privacitat.html` | 8, 17, 18, 129 | Igual |
| `cookies.html` | 8, 17, 18, 129 | Igual |
| `avis-legal.html`, `privacitat.html`, `cookies.html` | vegeu la secció 6 | Marques `[A COMPLETAR]` |

Ja es **generen sols** amb el domini configurat: `robots.txt`, `sitemap.xml` i les pàgines PHP (`blog.php`, `articulo.php`, `contacto.php`), que llegeixen la configuració. El `sitemap.xml` generat encara llista les pàgines legals amb l'extensió `.html`.

## 8. Contingut

- [ ] **Treure l'entrada de prova del blog** · `BLOQUEJANT` · `Client (dades)`
  On: `data/posts.php:132` (`entrada-de-prova-al-blog`, «Entrada de prova al blog»).
  Com: esborrar aquest bloc de l'array. Els likes i les visites de la resta no es perden.
- [ ] **Revisar o substituir les altres 7 entrades** · `RECOMANAT` · `Client (dades)`
  On: `data/posts.php`. No consta que el client hagi validat els textos: a confirmar amb el client.
  Com: el client revisa o substitueix els articles; s'ajusten `published_at` i `is_featured`.
- [ ] **Decidir si el blog s'indexa** · `RECOMANAT` · `Client (dades)`
  On: `blog_indexable` a la configuració (decisió 006). Per defecte el blog té `noindex` i no surt al sitemap.
  Com: posar-lo a `true` només quan el contingut sigui definitiu; llavors el sitemap inclou el blog i totes les entrades.
- [ ] **Imatge per compartir a xarxes** · `RECOMANAT` · `Client (dades)`
  On: `og:image` és el logotip (2156×671) a `includes/head.php:39-43` i a `index.html`, amb `twitter:card` de tipus `summary`.
  Com: facilitar una imatge de 1200×630 i canviar el meta i la targeta a `summary_large_image`.
- [ ] **Confirmar les versions finals dels vídeos** · `OPCIONAL` · `Client (dades)`
  On: `assets/video/vicity-hero.mp4`, `vicity-animacio.mp4`, `vicity-conversa.mp4` i els seus pòsters.
  Com: el client confirma que són definitius; si no, se substitueixen mantenint el nom i es regenera el pòster.
- [ ] **Alternativa textual als vídeos** · `RECOMANAT` · `Vora Studio (millora de codi)`
  On: els tres vídeos de `index.html` només porten `aria-label` (decisions 044 i 051); falta una transcripció o descripció completa (WCAG 1.2.1).
  Com: afegir un text equivalent o una transcripció accessible.

### Com afegir una entrada al blog

1. Obrir `data/posts.php` i afegir un bloc més a l'array amb els camps: `slug` (únic, minúscules i guions), `title`, `category` (una de `includes/categories.php`: `ia`, `recaptacio`, `integracio`, `casos-us`), `excerpt`, `body` (text pla; paràgrafs separats per una línia en blanc), `cover` (`lav`, `purple` o `ink`), `read_minutes`, `published_at` (`AAAA-MM-DD`), `is_featured` (només la destacada), `images` i `links`.
2. Si porta imatges, pujar-les a `assets/img/blog/<slug>/` (webp, jpg, jpeg, png o avif) i referenciar-les amb la ruta i un `alt` obligatori; un enllaç és `['label' => '…', 'url' => 'https://…']` (només `http` o `https`). Format complet a la decisió 009.
3. Desar i refrescar el blog: la base de dades es sincronitza sola en detectar el canvi del fitxer.
4. Per esborrar una entrada, treure el bloc de `data/posts.php`.

## 9. Proves finals

Aquestes proves **només les pot fer el client** (o qui tingui accés al servidor real).

- [ ] **Formulari real en producció** · `BLOQUEJANT` · `Client / allotjament` · **Prova final del client**
  On: vegeu «Domini, DNS i correu».
  Com: un enviament de prova, amb èxit i amb error (camps buits), i comprovar el correu rebut.
- [ ] **HTTPS i cookies `Secure`** · `BLOQUEJANT` · `Client / allotjament` · **Prova final del client**
  On: tot el lloc.
  Com: comprovar el cadenat del navegador i que les cookies `vc_l`, `vc_v` i `vc_contact` porten `Secure`.
- [ ] **Safari, Firefox i mòbil real** · `BLOQUEJANT` · `Client / allotjament` · **Prova final del client**
  On: 36 decisions d'`openspec/decisions` acaben amb «No provat». Tot s'ha comprovat amb Chrome automatitzat.
  Com: recórrer inici, blog amb filtre, un article amb likes, formulari (èxit i error), vídeos i toast.
- [ ] **Lector de pantalla** · `RECOMANAT` · `Client / allotjament` · **Prova final del client**
  On: formulari, toast, botó de pausa dels vídeos i carrusel (decisions 040, 041, 044, 051 i 052).
  Com: NVDA a Windows i VoiceOver a Mac o iPhone.
- [ ] **Lighthouse i accessibilitat** · `RECOMANAT` · `Client / allotjament` · **Prova final del client**
  On: no s'ha executat cap auditoria.
  Com: Lighthouse a inici, blog i article, i `axe` per a l'accessibilitat.
- [ ] **Avís sense JavaScript** · `RECOMANAT` · `Client / allotjament` · **Prova final del client**
  On: decisió 042, marcada com a «No provat».
  Com: desactivar JavaScript i recarregar inici, blog i formulari.
- [ ] **Enllaços trencats** · `RECOMANAT` · `Client / allotjament` · **Prova final del client**
  On: no hi ha cap revisió automàtica.
  Com: passar un rastrejador d'enllaços sobre el domini publicat.

## 10. Vista prèvia a GitHub Pages

La vista prèvia per al client es publica a GitHub Pages (`.github/workflows/pages.yml`, que desplega tot el repositori excepte `.git`, `.github` i `docs`). GitHub Pages és **allotjament estàtic: no executa PHP**.

| Funciona | No funciona |
|---|---|
| Portada, pàgines legals, vídeos, animacions i carrusel | `blog.php` i `articulo.php` (el blog) |
| | `like.php` (likes i visites) |
| | `contacto.php` i `token.php` (el formulari) |

Conseqüències:

- **L'allotjament final necessita PHP**: el blog, els comptadors i el formulari no funcionen sense ell. Pages només serveix de vista prèvia.
- **Els `.htaccess` no s'apliquen a Pages**: allà `includes/*.php`, `data/posts.php` i `openspec/` es poden descarregar com a text. El contingut ja és públic al repositori i no hi ha cap secret versionat, però no és un desplegament vàlid per a producció.
- **La conversió de la portada i les pàgines legals a PHP està en pausa a propòsit**, per no trencar aquesta vista prèvia (si passessin a `.php`, Pages no les mostraria).
- **Opció futura**: un script d'exportació estàtica que generi tot l'HTML (portada, legals, blog i una pàgina per entrada) a partir de `data/posts.php` i la configuració. Els likes, les visites i el formulari quedarien inactius o s'haurien de substituir per un servei extern.
- [ ] **Decidir si el repositori ha de ser públic** · `RECOMANAT` · `Client (dades)`
  On: segons les notes del projecte, `VoraStudio/viceCity` és públic: a confirmar. Aquest document i les decisions d'`openspec` són visibles si ho és. No s'han trobat claus ni credencials a l'arbre ni a l'historial (cerca limitada).
  Com: si no ha de ser públic, passar-lo a privat.

## 11. Deute tècnic i millores opcionals

Res d'això impedeix publicar; són millores de manteniment, a càrrec de `Vora Studio (millora de codi)` si el client ho encarrega.

- [ ] **Compilar el CSS de Tailwind per a producció** · `RECOMANAT`
  On: `@tailwindcss/browser` es carrega des del CDN a `includes/head.php` i a `index.html`; genera els estils al navegador en cada visita.
  Com: generar un CSS estàtic i eliminar el script del CDN; això també permetria una CSP estricta.
- [ ] **Passar `index.html` i les legals a PHP** · `OPCIONAL`
  On: `index.html`, `avis-legal.html`, `privacitat.html`, `cookies.html`, `assets/js/components/home-href.js`, `includes/head.php`, `contacto.php` i el sitemap (referències a `index.html`). **En pausa a propòsit** (vegeu la secció 10).
  Com: llegir la configuració única des d'aquestes pàgines (domini, titular, `site-footer.js`), incrustar el token CSRF al servidor (sense `token.php` ni espera del botó, i amb token també sense JS), compartir `head.php` i `foot.php` amb el blog i afegir una redirecció 301 de `index.html` a `index.php`.
- [ ] **Nom de domini fix a reCAPTCHA en entorns de prova** · `OPCIONAL`
  On: `hostname` es deriva de `base_url`, així que en preproducció caldria un `site-config.local.php` amb el domini de proves.
  Com: documentat; no requereix canvi de codi.
- [ ] **Enviament sense JavaScript sense token CSRF** · `OPCIONAL`
  On: `contacto.php` (decisió 010). La ruta sense JS només valida l'origen perquè una pàgina estàtica no pot dur el token. Límit conegut i de risc baix.
  Com: es resol amb el pas a `index.php`.
- [ ] **Límits antiabús dels comptadors** · `OPCIONAL`
  On: decisió 008. Els likes i les visites depenen de cookies i es poden inflar; són orientatius.
  Com: afegir límit per IP o captcha si el client ho demana.
- [ ] **Validació d'imatges i enllaços duplicada** · `OPCIONAL`
  On: `includes/db.php` (`isValidPostImage` i `isValidPostLink` repeteixen les regles de `validatePostImages` i `validatePostLinks`, amb els límits 200, 300 i 120 escrits dues vegades).
  Com: extreure els límits a constants o fer que `validate*` reutilitzi `isValid*`.
- [ ] **`initContactForm` fa massa coses** · `OPCIONAL`
  On: `assets/js/script.js` (unes 135 línies: token i reCAPTCHA, errors per camp, estat del botó i enviament).
  Com: moure els errors per camp a un mòdul, com ja es va fer amb `toast.js`.
- [ ] **Acció de reCAPTCHA escrita a dos llocs** · `OPCIONAL`
  On: `"contacte"` a `assets/js/script.js` i `'action' => 'contacte'` a `includes/recaptcha-config.php`. Si divergeixen, el servidor rebutja el token sense avisar.
  Com: que `token.php` lliuri l'acció juntament amb la clau de lloc.
- [ ] **Listeners `input` i `change` duplicats** · `OPCIONAL`
  On: `assets/js/script.js` (`initContactForm`, mateix handler registrat dues vegades).
  Com: registrar-lo amb un bucle sobre els dos esdeveniments.
- [ ] **Dependències des de CDN** · `OPCIONAL`
  On: Tailwind, GSAP, ScrollTrigger, SplitText, Draggable i InertiaPlugin des de `cdn.jsdelivr.net`, amb SRI.
  Com: servir-les en local si es vol evitar que la IP del visitant arribi a un tercer o dependre del CDN.
- [ ] **Icones del lloc** · `OPCIONAL`
  On: només hi ha una icona SVG (`includes/head.php`).
  Com: afegir `apple-touch-icon` i una versió PNG.
- [ ] **Pòster orfe `vicity-demo-poster.webp`** · `OPCIONAL`
  On: `assets/video/vicity-demo-poster.webp` ja no el fa servir cap pàgina des que el hero porta `vicity-hero-poster.webp`.
  Com: esborrar-lo si el client no el vol conservar.

## 12. Traspàs d'accessos i de manteniment

- [ ] **Traspàs d'accessos i de manteniment** · `RECOMANAT` · `Client / allotjament`
  On: fora del repositori. A confirmar amb el client.
  Com: decidir qui allotja la web, qui administra el DNS i el correu, i qui edita `data/posts.php` (no hi ha panell d'administració).
