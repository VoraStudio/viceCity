# 050 — Degradat continu al blog

**Stack**: html + tailwind
**Estat**: acceptat
**Data**: 2026-10-09

## Context

A `blog.php` es notava un tall entre la capçalera i el contingut: el degradat del hero acabava en `paper` sòlid mentre el fons de sota ja tenia un to lavanda, i la taca difuminada de l'esquerra quedava retallada per baix pel `overflow-hidden` de la secció. A més, el contingut acabava en `paper` pla just abans del footer (`bg-lav-100/40`), sense enllaç visual.

## Decisió

- **`<main>` amb `bg-linear-to-b from-paper to-lav-100/40`**: és el mateix final de degradat que la secció de contacte de la pàgina principal, i desemboca exactament en el `bg-lav-100/40` del footer. Va a `<main>` i no a l'última secció perquè el llistat, la llista buida o una categoria filtrada comparteixen el mateix fons.
- **Hero del blog**: `to-paper` passa a `to-transparent`, de manera que el degradat de la capçalera es fon amb el del `<main>` en lloc d'acabar en un color sòlid.
- **`overflow-hidden` passa a `overflow-x-clip`** a la secció del hero: continua retallant pels costats (no hi ha scroll horitzontal) però la taca lavanda de l'esquerra ja no queda tallada per baix.
- Només tokens del theme (`paper`, `lav-100`), cap color nou.

## Conseqüències

- Del títol a les cards el fons és continu, sense franja ni vora, i les cards es fonen amb el footer.
- La taca lavanda de l'esquerra pot sobresortir per sota del hero i tenyir lleugerament el contingut proper; amb un amplada molt estreta convé revisar que no tapi res.
- Només afecta `blog.php`. `articulo.php` manté el fons pla.
- Comprovat en Chrome real amb una captura a 1850 px d'amplada (la zona on es veia el tall): sense tall visible i sense scroll horitzontal. No revisat en mòbil.
