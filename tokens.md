# Tokens — Vicity

Referència de tokens del projecte. Estat: **Part 1 de l'INTAKE (colors, fonts, escala tipogràfica) confirmada per Pau el 2026-10-05**. Part 2 (components) confirmada el mateix dia. Part 3 (seccions) pendent.

## Fonts d'origen

| Dada | Font |
|---|---|
| Colors | `docs/Identitat Visual/Colors_Vicity.pdf` |
| Noms de token | `docs/Identitat Visual/Presentació/Manual destil_Vicity.pdf`, pàg. 12 |
| Escala tipogràfica | `docs/Identitat Visual/tipografia.pdf` (= pàg. 13 del manual) |
| Fitxers de font | `assets/fonts/` |

`docs/` és local (ignorat per Git). Aquest fitxer és la còpia versionada de referència.

## 1. Colors

```css
@theme {
  --color-lav-100: #D9C7FF;
  --color-lav-300: #B08CFF;
  --color-purple-500: #8F63FF;
  --color-purple-700: #5E35B1;
  --color-ink: #0D252F;
  --color-paper: #F7F8F6;
  --color-gray: #B4B4B4;
}
```

| Token | HEX | RGB | Nom al PDF de colors |
|---|---|---|---|
| `lav-100` | `#D9C7FF` | 217, 199, 255 | Morat clar |
| `lav-300` | `#B08CFF` | 176, 140, 255 | Lavanda clar |
| `purple-500` | `#8F63FF` | 143, 99, 255 | Lavanda fosc |
| `purple-700` | `#5E35B1` | 94, 53, 177 | Morat fosc |
| `ink` | `#0D252F` | 13, 37, 47 | Blau nit |
| `paper` | `#F7F8F6` | 247, 248, 246 | Blanc trencat |

Sense token al manual: **Gris `#B4B4B4`** (180, 180, 180). S'afegeix el token `gray` (nom triat per fer-lo servir a les pestanyes).

## 2. Fonts

```css
@theme {
  --font-head: 'Red Hat Display', sans-serif;
  --font-body: 'Quicksand', sans-serif;
}
```

| Família | Rol | Pesos al manual | Càrrega |
|---|---|---|---|
| Red Hat Display | Titulars | 400, 500, 600, 700 | `@font-face` local, `assets/fonts/Red_Hat_Display/` |
| Quicksand | Text de cos i interfície | no consta (etiqueta: 700) | `@font-face` local, `assets/fonts/Quicksand/` |

Els noms `--font-head` i `--font-body` no són al manual: es reaprofiten de `index.html`.

## 3. Escala tipogràfica

Valors del manual en px, convertits a rem sobre una base de 16 px.

```css
@theme {
  --text-title-1: 3.75rem;
  --text-title-2: 2.5rem;
  --text-body: 1.375rem;
  --text-label: 0.75rem;
}
```

| Rol | Font | Mida | Pes |
|---|---|---|---|
| Títol 1 | Red Hat Display | 60 px (`3.75rem`) | 700 (mostra del manual) |
| Títol 2 | Red Hat Display | 40 px (`2.5rem`) | 400 (mostra del manual) |
| Text de cos | Quicksand | 22 px (`1.375rem`) | no consta |
| Etiqueta | Quicksand | 12 px (`0.75rem`) | 700, majúscules |

Decisió de Pau (2026-10-05): l'etiqueta és **12 px en bold**. El «Quicksand 15» que apareix a la mateixa fila del manual queda descartat.

## 4. Components — regles del manual

Només les regles escrites. Les classes Tailwind es defineixen a la Part 2.

- Un únic botó primari per pantalla; la resta en secundari o ghost.
- Formularis: vores de 1,5 px; focus en `purple-500` amb halo suau.
- Targetes: cantonada de 30 px; la variant fosca, per a mòduls destacats.

### Detall visual (`docs/Identitat Visual/ui.pdf`)

Lectura visual del PDF. **No hi ha mides** (alçades, paddings, radis de botó): ni `ui.pdf` ni el manual les especifiquen. Les classes Tailwind es defineixen a la Part 2.

| Component | Variant | Aspecte |
|---|---|---|
| Botó | Primari («Continuar») | Píndola, fons `purple-700`, text blanc |
| Botó | Secundari («Cancel·lar») | Píndola, vora `purple-700`, fons transparent, text `purple-700` |
| Botó | Deshabilitat | Píndola, fons `lav-100`, text lila |
| Targeta | Estàndard | Vora `purple-700`, fons clar, títol bold en `purple-700`, text de cos |
| Targeta | Dada destacada («142») | Igual, amb la xifra grossa en bold `purple-700` |
| Targeta | Fosca («Pla Vicity+») | Fons `purple-700`, text blanc |
| Formulari | Contenidor | Fons gris molt clar, cantonada arrodonida |
| Formulari | Camp | Fons clar, vora fina de 1,5 px, etiqueta de Quicksand a sobre |
| Formulari | Casella | Camp gris amb casella i text «Sí» |
| Formulari | Enviar | Fons `lav-300`, text fosc, amplada completa |
| Etiqueta | «Nou» | Píndola `lav-100`, text lila bold |
| Etiqueta | «Urgent» | Píndola `lav-300`, text blanc bold |
| Etiqueta | «Vicity+» | Píndola `ink`, text blanc bold |
| Etiqueta | «Termini obert» | Píndola amb vora `purple-700` i punt, text `purple-700` bold |
| Etiqueta | «Pendent de revisió» | Píndola amb vora fosca, text negre bold |
| Pestanyes | Contenidor | Vora gris (`#B4B4B4`), arrodonit |
| Pestanyes | Activa («Pendent») | Fons `lav-100`, text `purple-700` bold |
| Pestanyes | Inactives | Fons `paper`, text `purple-700` bold |

El PDF del manual anuncia també una secció «Alertes» (pàg. 11), però no té cap pàgina de contingut: **no consta**.

### Classes Tailwind (Part 2, confirmada per Pau el 2026-10-05)

Regla de Pau: **sempre mesures de Tailwind**, sense valors arbitraris. Les classes surten de `ui_metrics.py` sobre el `ui.pdf` apaïsat 1440×810 (còpia de `sdd-vd/sdd-local`, el mateix disseny que `ui_final.pdf`, que és un A4 vertical i no es pot mesurar). Quan la nota del PDF i la mesura difereixen, guanya la classe Tailwind mesurada.

| Component | Variant | Classes |
|---|---|---|
| Botó | Primari | `h-11 rounded-full bg-purple-700 text-white` |
| Botó | Secundari | `h-11 rounded-full border-2 border-purple-700 text-purple-700` |
| Botó | Deshabilitat | `h-11 rounded-full bg-lav-100 text-purple-700` |
| Targeta | Estàndard | `rounded-3xl border-2 border-purple-700` |
| Targeta | Fosca | `rounded-3xl bg-purple-700 text-white` |
| Camp de formulari | Text | `h-9 rounded-sm border bg-paper` |
| Formulari | Contenidor | `rounded-xl bg-paper` |
| Botó enviar | — | `h-9 rounded-xl bg-lav-300 text-ink` |
| Etiqueta | Nou | `h-11 rounded-full bg-lav-100 text-purple-700 font-bold` |
| Etiqueta | Urgent | `h-11 rounded-full bg-lav-300 text-white font-bold` |
| Etiqueta | Vicity+ | `h-11 rounded-full bg-ink text-white font-bold` (mesurat `#0D2E32`, `SENSE_TOKEN`, es fa servir `ink`) |
| Etiqueta | Termini obert | `h-11 rounded-full border border-purple-700 text-purple-700 font-bold` |
| Etiqueta | Pendent de revisió | `h-11 rounded-full border border-ink text-ink font-bold` (mesurat `#2C2C2C`, `SENSE_TOKEN`, es fa servir `ink`) |
| Pestanyes | Contenidor | `h-16 rounded-3xl border-2 border-gray` |
| Pestanyes | Activa | `h-13 rounded-2xl bg-lav-100 text-purple-700 font-bold` |
| Pestanyes | Inactiva | `h-13 rounded-2xl bg-paper text-purple-700 font-bold` |

El contenidor de pestanyes fa servir `border-gray` amb el Gris `#B4B4B4` de marca, mesurat com a `SENSE_TOKEN`. Per poder-lo fer servir es defineix el token `gray` al `@theme`.

```css
@theme {
  --color-gray: #B4B4B4;
}
```

**Decisions de la discrepància nota/mesura:**

| Tema | Nota del PDF | Mesura | Triat |
|---|---|---|---|
| Radi de targeta | 30 px | 23 px | `rounded-3xl` |
| Vora dels camps | 1,5 px | 0,5 px | `border` (1 px, la mesura de Tailwind més propera) |

**Estats:** hover, active i disabled: *no especificat*. Focus: només consta la nota «morat viu amb halo suau», `purple-500`. «Alertes» no consta.

**`SENSE_TOKEN` sense resoldre** (mesurats als píxels): `#8C8C8C`, `#898989` (vora dels camps), `#D8D8D8` (casella). No s'han incorporat com a tokens.

## 5. Avisos i decisions pendents

| Tema | Detall |
|---|---|
| Blau nit | Manual i PDF de colors: `#0D252F`. `index.html` (base intocable): `#0B2530`. Pendent de Pau. |
| Noms de color | Els noms canvien entre el PDF de colors i el manual: `#D9C7FF` Morat clar / Lavanda clar; `#B08CFF` Lavanda clar / Lavanda; `#8F63FF` Lavanda fosc / Morat viu. Els tokens CSS no se'n ressenten. |
| Mida del cos | Manual: 22 px. `index.html`: 18 px. Pendent de Pau. |
| Regla de color | El manual diu que els tons lavanda no porten mai text a sobre, però els seus components en porten. Pendent de Pau. |
| Gris | Sense token (vegeu l'apartat 1). |
| Pesos de Quicksand | No consten al manual. |
