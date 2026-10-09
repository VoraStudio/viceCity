# 008 — Comptadors de «m'agrada» i visites al blog

**Stack**: arquitectura
**Estat**: acceptat
**Data**: 2026-10-09

## Context

Cada entrada del blog ha de mostrar un comptador de «m'agrada» (cor) i un de visites (ull), i tots dos han de ser funcionals. El blog ja és PHP + SQLite (decisió 006) i no hi ha comptes d'usuari: no es pot saber qui és qui més enllà del navegador.

## Decisió

- **Esquema**: dues columnes noves a `posts`, `likes` i `views` (`INTEGER NOT NULL DEFAULT 0`). `createSchema` les inclou en crear la taula i `addMissingPostColumns` les afegeix amb `ALTER TABLE` si la BD ja existia (consulta `PRAGMA table_info`), de manera idempotent i sense perdre dades. La sincronització amb `data/posts.php` (`syncPosts`) no toca aquestes columnes: l'`upsert` no les anomena, així que els comptadors sobreviuen a editar o tornar a sincronitzar les entrades.
- **Visites**: `articulo.php` crida `recordView` abans de generar cap sortida. Només compta peticions `GET` que no siguin prefetch/prerender (`Sec-Purpose`, `Purpose`, `X-Moz`) ni d'un User-Agent evident de bot o eina (patró a `BOT_USER_AGENT_PATTERN`), i només una vegada per visitant i article, recordat amb la cookie `vc_v` (llista de slugs). L'increment és atòmic i preparat: `UPDATE posts SET views = views + 1 WHERE slug = :slug`.
- **«M'agrada»**: `like.php` accepta només POST i fa *toggle* segons la cookie `vc_l` (llista de slugs): sense el slug a la cookie suma 1, amb el slug resta 1 amb `MAX(likes - 1, 0)`, de manera que el comptador no baixa de 0. Respon JSON (`liked`, `likes`, `label`) si la petició porta `Accept: application/json` i, si no, redirigeix amb 303 a l'article (patró PRG).
- **Millora progressiva**: el botó és un `<form method="post" action="like.php">` que funciona sense JS. `script.js` (`initLikeButtons`) intercepta l'enviament, fa `fetch`, actualitza `aria-pressed` i el text del comptador (`aria-live="polite"`) i, si el `fetch` falla, envia el formulari de manera normal. L'estat inicial del botó es calcula al servidor llegint la cookie.
- **Cookies**: `HttpOnly`, `SameSite=Lax`, `Secure` només si la petició és HTTPS, caducitat d'un any, camí derivat de la ubicació dels scripts (així funciona en un subdirectori) i un màxim de 100 slugs per cookie (es descarten els més antics). Només hi ha slugs públics, cap dada personal.
- **Validació i origen**: el slug ha de complir el patró de `validatePost` i existir a la BD (400 i 404 en cas contrari); la resta de mètodes responen 405 amb `Allow: POST`. L'origen es comprova amb `Sec-Fetch-Site` (només `same-origin` o `none`) i, si el navegador no l'envia, amb la capçalera `Origin` contra `Host`. Si no n'hi ha cap, es deixa passar (clients antics). No hi ha token CSRF, igual que a `contacto.php`.
- **Presentació**: `renderPostStats` mostra cor i ull amb el nombre compacte (`formatCount`: `1,2 k`, `3 M`) a les cards i a l'article destacat, amb el recompte complet com a text per a lectors de pantalla. A `articulo.php` el botó «M'agrada» i les xifres van al peu de l'article, com a darrer bloc sota el text, amb el cor ple quan està premut (`aria-pressed`). Només s'usen tokens del theme.
- **Dades inicials**: totes les entrades comencen a 0, perquè la llavor (`data/posts.php`) no té mètriques reals i no s'inventen.

## Límits antiabús

- Una cookie no identifica una persona: qui l'esborri, usi una finestra privada o enviï POST directes pot sumar visites i «m'agrada» tantes vegades com vulgui. És un comptador d'interès aproximat, no una mètrica fiable ni auditable.
- No hi ha límit de freqüència per IP ni captcha. Si apareix abús, valorar un límit per IP amb una taula a SQLite o una regla al servidor web.
- El filtre de bots per User-Agent només atura els rastrejadors que s'identifiquen; qui falsifica el User-Agent compta.
- La comprovació d'origen depèn de capçaleres que envia el navegador; no protegeix contra clients que no són navegadors.

## Conseqüències

- Cada visita nova d'un article i cada «m'agrada» escriuen a SQLite, a diferència de les peticions normals del blog. Amb poca concurrència és assumible; si creix, valorar mode WAL o acumular les visites.
- La BD existent es migra sola a la primera petició després del desplegament; `data/blog.sqlite` no es versiona.
- Sense JS el botó recarrega la pàgina (PRG).
- No hi ha comentaris: es va descartar.
- Si es va provar la funció de comentaris en local, la BD `data/blog.sqlite` (no versionada) pot conservar una taula `comments` sense ús; cap codi la fa servir i es pot eliminar sense efectes.
- **Comprovat**: sintaxi (`php -l` i `node --check`), la migració sobre una còpia de la BD antiga (columnes afegides, 8 files intactes, segona execució sense canvis), `formatCount`, i amb `php -S` sobre una còpia: visita amb i sense cookie, HEAD, User-Agent de bot i prefetch (no compten), *toggle* de «m'agrada» d'anada i tornada, no baixar de 0, slug inexistent (404) i no vàlid o en forma d'array (400), GET al *endpoint* (405), origen creuat i `Sec-Fetch-Site: cross-site` (403), mateix origen (200), resposta 303 sense JS, estat inicial `aria-pressed` llegit de la cookie i renderitzat de `blog.php` amb els comptadors.
- **No provat**: el resultat en un navegador (aspecte, focus, que Tailwind generi `aria-pressed:` i `group-aria-pressed:`, el `fetch` de `script.js`), el comportament de les cookies en HTTPS o en un subdirectori real d'Apache, ni la migració sobre la BD real de Pau.
