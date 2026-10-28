# 014 — Web preparada per traduir: diccionaris JSON, recàrrega i selector natiu

**Stack**: arquitectura (js + json + php)
**Estat**: acceptat
**Data**: 2026-10-28

## Context

El client vol el web «preparat per traduir»: que l'única feina pendent sigui escriure text a `assets/i18n/es.json` i `assets/i18n/en.json`. El català és la llengua base, és el que hi ha escrit al HTML i ha d'estar complet. Una primera versió (canvi d'idioma en calent, esdeveniment `languagechange`, control de peticions desfasades, regla de `<html lang>` segons si hi havia traduccions i selector de tres botons que només cabia a partir de 1536 px) era massa complexa per al que cal. Aquesta decisió la substitueix per la versió més simple que cobreix tot el web.

## Decisió

- **Diccionaris** `assets/i18n/ca.json`, `es.json` i `en.json`: objectes imbricats, claus per camí de punts i organitzades per pàgina i secció (`common.*`, `header.*`, `footer.*`, `video.*`, `home.hero.*`, `contact.*`, `blog.*`, `legal.privacy.*`, `errors.*`). `ca.json` té el text català literal del HTML; `es.json` i `en.json` tenen exactament les mateixes claus amb `""`, excepte les rutes dels vídeos en castellà. Un valor buit compta com a absent.
- **Cadena de recurs**: idioma actiu → català → l'element no es toca (es queda el català del HTML). `t(key, params)` per al text generat per JS retorna el mateix recurs i, com a darrer recurs, la clau. Els marcadors `{nom}` es substitueixen amb `params` (p. ex. `home.platform.status`).
- **Contracte de marcatge**:
  - `data-i18n="clau"` escriu `textContent`. Si un element barreja text i icones, el text va dins d'un `<span data-i18n>`.
  - `data-i18n-attr="alt:clau;aria-label:clau;content:clau;src:clau;poster:clau"` escriu atributs. `<title>` porta `data-i18n` i les `<meta>` (descripció, `og:*`, `twitter:*`) porten `data-i18n-attr="content:…"`.
  - `data-i18n-html="clau"` escriu `innerHTML` **només** als textos amb marcatge en línia (enllaços, `<span class="font-bold">`, `<mark>` dels textos legals). És acceptable perquè els diccionaris són fitxers del repositori, no entrada d'usuari; un valor que vingués de fora no podria anar mai per aquí.
  - Els enllaços externs (els que porten a un altre domini) s'obren en una pestanya nova i porten un text ocult `common.newTab`.
- **Canvi d'idioma = desar i recarregar**. El selector desa l'idioma a `localStorage` (`vicity-lang`, dins de `try/catch`) i crida `location.reload()`. No hi ha esdeveniments, ni repintat en calent, ni curses entre peticions.
- **Càrrega** (`initI18n`): llegeix l'idioma desat (vàlids `ca`, `es`, `en`; per defecte `ca`), carrega sempre `ca.json` (cal per a `t()` i el recurs) i, si és un altre idioma, el seu diccionari, amb `fetch(new URL("../../i18n/<lang>.json", import.meta.url))`. En català no es toca el DOM. En un altre idioma posa `<html lang>` a l'idioma triat, aplica els textos a tot el document, reformata les dates `<time datetime>` del blog amb `Intl.DateTimeFormat` i, si canvia el `src` d'un `<source>`, crida `load()` del vídeo. Si el diccionari de l'idioma triat no es pot carregar, la pàgina es queda en català.
- **Ordre**: `script.js` fa `await initI18n()` abans de cap animació. `title-reveal.js` ja no s'executa sol: exporta `initTitleReveal()` i el crida `script.js`, de manera que SplitText sempre divideix el text definitiu. El `<site-header>` i el `<site-footer>` es pinten abans (el seu mòdul va primer), així que els tradueix el mateix `initI18n`.
- **Selector**: un `<select>` natiu dins de la barra, visible a totes les amplades (a mòbil, al costat del botó del menú). Mostra `CA`, `ES`, `EN`; cada `<option>` porta `lang` i l'autònim com a `aria-label` (els autònims viuen a `i18n.js`, perquè no es tradueixen). L'etiqueta visualment oculta surt de `language.label`. Com tota la capçalera, només existeix amb JavaScript. Per fer-lo cabre s'han ajustat espais de la capçalera, mesurats amb Chrome a 360, 390, 1360, 1440 i 1536 px (amplada natural de tots els elements, sense que s'encongeixin):
  - mòbil: `px-3` a la barra, sense `mr-4` al logotip, `gap-1.5` als botons, botó «Demo» a `px-4` i selector de 56 px (64 px des de `md`);
  - de 1360 a 1535 px: barra a `xl:px-8`, enllaços sense `gap` (ja tenen `px-2`) i botons a `gap-3`; des de `2xl` tornen `px-16`, `gap-5` i `gap-4`.
- **Vídeos**: els tres `<video>` de `index.html` tradueixen `poster` i `aria-label`, i el seu `<source>` el `src` (`video.hero`, `video.integration`, `video.ai`). `es.json` apunta a `vicity-hero-es.mp4`, `vicity-animacio-es.mp4` i `vicity-conversa-es.mp4` i als pòsters `-es-poster.webp`, generats amb ffmpeg amb el mateix fotograma i la mateixa mida que els originals (hero 2,2 s a 1280 px; animació 5 s i conversa 11,5 s a 1200 px; decisions [051](../css/051-videos-a-integracio-i-ia.md) i [053](../css/053-video-animat-del-hero.md)). `en.json` els deixa buits: es veuen els catalans.
- **PHP**: `includes/i18n.php` llegeix els mateixos JSON amb `t($key, $lang, $params)` (mateix recurs al català) i `i18nLanguage()` valida l'idioma contra `ca`, `es`, `en` (qualsevol altre valor és `ca`, i per tant no es pot llegir cap altre fitxer). El front envia `lang` al `POST` del formulari de contacte i del botó «M'agrada»; els missatges del servidor i els errors de validació surten en aquell idioma (les etiquetes dels camps són les mateixes claus que el formulari). Les pàgines PHP es pinten en català amb `data-i18n` perquè el front les tradueixi: `head.php` rep `$page['i18n']` (p. ex. `blog.meta`) per al títol i la descripció; sense aquesta clau no es tradueixen (el títol d'un article és contingut). Els recomptes («3 visites») marquen el nom amb `blog.views.one|other` i `blog.likes.one|other`.

## Fora d'abast (es queden en català)

- Contingut del blog: títols, resums, text, imatges i enllaços dels articles (`data/posts.php` i SQLite).
- El bloc JSON-LD de `index.html`.
- El correu que rep l'equip de Vicity (assumpte i cos).
- L'avís `<noscript>`: sense JavaScript no hi ha elecció d'idioma, i sense Tailwind no hi ha `sr-only` per amagar res.
- Les pàgines de resultat de `contacto.php` quan s'envia sense JavaScript (només es veuen així) i els errors de protocol de `like.php` i `token.php`, que el front no mostra mai.

## Conseqüències

- Una sola URL per pàgina: els cercadors indexen només el català i no hi ha `hreflang`. Posicionar en altres idiomes demanaria URL per idioma i seria una decisió nova.
- En castellà o anglès es veu un instant el català a la primera pintada, abans que arribin els diccionaris (les animacions esperen, però el text estàtic ja és a la pantalla).
- Sempre es descarrega `ca.json` (també en català), perquè `t()` el necessita per als textos de JS.
- Els textos de `ca.json` són una còpia del català del HTML: si es canvia un text català, cal canviar-lo als dos llocs. Els textos de les pàgines PHP que depenen del nom de marca (`VICITY_SITE_NAME`) són a `ca.json` amb «Vicity»; el missatge d'èxit del contacte fa servir `{site}`, que surt de la configuració.
- Mentre un diccionari sigui incomplet, els fragments sense traduir es mostren en català dins d'un `<html lang="es">` o `lang="en"`. Les dates del blog sí que canvien d'idioma (les formata el navegador).
- Si l'emmagatzematge està bloquejat, en recarregar es torna a veure el català.
- La barra de la capçalera està mesurada amb els textos catalans; traduccions dels enllaços o dels botons molt més llargues que el català poden no cabre entre 1360 i 1535 px.
- `node --test tests/` no funciona a Node 26 (tracta la carpeta com un mòdul): s'usa `node --test "tests/*.test.mjs"`.

## Verificat

- Tests (`tests/i18n.test.mjs`, 21) de la lògica pura: camí de punts, recurs al català, valors buits, cadena de prototips, marcadors `{nom}` i lectura de `data-i18n-attr`. Escrits abans de la implementació i vistos fallar.
- Els tres JSON es llegeixen bé, `es` i `en` tenen les mateixes 281 claus que `ca`, cap valor de `ca` és buit i totes les claus que fan servir HTML, JS i PHP existeixen.
- Auditoria amb un script temporal (no versionat) sobre `index.html`, les legals, 404, 500, `<site-header>`, `<site-footer>`, `blog.php`, un article, l'article inexistent i `contacto.php`: cap text visible ni `alt`, `aria-label`, `title`, `placeholder` o `<meta>` sense clau, fora del contingut del blog, les adreces de correu i els codis del selector.
- Chrome sense interfície contra `php -S`, amb un `es.json` fals injectat només al navegador (cada valor català amb el prefix «[ES] » i les rutes reals dels vídeos): totes les pàgines tradueixen cada text, atribut, títol i `<meta>`; els vídeos passen als fitxers `-es` i es reprodueixen en entrar a la pantalla; SplitText divideix el text ja traduït; el menú, la notificació d'error del formulari i el seu botó de tancar surten en castellà; el `POST` del contacte porta `lang=es`; en recarregar es manté l'idioma. Amb l'`es.json` real (buit) es veu tot en català i els vídeos en castellà. Sense desbordament horitzontal ni a 390, 1024, 1360 i 1440 px en català, i el selector és visible a totes. A la consola només hi ha els errors de xarxa provocats per la prova (connexió refusada, 404 i 405 esperats).
- `node --check` als JS i `php -l` als PHP modificats. `t()` de PHP provat des de la línia d'ordres (recurs, marcadors i idioma invàlid).

## Pendent

- No provat sota Apache real (ha de servir `.json` com a `application/json`) ni a Firefox o Safari.
- Els textos de les pàgines legals encara són esborranys pendents de revisió jurídica.
