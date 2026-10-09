# 009 — Imatges i enllaços a les entrades del blog

**Stack**: arquitectura
**Estat**: acceptat
**Data**: 2026-10-09

## Context

Cada entrada del blog ha de poder portar una o més imatges i enllaços. Fins ara el cos (`body`) és text pla que `articulo.php` divideix en paràgrafs i escapa amb `e()` (decisió 006), de manera que qualsevol `<img>` o `<a>` escrit al text es mostraria literal. Això és intencionat: deixar passar HTML cru obre la porta a XSS.

## Decisió

S'han valorat tres vies i s'ha triat la **B, camps estructurats**:

- **A. Markdown mínim al cos**: descartada de moment. Permetria intercalar imatges al text, però obliga a escriure o afegir un conversor i a mantenir-lo segur.
- **B. Camps estructurats `images` i `links`**: triada. No cal parsejar res, el cos continua sent text pla escapat i cada valor es valida per separat.
- **C. HTML permès amb llista blanca**: descartada. És la més arriscada: un filtre mal fet equival a un XSS.

Format a `data/posts.php`, tots dos camps opcionals i llistes (poden anar buides):

```php
'images' => [
    ['src' => 'assets/img/blog/<slug>/foto.webp', 'alt' => 'Descripció', 'caption' => 'Peu de foto (opcional)'],
],
'links' => [
    ['label' => 'Text de l’enllaç', 'url' => 'https://exemple.cat/pagina'],
],
```

- **Validació** (`validatePost` a `includes/db.php`, amb missatges en català com la resta):
  - `images`: ha de ser una llista de com a màxim 12 elements. `src` ha de ser relatiu, sota `assets/img/blog/`, només amb caràcters `A-Za-z0-9._/-`, sense `..` ni `//` i amb extensió `webp`, `jpg`, `jpeg`, `png` o `avif` (SVG exclòs). `alt` és obligatori (màxim 200 caràcters, accessibilitat) i `caption` és opcional (màxim 300).
  - `links`: llista de com a màxim 10 elements. `label` obligatori (màxim 120) i `url` que superi `filter_var(FILTER_VALIDATE_URL)` amb esquema `http` o `https` i host. Això bloqueja `javascript:`, `data:` i `ftp:`, també amb formes com `javascript://host/%0a...`.
- **BD**: columnes `images` i `links` (`TEXT NOT NULL DEFAULT '[]'`) amb el JSON de cada llista, serialitzat amb `JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR` i normalitzat (només les claus conegudes). Es creen a `createSchema` i s'afegeixen a `addMissingPostColumns` amb `ALTER TABLE` de forma idempotent; en afegir-les s'esborra el hash de `meta` perquè la següent sincronització torni a volcar `data/posts.php`. A diferència de `likes` i `views`, **formen part del contingut editorial**: `syncPosts` les actualitza i el hash de `posts.php` ja cobreix qualsevol canvi.
- **Lectura defensiva**: `getPostBySlug` descodifica el JSON i el filtra amb les mateixes regles (`isValidPostImage`, `isValidPostLink`). Si el JSON és corrupte o no és una llista, retorna una llista buida; si algun element no compleix les regles, s'ignora. `getPosts` (llistat) no carrega aquestes columnes.
- **Presentació** (`articulo.php`): sota el cos i abans del peu de «m'agrada».
  - Imatges: cada una en un `<figure>` amb `<img loading="lazy" decoding="async">`, `alt` escapat i `<figcaption>` si hi ha peu. Quadrícula d'una columna al mòbil i de dues a `md` i més quan hi ha més d'una imatge.
  - Enllaços: bloc «Per saber-ne més» amb una llista d'`<a rel="noopener noreferrer">`, sense `target="_blank"`. Etiqueta i URL passen per `e()`.
  - Cada bloc s'omet si no hi ha elements. Només tokens del theme.
- **Carpeta d'imatges**: `assets/img/blog/`, una subcarpeta per entrada (amb un `.gitkeep` per versionar-la). Ni `robots.txt` ni els `.htaccess` la bloquegen.
- Les 8 entrades reals porten `'images' => []` i `'links' => []` perquè es puguin omplir sense haver de recordar el format. Buits, el HTML és idèntic al d'abans.

## Conseqüències

- **Les imatges no van intercalades al text**: es mostren totes juntes després del cos. Per intercalar-les caldria la via A (Markdown mínim) o un format de blocs, i es pot afegir més endavant sense canviar aquest esquema.
- **La coberta de les cards continua sent una variant de color** (`cover`: `lav`, `purple` o `ink`); aquestes imatges només apareixen al detall de l'article.
- Les imatges han d'estar copiades a `assets/img/blog/` abans de referenciar-les: la validació comprova la ruta i l'extensió, **no que el fitxer existeixi**.
- Les imatges no porten `width` i `height`, de manera que poden produir un petit canvi de maquetació en carregar.
- Els textos d'`alt`, `caption` i `label` són text, no HTML: si porten `<script>` o etiquetes, es mostren literals i escapats.
- Corregit de passada: els missatges d'error de `validatePost` escrivien `«$slug»` i PHP interpretava `»` com a part del nom de la variable (`$slug»`, indefinida). Ara usen `«{$slug}»`.
- Verificat amb PHP CLI i Chrome real sobre una còpia del projecte i de la BD: 22 casos de validació (inclosos `..`, SVG, `javascript:`, `ftp:` i `<script>` a `alt`, `label` i `caption`), migració idempotent d'una BD antiga, JSON corrupte, `likes` i `views` conservats en tornar a sincronitzar, HTML idèntic al d'abans a les 8 entrades amb llistes buides, i imatges carregades (`naturalWidth` > 0).
- Pendent: provar-ho amb imatges i enllaços reals de contingut, i en producció.
