// Carrusel vertical cíclic de la secció 2: la roda del ratolí passa de targeta quan el cursor és sobre la pila
const section = document.querySelector("[data-stack-carousel]");
const stage = section.querySelector("[data-stack-stage]");
const cards = gsap.utils.toArray("[data-stack-list] > li", section);

// Col·loca cada targeta segons la distància a la targeta activa
const render = (activa) => {
  cards.forEach((card, i) => {
    // wrap fa que la targeta que surt per un extrem torni a entrar per l'altre
    const distancia = gsap.utils.wrap(-cards.length / 2, cards.length / 2, i - activa);
    const lejos = Math.abs(distancia);

    gsap.set(card, {
      yPercent: -50,
      y: distancia * 116,
      scale: 1 - lejos * 0.1,
      // L'opacitat arriba a 0 a l'extrem, així el salt del bucle no es veu
      opacity: 1 - (lejos / (cards.length / 2)) ** 2,
      zIndex: Math.round(100 - lejos * 10),
    });
  });
};

// Només a escriptori i sense reduced-motion
gsap.matchMedia().add("(min-width: 64rem) and (prefers-reduced-motion: no-preference)", () => {
  section.dataset.stack = "on";

  const estado = { activa: 0 };
  let meta = 0; // targeta de destí (pot créixer sense límit)
  let bloqueadoHasta = 0;

  render(estado.activa);

  const ir = (paso) => {
    // El bloqueig dura més que l'animació: el trackpad continua enviant events per inèrcia
    if (performance.now() < bloqueadoHasta) return;
    bloqueadoHasta = performance.now() + 900;
    meta += paso;

    gsap.to(estado, {
      activa: meta,
      duration: 0.6,
      ease: "power2.inOut",
      onUpdate: () => render(estado.activa),
    });
  };

  const onWheel = (event) => {
    event.preventDefault();
    if (Math.abs(event.deltaY) < 4) return;
    ir(Math.sign(event.deltaY));
  };

  stage.addEventListener("wheel", onWheel, { passive: false });

  // En sortir d'escriptori es desfà tot
  return () => {
    stage.removeEventListener("wheel", onWheel);
    delete section.dataset.stack;
    gsap.set(cards, { clearProps: "all" });
  };
});
