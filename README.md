# Vicity — web

Web corporativa de Vicity, plataforma de gestió tributària i recaptació per a administracions locals. Són pàgines HTML, Tailwind CSS i JavaScript sense pas de compilació, més un blog amb PHP i SQLite (no hi ha panell d'administració: les entrades són a `data/posts.php`).

Domini: el defineix el client a `includes/site-config.php` (vegeu «Configuració del lloc»).

## Stack

| Capa | Tecnologia |
|---|---|
| Marcatge | HTML5 semàntic, en català (`lang="ca"`) |
| Estils | Tailwind CSS v4 en mode navegador (`@tailwindcss/browser`), amb els tokens del projecte en un `<style type="text/tailwindcss">` per pàgina |
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

Cal connexió a internet: sense ella no es carrega Tailwind i la web es veu sense estils. Segons la documentació de Tailwind, el mode navegador està pensat per a desenvolupament i prototips. Es manté així de manera deliberada: l'allotjament és bàsic i no hi ha compilador (decisió 042).

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
├── includes/                  PHP: blog (BD, categories, helpers), contacte (validació, CSRF, reCAPTCHA) i capçalera/peu comuns
├── data/                      posts.php (les entrades) i blog.sqlite (generada, no es versiona)
├── robots.php, sitemap.php    Generats a partir de la configuració del lloc (servits com /robots.txt i /sitemap.xml pel .htaccess)
├── tokens.md                  Tokens de disseny (colors, fonts, escala tipogràfica)
├── assets/
│   ├── fonts/                 Tipografies en local
│   ├── img/                   Logotips i imatges (blog/: imatges dels articles)
│   ├── video/                 Vídeo de la demo i pòster
│   └── js/
│       ├── script.js          Animacions d'entrada, una funció per secció
│       ├── js-flag.js         Marca <html> amb la classe "js"
│       ├── components/        <site-header> i <site-footer>
│       └── modules/           Menú, acordió, carrusel, ripple i títols
└── openspec/                  Decisions del projecte (sempre en català)
```

## Configuració del lloc

El domini, el correu del formulari i les dades del titular es defineixen en un sol lloc: `includes/site-config.php`. Tots els valors pendents comencen per `PENDENT`.

1. Copiar `includes/site-config.local.example.php` a `includes/site-config.local.php` (ignorat per git) i posar-hi els valors reals; se sumen als valors per defecte.
2. Mentre quedi algun valor `PENDENT`, el lloc es mostra com a **esborrany**: franja visible a les pàgines PHP, `noindex, nofollow` i `robots.txt` amb `Disallow: /`.
3. Mentre `mail_recipient` o `mail_sender` siguin `PENDENT`, el formulari de contacte respon 503 i no envia res.
4. `blog_indexable` és `false` (decisió 006): el blog segueix amb `noindex` i fora del sitemap fins que s'activi.

## Activar reCAPTCHA al formulari de contacte

Està implementat però **desactivat** fins que hi hagi claus reals (vegeu la decisió 010). Per activar-lo:

1. Crear les claus reCAPTCHA v3 per al domini definitiu (el `hostname` que es verifica surt de `base_url` a `includes/site-config.php`).
2. A `includes/recaptcha-config.php`, posar la clau de lloc a `site_key` en lloc de `PENDENT_CLAU_DE_SITE`.
3. Copiar `includes/secrets.example.php` a `includes/secrets.local.php` (ignorat per git) i posar-hi la clau secreta. No es puja mai al repositori.
4. Comprovar que PHP té cURL amb `curl.cainfo` configurat.

## Convencions

- **Mobile first**: la base és mòbil i es puja amb `md:` i `lg:`.
- **Tokens**: els colors, tipografies i breakpoints viuen al bloc `@theme` de cada pàgina. Està copiat a les 4 pàgines HTML i a `includes/head.php` (blog): un canvi s'ha de fer als 5 llocs.
- **Header i footer**: viuen en un sol lloc (`assets/js/components/`) i es pinten amb JavaScript.
- **Animacions**: `assets/js/script.js`, una funció `init…Animation` per secció, amb separadors per comentari.
- **Decisions**: cada decisió es documenta a `openspec/decisions/` i porta el seu commit.
- **Comentaris**: en català o castellà, mai en anglès.

## Material de client

La carpeta `docs/` (disseny, identitat visual i manual de marca) conté material del client i **no es versiona ni es publica**. `tokens.md` i algunes decisions hi fan referència: només existeix en local.
