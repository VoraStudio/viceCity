# 012 — Pàgines d'error pròpies (404 i 500)

**Stack**: arquitectura (html estàtic, apache)
**Estat**: acceptat
**Data**: 2026-10-14

## Context

Quan una URL no existeix o PHP falla, Apache mostra la seva pàgina per defecte: sense la marca de Vicity, sense navegació i en anglès. Cal una pàgina pròpia, coherent amb la resta del web, que no depengui de PHP (un 500 sol ser precisament PHP trencat).

## Decisió

- **`404.html` i `500.html` a l'arrel**, estàtiques i sense PHP. Mateix `<head>`, bloc `@theme` i web components (`<site-header>`, `<site-footer>`) que `avis-legal.html`; títol, text curt i un enllaç «Tornar a l'inici». La 404 afegeix un enllaç al blog; la 500 no, perquè el blog és PHP.
- **`.htaccess`**: `ErrorDocument 404 /404.html` i `ErrorDocument 500 /500.html`, sempre amb ruta local (una URL completa faria un 302 i perdria l'estat d'error).
- **Rutes absolutes** (`/assets/...`, `/`) i `<base href="/">`. `ErrorDocument` serveix la pàgina sota la URL que ha fallat (p. ex. `/a/b/c`), de manera que les rutes relatives es trencarien. Els web components (`site-header.js`, `site-footer.js`, `home-href.js`) generen enllaços relatius (`index.html`, `blog.php`, `assets/img/...`) i `home-href.js` decideix si som a la portada amb `location.pathname`; amb `<base href="/">` tots aquests enllaços es resolen des de l'arrel sense tocar els components.
- **No indexables**: `<meta name="robots" content="noindex, nofollow">`, sense `canonical` ni `og:url`, i títols diferents («Pàgina no trobada», «Error del servidor»).
- **El 403 no es mapeja**: les carpetes `data/`, `includes/` i `openspec/` responen 403 (`RedirectMatch` i `Require all denied`). Un `ErrorDocument 403 /404.html` serviria el text «Pàgina no trobada» amb estat 403: l'estat ja revela que existeix, i el cos contradiu l'estat. No amaga res i confon, per tant es deixa la resposta per defecte.
- **Sense enllaç «Vés al contingut»**: amb `<base href="/">`, un enllaç `#contingut` navegaria a `/#contingut`. La pàgina és curta i el contingut va just darrere de la capçalera.

## Conseqüències

- **No funciona des d'una subcarpeta**: les rutes absolutes assumeixen que el web és a l'arrel del domini. Vàlid per a producció (`/web` a cdmon és l'arrel), no per a una instal·lació sota `/voraData/viceCity/` ni per a un GitHub Pages de projecte (`usuari.github.io/repo/`).
- **El bloc de tokens es copia per sisena vegada** (portada, tres legals i aquestes dues pàgines, a més de `includes/head.php`). El README passa de «5 llocs» a 7 llocs.
- **La 500 estàtica no pot mostrar detalls** (ni el motiu, ni un identificador d'error); el detall és als logs del servidor.
- **A GitHub Pages**, `404.html` es serveix automàticament per a qualsevol ruta inexistent, sense `.htaccess`. `500.html` no s'hi usa mai.
- **Dependència del CDN**: igual que la resta de pàgines, Tailwind es genera al navegador; si el CDN cau, la pàgina d'error es veu sense estils (el contingut continua llegible).

## Verificat

- Sintaxi i existència de tots els recursos referenciats (`assets/js`, `assets/css`, fonts, icona).
- `.htaccess` amb les noves directives: Apache de XAMPP local respon 200 a `index.html` i a `404.html` (sense error 500 per sintaxi).
- Les URLs `/no-existeix` sota `/voraData/viceCity/` no es poden usar per provar-ho: `/404.html` apunta a l'arrel d'`htdocs`, no al projecte.

## Pendent

- Provar una 404 real en un host amb el projecte a l'arrel (VirtualHost o el servidor definitiu): estat 404, estils, enllaços del capçal i peu des d'una URL niada (`/a/b/c`).
- Comprovar `/500.html` al servidor definitiu; forçar un 500 real no és pràctic.
- Confirmar amb cdmon que `ErrorDocument` està permès al `.htaccess` (`AllowOverride`).
