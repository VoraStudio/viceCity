# Guia de desplegament a cdmon

Guia pas a pas per publicar la web de Vicity en un allotjament de **cdmon**. Està pensada per a qui administri l'allotjament i el domini del client, no cal ser desenvolupador. La llista del que queda pendent de configurar és a [`PENDENTS.md`](PENDENTS.md).

**Com llegir aquesta guia.** Cada pas diu què cal fer, on es fa i com es comprova que ha funcionat. Tot el que no s'ha pogut verificar a la documentació oficial de cdmon porta la marca **A confirmar amb cdmon**: no és un error, és un punt que cal preguntar-los abans (hi ha la llista de preguntes a l'apartat 12).

**Termes que apareixen.** *Hosting*: el servidor on viu la web. *DNS*: l'agenda que diu a Internet a quin servidor va cada domini. *FTP/SFTP*: programes per pujar fitxers al servidor. *`.htaccess`*: fitxer de configuració d'Apache que ja porta el projecte. *SPF, DKIM, DMARC*: tres registres DNS que demostren que els correus del domini són legítims.

---

## 0. Resum i requisits previs

La web té dues parts:

- **Pàgines estàtiques**: portada i textos legals (`index.html`, `avis-legal.html`, `privacitat.html`, `cookies.html`). Funcionen en qualsevol servidor.
- **Part dinàmica en PHP**: blog, m'agrada i visites, formulari de contacte, `robots.txt` i `sitemap.xml` generats. Necessita un servidor amb PHP.

| Requisit | Detall | Estat a cdmon |
|---|---|---|
| Domini | El contracta el client; cal saber-ne el nom exacte | Pendent del client |
| Allotjament amb PHP **8.1 o superior** | Per ser compatible amb el codi | cdmon ofereix de PHP 7.4 en amunt (veure apartat 4) |
| Extensions PHP `pdo_sqlite`, `curl`, `mbstring` | `pdo_sqlite` guarda el blog, `curl` serveix per a reCAPTCHA, `mbstring` per al correu | **A confirmar amb cdmon** |
| Apache amb `.htaccess` i `mod_rewrite` | Protegeix `data/` i `includes/` i serveix `robots.txt` i `sitemap.xml` | **A confirmar amb cdmon** |
| HTTPS (certificat SSL) | Les cookies i el formulari ho necessiten | cdmon ofereix Let's Encrypt gratuït (apartat 6) |
| Buzó de correu del domini | Per al remitent i el destinatari del formulari | Pendent del client |

**Mapa de casos** (cada pas indica si depèn del cas):

| Cas | Domini | Allotjament |
|---|---|---|
| A | cdmon | cdmon |
| B | Un altre proveïdor | cdmon |
| C | cdmon | Un altre proveïdor |
| D | Subdomini de proves, abans de publicar | cdmon |
| E | Servidor propi (VPS o dedicat) | Fora de l'allotjament compartit |

---

## 1. Casos de desplegament

### Cas A. Domini i allotjament a cdmon

- [ ] Associar el domini a l'allotjament. **A confirmar amb cdmon**: nom exacte de l'opció al panell.
- [ ] Pujar la web (apartats 2 i 3).
- [ ] Activar el certificat SSL i forçar HTTPS (apartat 6).
- [ ] El DNS el gestiona cdmon: no cal canviar res si el domini ja apunta a l'allotjament. Si cal editar registres: `DNS` > `Gestionar DNS` (apartat 7).

### Cas B. Domini en un altre proveïdor i allotjament a cdmon

Hi ha dues maneres. Tria-ne una:

1. **Mantenir el DNS al proveïdor actual** i crear-hi els registres que apunten a cdmon:
   - Registre `A` amb nom `@` apuntant a la IP del servidor web.
   - Registre `CNAME` amb nom `www` apuntant al propi domini.
   - La IP la mostra el panell de cdmon a `Servidor` > `Información del Servidor` (columna esquerra).
2. **Passar el DNS a cdmon**: canviar els servidors de noms del domini als de cdmon, des del proveïdor on s'ha registrat. **A confirmar amb cdmon** quins són.

**Propagació**: cdmon no indica quant triga. Com a norma general, els canvis de DNS poden trigar des d'uns minuts fins a 24-48 hores.

### Cas C. Domini a cdmon i allotjament en un altre proveïdor

- [ ] A `DNS` > `Gestionar DNS` de cdmon, crear un registre `A` amb nom `@` apuntant a la IP del nou allotjament i un `CNAME` `www`.
- [ ] El certificat SSL de cdmon només cobreix els dominis allotjats a cdmon: el certificat s'ha de crear a l'allotjament extern.
- [ ] Els passos 2 a 5 i 8 en endavant depenen del proveïdor extern.

### Cas D. Entorn de proves (subdomini)

Recomanat abans de publicar. Per exemple `proves.elvostredomini.cat`.

- [ ] Crear el subdomini a l'allotjament. **A confirmar amb cdmon**: com i si el pla ho permet.
- [ ] Pujar-hi la web amb el seu propi `includes/site-config.local.php`, amb `base_url` del subdomini.
- [ ] **Atenció**: si totes les dades estan completes, la web **deixa de ser un esborrany** i es pot indexar. Per evitar que Google indexi l'entorn de proves, protegiu-lo amb contrasenya (**A confirmar amb cdmon**: si el pla permet protegir directoris) o deixeu-hi algun valor `PENDENT` perquè mantingui `noindex`.

### Cas E. Servidor propi (VPS o dedicat)

No s'ha pogut verificar l'oferta ni el panell dels servidors de cdmon: **A confirmar amb cdmon**. Si s'usa un servidor propi:

- Cal Apache amb `mod_rewrite` i `AllowOverride All`, o bé Nginx amb regles equivalents. El `.htaccess` del projecte **no funciona amb Nginx**.
- Equivalent orientatiu per a Nginx (no provat):

```nginx
location ~ ^/(openspec|docs|includes|data|\.git|\.github|\.atl)(/|$) { return 403; }
location ~ \.(md|sqlite)$ { return 403; }
location = /robots.txt  { rewrite ^ /robots.php last; }
location = /sitemap.xml { rewrite ^ /sitemap.php last; }
```

---

## 2. Preparar el paquet de fitxers

- [ ] Obtenir el codi. Des de la carpeta del projecte, aquesta ordre crea un `.zip` amb només el necessari (provada):

```bash
git archive --format=zip -o vicity.zip HEAD -- . ':(exclude)openspec' ':(exclude).github' ':(exclude)*.md'
```

- [ ] Alternativa sense Git: a GitHub, `Code` > `Download ZIP` i després esborrar les carpetes `openspec` i `.github` i els fitxers `.md`.
- [ ] **El que cal pujar**: `index.html`, les pàgines `.html` legals, els `.php` de l'arrel, `assets/`, `includes/`, `data/` (només `posts.php` i `.htaccess`) i el `.htaccess` de l'arrel.
- [ ] **El que NO s'ha de pujar**:
  - `.git`, `.github`, `docs/` i `openspec/`: material de treball, no forma part de la web.
  - Els `.md` (`README.md`, `PENDENTS.md`, `DEPLOY.md`): el `.htaccess` ja els bloqueja, però no cal pujar-los.
  - `data/blog.sqlite` del vostre ordinador: conté dades de proves. La base de dades **es crea sola** a la primera visita de `blog.php` a partir de `data/posts.php`.
  - `includes/site-config.local.php` i `includes/secrets.local.php`: no estan al repositori. Es creen al servidor (apartats 5 i 8).
- [ ] Comprovar que l'arxiu conté el `.htaccess` (és un fitxer ocult). Si el comprimiu a mà des de l'Explorador, comproveu que s'inclou.

> **Vista prèvia a GitHub Pages.** El flux de treball actual (`.github/workflows/pages.yml`) publica tot el repositori excepte `.git`, `.github` i `docs`. Això inclou `includes/`, `data/posts.php`, `openspec/` i els `.md` com a fitxers visibles. No hi ha secrets al repositori, però convé excloure'ls abans de compartir la vista prèvia. A Pages no funciona cap part en PHP (blog, m'agrada, formulari).

---

## 3. Pujar els fitxers a cdmon

**On són les dades de connexió.** Al panell de cdmon, a la gestió de l'allotjament, a la secció d'informació de l'allotjament (a baix a l'esquerra hi ha les dades d'FTP).

- [ ] Instal·lar un client FTP, per exemple FileZilla o WinSCP (cdmon té guies per a tots dos).
- [ ] Connectar amb les dades d'FTP. Protocol FTP al port 21; SFTP al port 22 **si està activat al vostre pla** (**A confirmar amb cdmon**).
- [ ] **Carpeta pública: `/web`**. A cdmon és l'arrel de la web, no `public_html`. Tot el contingut del paquet va **dins de `/web`**, no a la carpeta superior.
- [ ] Pujar el contingut. A l'arrel de `/web` han d'haver-hi `index.html`, `blog.php` i la carpeta `assets/`.
- [ ] Alternativa: el client FTP en línia del panell (net2ftp), amb un límit de 35 MB per fitxer. Pot servir per a fitxers petits, no per a tot el paquet.
- [ ] Activar a l'FTP la visualització de **fitxers ocults** i comprovar que `/web/.htaccess` i `/web/data/.htaccess` s'han pujat.

**Permisos.** La carpeta `data/` ha de ser escrivible per PHP, perquè SQLite hi crea `blog.sqlite`. Valors habituals: carpetes 755 i fitxers 644. **A confirmar amb cdmon** si cal ajustar-los i quin usuari executa PHP.

**Comprovació**: obrir `https://elvostredomini.cat/` i veure la portada.

---

## 4. Versió de PHP i errors

- [ ] Al panell, a la gestió de l'allotjament, **"Información del alojamiento"** > **"Versión PHP"** > botó **"Cambiar"**. A la pantalla següent, desplegable **"Versión PHP para su alojamiento"**, triar com a mínim **8.1** (millor la més recent suportada) i prémer **"Modificar versión"**.
- Segons cdmon, el canvi és immediat i no cal migració. Les versions sense suport (EOL) només estan disponibles amb el complement *PHP Legacy*, de pagament mensual: no s'han d'usar.
- cdmon indica que el canvi s'aplica a **tot l'allotjament**. **A confirmar amb cdmon** si es pot fixar per domini o carpeta.
- Els ajustos de PHP (memòria, temps…) es gestionen des del panell, a l'apartat `Servidor` > configuració de PHP. **A confirmar amb cdmon** el nom exacte de l'opció.
- [ ] Comprovar les extensions `pdo_sqlite`, `curl` i `mbstring`. **A confirmar amb cdmon**: no s'han pogut verificar a la seva documentació. Una manera de comprovar-ho és que, un cop publicat, `blog.php` carregui sense error i el formulari respongui.
- [ ] Mostrar errors **només en desenvolupament**: en producció `display_errors` ha d'estar desactivat. **A confirmar amb cdmon** el valor per defecte del pla.
- **On veure els errors**: panell, secció `Servidor`, opció **"Logs: PHP y servidor web"**, i triar el tipus de log al desplegable. Només mostra els errors del dia actual; l'historial (fins a 30 dies, comprimit) és a la carpeta `/errors` de l'arrel de l'allotjament, per FTP.

---

## 5. Configuració única del lloc

Tot el que depèn del client es defineix en **un sol fitxer**: `includes/site-config.php`. Per no tocar el fitxer original, creeu-ne una còpia local.

- [ ] Copiar `includes/site-config.local.example.php` a `includes/site-config.local.php` (al servidor, per FTP). Aquest fitxer no s'ha de pujar mai al repositori.
- [ ] Editar `includes/site-config.local.php` i posar-hi els valors reals. Se sumen als valors per defecte.

| Camp | Què posar |
|---|---|
| `base_url` | Domini definitiu amb `https://` i **sense barra final** (`https://www.elvostredomini.cat`) |
| `mail_recipient` | Correu que rebrà els missatges del formulari |
| `mail_sender` | Correu remitent: ha de ser d'un **buzó del mateix domini** |
| `mail_subject_prefix` | Opcional. Per defecte `[Vicity web]` |
| `blog_indexable` | `false` fins que el client revisi el contingut del blog; `true` per publicar-lo a Google |
| `owner` → `razon_social`, `nif`, `domicilio`, `registro_mercantil`, `email_contacto`, `telefono`, `dpd_contacto`, `autoridad_control` | Dades del titular, que facilita el client |

**Què passa mentre quedi algun valor `PENDENT`:**

- Les pàgines PHP mostren una franja d'**esborrany** a dalt.
- El lloc porta `noindex, nofollow` (Google no l'indexarà) i `robots.txt` diu `Disallow: /`.
- Mentre `mail_recipient` o `mail_sender` siguin `PENDENT`, el formulari respon **503** («El formulari encara no està configurat») i no envia res.

**Comprovació**: obrir `/blog.php`. Quan tots els valors estan complets, la franja desapareix.

---

## 6. Domini i HTTPS

- [ ] **Activar el certificat SSL.** cdmon ofereix un certificat Let's Encrypt gratuït amb l'allotjament, que s'instal·la amb un sol clic i es renova automàticament. Al panell, a la gestió de l'allotjament, apartat de seguretat, **"Certificados SSL"**. **A confirmar amb cdmon** el nom exacte del menú.
- [ ] **Forçar HTTPS.** Un cop actiu el certificat, el panell ofereix una opció per forçar la navegació per HTTPS (segons guies de tercers, anomenada **"Forzar HTTPS"**; **A confirmar amb cdmon**).
- [ ] Triar **amb o sense `www`** (per exemple, sempre `www.elvostredomini.cat`) i que coincideixi amb `base_url`. Les cookies de la web (`vc_l`, `vc_v` i la sessió del formulari `vc_contact`) es marquen `Secure` automàticament sota HTTPS.
- [ ] Si el panell no ofereix la redirecció, es pot afegir al principi del `.htaccess` (exemple, no provat a cdmon):

```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

- [ ] Opcional: capçalera HSTS un cop tot funcioni correctament en HTTPS. **A confirmar amb cdmon** que el pla permet les capçaleres (`mod_headers`).

**Comprovació**: `http://elvostredomini.cat` ha de redirigir a `https://` i el navegador ha de mostrar el cadenat sense avisos.

---

## 7. Correu del formulari: SPF, DKIM i DMARC

El formulari envia el correu amb la funció `mail()` de PHP. **No hi ha PHPMailer ni connexió SMTP amb autenticació.** Que el correu arribi a la safata d'entrada i no a brossa depèn de la configuració de l'allotjament i del DNS.

- [ ] Crear un **buzó del mateix domini** per al remitent (per exemple `noreply@elvostredomini.cat`) i un altre per al destinatari.
- [ ] **SPF.** Registre `TXT` amb nom `@`. A cdmon: `DNS` > **"Gestionar DNS"** > **"Nuevo registro"** > tipus **TXT/SPF** > nom i valor > desar. cdmon publica aquest exemple per als seus servidors:

```
v=spf1 mx a ptr ip4:46.16.60.0/23 a:cdmon.com include:srv.cat ~all
```

  **A confirmar amb cdmon** si aquest valor és el que correspon al vostre pla i al correu enviat des de l'allotjament web. Després de desar, cal esperar uns 5 minuts.
- [ ] **DKIM.** cdmon **no genera la clau DKIM**: la dona el proveïdor de correu. S'afegeix igualment al DNS: `DNS` > **"Nuevo registro"** > **TXT/SPF** > **"Un subdominio concreto"** > subdomini `selector._domainkey` (el selector el dona el proveïdor) > valor = la clau > desar. **A confirmar amb cdmon** si els correus enviats amb `mail()` des de l'allotjament es signen amb DKIM.
- [ ] **DMARC.** Registre `TXT` amb nom `_dmarc`. Valor orientatiu per començar sense bloquejar res (no és específic de cdmon):

```
v=DMARC1; p=none; rua=mailto:correu-de-informes@elvostredomini.cat
```

- [ ] **Prova real.** Enviar el formulari des del web publicat i comprovar que el correu arriba. A Gmail, `Mostrar original` ha d'indicar `SPF: PASS` i, si s'ha configurat, `DKIM: PASS`.
- [ ] **Si el correu no surt o cau a brossa**: preguntar a cdmon si `mail()` està permès i si té límits d'enviament (**A confirmar amb cdmon**). La solució habitual és enviar per **SMTP autenticat**, i això requereix un canvi de codi (afegir PHPMailer) que ha de fer qui mantingui la web.

---

## 8. reCAPTCHA v3

Està implementat però **desactivat**. Fins que no hi hagi claus, **la web no té cap captcha efectiu**: el formulari només es protegeix amb un token, un límit entre enviaments, una trampa de temps, un camp trampa i la comprovació d'origen. Això no atura un robot que s'ho proposi.

- [ ] Crear les claus reCAPTCHA **v3** a la consola de Google (<https://www.google.com/recaptcha/admin>) per al domini definitiu.
- [ ] Posar la **clau de lloc** (pública) a `includes/recaptcha-config.php`, al camp `site_key`, en lloc de `PENDENT_CLAU_DE_SITE`.
- [ ] Copiar `includes/secrets.example.php` a `includes/secrets.local.php` **al servidor** i posar-hi la **clau secreta** a `recaptcha_secret`. **No es puja mai al repositori.**
- [ ] El `hostname` que es verifica surt de `base_url`: no s'ha de tocar.
- [ ] Provar el formulari. Si respon **503**, mirar el log d'errors (apartat 4): pot ser que PHP no tingui el certificat arrel configurat per a cURL (`curl.cainfo`). **A confirmar amb cdmon**.
- **Atenció.** Si en producció falten les claus, el captcha queda desactivat sense avisar. Cal assegurar-se que estan posades abans de publicar.

---

## 9. Dades escrites a mà mentre la portada i les legals són estàtiques

La portada i les tres pàgines legals són HTML estàtic i **no llegeixen** la configuració única. El domini, el correu, el telèfon i l'adreça hi són escrits a mà i cal canviar-los **fitxer per fitxer**. (`robots.txt` i `sitemap.xml` ja es generen sols a partir de `base_url`.)

| Fitxer | Línies | Què cal canviar |
|---|---|---|
| `index.html` | 11, 30, 33, 49, 57, 58 | Domini (`canonical`, `og:url`, `og:image`, dades estructurades) |
| `index.html` | 238, 1544, 1547 | Correu de contacte |
| `index.html` | 1581 | Adreça (és d'exemple) |
| `assets/js/components/site-footer.js` | 124, 126 | Correu de contacte |
| `assets/js/components/site-footer.js` | 136 | Telèfon (és d'exemple) |
| `assets/js/components/site-footer.js` | 142 | Adreça (és d'exemple) |
| `avis-legal.html`, `privacitat.html`, `cookies.html` | 8, 17, 18 | Domini (`canonical`, `og:url`, `og:image`) |
| `avis-legal.html`, `privacitat.html`, `cookies.html` | 129 | Correu i telèfon de contacte |
| `avis-legal.html` | 145, 148, 154, 162 | Marques `[A COMPLETAR]` |
| `privacitat.html` | 145, 147, 155, 157, 160 | Marques `[A COMPLETAR]` |
| `cookies.html` | 145, 149, 150, 152 | Marques `[A COMPLETAR]` |

Per trobar-ho tot d'un cop, cerqueu al conjunt de fitxers `vicity.cat`, `93 123 45 67`, `Diagonal` i `A COMPLETAR`.

**Textos legals.** Les dades del titular les facilita el client. Els textos són un **esborrany, no assessorament jurídic**: els ha de revisar el client o el seu assessor abans de publicar-los. Si s'afegeix analítica o altres cookies, cal actualitzar-los.

---

## 10. Contingut del blog

- [ ] Revisar amb el client les entrades de `data/posts.php` (actualment, 7). Comprovar que no queda cap entrada de prova.
- [ ] **Afegir una entrada**: editar `data/posts.php` i afegir un bloc amb aquests camps:

| Camp | Valor |
|---|---|
| `slug` | Únic, en minúscules i amb guions |
| `title`, `excerpt`, `body` | `body` és text pla; els paràgrafs se separen amb una línia en blanc |
| `category` | `ia`, `recaptacio`, `integracio` o `casos-us` |
| `cover` | `lav`, `purple` o `ink` |
| `read_minutes` | Minuts de lectura |
| `published_at` | Data `YYYY-MM-DD` |
| `is_featured` | `1` només per a la destacada |
| `images`, `links` (opcionals) | Format a la decisió `openspec/decisions/arquitectura/009` |

- [ ] Recarregar `blog.php`: la base de dades es sincronitza sola quan canvia `posts.php`. Els m'agrada i les visites **no es perden**.
- [ ] Les imatges de les entrades es pugen a `assets/img/blog/<slug>/`.
- [ ] **Còpia de seguretat**: `data/blog.sqlite` conté els m'agrada i les visites. Baixeu-la per FTP abans de qualsevol actualització. **A confirmar amb cdmon** si l'allotjament fa còpies automàtiques i com es restauren.

---

## 11. Comprovacions després de publicar

- [ ] `/` carrega la portada amb estils i vídeos.
- [ ] `/blog.php` mostra les entrades; fer clic a una i veure que carrega l'article.
- [ ] El botó d'm'agrada d'un article canvia el comptador.
- [ ] `/robots.txt` i `/sitemap.xml` responen i tenen el domini correcte.
- [ ] `/avis-legal.html`, `/privacitat.html` i `/cookies.html` carreguen.
- [ ] Aquestes adreces han de respondre **403** (prohibit):
  - `/data/blog.sqlite`
  - `/data/posts.php`
  - `/includes/db.php`
  - `/includes/site-config.php`
  - `/README.md`
  - `/.htaccess`
  - `/openspec/`
- [ ] **Formulari real**: enviar-ne un i comprovar que el correu arriba (apartat 7).
- [ ] La franja d'esborrany **no** apareix i el HTTPS és vàlid.
- [ ] Les cookies `vc_l`, `vc_v` i `vc_contact` es creen amb `Secure` i `HttpOnly`.
- [ ] Opcional: donar el domini d'alta a Google Search Console i enviar el sitemap; passar Lighthouse.

---

## 12. Problemes freqüents i preguntes per a cdmon

| Símptoma | Causa probable | Solució |
|---|---|---|
| Error 500 | Versió de PHP massa antiga | Apartat 4: PHP 8.1 o superior |
| El blog surt buit o dona error de base de dades | `data/` no escrivible o falta `pdo_sqlite` | Revisar permisos i preguntar a cdmon per l'extensió |
| `/robots.txt` o `/sitemap.xml` donen 404 | `mod_rewrite` o `.htaccess` no actiu | Preguntar a cdmon; comprovar que `.htaccess` s'ha pujat |
| Les carpetes protegides es veuen | El `.htaccess` no s'aplica | Comprovar `AllowOverride`; **A confirmar amb cdmon** |
| El formulari respon 503 | Correu no configurat, o reCAPTCHA amb problema de certificat | Apartats 5 i 8; mirar el log d'errors |
| Els correus cauen a brossa | Falta SPF o DKIM, o remitent que no és del domini | Apartat 7 |
| La web es veu sense estils | La web carrega Tailwind i GSAP des de CDN i necessita Internet | Comprovar la connexió i bloquejos de xarxa |
| La franja d'esborrany no desapareix | Queda algun valor `PENDENT` | Revisar tots els camps de `site-config.local.php` |

**Preguntes per fer a cdmon abans de desplegar** (no s'han pogut verificar a la seva documentació):

- [ ] Quin pla d'allotjament inclou PHP 8.1 o superior amb `pdo_sqlite`, `curl` i `mbstring`?
- [ ] Les regles `.htaccess` i `mod_rewrite` estan actives? Quina versió d'Apache hi ha?
- [ ] `data/` és escrivible per PHP amb els permisos per defecte?
- [ ] La funció `mail()` de PHP està permesa? Hi ha límits d'enviament? Signa amb DKIM?
- [ ] Quin SPF correspon al correu enviat des de l'allotjament web?
- [ ] L'SFTP està disponible al pla?
- [ ] Es pot fixar la versió de PHP per domini o subdomini?
- [ ] Com es crea un subdomini de proves i es protegeix amb contrasenya?
- [ ] L'allotjament fa còpies de seguretat? Com es restauren?
- [ ] Quin és el nom exacte de l'opció per forçar HTTPS i dels servidors de noms?

---

## 13. Actualitzacions i manteniment

- **Actualitzar la web**: pugeu només els fitxers canviats, sobreescrivint els del servidor. **No sobreescriviu ni esborreu** `data/blog.sqlite`, `includes/site-config.local.php` ni `includes/secrets.local.php`.
- **Tornar enrere**: conserveu el `.zip` anterior i la còpia de `data/blog.sqlite`; per desfer una actualització, torneu a pujar el `.zip` anterior.
- **Entrades noves**: es publiquen editant `data/posts.php` (apartat 10).
- **Qui manté la web després**: **A confirmar amb el client**.

---

## Fonts consultades

- Canviar la versió de PHP: <https://helpdesk.cdmon.com/portal/es/kb/articles/c%C3%B3mo-modificar-la-versi%C3%B3n-de-php>
- Pujar la web per FTP (carpeta `/web`): <https://helpdesk.cdmon.com/portal/en/kb/articles/how-to-upload-my-website-to-hosting-ftp>
- Connectar per FTP amb FileZilla: <https://www.cdmon.com/es/blog/como-conectar-por-ftp-mediante-filezilla>
- Dades d'accés a l'FTP: <https://helpdesk.cdmon.com/portal/en/kb/articles/where-to-view-ftp-access-data>
- Certificat de seguretat gratuït: <https://www.cdmon.com/es/blog/el-certificado-de-seguridad-gratuito-ha-llegado-a-cdmon>
- Afegir un DKIM al DNS de cdmon: <https://helpdesk.cdmon.com/portal/en/kb/articles/how-to-add-a-dkim-to-the-cdmon-dns>
- Configurar l'SPF al DNS estàtic: <https://www.cdmon.com/es/blog/como-configurar-el-registro-spf-para-el-correo-en-el-dns-estatico>
- Servir cdmon amb DNS extern: <https://helpdesk.cdmon.com/portal/es/kb/articles/c%C3%B3mo-configurar-los-servicios-de-cdmon-con-el-dns-externo>
- Veure els logs d'errors: <https://helpdesk.cdmon.com/portal/en/kb/articles/where-to-view-accommodation-error-logs>
- PHP 8.3 a cdmon: <https://cdmon.com/en/blog/php-8-3-at-cdmon>
- Informació bàsica de PHP: <https://helpdesk.cdmon.com/portal/en/kb/articles/php-basic-information>
- Configuració de PHP des del panell: <https://www.cdmon.com/en/blog/configure-php>

Algunes d'aquestes pàgines només s'han pogut consultar a través del resum d'un cercador, sense obrir-les. Per això el menú exacte, la versió màxima de PHP i les extensions disponibles queden com a **A confirmar amb cdmon**.
