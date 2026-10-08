# Vicity — web

Web corporativa de Vicity, plataforma de gestió tributària i recaptació per a administracions locals. És un lloc estàtic: HTML, Tailwind CSS i JavaScript, sense backend ni pas de compilació.

Domini: <https://www.vicity.cat>

## Stack

| Capa | Tecnologia |
|---|---|
| Marcatge | HTML5 semàntic, en català (`lang="ca"`) |
| Estils | Tailwind CSS v4 en mode navegador (`@tailwindcss/browser`), amb els tokens del projecte en un `<style type="text/tailwindcss">` per pàgina |
| Scripts | JavaScript vanilla amb mòduls ES (`type="module"`) |
| Animació | GSAP 3.15.0: `gsap`, `ScrollTrigger`, `SplitText`, `Draggable` i `InertiaPlugin` |
| Components | Web Components propis (`<site-header>`, `<site-footer>`), sense Shadow DOM |
| Tipografies | Red Hat Display (títols) i Quicksand (text), en local, WOFF2 variable |
| Format | Prettier (`.prettierrc`) |
| Desplegament | GitHub Pages, amb `.github/workflows/pages.yml` |

## Dependències

No hi ha `package.json`, ni `npm install`, ni `node_modules`. Totes les dependències es carreguen per CDN (jsDelivr), amb la versió fixada i un hash `integrity` (SRI):

| Dependència | Versió | Per a què |
|---|---|---|
| `@tailwindcss/browser` | 4.3.3 | Genera les utilitats de Tailwind al navegador |
| `gsap` | 3.15.0 | Motor d'animació |
| `ScrollTrigger` | 3.15.0 | Animacions lligades al scroll |
| `SplitText` | 3.15.0 | Divideix el text en línies |
| `Draggable` i `InertiaPlugin` | 3.15.0 | Arrossegar el carrusel en tauleta i mòbil |

Cal connexió a internet: sense ella no es carrega Tailwind i la web es veu sense estils. Segons la documentació de Tailwind, el mode navegador està pensat per a desenvolupament i prototips. Es manté així de manera deliberada: l'allotjament és bàsic i no hi ha compilador (decisió 042).

## Com executar-lo (sense XAMPP)

No cal XAMPP ni cap servidor PHP. Només cal servir la carpeta per HTTP, perquè els mòduls ES i els Web Components no funcionen obrint els fitxers amb `file://`. Qualsevol d'aquestes opcions serveix:

```bash
# Python 3
python -m http.server 8080

# Node.js
npx serve .
```

També es pot usar l'extensió **Live Server** de VS Code. Després s'obre <http://localhost:8080/> al navegador.

Les rutes són relatives, així que la web funciona des de l'arrel d'un servidor o des d'una subcarpeta.

## Estructura

```
.
├── index.html, blog.html, avis-legal.html, privacitat.html, cookies.html
├── robots.txt, sitemap.xml
├── tokens.md                  Tokens de disseny (colors, fonts, escala tipogràfica)
├── assets/
│   ├── fonts/                 Tipografies en local
│   ├── img/                   Logotips i imatges
│   ├── video/                 Vídeo de la demo i pòster
│   └── js/
│       ├── script.js          Animacions d'entrada, una funció per secció
│       ├── js-flag.js         Marca <html> amb la classe "js"
│       ├── components/        <site-header> i <site-footer>
│       └── modules/           Menú, acordió, carrusel, ripple i títols
└── openspec/                  Decisions del projecte (sempre en català)
```

## Convencions

- **Mobile first**: la base és mòbil i es puja amb `md:` i `lg:`.
- **Tokens**: els colors, tipografies i breakpoints viuen al bloc `@theme` de cada pàgina. Ara mateix està copiat a les 5 pàgines: un canvi s'ha de fer a totes.
- **Header i footer**: viuen en un sol lloc (`assets/js/components/`) i es pinten amb JavaScript.
- **Animacions**: `assets/js/script.js`, una funció `init…Animation` per secció, amb separadors per comentari.
- **Decisions**: cada decisió es documenta a `openspec/decisions/` i porta el seu commit.
- **Comentaris**: en català o castellà, mai en anglès.

## Material de client

La carpeta `docs/` (disseny, identitat visual i manual de marca) conté material del client i **no es versiona ni es publica**. `tokens.md` i algunes decisions hi fan referència: només existeix en local.
