import { home, logoHref } from "./home-href.js";

class SiteHeader extends HTMLElement {
  connectedCallback() {
    if (this.firstElementChild) return;

    this.innerHTML = `
    <header id="inici" class="bg-lav-100/60 pt-4 md:pt-6">
      <div class="mx-auto w-full max-w-none px-4 md:px-8">
        <nav
          class="relative flex items-center justify-between gap-2 rounded-full bg-white px-4 py-3 shadow-lg shadow-purple-700/10 md:px-6 xl:px-16"
          aria-label="Navegació principal"
        >
          <a
            href="${logoHref}"
            class="shrink-0 rounded-full focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
          >
            <img
              src="assets/img/logo/imagotip/svg/sense-area-seguretat/1-imagotip-fons-blanc.svg"
              alt="Vicity"
              width="129"
              height="40"
              class="h-8 w-auto md:h-10 mr-4"
            />
          </a>

          <ul class="hidden items-center xl:gap-6 gap-2 font-head text-base font-medium menu:flex">
            <li>
              <a
                href="${home}#solucions"
                class="relative whitespace-nowrap rounded-full px-2 py-2 transition-colors hover:text-purple-700 after:absolute after:bottom-0.5 after:left-1/2 after:h-1 after:w-4 after:-translate-x-1/2 after:rounded-full after:bg-purple-700 after:opacity-0 hover:after:opacity-100 motion-reduce:transition-none focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                >Solucions</a
              >
            </li>
            <li>
              <a
                href="${home}#plataforma"
                class="relative whitespace-nowrap rounded-full px-2 py-2 transition-colors hover:text-purple-700 after:absolute after:bottom-0.5 after:left-1/2 after:h-1 after:w-4 after:-translate-x-1/2 after:rounded-full after:bg-purple-700 after:opacity-0 hover:after:opacity-100 motion-reduce:transition-none focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                >Plataforma</a
              >
            </li>
            <li>
              <a
                href="${home}#administracions"
                class="relative whitespace-nowrap rounded-full px-2 py-2 transition-colors hover:text-purple-700 after:absolute after:bottom-0.5 after:left-1/2 after:h-1 after:w-4 after:-translate-x-1/2 after:rounded-full after:bg-purple-700 after:opacity-0 hover:after:opacity-100 motion-reduce:transition-none focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                >Administracions</a
              >
            </li>
            <li>
              <a
                href="${home}#recursos"
                class="relative whitespace-nowrap rounded-full px-2 py-2 transition-colors hover:text-purple-700 after:absolute after:bottom-0.5 after:left-1/2 after:h-1 after:w-4 after:-translate-x-1/2 after:rounded-full after:bg-purple-700 after:opacity-0 hover:after:opacity-100 motion-reduce:transition-none focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                >Recursos</a
              >
            </li>
            <li>
              <a
                href="blog.php"
                class="relative whitespace-nowrap rounded-full px-2 py-2 transition-colors hover:text-purple-700 after:absolute after:bottom-0.5 after:left-1/2 after:h-1 after:w-4 after:-translate-x-1/2 after:rounded-full after:bg-purple-700 after:opacity-0 hover:after:opacity-100 motion-reduce:transition-none focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                >Blog</a
              >
            </li>
            <li>
              <a
                href="${home}#contacte"
                class="relative whitespace-nowrap rounded-full px-2 py-2 transition-colors hover:text-purple-700 after:absolute after:bottom-0.5 after:left-1/2 after:h-1 after:w-4 after:-translate-x-1/2 after:rounded-full after:bg-purple-700 after:opacity-0 hover:after:opacity-100 motion-reduce:transition-none focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                >Contacte</a
              >
            </li>
          </ul>

          <div class="flex items-center gap-2 md:gap-3 menu:gap-6">
            <a
              href="${home}#contacte"
              data-ripple
              data-ripple-text-color="var(--color-white)"
              class="relative hidden h-11 overflow-hidden whitespace-nowrap items-center justify-center rounded-full px-6 font-body menu:inline-flex focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30 bg-purple-100 text-purple-700 outline-2 outline-purple-700 font-bold -outline-offset-1"
            >
              <span data-ripple-fill aria-hidden="true" class="pointer-events-none absolute top-0 left-0 size-2.5 rounded-full bg-purple-700"></span>
              <span data-ripple-text class="relative z-10">Parla amb un especialista</span>
            </a>
            <a
              href="${home}#demo"
              data-ripple
              data-ripple-text-color="var(--color-purple-700)"
              class="relative inline-flex h-11 overflow-hidden whitespace-nowrap items-center justify-center rounded-full px-6 font-body focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30 bg-purple-700 text-white outline-2 -outline-offset-1 outline-purple-700 font-bold"
            >
              <span data-ripple-fill aria-hidden="true" class="pointer-events-none absolute top-0 left-0 size-2.5 rounded-full bg-purple-100"></span>
              <span data-ripple-text class="relative z-10">
                <span class="sr-only menu:not-sr-only font-bold">Sol·licita una demo</span>
                <span aria-hidden="true" class="menu:hidden">Demo</span>
              </span>
            </a>
            <button
              type="button"
              class="inline-flex size-11 items-center justify-center rounded-full border-2 border-purple-700 text-purple-700 cursor-pointer xl:cursor-none menu:hidden focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
              aria-label="Obre el menú"
              aria-expanded="false"
              aria-controls="menu-mobil"
              data-nav-toggle
            >
              <svg
                data-icon-open
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                width="24"
                height="24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="size-6"
                aria-hidden="true"
              >
                <path d="M4 6h16M4 12h16M4 18h16" />
              </svg>
              <svg
                data-icon-close
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                width="24"
                height="24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="hidden size-6"
                aria-hidden="true"
              >
                <path d="M6 6l12 12M18 6L6 18" />
              </svg>
            </button>
          </div>

          <div
            id="menu-mobil"
            class="absolute inset-x-0 top-full z-40 mt-2 hidden rounded-3xl border-2 border-purple-700 bg-white p-4 shadow-lg shadow-purple-700/10 menu:hidden"
            data-nav-menu
          >
            <ul class="flex flex-col font-head text-lg font-medium">
              <li>
                <a
                  href="${home}#solucions"
                  class="relative block w-fit rounded-2xl px-4 py-3 transition-colors hover:text-purple-700 after:absolute after:bottom-0.5 after:left-1/2 after:h-1 after:w-4 after:-translate-x-1/2 after:rounded-full after:bg-purple-700 after:opacity-0 hover:after:opacity-100 motion-reduce:transition-none focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                  >Solucions</a
                >
              </li>
              <li>
                <a
                  href="${home}#plataforma"
                  class="relative block w-fit rounded-2xl px-4 py-3 transition-colors hover:text-purple-700 after:absolute after:bottom-0.5 after:left-1/2 after:h-1 after:w-4 after:-translate-x-1/2 after:rounded-full after:bg-purple-700 after:opacity-0 hover:after:opacity-100 motion-reduce:transition-none focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                  >Plataforma</a
                >
              </li>
              <li>
                <a
                  href="${home}#administracions"
                  class="relative block w-fit rounded-2xl px-4 py-3 transition-colors hover:text-purple-700 after:absolute after:bottom-0.5 after:left-1/2 after:h-1 after:w-4 after:-translate-x-1/2 after:rounded-full after:bg-purple-700 after:opacity-0 hover:after:opacity-100 motion-reduce:transition-none focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                  >Administracions</a
                >
              </li>
              <li>
                <a
                  href="${home}#recursos"
                  class="relative block w-fit rounded-2xl px-4 py-3 transition-colors hover:text-purple-700 after:absolute after:bottom-0.5 after:left-1/2 after:h-1 after:w-4 after:-translate-x-1/2 after:rounded-full after:bg-purple-700 after:opacity-0 hover:after:opacity-100 motion-reduce:transition-none focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                  >Recursos</a
                >
              </li>
              <li>
                <a
                  href="blog.php"
                  class="relative block w-fit rounded-2xl px-4 py-3 transition-colors hover:text-purple-700 after:absolute after:bottom-0.5 after:left-1/2 after:h-1 after:w-4 after:-translate-x-1/2 after:rounded-full after:bg-purple-700 after:opacity-0 hover:after:opacity-100 motion-reduce:transition-none focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                  >Blog</a
                >
              </li>
              <li>
                <a
                  href="${home}#contacte"
                  class="relative block w-fit rounded-2xl px-4 py-3 transition-colors hover:text-purple-700 after:absolute after:bottom-0.5 after:left-1/2 after:h-1 after:w-4 after:-translate-x-1/2 after:rounded-full after:bg-purple-700 after:opacity-0 hover:after:opacity-100 motion-reduce:transition-none focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                  >Contacte</a
                >
              </li>
            </ul>
            <a
              href="${home}#contacte"
              data-ripple
              data-ripple-text-color="var(--color-white)"
              class="relative mt-2 flex h-11 overflow-hidden whitespace-nowrap items-center justify-center rounded-full px-6 font-body focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30 bg-purple-100 text-purple-700 outline-2 outline-purple-700 font-bold -outline-offset-1"
            >
              <span data-ripple-fill aria-hidden="true" class="pointer-events-none absolute top-0 left-0 size-2.5 rounded-full bg-purple-700"></span>
              <span data-ripple-text class="relative z-10">Parla amb un especialista</span>
            </a>
          </div>
        </nav>
      </div>
    </header>
    `;
  }
}

customElements.define("site-header", SiteHeader);
