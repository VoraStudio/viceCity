# 040 — Formulari de contacte amb estats d'error i èxit accessibles

**Stack**: html + css
**Estat**: acceptat
**Data**: 2026-10-08

## Context

Els camps del formulari de `#contacte` tenien la vora `border-gray` (`#b4b4b4`, uns 2:1 de contrast, per sota del 3:1 que demana WCAG 1.4.11) i l'únic senyal d'error era un canvi de color de la vora (`user-invalid:border-purple-700`): el color com a única pista (WCAG 1.4.1) i sense missatge (3.3.1).

## Decisió

- **Tokens nous** a l'`@theme` d'`index.html`: `--color-error: #b3261e` (vermell) i `--color-success: #1e7e34` (verd), tots dos amb contrast de 4,5:1 o més sobre blanc.
- **Vora neutra `border-ink/50`** als controls (uns 3,2:1 sobre blanc, calculat a mà).
- **Estats amb `user-invalid:` i `user-valid:`**: invàlid a `border-error` amb un anell suau, vàlid a `border-success`. Les variants `user-*` només s'activen després que l'usuari interactuï.
- **Missatge de text sota cada camp obligatori** (nom, organització, correu, motiu i casella de privacitat), amb una icona `aria-hidden`, amagat per defecte i mostrat amb el patró `peer`/`peer-user-invalid:`. Cada control hi apunta amb `aria-describedby`. Textos: «Aquest camp és obligatori.» i «Introdueix un correu electrònic vàlid.».
- **Camps opcionals** (càrrec, telèfon, missatge): només la vora neutra, perquè no poden ser invàlids.
- No canvien `action`, `method`, `enctype`, els noms dels camps, les etiquetes ni els `required`.

## Conseqüències

- L'ordre entre `hover:border-purple-700` i les vores d'estat el decideix Tailwind: un camp ja validat pot veure's lila en passar el ratolí en comptes de vermell o verd. Cal comprovar-ho al navegador. El mateix passa amb `user-invalid:ring-2` i `focus-visible:ring-4`.
- L'enviament continua sent un `mailto:` sense confirmació: pendent d'un endpoint real.
- No provat en un navegador, només comprovat el codi.
