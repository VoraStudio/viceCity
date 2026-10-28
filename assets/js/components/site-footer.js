import { home, logoHref } from "./home-href.js";

class SiteFooter extends HTMLElement {
  connectedCallback() {
    if (this.firstElementChild) return;

    this.innerHTML = `
    <footer id="peu" class="overflow-hidden bg-lav-100/40 pt-12 md:pt-16">
      <div class="mx-auto w-full max-w-7xl px-4 md:px-8 lg:px-10">
        <div class="flex flex-col gap-6 border-b border-lav-100 pb-8 lg:flex-row lg:items-end lg:justify-between">
          <div data-footer-item="left">
            <a
              href="${logoHref}"
              class="inline-block rounded-full focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
            >
              <img
                src="assets/img/logo/imagotip/svg/sense-area-seguretat/1-imagotip-fons-blanc.svg"
                alt="Vicity"
                width="129"
                height="40"
                class="h-10 w-auto"
                loading="lazy"
                decoding="async"
              />
            </a>
            <p class="mt-3 text-base text-ink/80" data-i18n="common.tagline">Gestiona avui. Prepara't per demà.</p>
          </div>
          <div data-footer-item="right" class="flex flex-col gap-3 sm:flex-row">
            <a
              href="${home}#contacte"
              data-ripple
              data-ripple-text-color="var(--color-white)"
              class="relative inline-flex h-11 items-center justify-center overflow-hidden rounded-full px-6 font-body focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30 bg-purple-100 text-purple-700 outline-2 outline-purple-700 font-bold -outline-offset-1"
            >
              <span data-ripple-fill aria-hidden="true" class="pointer-events-none absolute top-0 left-0 size-2.5 rounded-full bg-purple-700"></span>
              <span data-ripple-text class="relative z-10" data-i18n="common.cta.specialist">Parla amb un especialista</span>
            </a>
            <a
              href="${home}#demo"
              data-ripple
              data-ripple-text-color="var(--color-purple-700)"
              class="relative inline-flex h-11 items-center justify-center overflow-hidden rounded-full px-6 font-body focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30 bg-purple-700 text-white outline-2 -outline-offset-1 outline-purple-700 font-bold"
            >
              <span data-ripple-fill aria-hidden="true" class="pointer-events-none absolute top-0 left-0 size-2.5 rounded-full bg-purple-100"></span>
              <span data-ripple-text class="relative z-10" data-i18n="common.cta.demo">Sol·licita una demo</span>
            </a>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-x-6 gap-y-10 pt-10 pb-2 md:grid-cols-3 md:gap-10 lg:pb-10">
          <nav data-footer-item="up" aria-labelledby="peu-enllacos">
            <h2 id="peu-enllacos" class="font-head text-base font-bold" data-i18n="footer.links">Enllaços</h2>
            <ul class="mt-4 flex flex-col gap-3 text-base text-ink/80">
              <li>
                <a
                  href="${home}#solucions"
                  class="rounded-sm focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                  data-i18n="common.nav.solutions"
                  >Solucions</a
                >
              </li>
              <li>
                <a
                  href="${home}#plataforma"
                  class="rounded-sm focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                  data-i18n="common.nav.platform"
                  >Plataforma</a
                >
              </li>
              <li>
                <a
                  href="${home}#administracions"
                  class="rounded-sm focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                  data-i18n="common.nav.administrations"
                  >Administracions</a
                >
              </li>
              <li>
                <a
                  href="${home}#recursos"
                  class="rounded-sm focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                  data-i18n="common.nav.resources"
                  >Recursos</a
                >
              </li>
              <li>
                <a
                  href="blog.php"
                  class="rounded-sm focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                  data-i18n="common.nav.blog"
                  >Blog</a
                >
              </li>
            </ul>
          </nav>
          <nav data-footer-item="fade" aria-labelledby="peu-legal" class="order-last col-span-2 md:order-none md:col-span-1">
            <h2 id="peu-legal" class="font-head text-base font-bold" data-i18n="footer.legal">Legal</h2>
            <ul class="mt-4 flex flex-row flex-wrap gap-x-6 gap-y-3 text-base text-ink/80 md:flex-col">
              <li>
                <a
                  href="avis-legal.html"
                  class="rounded-sm focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                  data-i18n="common.legal.notice"
                  >Avís legal</a
                >
              </li>
              <li>
                <a
                  href="privacitat.html"
                  class="rounded-sm focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                  data-i18n="common.legal.privacy"
                  >Privacitat</a
                >
              </li>
              <li>
                <a
                  href="cookies.html"
                  class="rounded-sm focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                  data-i18n="common.legal.cookies"
                  >Cookies</a
                >
              </li>
            </ul>
          </nav>
          <div data-footer-item="up">
            <h2 class="font-head text-base font-bold" data-i18n="common.nav.contact">Contacte</h2>
            <dl class="mt-4 flex flex-col gap-3 text-base">
              <div>
                <dt class="text-label font-bold text-ink/70" data-i18n="common.contact.email">Correu electrònic</dt>
                <dd>
                  <a
                    href="mailto:info@vicity.es"
                    class="rounded-sm text-purple-700 focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                    >info@vicity.es</a
                  >
                </dd>
              </div>
              <div>
                <dt class="text-label font-bold text-ink/70" data-i18n="common.contact.phone">Telèfon</dt>
                <dd>
                  <a
                    href="tel:+34972183203"
                    class="block w-fit rounded-sm focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                    >+34 972 18 32 03</a
                  >
                  <a
                    href="tel:+34679279183"
                    class="block w-fit rounded-sm focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                    >+34 679 27 91 83</a
                  >
                </dd>
              </div>
              <div>
                <dt class="text-label font-bold text-ink/70" data-i18n="common.contact.address">Adreça</dt>
                <dd data-i18n="common.contact.addressValue">Carrer Pic de Peguera 11, Edifici Giro emprèn, Despatx A2/18B, 17003 Girona</dd>
              </div>
            </dl>
          </div>
        </div>

        <div data-footer-item="fade" class="relative mt-0 overflow-hidden lg:mt-4">
          <p data-footer-logo
            aria-hidden="true"
            class="font-head text-[38vw] lg:text-[18vw] text-center leading-none font-bold tracking-tight whitespace-nowrap bg-linear-to-b from-lav-100 to-transparent bg-clip-text text-transparent select-none"
          >
            vicity
          </p>
          <div class="absolute inset-x-0 bottom-4 flex flex-wrap items-center justify-center gap-x-4 gap-y-1 px-4 text-center text-sm text-ink/70">
            <p data-i18n="footer.copyright">© 2026 Vicity. Tots els drets reservats.</p>
            <p class="inline-flex items-center gap-2">
              <span data-i18n="footer.developedBy">Desenvolupat per</span>
              <a
                href="https://vorastudio.cat"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex rounded-sm focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
              >
                <img
                  src="assets/img/clients/vora.png"
                  alt="Vora Studio"
                  width="1550"
                  height="504"
                  loading="lazy"
                  decoding="async"
                  class="h-4 w-auto brightness-0 opacity-60"
                />
                <span class="sr-only" data-i18n="common.newTab">(s'obre en una pestanya nova)</span>
              </a>
            </p>
          </div>
        </div>
      </div>
    </footer>
    `;
  }
}

customElements.define("site-footer", SiteFooter);
