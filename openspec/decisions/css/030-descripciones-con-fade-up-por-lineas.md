# 030 — Descripcions amb fade up per línies

**Stack**: js
**Estat**: acceptat
**Data**: 2026-10-08

## Context

Les descripcions del hero, Especialització, Integració, IA i Azure feien servir l'efecte de profunditat a X per caràcters (decisions 022, 024, 025, 026 i 027). Solucions i Contacte no tenien efecte a la seva descripció. Es prova primer al hero i s'estén a totes.

## Decisió

- **Un únic efecte**: fade up amb `stagger` per línies, amb `SplitText` (`type: "lines"`). `linesFadeUp` (`y: 30`, 0,8 s, `power3.out`, `stagger: 0.15`) i `splitIntoLines` viuen al bloc `COMUNES` de `script.js`.
- **S'aplica a les 7 descripcions**: hero, Solucions (nova), Especialització, Integració (2 paràgrafs), IA, Azure i Contacte (nova). A Solucions i Contacte s'afegeixen `data-solutions-description` i `data-contact-description` i l'animació es col·loca amb posició absoluta (`0.4` i `0.3`) per no moure la resta de la timeline.
- **IA conserva el seu tempo més ràpid** amb un override local: `{ ...linesFadeUp, duration: 0.5, stagger: 0.1 }`.
- S'eliminen `charsDepthReveal` i `splitIntoChars`, que queden sense ús. Els dos efectes continuen a l'historial de git.
- Solucions i Contacte passen a esperar `document.fonts.ready` abans de dividir en línies, igual que la resta.

## Conseqüències

- **El split per línies es calcula una sola vegada**: en girar el dispositiu o redimensionar, les línies no es recalculen. `title-reveal.js` ho resol amb `autoSplit: true`; si es nota, es poden passar les seccions a `autoSplit`.
- Solucions té una descripció d'una sola línia, així que no hi ha `stagger` per veure.
- Substitueix l'efecte de caràcters de les decisions 022, 024, 025, 026 i 027 pel que fa a les descripcions.
- No provat en un navegador, només comprovat el codi.
