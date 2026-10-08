# 006 — Blog amb SQLite i PHP

**Stack**: arquitectura
**Estat**: acceptat
**Data**: 2026-10-08

## Context

El blog (`blog.html`) era una maqueta estàtica: un article destacat i sis targetes escrites a mà, i uns filtres de categoria que enllaçaven a `?categoria=...` però no filtraven res (decisió 031). Per tenir articles reals, filtre per categoria i una pàgina de detall cal una font de dades. L'allotjament admet PHP, però és bàsic (sense compilador, decisió 042) i no es vol un panell d'administració, ni login, ni formulari d'alta.

## Decisió

- **SQLite via PDO** (`data/blog.sqlite`), amb `ERRMODE_EXCEPTION` i `FETCH_ASSOC`. La BD es crea sola i **no es versiona** (`data/*.sqlite` a `.gitignore`).
- **Les entrades viuen al codi**, a `data/posts.php` (sense panell). La BD es sincronitza amb aquest fitxer: es guarda el seu hash a la taula `meta` i, quan canvia, es fa upsert per `slug` i s'esborren les entrades que ja no hi són, tot en una transacció. Editar `posts.php` i refrescar ja es veu; en les peticions normals no s'escriu res.
- **Categories en un sol lloc**: `includes/categories.php` (slug => etiqueta). Els chips del hero i la validació de les entrades en depenen.
- **Filtre per `?categoria=<slug>`** amb consulta preparada. Un slug desconegut **no dona 404**: es mostren totes les entrades i el chip «Tots» queda actiu. L'estat actiu es marca amb `aria-current="page"` i, si una categoria no té entrades, es mostra un missatge buit amb enllaç a «Tots».
- **`blog.html` passa a `blog.php`** (mateix markup, classes i `<head>`; els hooks `data-blog-*` s'imprimeixen des de PHP). Nova pàgina de detall `articulo.php?slug=...`, amb 404 real si el slug no existeix. El `<head>` i els scripts comuns són a `includes/head.php` i `includes/foot.php`. Tota la sortida s'escapa amb `htmlspecialchars`.
- **`data/` i `includes/` porten `.htaccess` amb `Require all denied`** perquè la BD i el codi no es puguin descarregar.
- `initBlogAnimation` (`assets/js/script.js`) ara tolera que no hi hagi destacat o targetes: només exigeix el hero.
- Tots els enllaços a `blog.html` (header, footer, `index.html`, pàgines legals, bloc `<noscript>`) apunten a `blog.php`.

## Conseqüències

- **El desplegament actual no serveix el blog.** `.github/workflows/pages.yml` publica a GitHub Pages, que no executa PHP: el blog ha d'anar a l'allotjament amb PHP (amb l'extensió `pdo_sqlite`). Això matisa la decisió 042 i el caràcter «lloc estàtic» de la web: les pàgines HTML continuen sent estàtiques, però el blog ja no.
- A l'allotjament, la carpeta `data/` ha de ser **escrivible** pel servidor web (SQLite hi crea el fitxer i el seu journal).
- Afegir una entrada vol dir editar `data/posts.php` i desplegar; no hi ha edició des del navegador.
- `blog.php` i `articulo.php` mantenen `noindex, follow` (com `blog.html`) i no són a `sitemap.xml`.
- Els articles del blog són text de mostra en català; cal revisar-los abans de publicar.
- **Comprovat**: sintaxi (`php -l`), `pdo_sqlite` disponible, respostes HTTP amb `php -S` (filtres, nombre de targetes, `aria-current`, 404, estat buit i resincronització en editar `posts.php`).
- **No comprovat**: el resultat en un navegador (estils, animacions GSAP, header i footer) ni que Apache respecti els `.htaccess` (`php -S` no els aplica, i allà `data/blog.sqlite` sí que es serveix). Cal verificar-ho a l'allotjament amb `AllowOverride` actiu.
