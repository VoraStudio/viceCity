const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

// ==========================================================================
// COMUNES
// ==========================================================================
const charsDepthReveal = {
  x: 100,
  z: -20,
  rotateX: -20,
  opacity: 0,
  duration: 0.6,
  ease: "power4.out",
  transformOrigin: "50% 0% -50px",
  stagger: { amount: 0.9 },
};

const splitIntoChars = (element) => {
  gsap.set(element, { perspective: 1000 });
  const split = SplitText.create(element, { type: "words,chars" });
  gsap.set([split.words, split.chars], { display: "inline-block" });
  return split.chars;
};

// ==========================================================================
// HEADER
// ==========================================================================
const initHeaderAnimation = () => {
  const nav = document.querySelector("#inici nav");
  if (!nav) return;

  const items = [
    ...nav.querySelectorAll(":scope > a"),
    ...nav.querySelectorAll(":scope > ul > li"),
    ...nav.querySelectorAll(":scope > div:not([data-nav-menu]) > *"),
  ];

  const timeline = gsap.timeline({ defaults: { clearProps: "transform,opacity" } });

  timeline
    .from(nav, { y: -40, opacity: 0, duration: 0.8, ease: "power3.out" })
    .from(items, { y: -12, opacity: 0, duration: 0.5, ease: "power2.out", stagger: 0.08 }, "-=0.6");
};

// ==========================================================================
// HERO
// ==========================================================================
const initHeroAnimation = () => {
  const subtitle = document.querySelector("[data-hero-subtitle]");
  const ctas = document.querySelectorAll("[data-hero-ctas] > a");
  const visual = document.querySelector("[data-hero-visual]");
  if (!subtitle || !ctas.length || !visual) return;

  gsap.registerPlugin(ScrollTrigger, SplitText);

  document.fonts.ready.then(() => {
    const chars = splitIntoChars(subtitle);

    const timeline = gsap.timeline({
      scrollTrigger: {
        trigger: subtitle,
        start: "top 85%",
        endTrigger: visual,
        end: "bottom top",
        toggleActions: "play reset play reset",
      },
    });

    timeline
      .from(chars, charsDepthReveal, 0.6)
      .from(ctas, { y: 30, opacity: 0, duration: 0.6, ease: "power2.out", stagger: 0.15, clearProps: "transform,opacity" }, "-=0.9")
      .from(visual, { opacity: 0, duration: 5, ease: "power2.out", clearProps: "opacity" }, "-=1.9");

    ScrollTrigger.refresh();
  });
};

// ==========================================================================
// SOLUCIONS
// ==========================================================================
const initSolutionsAnimation = () => {
  const section = document.querySelector("#solucions");
  const cards = document.querySelectorAll("[data-solutions-card]");
  const strip = document.querySelector("[data-trust-strip]");
  if (!section || !cards.length || !strip) return;

  gsap.registerPlugin(ScrollTrigger);

  const timeline = gsap.timeline({
    scrollTrigger: {
      trigger: section,
      start: "top 70%",
      endTrigger: "#administracions",
      end: "bottom top",
      toggleActions: "play reset play reset",
    },
  });

  timeline
    .from(cards, {
      x: (index) => (index % 2 === 0 ? -80 : 80),
      opacity: 0,
      duration: 0.8,
      ease: "power3.out",
      stagger: 0.2,
      clearProps: "transform,opacity",
    })
    .from(strip, { y: 40, opacity: 0, duration: 0.8, ease: "power2.out", clearProps: "transform,opacity" }, "-=0.9");
};

// ==========================================================================
// PLATAFORMA
// ==========================================================================
const initPlatformAnimation = () => {
  const section = document.querySelector("[data-stack-carousel]");
  if (!section) return;

  const description = section.querySelector("[data-platform-description]");
  const cards = gsap.utils.toArray("[data-stack-list] > li", section);
  if (!description || !cards.length) return;

  gsap.registerPlugin(ScrollTrigger, SplitText);

  document.fonts.ready.then(() => {
    const chars = splitIntoChars(description);

    const timeline = gsap.timeline({
      scrollTrigger: {
        trigger: description,
        start: "top 85%",
        endTrigger: section,
        end: "bottom top",
        toggleActions: "play reset play reset",
      },
    });

    timeline.from(chars, charsDepthReveal).from(
      cards,
      {
        x: (index) => (index % 2 === 0 ? -80 : 80),
        opacity: 0,
        duration: 0.8,
        ease: "power3.out",
        stagger: 0.2,
      },
      "-=0.4"
    );

    ScrollTrigger.refresh();
  });
};

// ==========================================================================
// INTEGRACIÓ
// ==========================================================================
const initIntegrationAnimation = () => {
  const section = document.querySelector("[data-integration]");
  if (!section) return;

  const subtitle = section.querySelector("[data-integration-subtitle]");
  const descriptions = section.querySelectorAll("[data-integration-description]");
  const image = section.querySelector("[data-integration-image]");
  const card = section.querySelector("[data-integration-card]");
  if (!subtitle || !descriptions.length || !image || !card) return;

  gsap.registerPlugin(ScrollTrigger, SplitText);

  document.fonts.ready.then(() => {
    const chars = [...descriptions].flatMap((description) => splitIntoChars(description));

    const timeline = gsap.timeline({
      scrollTrigger: { trigger: section, start: "top 70%", end: "bottom top", toggleActions: "play reset play reset" },
    });

    timeline
      .from(subtitle, { x: 60, opacity: 0, duration: 0.8, ease: "power3.out", clearProps: "transform,opacity" })
      .from(chars, charsDepthReveal, "-=0.8")
      .fromTo(
        image,
        { clipPath: "inset(0% 100% 0% 0%)" },
        { clipPath: "inset(0% 0% 0% 0%)", duration: 1.5, ease: "power2.inOut", clearProps: "clipPath" },
        "-=1.6"
      )
      .from(card, { y: 30, opacity: 0, duration: 0.6, ease: "power2.out", clearProps: "transform,opacity" }, "-=0.4");

    ScrollTrigger.refresh();
  });
};

// ==========================================================================
// IA
// ==========================================================================
const initAiAnimation = () => {
  const section = document.querySelector("[data-ia]");
  if (!section) return;

  const subtitle = section.querySelector("[data-ia-subtitle]");
  const items = section.querySelectorAll("[data-ia-item]");
  const description = section.querySelector("[data-ia-description]");
  const image = section.querySelector("[data-ia-image]");
  if (!subtitle || !items.length || !description || !image) return;

  gsap.registerPlugin(ScrollTrigger, SplitText);

  document.fonts.ready.then(() => {
    const chars = splitIntoChars(description);

    const timeline = gsap.timeline({
      scrollTrigger: { trigger: section, start: "top 70%", end: "bottom top", toggleActions: "play reset play reset" },
    });

    timeline
      .from(subtitle, { y: 40, opacity: 0, duration: 0.8, ease: "power3.out", clearProps: "transform,opacity" })
      .from(items, { x: -60, opacity: 0, duration: 0.8, ease: "power3.out", stagger: 0.2, clearProps: "transform,opacity" }, "-=0.4")
      .from(chars, { ...charsDepthReveal, duration: 0.4, stagger: { amount: 0.5 } }, "-=0.8")
      .fromTo(
        image,
        { clipPath: "inset(0% 0% 0% 100%)" },
        { clipPath: "inset(0% 0% 0% 0%)", duration: 1.5, ease: "power2.inOut", clearProps: "clipPath" },
        "-=1.6"
      );

    ScrollTrigger.refresh();
  });
};

// ==========================================================================
// AZURE
// ==========================================================================
const initAzureAnimation = () => {
  const section = document.querySelector("[data-azure]");
  if (!section) return;

  const description = section.querySelector("[data-azure-description]");
  const items = section.querySelectorAll("[data-azure-item]");
  const cards = document.querySelectorAll("[data-resources-card]");
  const blogStrip = document.querySelector("[data-blog-strip]");
  if (!description || !items.length || !cards.length || !blogStrip) return;

  gsap.registerPlugin(ScrollTrigger, SplitText);

  document.fonts.ready.then(() => {
    const chars = splitIntoChars(description);

    const timeline = gsap.timeline({
      scrollTrigger: {
        trigger: section,
        start: "top 70%",
        endTrigger: "#recursos",
        end: "bottom top",
        toggleActions: "play reset play reset",
      },
    });

    timeline
      .from(chars, charsDepthReveal)
      .from(items, { y: 40, opacity: 0, duration: 0.8, ease: "power3.out", stagger: 0.2, clearProps: "transform,opacity" }, "-=0.4")
      .from(
        cards,
        {
          x: (index) => (index === 0 ? -60 : 0),
          y: (index) => (index === 0 ? 0 : 40),
          opacity: 0,
          duration: 1.1,
          ease: "power3.out",
          stagger: 0.3,
          clearProps: "transform,opacity",
        },
        "-=0.4"
      )
      .from(blogStrip, { y: 40, opacity: 0, duration: 0.8, ease: "power2.out", clearProps: "opacity" }, "-=0.4");

    ScrollTrigger.refresh();
  });
};

// ==========================================================================
// CONTACTE
// ==========================================================================
const initContactAnimation = () => {
  const section = document.querySelector("[data-contact]");
  if (!section) return;

  const info = section.querySelector("[data-contact-info]");
  const form = section.querySelector("[data-contact-form]");
  if (!info || !form) return;

  gsap.registerPlugin(ScrollTrigger);

  const timeline = gsap.timeline({
    scrollTrigger: { trigger: section, start: "top 70%", end: "bottom top", toggleActions: "play reset play reset" },
  });

  timeline
    .from(info, { x: -80, opacity: 0, duration: 1, ease: "power3.out", clearProps: "transform,opacity" })
    .from(form, { x: 80, opacity: 0, duration: 1, ease: "power3.out", clearProps: "transform,opacity" }, "<");
};

// ==========================================================================
// FOOTER
// ==========================================================================
const footerDirections = {
  left: { x: -60, y: 0 },
  right: { x: 60, y: 0 },
  up: { x: 0, y: 40 },
  fade: { x: 0, y: 0 },
};

const initFooterAnimation = () => {
  const footer = document.querySelector("#peu");
  const items = document.querySelectorAll("[data-footer-item]");
  const logo = document.querySelector("[data-footer-logo]");
  if (!footer || !items.length || !logo) return;

  gsap.registerPlugin(ScrollTrigger);

  const timeline = gsap.timeline({
    scrollTrigger: { trigger: footer, start: "top 85%", end: "bottom top", toggleActions: "play reset play reset" },
  });

  timeline
    .from(items, {
      x: (index, item) => footerDirections[item.dataset.footerItem].x,
      y: (index, item) => footerDirections[item.dataset.footerItem].y,
      opacity: 0,
      duration: 0.8,
      ease: "power3.out",
      stagger: 0.15,
      clearProps: "transform,opacity",
    })
    .from(logo, { yPercent: 100,  duration: 3.5, ease: "power3.out", clearProps: "transform,opacity" }, "-=1.6");
};

if (!prefersReducedMotion) {
  initHeaderAnimation();
  initHeroAnimation();
  initSolutionsAnimation();
  initPlatformAnimation();
  initIntegrationAnimation();
  initAiAnimation();
  initAzureAnimation();
  initContactAnimation();
  initFooterAnimation();
}
