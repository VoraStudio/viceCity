# 014 — Web preparada per traduir: diccionaris JSON i mòdul `i18n.js`

**Stack**: arquitectura (js + json)
**Estat**: acceptat
**Data**: 2026-10-28

## Context

El client ha demanat que el web quedi «preparat per traduir». Els idiomes (català, castellà i anglès) encara no són definitius: el català és la llengua base i ha d'estar complet; el castellà i l'anglès es deixen com a esquelets perquè es tradueixin més endavant. No hi ha traducció automàtica. Aquest primer pas només posa els fonaments (diccionaris i mòdul); el selector d'idioma, el marcatge `data-i18n` a `index.html`, els textos generats per JS i els textos de PHP vénen en passos posteriors.

## Decisió

- **Diccionaris** `assets/i18n/ca.json`, `es.json` i `en.json`: objectes imbricats amb clau per camí de punts (`t("language.label")`). `es` i `en` tenen exactament les mateixes claus que `ca`, amb `""` per a tot el que encara no està traduït. Els autònims (`language.names.ca|es|en`) són el mateix text als tres diccionaris: el nom d'un idioma s'escriu sempre en aquell idioma.
- **Mòdul** `assets/js/modules/i18n.js`, amb idiomes `["ca", "es", "en"]`, per defecte `ca` i clau de `localStorage` `vicity-lang`. API pública: `initI18n()`, `setLanguage(lang)`, `getLanguage()`, `t(key)` i `applyTranslations(root)`; a més s'exporten les funcions pures (`isSupportedLanguage`, `lookupKey`, `findTranslation`, `resolveTranslation`, `parseAttributeSpec`).
- **Cadena de recurs**: idioma actiu → `ca` → la clau mateixa. Un valor buit (`""`) compta com a absent, així que una clau pendent de traduir mostra el català sense cap tractament especial.
- **Contracte de marcatge**: `[data-i18n="clau"]` escriu `textContent` (mai `innerHTML`); `[data-i18n-attr="alt:clau;aria-label:altra.clau"]` escriu els atributs indicats. Si la clau no existeix en cap diccionari no es toca l'element: es queda el text català del HTML. Només `t()` retorna la clau com a darrer recurs. `applyTranslations(root)` treballa sobre qualsevol element (també l'arrel mateixa), de manera que serveix per a nodes inserts dinàmicament.
- **Preferència a `localStorage`**, sense cap cookie: `cookies.html` no canvia. Tots els accessos van dins de `try/catch` (mode privat o emmagatzematge bloquejat). Sense detecció de l'idioma del navegador per ara: sense preferència desada, `ca`.
- **Càrrega**: `fetch` de `new URL("../../i18n/<lang>.json", import.meta.url)`, relatiu al mòdul i per tant vàlid sota qualsevol camí base. Sempre es carrega `ca` (recurs) i l'idioma actiu, i es guarden en memòria. Si un `fetch` falla no es llença res: el web continua amb el que hi ha i el català del HTML queda visible (millora progressiva; sense JS es veu català, com a la [042](../css/042-avis-noscript.md)).
- **`setLanguage`** valida l'idioma, carrega els diccionaris, desa la preferència, torna a aplicar els textos, actualitza `<html lang>` i dispara `languagechange` a `document` amb `detail: { lang }` perquè els components posteriors es puguin tornar a pintar. Només s'aplica l'última petició: una càrrega lenta no trepitja una elecció posterior. Si el diccionari demanat no es pot carregar, es conserva l'idioma anterior i no es dispara cap esdeveniment.
- **Tests**: la lògica pura (camí de punts, cadena de recurs, lectura de `data-i18n-attr`) no depèn de DOM, `fetch` ni `localStorage` i es prova amb `node --test` a `tests/i18n.test.mjs`, sense dependències. `/tests` va a `export-ignore`: no entra al paquet de desplegament.
- **Cablejat**: `script.js` importa i crida `initI18n()` abans de les animacions. Sense cap `data-i18n` al marcatge, l'efecte visible és nul.

## Conseqüències

- Només client: hi ha una sola URL, de manera que els cercadors indexen només el català i no hi ha `hreflang`. Si més endavant interessa posicionar en altres idiomes caldran URL per idioma, i serà una decisió nova.
- Els textos amb marcatge en línia (enllaços, `<strong>`…) no poden usar `textContent` sense perdre el marcatge. Caldrà decidir-ho quan s'hi arribi (una clau per fragment, o un mecanisme explícit).
- Els textos generats per JS (`script.js`, `toast.js`, `aria-label` de `nav.js`) i els de PHP (`head.php`, formulari, blog, missatges del servidor) queden fora d'aquest pas.
- Els components `site-header` i `site-footer` (decisió [004](004-header-y-footer-como-web-components.md)) pinten el contingut amb `innerHTML` a `connectedCallback`, que s'executa abans que `script.js`: `applyTranslations(document)` ja el veu. Si es tornen a pintar, caldrà cridar `applyTranslations(this)`.
- Les animacions amb SplitText divideixen el text en línies. Canviar el text després de dividir-lo les trenca: caldrà aplicar els textos abans de dividir, o tornar a dividir a `languagechange`. `applyText` ja no escriu res si el text no canvia, de manera que en català no toca el DOM.
- Mentre un diccionari sigui incomplet, els fragments sense traduir es mostren en català dins d'un `<html lang="es">` o `<html lang="en">`; un lector de pantalla els llegirà amb la veu de l'altre idioma. Es pot millorar marcant `lang="ca"` als elements que cauen al recurs.
- `initI18n()` no dispara `languagechange` (només ho fa `setLanguage`). Un component que es pinti abans que acabi la càrrega, amb una preferència desada diferent de `ca`, no s'assabentaria del canvi.
- `languagechange` és també el nom d'un esdeveniment natiu de `window`; no es barregen perquè el nostre va a `document` i no fa bubbling.
- Amb l'emmagatzematge bloquejat, l'elecció d'idioma només dura fins que es recarrega la pàgina.
- `node --test tests/` no funciona a Node 26 (tracta la carpeta com un mòdul): s'usa `node --test "tests/*.test.mjs"`.

## Verificat

- 22 tests de les funcions pures escrits abans de la implementació i vistos fallar (mòdul inexistent), i després en verd.
- Els tres JSON es llegeixen bé, `es` i `en` tenen les mateixes claus que `ca` (només `language.label` buit) i els autònims són idèntics als tres.
- Simulació del DOM amb un script temporal (no versionat): inicialització per defecte i amb preferència desada o invàlida, `setLanguage`, esdeveniment amb `detail`, memòria cau (un sol `fetch` per idioma), `fetch` fallit, `localStorage` bloquejat, petició lenta trepitjada per una de posterior i `applyTranslations` sobre l'arrel mateixa.
- Chrome amb `php -S`: `index.html`, `cookies.html` i `blog.php` carreguen `ca.json` (200), sense errors a la consola i amb el text intacte; a `cookies.html`, `setLanguage("en")` canvia `<html lang>`, desa `vicity-lang` i dispara `languagechange`.
- `node --check` sense errors a `i18n.js` i `script.js`.

## Pendent

- Passos següents: selector d'idioma, marcatge `data-i18n` a `index.html` i a les pàgines legals, textos de JS i de PHP.
- No provat sota Apache real (el `.htaccess` no bloqueja `.json` i Apache l'ha de servir com a `application/json`) ni a Firefox o Safari.
