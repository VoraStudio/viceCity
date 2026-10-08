const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

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
    gsap.set(subtitle, { perspective: 1000 });

    const split = SplitText.create(subtitle, { type: "words,chars" });
    gsap.set([split.words, split.chars], { display: "inline-block" });

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
      .from(
        split.chars,
        {
          x: 100,
          z: -20,
          rotateX: -20,
          opacity: 0,
          duration: 0.6,
          ease: "power4.out",
          transformOrigin: "50% 0% -50px",
          stagger: { amount: 0.9},
        },
        0.6
      )
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

if (!prefersReducedMotion) {
  initHeaderAnimation();
  initHeroAnimation();
  initSolutionsAnimation();
}
