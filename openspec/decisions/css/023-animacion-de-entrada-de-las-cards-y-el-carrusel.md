# 023 — Animació d'entrada de les cards i el carrusel de logos

**Stack**: js + css
**Estat**: acceptat
**Data**: 2026-10-08

## Context

Continua les decisions 021 i 022. Les 4 cards de «Gestiona avui» (`#solucions`) i la franja de logos d'`#administracions` entren amb una sola timeline a `script.js`.

## Decisió

- **Una timeline per a les dues seccions**: la franja forma part del bloc `SOLUCIONS` i no té bloc propi.
- **Cards**: entren amb fade i `stagger: 0.2`, alternant el costat per índex (`x: (index) => index % 2 === 0 ? -80 : 80`, valor basat en funció). Marcades amb `data-solutions-card`.
- **Franja de logos**: puja amb fade (`y: 40`) després de les cards, amb la posició `"-=0.9"`, marcada amb `data-trust-strip`. S'anima la caixa exterior i no el marquee, que ja fa servir `transform` al seu `animate-marquee`.
- **`ScrollTrigger` amb `endTrigger: "#administracions"` i `end: "bottom top"`.** La franja viu en una altra `<section>` i el `reset` de `play reset play reset` no s'ha de disparar mentre continuï en pantalla.
- **`overflow-x-clip` a `#solucions`**: les cards arrenquen fora de l'amplada (`x: ±80`) i sense ell hi hauria scroll horitzontal durant l'animació. `clip`, a diferència de `hidden`, no crea un contenidor de scroll.
- **Padding de `#solucions` a `lg`**: de `lg:py-20` a `lg:pt-20 lg:pb-10`, per pujar la franja. Es va separar en `pt` i `pb` per no dependre de l'ordre del CSS generat.

## Conseqüències

- La franja s'anima amb el trigger de les cards: si queda per sota de la pantallada, el seu fade pot passar abans de veure's.
- Es manté la secció `#administracions` separada a l'HTML perquè el menú enllaça al seu id.
- No provat en un navegador, només comprovat el codi.
