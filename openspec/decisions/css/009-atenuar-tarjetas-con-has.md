# 009 — Atenuar les altres targetes d'«Especialització» amb :has

**Stack**: css
**Estat**: acceptat
**Data**: 2026-10-06
**Substituïda per**: 019 (a `lg` l'atenuació de les targetes amb `:has` queda substituïda pel carrusel vertical cíclic)

## Context

La secció 2 mostra quatre targetes en cascada. A `lg` s'apilen a la mateixa cel·la del grid amb desplaçaments, de manera que l'`<ol>` és una caixa gran amb buits. Es vol que, en passar el ratolí per una targeta, les altres s'atenuïn.

## Decisió

- **Selector relacional al pare:** a l'`<ol>` s'afegeix `[&:has(li:hover)>li:not(:hover)]:opacity-50`. Es llegeix: «si l'`ol` conté una `li` en hover, les `li` filles que no són en hover passen a `opacity-50`».
- Es tria `:has` davant de `group` + `group-hover`: `group` s'activaria en passar pels buits de l'`<ol>` i atenuaria també la targeta assenyalada. Amb `:has(li:hover)` només s'activa amb el ratolí sobre una targeta real.
- **Transició** a cada `<li>`: `transition-opacity duration-300 ease-out motion-reduce:transition-none`. Va a la `<li>`, que és qui canvia d'opacitat, no a l'`<ol>`.
- **Targeta al davant:** `lg:hover:z-50` a cada `<li>`, per sobre de la cascada (`lg:z-10` a `lg:z-40`), perquè una targeta parcialment tapada surti sencera en hover.
- **Alçades:** es treu `items-start` de l'`<ol>`. Les targetes d'una mateixa fila de la graella passen a igualar la seva alçada (`items-stretch` per defecte).

## Conseqüències

- **No està fet:** el fons diferent a la targeta en hover. Cal triar color, i els fons lavanda amb text continuen sense resoldre des de la decisió 004.
- **No està fet:** igualar l'alçada de totes les targetes entre files diferents (`auto-rows-fr`). En mòbil la targeta 04 continua sent més baixa, perquè no té la fila de l'etiqueta.
- Amb `opacity-50` les targetes que se solapen a `lg` deixen veure la que tenen a sota a través d'elles. No s'ha comprovat visualment.
- `z-index` no s'anima: en deixar el hover torna de cop al valor original.
- Diverses llistes de classes s'han partit en dues línies dins de l'atribut `class`. Funciona, però convé tornar a una sola línia.
