# 016 — SEO, domini definitiu i dependències fixades

**Stack**: html
**Estat**: acceptat (parcial: favicon pendent)
**Data**: 2026-10-07

## Context

El `<head>` només tenia títol i descripció, els scripts de CDN feien servir `@4` i `@3` (versió flotant, sense integritat) i els logos tenien espais i accents a la ruta.

## Decisió

- **Domini**: `https://www.vicity.cat`, el mateix literal a `canonical`, Open Graph, Twitter, JSON-LD, `robots.txt` i `sitemap.xml`. Si cal canviar-lo, es fa amb buscar i reemplaçar a tot el projecte (no hi ha build). El domini és definitiu des del 2026-10-08.
- **Metadades**: `canonical`, `theme-color` (`#5e35b1`, és `purple-700`), Open Graph, Twitter card `summary` i JSON-LD `Organization` només amb nom, URL, logo i descripció. No s'afegeix `color-scheme`: la pàgina és només clara i no hi aporta res.
- **Favicon**: `assets/img/logo/isotip/svg/sense-area-seguretat/1-isotip-fons-blanc.svg` com a `rel="icon"` SVG. No és quadrat (283 × 232) i no hi ha `.ico` ni `apple-touch-icon` PNG.
- **Imatge social**: l'imagotip PNG (2156 × 671). No hi ha una imatge 1200 × 630 pensada per a xarxes.
- **Dependències fixades amb SRI** (hash SHA-384 calculat descarregant el fitxer): `@tailwindcss/browser@4.3.3`, `gsap@3.15.0` i `ScrollTrigger@3.15.0`, amb `integrity` i `crossorigin="anonymous"`; jsDelivr envia `access-control-allow-origin: *`. GSAP carrega amb `defer` al `<head>`: els scripts `defer` i els mòduls entren a la mateixa cua i s'executen en ordre de document, així que `gsap` ja existeix quan corren `ripple.js` i `title-reveal.js`. L'script de Tailwind continua sent síncron per no mostrar contingut sense estils.
- **Rutes de logos**: `assets/img/Logotips/...` passa a `assets/img/logo/{imagotip,isotip,logotip}/{pdf,png,svg}/[sense-area-seguretat/]N-nom-en-minuscules.ext`. Es va treure el sufix de captura (`12.19.49`) dels PNG de l'isotip. `logoVora.png` passa a `assets/img/clients/vora.png`.

## Conseqüències

- En actualitzar una versió cal recalcular el hash (`curl -sL <url> | openssl dgst -sha384 -binary | openssl base64 -A`). Un hash desfasat bloqueja l'script i la pàgina queda sense estils.
- El JSON-LD és un bloc de dades (`type="application/ld+json"`), no codi executable; és l'única etiqueta `<script>` amb contingut en línia.
- Els noms numerats dels logos es conserven (`1-…` a `6-…`) perquè identifiquen la variant de fons.
