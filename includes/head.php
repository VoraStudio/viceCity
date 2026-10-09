<?php

declare(strict_types=1);

/**
 * Capçalera comuna de les pàgines del blog (fins a <site-header>).
 * Espera $page = ['title', 'description', 'path'] definit abans de fer l'include.
 */
$canonicalUrl = 'https://www.vicity.cat/' . $page['path'];
?>
<!doctype html>
<html lang="ca" class="motion-safe:scroll-smooth">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= e($page['title']) ?></title>
    <meta name="description" content="<?= e($page['description']) ?>" />
    <meta name="robots" content="noindex, follow" />
    <link rel="canonical" href="<?= e($canonicalUrl) ?>" />
    <meta name="theme-color" content="#5e35b1" />
    <link rel="icon" type="image/svg+xml" href="assets/img/logo/isotip/svg/sense-area-seguretat/1-isotip-fons-blanc.svg" />

    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Vicity" />
    <meta property="og:locale" content="ca_ES" />
    <meta property="og:title" content="<?= e($page['title']) ?>" />
    <meta property="og:description" content="<?= e($page['description']) ?>" />
    <meta property="og:url" content="<?= e($canonicalUrl) ?>" />
    <meta property="og:image" content="https://www.vicity.cat/assets/img/logo/imagotip/png/sense-area-seguretat/1-imagotip-fons-blanc.png" />
    <meta property="og:image:width" content="2156" />
    <meta property="og:image:height" content="671" />
    <meta property="og:image:alt" content="Logotip de Vicity" />
    <meta name="twitter:card" content="summary" />

    <link rel="preload" href="assets/fonts/Red_Hat_Display/RedHatDisplay-VariableFont_wght.woff2" as="font" type="font/woff2" crossorigin />
    <link rel="preload" href="assets/fonts/Quicksand/Quicksand-VariableFont_wght.woff2" as="font" type="font/woff2" crossorigin />

    <script src="assets/js/js-flag.js"></script>
    <link rel="stylesheet" href="assets/css/reveal.css" />
    <script
      src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4.3.3/dist/index.global.js"
      integrity="sha384-2ql948lIdLcGEE0/qxNiudyTjgauA3RDJERu5xW75kFCvSl5a9odyQYCb6tEjnmB"
      crossorigin="anonymous"
    ></script>
    <script
      defer
      src="https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/gsap.min.js"
      integrity="sha384-XmJ9SoHtVOHoQUcKvFAzVXwdkKo1Ie3bhmSoIAkcdsHGaIrVJIkmozyq0FJeb/Ly"
      crossorigin="anonymous"
    ></script>
    <script
      defer
      src="https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/ScrollTrigger.min.js"
      integrity="sha384-wl5TeDVvOWt30Pbf8aSo2ZrzsOjddu3avOBvHe+p+OhJt9gP6w9YXmDkN5DK2/dF"
      crossorigin="anonymous"
    ></script>
    <script
      defer
      src="https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/SplitText.min.js"
      integrity="sha384-SWJ0lLVRoipvHh59xj0pL7uC7Ih51F+5smaFtrG+2nr+TlDZU5SYJHmxfolbeNTr"
      crossorigin="anonymous"
    ></script>
    <style type="text/tailwindcss">
      /* Fonts locals variables (WOFF2) */
      @font-face {
        font-family: "Red Hat Display";
        src: url("assets/fonts/Red_Hat_Display/RedHatDisplay-VariableFont_wght.woff2") format("woff2");
        font-weight: 300 900;
        font-style: normal;
        font-display: swap;
      }

      @font-face {
        font-family: "Quicksand";
        src: url("assets/fonts/Quicksand/Quicksand-VariableFont_wght.woff2") format("woff2");
        font-weight: 300 700;
        font-style: normal;
        font-display: swap;
      }

      @custom-variant js (&:where(.js *));

      @utility animated-border {
        &::before {
          content: "";
          position: absolute;
          inset: var(--border-inset, -3px);
          z-index: 1;
          padding: 4px;
          border-radius: inherit;
          background: conic-gradient(
            from var(--border-angle),
            var(--color-purple-700),
            var(--color-lav-300),
            var(--color-purple-500),
            var(--color-lav-100),
            var(--color-purple-700)
          );
          mask:
            linear-gradient(#000 0 0) content-box,
            linear-gradient(#000 0 0);
          mask-composite: exclude;
          opacity: 0;
          pointer-events: none;
          transition: opacity 0.3s;
        }

        &:hover::before {
          opacity: 1;
        }
      }

      site-header,
      site-footer {
        display: block;
      }

      @theme {
        --color-lav-100: #d9c7ff;
        --color-lav-300: #b08cff;
        --color-purple-500: #8f63ff;
        --color-purple-100: #efe7ff;
        --color-purple-700: #5e35b1;
        --color-purple-800: #432184;
        --color-ink: #0d252f;
        --color-paper: #f7f8f6;
        --color-gray: #b4b4b4;
        /* Gris de les caixes de placeholder (imatges no disponibles) */
        --color-placeholder: #d9d9d9;

        /* El menú completo del header aparece desde 1360px (por debajo no caben los enlaces en una línea) */
        --breakpoint-menu: 85rem;

        --font-head: "Red Hat Display", sans-serif;
        --font-body: "Quicksand", sans-serif;

        --text-title-1: 3.75rem;
        --text-title-2: 2.5rem;
        --text-body: 1.375rem;
        --text-label: 0.75rem;
        --animate-marquee: marquee 20s linear infinite;

        @keyframes marquee {
  to {
    transform: translateX(-50%);
  }
}
      }
    </style>
  </head>
  <body class="bg-paper font-body text-base text-ink antialiased">
    <a
      href="#contingut"
      class="sr-only focus-visible:not-sr-only focus-visible:absolute focus-visible:top-2 focus-visible:left-2 focus-visible:z-50 focus-visible:rounded-full focus-visible:bg-purple-700 focus-visible:px-4 focus-visible:py-2 focus-visible:text-white focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
      >Vés al contingut</a
    >

    <noscript>
      <link rel="stylesheet" href="assets/css/noscript.css" />
      <div class="noscript">
        <p class="noscript__title">Aquesta web necessita JavaScript per mostrar-se correctament.</p>
        <p>Activa JavaScript al navegador o fes servir aquests enllaços:</p>
        <ul class="noscript__links">
          <li><a href="index.html">Inici</a></li>
          <li><a href="blog.php">Blog</a></li>
          <li><a href="avis-legal.html">Avís legal</a></li>
          <li><a href="privacitat.html">Privacitat</a></li>
          <li><a href="cookies.html">Cookies</a></li>
        </ul>
        <p>Contacte: <a href="mailto:info@vicity.cat">info@vicity.cat</a> · <a href="tel:+34931234567">+34&nbsp;93&nbsp;123&nbsp;45&nbsp;67</a></p>
      </div>
    </noscript>

    <site-header></site-header>
