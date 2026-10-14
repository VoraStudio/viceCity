# Desplegament i traspàs al client

Estat revisat el 2026-10-14. Aquest document diu **què queda per fer** i **com publicar la web**. Està pensat per a qui administri l'allotjament i el domini del client; no cal ser desenvolupador.

Vora Studio lliura la web preparada, però **no té accés a l'allotjament, al domini ni al DNS**, i encara no se sap quin domini es contractarà. Tot el que depèn del servidor, del domini o de les dades reals és del client o del seu proveïdor.

**Veredicte:** la web està llesta per a una revisió amb el client. **No és publicable fins que es tanqui el bloc «1. Què falta».**

**Marca «A confirmar amb cdmon»:** punt que no s'ha pogut verificar a la documentació oficial de cdmon; cal preguntar-ho abans (llista a l'apartat 11).

---

## 0. Com funciona (resum)

- **Pàgines estàtiques:** portada i textos legals (`index.html`, `avis-legal.html`, `privacitat.html`, `cookies.html`).
- **Part dinàmica en PHP:** blog, m'agrada i visites, formulari de contacte, `robots.txt` i `sitemap.xml` generats.
- **Configuració per variables:** domini, correu i titular surten de variables d'entorn o d'un fitxer `.env` (apartat 5); al codi no hi ha cap valor. Mentre quedi alguna variable buida o sense definir, la web **es mostra com a esborrany**: franja visible, `noindex`, `robots.txt` amb `Disallow: /` i formulari desactivat (503). Un desplegament sense configurar no passa desapercebut ni s'indexa.
- **No hi ha panell d'administració:** les entrades del blog s'editen a `data/posts.php`.

Requisits de l'allotjament:

| Requisit | Detall |
|---|---|
| PHP **8.1 o superior** | El codi usa el tipus `never` (PHP 8.1) |
| Extensions `pdo_sqlite`, `curl`, `mbstring` | Blog (SQLite), reCAPTCHA i correu. **A confirmar amb cdmon** |
| Apache amb `.htaccess` i `mod_rewrite` | Protegeix `data/` i `includes/` i serveix `robots.txt` i `sitemap.xml`. **A confirmar amb cdmon** |
| `data/` escrivible per PHP | SQLite hi crea `blog.sqlite` |
| HTTPS | Les cookies i el formulari ho necessiten |
| Dues bústies del domini | Remitent i destinatari del formulari |

---

## 1. Què falta (checklist del client)

**Prioritats:** `BLOQUEJANT` (no es pot publicar sense això) · `RECOMANAT` · `OPCIONAL`.

### Dades i decisions que només pot donar el client

- [ ] **Domini definitiu** · `BLOQUEJANT`. Amb o sense `www`; ha de coincidir amb `base_url`.
- [ ] **Bústies de remitent i destinatari del formulari** · `BLOQUEJANT`. El remitent ha de ser del mateix domini.
- [ ] **Dades del titular** · `BLOQUEJANT`: raó social, NIF/CIF, domicili, registre mercantil (tom, foli i full), correu i telèfon de contacte, contacte del DPD i autoritat de control (AEPD o Autoritat Catalana de Protecció de Dades).
- [ ] **Confirmar telèfon i adreça** · `BLOQUEJANT`. Ara hi ha `+34 93 123 45 67` i «Avinguda Diagonal, 456, Barcelona», que son dades d'exemple.
- [ ] **Revisió jurídica dels tres textos legals** · `BLOQUEJANT`. `avis-legal.html`, `privacitat.html` i `cookies.html` són un **esborrany, no assessorament legal**. Cal omplir les marques `[A COMPLETAR]` i treure l'avís d'esborrany (línia 145 de cada fitxer). Pendents de definir: titular dels continguts, fur aplicable, termini de conservació i encarregats del tractament.
- [ ] **Inventari real de cookies** · `RECOMANAT`. Les del lloc són `vc_l` (m'agrada), `vc_v` (visites) i `vc_contact` (sessió tècnica del formulari), totes tècniques. No hi ha analítica. Si se n'afegeix, cal actualitzar `cookies.html` i `privacitat.html` i demanar consentiment previ.
- [ ] **Validar les 7 entrades del blog** · `RECOMANAT`. No consta que el client n'hagi validat els textos. Decidir si el blog s'indexa (`blog_indexable`; vegeu l'apartat 5).
- [ ] **Imatge per compartir a xarxes** · `RECOMANAT`. Ara és el logotip (2156×671, targeta `summary`). Cal una imatge de 1200×630 i passar a `summary_large_image`.
- [ ] **Qui manté la web després** · `RECOMANAT`: qui allotja, qui administra DNS i correu, i qui edita `data/posts.php`.

### Feina al servidor (client o proveïdor)

- [ ] Publicar els fitxers (apartats 2 i 3) · `BLOQUEJANT`.
- [ ] PHP 8.1+ i extensions comprovades (apartat 4) · `BLOQUEJANT`.
- [ ] Definir els valors reals amb un `.env` o amb variables del servidor (apartat 5) · `BLOQUEJANT`.
- [ ] Substituir a mà el domini, el correu, el telèfon i l'adreça als fitxers estàtics (apartat 8) · `BLOQUEJANT`.
- [ ] HTTPS actiu i forçat (apartat 6) · `BLOQUEJANT`.
- [ ] SPF, DKIM i DMARC (apartat 7) · `RECOMANAT`.
- [ ] Claus de reCAPTCHA v3 (apartat 7) · `RECOMANAT` (`BLOQUEJANT` si es publica sense altra protecció; vegeu l'avís).
- [ ] Proves finals (apartat 10) · `BLOQUEJANT`.
- [ ] Còpia periòdica de `data/blog.sqlite` (m'agrada i visites) · `RECOMANAT`.

### Millores de codi (les fa Vora Studio si el client ho encarrega)

- [ ] **reCAPTCHA que falli tancat en producció** · `RECOMANAT`. Si falten `VICITY_RECAPTCHA_SITE_KEY` o `VICITY_RECAPTCHA_SECRET`, el servidor se salta la verificació sense avisar (`includes/recaptcha.php`).
- [ ] **Límit d'enviaments per IP** · `RECOMANAT`. El límit de 10 s es guarda a la sessió: un bot que no envia la cookie obté una sessió nova a cada petició i podria enviar correu en massa.
- [ ] **Passar la portada i les legals a PHP** · `RECOMANAT`. Eliminaria el canvi manual de l'apartat 8 (llegirien la config), amb redirecció 301 dels `.html` i token CSRF a la portada (tanca el límit de l'enviament sense JavaScript, que ara només valida l'origen). Va en pausa perquè la vista prèvia a GitHub Pages no executa PHP.
- [ ] **Compilar el CSS de Tailwind** · `OPCIONAL`. Ara es genera al navegador des del CDN a cada visita; compilar-lo permetria una CSP estricta (i HSTS/CSP a l'apartat 6).
- [ ] **Servir GSAP i Tailwind en local** · `OPCIONAL`, per no dependre del CDN ni exposar la IP del visitant a un tercer.
- [ ] **`apple-touch-icon` i icona PNG** · `OPCIONAL` (ara només hi ha un SVG).

---

## 2. Preparar el paquet

Des de la carpeta del projecte, aquesta ordre crea un `.zip` amb només el necessari:

```bash
git archive --format=zip -o vicity.zip HEAD -- . ':(exclude)openspec' ':(exclude).github' ':(exclude)*.md'
```

Sense Git: a GitHub, `Code` > `Download ZIP` i després esborrar `openspec/`, `.github/` i els `.md`.

- **Puja:** `index.html`, les `.html` legals, els `.php` de l'arrel, `assets/`, `includes/`, `data/` (`posts.php` i `.htaccess`) i el `.htaccess` de l'arrel.
- **No pugis:** `.git`, `.github`, `openspec/` i els `.md`; ni `data/blog.sqlite` d'un ordinador local (la base **es crea sola** a la primera visita de `blog.php`).
- `.htaccess` és un fitxer ocult: comprova que és al paquet.
- `404.html` i `500.html` (pàgines d'error pròpies) **ja van al paquet**: l'ordre només exclou `*.md`, `openspec/` i `.github/`. Han d'anar a l'arrel de `/web`, perquè el `.htaccess` les serveix amb `ErrorDocument` per ruta absoluta.

> **Vista prèvia a GitHub Pages.** El flux (`.github/workflows/pages.yml`) publica tot el repositori excepte `.git`, `.github` i `docs`; no executa PHP, així que blog, m'agrada i formulari no hi funcionen. Els `.htaccess` tampoc s'hi apliquen: `includes/*.php`, `data/posts.php` i `openspec/` es poden descarregar com a text. No és un desplegament vàlid per a producció.

---

## 3. Pujar els fitxers a cdmon

- [ ] Dades d'FTP: panell de cdmon, gestió de l'allotjament, informació de l'allotjament (a baix a l'esquerra).
- [ ] Client FTP (FileZilla o WinSCP). FTP port 21; SFTP port 22 **si el pla ho permet** (**A confirmar amb cdmon**).
- [ ] **Carpeta pública: `/web`** (no `public_html`). Tot el paquet va **dins de `/web`**: a l'arrel hi han d'haver `index.html`, `blog.php` i `assets/`.
- [ ] Activar la visualització de **fitxers ocults** i comprovar que `/web/.htaccess` i `/web/data/.htaccess` hi són.
- [ ] Permisos: `data/` escrivible per PHP; valors habituals, carpetes 755 i fitxers 644 (**A confirmar amb cdmon**).

**Comprovació:** `https://elvostredomini.cat/` mostra la portada.

### On és el domini i l'allotjament

| Cas | Què fer |
|---|---|
| Domini i allotjament a cdmon | Associar el domini a l'allotjament. El DNS el gestiona cdmon. |
| Domini en un altre proveïdor, allotjament a cdmon | O bé registre `A` `@` a la IP del servidor (a `Servidor` > `Información del Servidor`) i `CNAME` `www`, o bé passar el DNS a cdmon. |
| Domini a cdmon, allotjament en un altre proveïdor | A `DNS` > `Gestionar DNS`, registre `A` `@` a la IP nova i `CNAME` `www`. El SSL s'ha de crear a l'allotjament extern. |
| Servidor propi (VPS) | Apache amb `mod_rewrite` i `AllowOverride All`. Nginx no llegeix `.htaccess`: vegeu l'annex. |

La propagació del DNS pot trigar des de minuts fins a 24-48 hores.

**Entorn de proves (recomanat):** un subdomini com `proves.elvostredomini.cat` amb el seu `.env`. Si les dades estan completes, la web **deixa de ser esborrany i s'indexa**: protegiu-lo amb contrasenya (**A confirmar amb cdmon**) o deixeu-hi alguna variable buida.

---

## 4. PHP

- [ ] Panell > gestió de l'allotjament > **"Información del alojamiento"** > **"Versión PHP"** > **"Cambiar"** > **8.1 o superior** > **"Modificar versión"**. El canvi és immediat i s'aplica a tot l'allotjament (**A confirmar amb cdmon** si es pot fixar per domini).
- [ ] Comprovar `pdo_sqlite`, `curl` i `mbstring` (**A confirmar amb cdmon**). Un cop publicat: si `blog.php` carrega i el formulari respon, hi són.
- [ ] `display_errors` desactivat en producció i, si es pot, `expose_php=Off`.
- **Errors:** panell > `Servidor` > **"Logs: PHP y servidor web"** (només el dia actual). L'historial de 30 dies és a la carpeta `/errors` de l'arrel, per FTP.

---

## 5. Configuració del lloc

Tota la configuració surt de **variables** (res no és escrit al codi). Hi ha dues vies, i es poden combinar. **Precedència: variable real del servidor > fitxer `.env`.** El `.env` només rellena el que l'entorn real no defineix. Una variable absent o buida compta com a **pendent**.

**Via A: fitxer `.env` (recomanada en un allotjament compartit).**

- [ ] Copiar `.env.example` a `.env` i omplir els valors (format `CLAU=VALOR`, comentaris amb `#`, valors opcionalment entre cometes; no s'interpreta res més).
- [ ] Ubicació: si el proveïdor ho permet, **un nivell per sobre de `/web`** (fora del que serveix Apache); si no, dins de `/web`, a l'arrel del projecte. L'aplicació busca primer `/web/.env` i després el del nivell superior.
- [ ] Protecció: `.env` és al `.gitignore` (no es puja al repositori) i el `.htaccess` de l'arrel denega qualsevol fitxer que comenci per punt (`Require all denied`). Comproveu-ho a l'apartat 10 (`/.env` ha de donar **403**).
- [ ] No es puja mai amb la resta de fitxers per error: només `.env.example` forma part del paquet.

**Via B: variables reals del servidor o del panell.** Definir-les allà si el proveïdor ho permet (**A confirmar amb cdmon**). Tenen prioritat sobre el `.env`.

| Camp | Variable |
|---|---|
| `base_url` | `VICITY_BASE_URL` |
| `site_name` | `VICITY_SITE_NAME` |
| `mail_recipient` | `VICITY_MAIL_RECIPIENT` |
| `mail_sender` | `VICITY_MAIL_SENDER` |
| `mail_subject_prefix` | `VICITY_MAIL_SUBJECT_PREFIX` (opcional) |
| `blog_indexable` | `VICITY_BLOG_INDEXABLE` (`1`, `true`, `on` o `yes` = cert; qualsevol altre valor o buit = fals) |
| `owner.razon_social` | `VICITY_OWNER_RAZON_SOCIAL` |
| `owner.nif` | `VICITY_OWNER_NIF` |
| `owner.domicilio` | `VICITY_OWNER_DOMICILIO` |
| `owner.registro_mercantil` | `VICITY_OWNER_REGISTRO_MERCANTIL` |
| `owner.email_contacto` | `VICITY_OWNER_EMAIL_CONTACTO` |
| `owner.telefono` | `VICITY_OWNER_TELEFONO` |
| `owner.dpd_contacto` | `VICITY_OWNER_DPD_CONTACTO` |
| `owner.autoridad_control` | `VICITY_OWNER_AUTORIDAD_CONTROL` |
| reCAPTCHA (apartat 7) | `VICITY_RECAPTCHA_SITE_KEY` i `VICITY_RECAPTCHA_SECRET` |

- No poseu secrets ni valors reals en un `SetEnv` del `.htaccess`: aquest fitxer és versionat. `SetEnv` només serviria per a valors no sensibles i d'exemple.
- Si un valor no sembla fer efecte, comproveu que no hi ha una variable real que pisi el `.env`.
- Amb `php -S` local, feu servir un `.env` a l'arrel o exporteu les variables abans: `VICITY_BASE_URL=https://exemple.cat php -S localhost:8080`.

**Què posar:**

| Camp | Què posar |
|---|---|
| `base_url` | Domini definitiu amb `https://` i **sense barra final** |
| `site_name` | Nom visible del lloc |
| `mail_recipient` | Correu que rep els missatges |
| `mail_sender` | Buzó **del mateix domini** |
| `mail_subject_prefix` | Opcional (si falta és `[<nom del lloc>]`) |
| `blog_indexable` | `false` fins que el contingut del blog sigui definitiu; llavors `true` |
| `owner.*` | `razon_social`, `nif`, `domicilio`, `registro_mercantil`, `email_contacto`, `telefono`, `dpd_contacto`, `autoridad_control` |

**Comprovació:** `/blog.php` no mostra la franja d'esborrany quan tot és complet.

**Avís:** la detecció només mira que cap valor sigui buit; un valor sense sentit però no buit es considerarà configurat.

---

## 6. HTTPS

- [ ] Activar el certificat Let's Encrypt gratuït de cdmon (panell, seguretat, **"Certificados SSL"**; nom exacte **A confirmar amb cdmon**).
- [ ] Forçar HTTPS des del panell (**"Forzar HTTPS"**, a confirmar) o afegir al principi del `.htaccess` (no provat a cdmon):

```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

- [ ] Triar amb o sense `www` i que coincideixi amb `base_url`. Les cookies es marquen `Secure` sota HTTPS.
- [ ] Opcional: HSTS un cop tot funcioni en HTTPS (`mod_headers`, **A confirmar amb cdmon**). El `.htaccess` només posa `X-Content-Type-Options` i `Referrer-Policy`.

**Comprovació:** `http://` redirigeix a `https://` i el cadenat surt sense avisos.

---

## 7. Correu i reCAPTCHA

### Correu

El formulari envia amb la funció `mail()` de PHP: **no hi ha SMTP autenticat ni PHPMailer**. Que arribi a la safata d'entrada depèn de l'allotjament i del DNS. Fins ara només s'ha provat amb un receptor simulat, **mai amb un enviament real**.

- [ ] **SPF:** registre `TXT` `@`. A cdmon: `DNS` > **"Gestionar DNS"** > **"Nuevo registro"** > **TXT/SPF**. Exemple de cdmon (a confirmar si correspon al pla): `v=spf1 mx a ptr ip4:46.16.60.0/23 a:cdmon.com include:srv.cat ~all`. Espereu uns 5 minuts.
- [ ] **DKIM:** la clau la dona el proveïdor de correu (cdmon no la genera). S'afegeix com a `TXT` amb subdomini `selector._domainkey`. **A confirmar amb cdmon** si `mail()` signa amb DKIM.
- [ ] **DMARC:** `TXT` `_dmarc`, valor inicial `v=DMARC1; p=none; rua=mailto:informes@elvostredomini.cat`.
- [ ] **Prova real:** enviar el formulari i, a Gmail, `Mostrar original` ha d'indicar `SPF: PASS` (i `DKIM: PASS` si s'ha configurat).
- [ ] Si el correu no surt o cau a brossa: preguntar a cdmon si `mail()` està permès i amb quins límits. La solució habitual és SMTP autenticat, que **és un canvi de codi** (afegir PHPMailer).

### reCAPTCHA v3

Està implementat però **desactivat**. **Avui no hi ha cap captcha efectiu**: el formulari es protegeix amb token CSRF, límit de 10 s per sessió, trampa de temps de 3 s, camp trampa i comprovació d'origen. Atura duplicats i robots simples, **no un bot que s'ho proposi**.

- [ ] Crear les claus v3 a <https://www.google.com/recaptcha/admin> per al domini definitiu.
- [ ] Posar la clau de lloc i la clau secreta a `VICITY_RECAPTCHA_SITE_KEY` i `VICITY_RECAPTCHA_SECRET`, al `.env` (via A de l'apartat 5) o com a variables reals del servidor (via B; **A confirmar amb cdmon**). Si falta qualsevol de les dues, reCAPTCHA queda desactivat.
- [ ] Revisar `min_score` (0,5). El `hostname` es deriva de `base_url`: no s'ha de tocar.
- [ ] Provar el formulari. Si respon **503**, mireu el log: pot faltar `curl.cainfo` (certificat arrel de cURL).
- [ ] Actualitzar `privacitat.html` i `cookies.html` (Google com a tercer, finalitat de seguretat i transferències internacionals).
- **Atenció:** amb reCAPTCHA actiu, l'enviament sense JavaScript queda bloquejat. Si en producció falten les claus, el captcha es desactiva **sense avisar**; comprovar-les abans de publicar.

---

## 8. Dades escrites a mà (portada i legals)

La portada i les pàgines legals són HTML estàtic i **no llegeixen** la configuració. El domini, el correu, el telèfon i l'adreça s'han de canviar **fitxer per fitxer**. (`robots.txt`, `sitemap.xml` i les pàgines PHP ja es generen sols.)

| Fitxer | Línies | Què cal canviar |
|---|---|---|
| `index.html` | 11, 30, 33, 49, 57-58 | Domini (`canonical`, `og:url`, `og:image`, JSON-LD) |
| `index.html` | 238, 1544, 1547 | Correu `info@vicity.cat` |
| `index.html` | ~240, ~1511 | Telèfon |
| `index.html` | 1581 | Adreça |
| `assets/js/components/site-footer.js` | 124, 126 | Correu |
| `assets/js/components/site-footer.js` | 134, 136 | Telèfon |
| `assets/js/components/site-footer.js` | 142 | Adreça |
| `avis-legal.html`, `privacitat.html`, `cookies.html` | 8, 17, 18 | Domini |
| `avis-legal.html`, `privacitat.html`, `cookies.html` | 129 | Correu i telèfon |
| `avis-legal.html` | 145, 148, 154, 162 | Marques `[A COMPLETAR]` |
| `privacitat.html` | 145, 147, 155, 157, 160 | Marques `[A COMPLETAR]` |
| `cookies.html` | 145, 149, 150, 152 | Marques `[A COMPLETAR]` |

Les línies poden desplaçar-se. Per trobar-ho tot, cerqueu `vicity.cat`, `93 123 45 67`, `Diagonal` i `A COMPLETAR`. El `sitemap.xml` generat llista les legals amb l'extensió `.html`.

---

## 9. Contingut del blog

**Afegir una entrada:** editar `data/posts.php` i afegir un bloc amb:

| Camp | Valor |
|---|---|
| `slug` | Únic, minúscules i guions |
| `title`, `excerpt`, `body` | `body` és text pla; paràgrafs separats per una línia en blanc |
| `category` | `ia`, `recaptacio`, `integracio` o `casos-us` |
| `cover` | `lav`, `purple` o `ink` |
| `read_minutes`, `published_at` | Minuts i data `AAAA-MM-DD` |
| `is_featured` | `1` només per a la destacada |
| `images`, `links` (opcionals) | Imatges a `assets/img/blog/<slug>/` amb `alt` obligatori; enllaços `http`/`https`. El format exacte és a la capçalera de `data/posts.php` |

Recarregar `blog.php`: la base es sincronitza sola i els m'agrada i les visites **no es perden**. Per esborrar una entrada, es treu el bloc.

**Abans d'activar `blog_indexable`:** el sitemap llistarà totes les entrades; comprovar que no n'hi ha de prova.

---

## 10. Proves finals

Només les pot fer qui tingui accés al servidor real. Fins ara tot s'ha provat amb Chrome automatitzat en local.

- [ ] `/` carrega amb estils i vídeos; `/blog.php` i un article; el m'agrada canvia el comptador.
- [ ] **Pàgina 404 pròpia:** una URL inexistent (p. ex. `/no-existeix`) mostra la pàgina «Pàgina no trobada» amb estat **404** (no 200 ni una redirecció). Forçar un error 500 no és pràctic; n'hi ha prou amb comprovar que `/500.html` carrega.
- [ ] `/robots.txt` i `/sitemap.xml` responen amb el domini correcte; les tres legals carreguen.
- [ ] Aquestes adreces donen **403**: `/data/blog.sqlite`, `/data/posts.php`, `/includes/db.php`, `/includes/site-config.php`, `/.env`, `/README.md`, `/.htaccess`, `/openspec/`.
- [ ] **Formulari real:** un enviament amb èxit i un amb error (camps buits); el correu arriba, amb remitent i `Reply-To` correctes, i no cau a brossa.
- [ ] La franja d'esborrany **no** apareix; HTTPS vàlid; cookies `vc_l`, `vc_v` i `vc_contact` amb `Secure` i `HttpOnly`.
- [ ] **Safari, Firefox i mòbil real:** fins ara tot s'ha provat només amb Chrome. Recórrer inici, blog amb filtre, article amb m'agrada, formulari, vídeos i toast.
- [ ] **Lector de pantalla** (NVDA, VoiceOver): formulari, toast, botó de pausa i carrusel · `RECOMANAT`.
- [ ] **Lighthouse i `axe`** a inici, blog i article · `RECOMANAT`.
- [ ] **Sense JavaScript:** avís `noscript` a inici, blog i formulari · `RECOMANAT`.
- [ ] **Enllaços trencats:** rastrejador sobre el domini publicat · `RECOMANAT`.
- [ ] Opcional: Google Search Console i sitemap.

---

## 11. Problemes freqüents

| Símptoma | Causa probable | Solució |
|---|---|---|
| Error 500 | PHP massa antic | Apartat 4: PHP 8.1+ |
| Blog buit o error de base de dades | `data/` no escrivible o falta `pdo_sqlite` | Permisos; preguntar a cdmon |
| `/robots.txt` o `/sitemap.xml` donen 404 | `mod_rewrite` o `.htaccess` no actius | Comprovar que `.htaccess` s'ha pujat; preguntar a cdmon |
| Les carpetes protegides es veuen | `.htaccess` no s'aplica | Revisar `AllowOverride` |
| El formulari respon 503 | Correu sense configurar o problema de certificat de reCAPTCHA | Apartats 5 i 7; mirar el log |
| Correus a brossa | Falta SPF/DKIM o remitent d'un altre domini | Apartat 7 |
| La web es veu sense estils | Tailwind i GSAP vénen d'un CDN | Comprovar la connexió i bloquejos de xarxa |
| La franja d'esborrany no desapareix | Queda alguna variable buida o sense definir | Omplir totes les variables del `.env` (o del servidor). Un `.env` mal ubicat (ni a l'arrel ni un nivell per sobre de `/web`) dona el mateix símptoma |

### Preguntes per a cdmon abans de desplegar

- [ ] Quin pla inclou PHP 8.1+ amb `pdo_sqlite`, `curl` i `mbstring`?
- [ ] `.htaccess` i `mod_rewrite` actius? Quina versió d'Apache?
- [ ] `data/` és escrivible per PHP amb els permisos per defecte?
- [ ] `mail()` permès? Límits d'enviament? Signa amb DKIM? Quin SPF correspon?
- [ ] SFTP disponible? Es pot fixar la versió de PHP per domini o subdomini?
- [ ] Com es crea i protegeix un subdomini de proves?
- [ ] Es poden definir variables d'entorn per a PHP (panell o `SetEnv`)? Arriben a `getenv()` o `$_SERVER`?
- [ ] Còpies de seguretat automàtiques i com es restauren?
- [ ] Nom exacte de l'opció per forçar HTTPS i dels servidors de noms?

---

## 12. Actualitzacions

- **Actualitzar:** pugeu només els fitxers canviats. **No sobreescriviu ni esborreu** `.env` ni `data/blog.sqlite`.
- **Tornar enrere:** conserveu el `.zip` anterior i una còpia de `data/blog.sqlite`.
- **Còpia de seguretat:** `data/blog.sqlite` és l'únic lloc on viuen els m'agrada i les visites; el contingut del blog es regenera de `posts.php`.

---

## Annex. Nginx (servidor propi, no provat)

El `.htaccess` no funciona amb Nginx. Equivalent orientatiu:

```nginx
location ~ ^/(openspec|includes|data|\.git|\.github|\.atl)(/|$) { return 403; }
location ~ \.(md|sqlite)$ { return 403; }
location = /robots.txt  { rewrite ^ /robots.php last; }
location = /sitemap.xml { rewrite ^ /sitemap.php last; }
```

## Fonts consultades

- PHP: <https://helpdesk.cdmon.com/portal/es/kb/articles/c%C3%B3mo-modificar-la-versi%C3%B3n-de-php>
- FTP i carpeta `/web`: <https://helpdesk.cdmon.com/portal/en/kb/articles/how-to-upload-my-website-to-hosting-ftp>
- Dades d'FTP: <https://helpdesk.cdmon.com/portal/en/kb/articles/where-to-view-ftp-access-data>
- SSL gratuït: <https://www.cdmon.com/es/blog/el-certificado-de-seguridad-gratuito-ha-llegado-a-cdmon>
- DKIM: <https://helpdesk.cdmon.com/portal/en/kb/articles/how-to-add-a-dkim-to-the-cdmon-dns>
- SPF: <https://www.cdmon.com/es/blog/como-configurar-el-registro-spf-para-el-correo-en-el-dns-estatico>
- DNS extern: <https://helpdesk.cdmon.com/portal/es/kb/articles/c%C3%B3mo-configurar-los-servicios-de-cdmon-con-el-dns-externo>
- Logs d'errors: <https://helpdesk.cdmon.com/portal/en/kb/articles/where-to-view-accommodation-error-logs>

Algunes pàgines només s'han consultat a través del resum d'un cercador; per això el menú exacte, la versió màxima de PHP i les extensions queden com a **A confirmar amb cdmon**.
