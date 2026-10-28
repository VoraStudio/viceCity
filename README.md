# Vicity — web

Web corporativa de Vicity, plataforma de gestió tributària i recaptació per a administracions locals. Són pàgines HTML, Tailwind CSS i JavaScript sense pas de compilació, més un blog amb PHP i SQLite (no hi ha panell d'administració: les entrades són a `data/posts.php`).

Domini: el defineix el client amb la variable `VICITY_BASE_URL`, a l'entorn o a `.env` (vegeu «Variables d'entorn»).

## Stack

| Capa | Tecnologia |
|---|---|
| Marcatge | HTML5 semàntic, en català (`lang="ca"`) |
| Estils | Tailwind CSS v4 en mode navegador (`@tailwindcss/browser`), en un `<style type="text/tailwindcss">` per pàgina |
| Scripts | JavaScript vanilla amb mòduls ES (`type="module"`) |
| Animació | GSAP 3.15.0: `gsap`, `ScrollTrigger`, `SplitText`, `Draggable` i `InertiaPlugin` |
| Components | Web Components propis (`<site-header>`, `<site-footer>`), sense Shadow DOM |
| Tipografies | Red Hat Display (títols) i Quicksand (text), en local, WOFF2 variable |
| Format | Prettier (`.prettierrc`) |
| Blog | PHP 8 amb PDO SQLite (extensió `pdo_sqlite`), BD a `data/blog.sqlite` |
| Desplegament | Pàgines estàtiques: GitHub Pages (`.github/workflows/pages.yml`). El blog necessita un allotjament amb PHP |

## Dependències

No hi ha `package.json`, ni `npm install`, ni `node_modules`. Totes les dependències es carreguen per CDN (jsDelivr), amb la versió fixada i un hash `integrity` (SRI):

| Dependència | Versió | Per a què |
|---|---|---|
| `@tailwindcss/browser` | 4.3.3 | Genera les utilitats de Tailwind al navegador |
| `gsap` | 3.15.0 | Motor d'animació |
| `ScrollTrigger` | 3.15.0 | Animacions lligades al scroll |
| `SplitText` | 3.15.0 | Divideix el text en línies |
| `Draggable` i `InertiaPlugin` | 3.15.0 | Arrossegar el carrusel en tauleta i mòbil |

Cal connexió a internet: sense ella no es carrega Tailwind i la web es veu sense estils. Segons la documentació de Tailwind, el mode navegador està pensat per a desenvolupament i prototips. Es manté així de manera deliberada: l'allotjament és bàsic i no hi ha compilador.

## Com executar-lo

Cal servir la carpeta per HTTP, perquè els mòduls ES i els Web Components no funcionen obrint els fitxers amb `file://`.

**Amb el blog (recomanat)**: el blog necessita PHP amb l'extensió `pdo_sqlite` (es comprova amb `php -m`). Des de l'arrel del projecte:

```bash
php -S localhost:8080
```

Després s'obre <http://localhost:8080/>. La primera visita a `blog.php` crea `data/blog.sqlite` (carpeta `data/` escrivible). Per afegir o editar articles, es modifica `data/posts.php` i es refresca la pàgina. Cada entrada pot portar `images` i `links` (vegeu la capçalera de `data/posts.php`); les imatges es copien a `assets/img/blog/`.

**Només les pàgines estàtiques** (sense blog): també serveix un servidor estàtic, i `blog.php` no funcionarà.

```bash
python -m http.server 8080
```

Les rutes són relatives, així que la web funciona des de l'arrel d'un servidor o des d'una subcarpeta. A l'allotjament, `data/` i `includes/` porten un `.htaccess` amb `Require all denied` (cal que Apache permeti `.htaccess`).

## Estructura

```
.
├── index.html, avis-legal.html, privacitat.html, cookies.html
├── blog.php, articulo.php      Llistat (amb filtre ?categoria=) i detall del blog
├── like.php                   Endpoint del botó «M'agrada» (POST)
├── contacto.php, token.php    Formulari de contacte: enviament (JSON o HTML) i token CSRF
├── .env.example               Plantilla de configuració (es copia a .env, que no es versiona)
├── includes/                  PHP: configuració (site-config.php, env.php), blog (BD, categories, helpers), contacte (validació, CSRF, reCAPTCHA) i capçalera/peu comuns
├── data/                      posts.php (les entrades del blog)
├── robots.php, sitemap.php    Generats a partir de la configuració del lloc (servits com /robots.txt i /sitemap.xml pel .htaccess)
├── 404.html, 500.html         Pàgines d'error pròpies, servides pel .htaccess
└── assets/
    ├── fonts/                 Tipografies en local
    ├── i18n/                  Textos per idioma: ca.json (base), es.json i en.json
    ├── img/                   Logotips i imatges (blog/: imatges dels articles)
    ├── video/                 Vídeos i pòsters (els acabats en -es, en castellà)
    └── js/
        ├── script.js          Animacions d'entrada, una funció per secció
        ├── js-flag.js         Marca <html> amb la classe "js"
        ├── components/        <site-header> i <site-footer>
        └── modules/           Menú, acordió, carrusel, ripple i títols
```

## Traduccions

El català és l'idioma base i és el que hi ha escrit a les pàgines. El castellà i l'anglès es tradueixen omplint dos fitxers, sense tocar cap pàgina:

- `assets/i18n/es.json` (castellà) i `assets/i18n/en.json` (anglès) tenen les mateixes claus que `assets/i18n/ca.json`, que conté el text català de referència.
- Per traduir un text, busqueu-lo a `ca.json`, localitzeu la mateixa clau a `es.json` o `en.json` i escriviu-hi la traducció entre les cometes.
- **Una clau buida (`""`) mostra el text en català.** Es pot traduir a poc a poc: el que falti es continua veient en català.
- Respecteu el que va entre claus, com `{label}`, `{limit}` o `{site}`: el web hi posa el valor en el moment. Si el text català porta etiquetes (`<a …>`, `<mark …>`, `<span …>`), copieu-les igual i traduïu només el text.
- Són fitxers JSON: unes cometes dobles dins d'un text s'escriuen `\"`. Si un fitxer queda mal format, la web es mostra en català; es pot comprovar amb qualsevol validador de JSON.

El visitant tria l'idioma al selector de la capçalera (CA, ES, EN). L'elecció es recorda al navegador i la pàgina es torna a carregar en aquell idioma. Els missatges del formulari de contacte també surten d'aquests fitxers.

**Vídeos en un altre idioma.** Cada vídeo té tres claus al bloc `video`: `src` (el fitxer de vídeo), `poster` (la imatge que es veu abans que comenci) i `label` (la descripció per a lectors de pantalla). Per posar, per exemple, el vídeo principal en anglès, es copien `vicity-hero-en.mp4` i la seva imatge `vicity-hero-en-poster.webp` a `assets/video/` i s'escriuen les rutes a `en.json`, com ja fa `es.json` amb els vídeos en castellà. Si la clau és buida, es veu el vídeo en català.

**Què no es tradueix.** Els articles del blog (títol, resum i text de `data/posts.php`), les dades estructurades per a cercadors de `index.html`, el correu que rep l'equip amb cada sol·licitud i l'avís que es mostra quan el navegador no té JavaScript. Hi ha una sola adreça per pàgina, de manera que els cercadors indexen la versió catalana. En castellà o anglès, el català es veu un instant mentre es carrega la pàgina.

## Desplegament i pendents

Els passos per publicar la web en qualsevol allotjament, la configuració del lloc, reCAPTCHA i la llista del que ha de fer el client són a [`DEPLOY.md`](DEPLOY.md). Mentre quedi alguna variable de configuració buida o sense definir, el lloc es mostra com a esborrany (`noindex` i formulari desactivat).

### Variables d'entorn

Tota la configuració del lloc surt de variables d'entorn; al codi no hi ha cap valor escrit. Precedència: **entorn real del servidor > fitxer `.env`**. El `.env` només rellena el que l'entorn no defineix. Una variable buida o sense definir compta com a pendent. Detall a [`DEPLOY.md`](DEPLOY.md).

Com fer-ho servir: copieu `.env.example` a `.env` (a l'arrel del projecte o un nivell per sobre, fora del web), ompliu els valors i pugeu-lo al servidor. El `.env` no es versiona i el `.htaccess` el bloqueja.

| Clau de configuració | Variable |
|---|---|
| `base_url` | `VICITY_BASE_URL` |
| `site_name` | `VICITY_SITE_NAME` (el fan servir els títols, les descripcions i els missatges de les pàgines PHP; si falta, se n'omet el nom) |
| `mail_recipient` | `VICITY_MAIL_RECIPIENT` |
| `mail_sender` | `VICITY_MAIL_SENDER` |
| `mail_subject_prefix` | `VICITY_MAIL_SUBJECT_PREFIX` (opcional: si falta és `[<nom del lloc>]`) |
| `blog_indexable` | `VICITY_BLOG_INDEXABLE` (`1`, `true`, `on`, `yes` = cert; si falta, fals) |
| `owner.razon_social`, `nif`, `domicilio`, `registro_mercantil`, `email_contacto`, `telefono`, `dpd_contacto`, `autoridad_control` | `VICITY_OWNER_RAZON_SOCIAL`, `VICITY_OWNER_NIF`, `VICITY_OWNER_DOMICILIO`, `VICITY_OWNER_REGISTRO_MERCANTIL`, `VICITY_OWNER_EMAIL_CONTACTO`, `VICITY_OWNER_TELEFONO`, `VICITY_OWNER_DPD_CONTACTO`, `VICITY_OWNER_AUTORIDAD_CONTROL` |
| reCAPTCHA: clau de lloc i clau secreta | `VICITY_RECAPTCHA_SITE_KEY`, `VICITY_RECAPTCHA_SECRET` |

En local, el més simple és un `.env` a l'arrel; també es poden exportar abans de `php -S`: `VICITY_BASE_URL=https://exemple.cat php -S localhost:8080`. No poseu secrets ni valors reals en un `SetEnv` del `.htaccess`: és versionat.

## Convencions

- **Mobile first**: la base és mòbil i es puja amb `md:` i `lg:`.
- **Header i footer**: viuen en un sol lloc (`assets/js/components/`) i es pinten amb JavaScript.
- **Animacions**: `assets/js/script.js`, una funció `init…Animation` per secció, amb separadors per comentari.
- **Comentaris**: en català o castellà.

