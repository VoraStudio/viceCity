# 038 — Domini definitiu i pàgines legals

**Stack**: html + css
**Estat**: acceptat
**Data**: 2026-10-08

## Context

Les 5 pàgines, `robots.txt` i `sitemap.xml` apuntaven a `https://EXAMPLE-DOMAIN.tld` (decisió 016). Les pàgines legals (`avis-legal`, `privacitat`, `cookies`) tenien el text gran i una columna estreta (`max-w-5xl`).

## Decisió

- **El domini definitiu és `https://www.vicity.cat`**: es substitueix el placeholder als `canonical`, `og:*`, `twitter:image`, JSON-LD, `robots.txt` i `sitemap.xml`.
- **Pàgines legals**: contenidor més ample, `max-w-7xl px-4 md:px-8 lg:px-10` (com la resta de la web); `h1` a `text-4xl md:text-5xl`, `h2` a `text-2xl md:text-3xl` i text a `text-lg`. Es mantenen indexables, tal com es va decidir.

## Conseqüències

- Amb 80 rem d'ample i `text-lg`, les línies arriben a uns 150 caràcters en pantalles grans, el doble del còmode per llegir. Es va decidir així de manera explícita.
- Les pàgines legals tenen unes 22 marques `[A COMPLETAR]` i són indexables: cal completar-les abans de publicar.
- No provat en un navegador, només comprovat el codi.
