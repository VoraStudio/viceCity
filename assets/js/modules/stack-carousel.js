// Carrusel vertical cíclic de la secció 2: la roda del ratolí passa de targeta quan el cursor és sobre la pila
const section = document.querySelector("[data-stack-carousel]");
const stage = section.querySelector("[data-stack-stage]");
const cards = gsap.utils.toArray("[data-stack-list] > li", section);

// Separació vertical entre targetes: més petita a mòbil i a tauleta
let separacion = 116;
gsap.matchMedia().add("(max-width: 47.999rem)", () => {
  separacion = 70;
  return () => {
    separacion = 116;
  };
});
gsap.matchMedia().add("(min-width: 48rem) and (max-width: 63.999rem)", () => {
  separacion = 64;
  return () => {
    separacion = 116;
  };
});

// Col·loca cada targeta segons la distància a la targeta activa
const render = (activa) => {
  cards.forEach((card, i) => {
    // wrap fa que la targeta que surt per un extrem torni a entrar per l'altre
    const distancia = gsap.utils.wrap(-cards.length / 2, cards.length / 2, i - activa);
    const lejos = Math.abs(distancia);

    gsap.set(card, {
      yPercent: -50,
      y: distancia * separacion,
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

// Tauleta i mòbil: s'arrossega la pila amb el dit (o el ratolí) i, en deixar anar, s'assenta a la targeta més propera
gsap.matchMedia().add("(max-width: 63.999rem) and (prefers-reduced-motion: no-preference)", () => {
  gsap.registerPlugin(Draggable, InertiaPlugin);
  section.dataset.stack = "on";

  const estado = { activa: 0 };
  let inicio = 0;
  let yInicio = 0;
  let objetivo = 0; // posició a la qual segueix l'activa amb retard, com un scrub
  const PX_POR_TARJETA = 100; // píxels de dit per passar una targeta: més alt, més lent

  render(estado.activa);

  // Draggable necessita un element que arrossegar: aquest és invisible i només en mesurem el desplaçament
  const proxy = document.createElement("div");
  const [arrastre] = Draggable.create(proxy, {
    type: "y",
    trigger: stage,
    // Draggable acumula la y entre arrossegaments: es mesura el desplaçament des del punt on es prem
    onPress: function () {
      gsap.killTweensOf(estado);
      inicio = estado.activa;
      objetivo = estado.activa;
      yInicio = this.y;
    },
    // Arrossegar cap amunt fa pujar la targeta següent: l'activa creix
    // L'activa no salta al dit: l'hi segueix amb retard (efecte scrub), i overwrite evita acumular animacions
    onDrag: function () {
      objetivo = inicio - (this.y - yInicio) / PX_POR_TARJETA;
      gsap.to(estado, {
        activa: objetivo,
        duration: 1.2,
        ease: "sine.out",
        overwrite: true,
        onUpdate: () => render(estado.activa),
      });
    },
    onRelease: function () {
      // Poca inèrcia: només un gest molt ràpid suma mitja targeta, que l'arrodoniment converteix en una
      const impulso = gsap.utils.clamp(-0.5, 0.5, -this.getVelocity("y") / 6000);
      gsap.to(estado, {
        activa: Math.round(objetivo + impulso),
        duration: 1.8,
        ease: "sine.inOut",
        overwrite: true,
        onUpdate: () => render(estado.activa),
      });
    },
  });

  // Fletxes de mòbil: cada clic mou una targeta amb la mateixa animació suau
  const prev = section.querySelector("[data-stack-prev]");
  const next = section.querySelector("[data-stack-next]");
  const controls = section.querySelector("[data-stack-controls]");
  if (controls) controls.hidden = false;

  const mover = (paso) => {
    objetivo = Math.round(objetivo) + paso;
    gsap.to(estado, {
      activa: objetivo,
      duration: 1,
      ease: "sine.inOut",
      overwrite: true,
      onUpdate: () => render(estado.activa),
    });
  };
  const onPrev = () => mover(-1);
  const onNext = () => mover(1);

  prev?.addEventListener("click", onPrev);
  next?.addEventListener("click", onNext);

  // En sortir de tauleta es desfà tot
  return () => {
    if (controls) controls.hidden = true;
    prev?.removeEventListener("click", onPrev);
    next?.removeEventListener("click", onNext);
    arrastre.kill();
    delete section.dataset.stack;
    gsap.set(cards, { clearProps: "all" });
  };
});
