const reveal = {
  force3D: true,
  rotateX: -90,
  skewX: 45,
  opacity: 0,
  duration: 0.8,
  ease: "linear",
};

const trigger = (title) => ({
  trigger: title,
  start: "top 85%",
  toggleActions: "play reset play reset",
});

const initTitleReveal = () => {
  const titles = [...document.querySelectorAll("[data-title-reveal]")];
  if (!titles.length) return;
  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

  gsap.registerPlugin(ScrollTrigger, SplitText);

  document.fonts.ready.then(() => {
    titles.forEach((title) => {
      if (title.querySelector("svg")) {
        gsap.set(title.parentElement, { perspective: 1200 });
        gsap.from(title, { ...reveal, scrollTrigger: trigger(title) });
        return;
      }

      gsap.set(title, { perspective: 1200 });
      SplitText.create(title, {
        type: "lines",
        autoSplit: true,
        onSplit: (split) =>
          gsap.from(split.lines, {
            ...reveal,
            stagger: 0.2,
            scrollTrigger: trigger(title),
          }),
      });
    });

    gsap.set(titles, { visibility: "visible" });
    ScrollTrigger.refresh();
  });
};

initTitleReveal();
