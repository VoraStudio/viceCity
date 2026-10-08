# 004 — Header i footer com a Web Components

**Stack**: arquitectura (html + js)
**Estat**: acceptat
**Data**: 2026-10-08

## Context

El header (~177 línies) i el footer (~173) estaven copiats a mà a les 5 pàgines (`index`, `avis-legal`, `privacitat`, `cookies`, `blog`). Ja s'havien desincronitzat: les pàgines internes no tenien el crèdit de Vora Studio ni la composició mòbil del footer, i no carregaven `script.js`, de manera que no s'animaven.

## Decisió

- **Dos custom elements** vanilla, `<site-header>` i `<site-footer>`, a `assets/js/components/` (`site-header.js`, `site-footer.js`, `site-components.js` com a punt d'entrada i `home-href.js` com a helper).
- **Light DOM, sense Shadow DOM**: els estils són classes de Tailwind d'un full global, que el Shadow DOM no veuria. El markup es pinta amb `innerHTML` a `connectedCallback`, amb guarda contra doble render.
- **Enllaços d'àncora resolts en pintar**: a la pàgina d'inici són `#solucions`, `#contacte`... i a les altres `index.html#solucions`, mitjançant `isHome`, `home` i `logoHref` de `home-href.js`.
- **El mòdul de components es carrega el primer** a cada pàgina, abans de `nav.js`, `ripple.js`, etc.: aquests scripts consulten el DOM del header i del footer en arrencar. Definir l'element millora les etiquetes ja parsejades de manera síncrona.
- **Les 4 pàgines internes carreguen també SplitText i `script.js`**, així el seu header i el seu footer s'animen igual que a l'inici. `script.js` no fa res quan falten els elements d'una secció.
- **`display: block`** per a `site-header` i `site-footer` al CSS de cada pàgina, per limitar el salt de maquetació.
- Les plantilles són idèntiques, llevat dels enllaços, als blocs que substitueixen (comprovat línia a línia contra git).

## Conseqüències

- Header i footer viuen en un únic lloc: un canvi es propaga a les 5 pàgines.
- **Sense JS desapareixen** el header i el footer, amb la navegació.
- Abans de pintar-se, les etiquetes no tenen alçada: pot haver-hi un petit salt en carregar.
- No provat en un navegador, només comprovat el codi.
