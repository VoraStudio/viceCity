const initTitleReveal = () => {
  const titles = [...document.querySelectorAll("[data-title-reveal]")];
  if (!titles.length) return;
  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

  gsap.registerPlugin(ScrollTrigger);

  titles.forEach((title) => {
    gsap.set(title.parentElement, { perspective: 1200 });
    gsap.from(title, {
      force3D: true,
      rotateX: -90,
      skewX: 45,
      opacity: 0,
      duration: 1,
      ease: "linear",
      scrollTrigger: {
        trigger: title,
        start: "top 85%",
        toggleActions: "play reset play reset",
      },
    });
  });

  window.addEventListener("load", () => ScrollTrigger.refresh());
};

initTitleReveal();
