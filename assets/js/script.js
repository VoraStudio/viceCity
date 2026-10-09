import { initToasts, showToast } from "./modules/toast.js";

const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

// ==========================================================================
// COMUNES
// ==========================================================================
const linesFadeUp = {
  y: 30,
  opacity: 0,
  duration: 0.8,
  ease: "power3.out",
  stagger: 0.15,
};

const splitIntoLines = (element) => SplitText.create(element, { type: "lines" }).lines;

const reveal = (...targets) => gsap.set(targets, { visibility: "visible" });

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

  reveal(nav);
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
    const lines = splitIntoLines(subtitle);

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
      .from(lines, linesFadeUp, 0.6)
      .from(ctas, { y: 30, opacity: 0, duration: 0.6, ease: "power2.out", stagger: 0.15, clearProps: "transform,opacity" }, "-=0.9")
      .from(visual, { opacity: 0, duration: 5, ease: "power2.out", clearProps: "opacity" }, "-=1.9");

    reveal(subtitle, ctas, visual);
    ScrollTrigger.refresh();
  });
};

const initHeroBorderAnimation = () => {
  const frame = document.querySelector("[data-hero-visual]");
  if (!frame) return;

  gsap.registerPlugin(ScrollTrigger);

  gsap.to(frame, {
    "--border-angle": "360deg",
    duration: 8,
    ease: "none",
    repeat: -1,
    scrollTrigger: { trigger: frame, start: "top bottom", end: "bottom top", toggleActions: "play pause resume pause" },
  });
};

const initHeroVideo = () => {
  const video = document.querySelector("[data-hero-video]");
  const toggle = document.querySelector("[data-hero-video-toggle]");
  if (!video || !toggle) return;

  let pausedByUser = prefersReducedMotion;

  const syncToggle = () => toggle.setAttribute("aria-pressed", String(video.paused));
  const play = () => video.play().catch(syncToggle);
  const playUnlessPausedByUser = () => {
    if (!pausedByUser) play();
  };

  video.addEventListener("play", syncToggle);
  video.addEventListener("pause", syncToggle);
  toggle.addEventListener("click", () => {
    pausedByUser = !video.paused;
    if (pausedByUser) {
      video.pause();
      return;
    }
    play();
  });
  syncToggle();

  if (prefersReducedMotion) return;

  gsap.registerPlugin(ScrollTrigger);

  ScrollTrigger.create({
    trigger: video,
    start: "top bottom",
    end: "bottom top",
    onEnter: playUnlessPausedByUser,
    onEnterBack: playUnlessPausedByUser,
    onLeave: () => video.pause(),
    onLeaveBack: () => video.pause(),
  });
};

// ==========================================================================
// SOLUCIONS
// ==========================================================================
const initSolutionsAnimation = () => {
  const section = document.querySelector("#solucions");
  const cards = document.querySelectorAll("[data-solutions-card]");
  const strip = document.querySelector("[data-trust-strip]");
  const description = document.querySelector("[data-solutions-description]");
  if (!section || !cards.length || !strip || !description) return;

  gsap.registerPlugin(ScrollTrigger, SplitText);

  document.fonts.ready.then(() => {
    const lines = splitIntoLines(description);

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
      .from(strip, { y: 40, opacity: 0, duration: 0.8, ease: "power2.out", clearProps: "transform,opacity" }, "-=0.9")
      .from(lines, linesFadeUp, 0.4);

    reveal(cards, strip, description);
    ScrollTrigger.refresh();
  });
};

const initCardsBorder = () => {
  const cards = document.querySelectorAll("[data-hover-border]");
  if (!cards.length) return;
  if (!window.matchMedia("(hover: hover)").matches) return;

  cards.forEach((card) => {
    const spin = gsap.to(card, { "--border-angle": "360deg", duration: 8, ease: "none", repeat: -1, paused: true });

    card.addEventListener("pointerenter", () => spin.play());
    card.addEventListener("pointerleave", () => spin.pause());
  });
};

// ==========================================================================
// PLATAFORMA
// ==========================================================================
const initPlatformAnimation = () => {
  const section = document.querySelector("[data-stack-carousel]");
  if (!section) return;

  const description = section.querySelector("[data-platform-description]");
  const stack = section.querySelector("[data-stack-list]");
  if (!description || !stack) return;

  gsap.registerPlugin(ScrollTrigger, SplitText);

  document.fonts.ready.then(() => {
    const lines = splitIntoLines(description);

    const timeline = gsap.timeline({
      scrollTrigger: {
        trigger: description,
        start: "top 85%",
        endTrigger: section,
        end: "bottom top",
        toggleActions: "play reset play reset",
      },
    });

    timeline
      .from(lines, linesFadeUp)
      .from(stack, { opacity: 0, duration: 1.4, ease: "power1.inOut", clearProps: "opacity" }, "-=0.4");

    reveal(description, stack);
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
    const lines = [...descriptions].flatMap((description) => splitIntoLines(description));

    const timeline = gsap.timeline({
      scrollTrigger: { trigger: section, start: "top 70%", end: "bottom top", toggleActions: "play reset play reset" },
    });

    timeline
      .from(subtitle, { x: 60, opacity: 0, duration: 0.8, ease: "power3.out", clearProps: "transform,opacity" })
      .from(lines, linesFadeUp, "-=0.8")
      .fromTo(
        image,
        { clipPath: "inset(0% 100% 0% 0%)" },
        { clipPath: "inset(0% 0% 0% 0%)", duration: 1.5, ease: "power2.inOut", clearProps: "clipPath" },
        "-=1.6"
      )
      .from(card, { y: 30, opacity: 0, duration: 0.6, ease: "power2.out", clearProps: "transform,opacity" }, "-=0.4");

    reveal(subtitle, descriptions, image, card);
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
    const lines = splitIntoLines(description);

    const timeline = gsap.timeline({
      scrollTrigger: { trigger: section, start: "top 70%", end: "bottom top", toggleActions: "play reset play reset" },
    });

    timeline
      .from(subtitle, { y: 40, opacity: 0, duration: 0.8, ease: "power3.out", clearProps: "transform,opacity" })
      .from(items, { x: -60, opacity: 0, duration: 0.8, ease: "power3.out", stagger: 0.2, clearProps: "transform,opacity" }, "-=0.4")
      .from(lines, { ...linesFadeUp, duration: 0.5, stagger: 0.1 }, "-=0.8")
      .fromTo(
        image,
        { clipPath: "inset(0% 0% 0% 100%)" },
        { clipPath: "inset(0% 0% 0% 0%)", duration: 1.5, ease: "power2.inOut", clearProps: "clipPath" },
        "-=1.6"
      );

    reveal(subtitle, items, description, image);
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
    const lines = splitIntoLines(description);

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
      .from(lines, linesFadeUp)
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

    reveal(description, items, cards, blogStrip);
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
  const description = section.querySelector("[data-contact-description]");
  if (!info || !form || !description) return;

  gsap.registerPlugin(ScrollTrigger, SplitText);

  document.fonts.ready.then(() => {
    const lines = splitIntoLines(description);

    const timeline = gsap.timeline({
      scrollTrigger: { trigger: section, start: "top 70%", end: "bottom top", toggleActions: "play reset play reset" },
    });

    timeline
      .from(info, { x: -80, opacity: 0, duration: 1, ease: "power3.out", clearProps: "transform,opacity" })
      .from(form, { x: 80, opacity: 0, duration: 1, ease: "power3.out", clearProps: "transform,opacity" }, "<")
      .from(lines, linesFadeUp, 0.3);

    reveal(info, form, description);
    ScrollTrigger.refresh();
  });
};

// ==========================================================================
// BLOG
// ==========================================================================
const initBlogAnimation = () => {
  const hero = document.querySelector("[data-blog-hero]");
  const featured = document.querySelector("[data-blog-featured]");
  const cards = document.querySelectorAll("[data-blog-card]");
  if (!hero) return;

  gsap.registerPlugin(ScrollTrigger);

  const fadeUp = { y: 40, opacity: 0, duration: 0.8, ease: "power3.out", clearProps: "transform,opacity" };
  const onScroll = (trigger) => ({ trigger, start: "top 85%", toggleActions: "play reset play reset" });

  gsap.from(hero, fadeUp);
  // El destacat i les cards poden no existir (categoria filtrada sense entrades o sense destacat)
  if (featured) gsap.from(featured, { ...fadeUp, scrollTrigger: onScroll(featured) });
  if (cards.length) gsap.from(cards, { ...fadeUp, stagger: 0.15, scrollTrigger: onScroll(cards[0].closest("ul")) });

  reveal(...[hero, featured, ...cards].filter(Boolean));
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

  reveal(items, logo);
};

// ==========================================================================
// CURSOR
// ==========================================================================
const cursorInteractive = "a[href], button, summary, label, input, select, textarea, [role='button'], [data-hover-border]";

const initCursor = () => {
  if (!window.matchMedia("(hover: hover) and (pointer: fine)").matches) return;

  const dot = document.createElement("div");
  const ring = document.createElement("div");
  dot.className = "pointer-events-none fixed top-0 left-0 z-[100] size-2 rounded-full bg-ink opacity-0";
  ring.className =
    "pointer-events-none fixed top-0 left-0 z-[100] size-10 rounded-full border-2 border-purple-700 opacity-0 transition-colors duration-300 data-[hover=true]:border-purple-500 motion-reduce:transition-none";
  dot.setAttribute("aria-hidden", "true");
  ring.setAttribute("aria-hidden", "true");
  document.body.append(ring, dot);

  gsap.set([dot, ring], { xPercent: -50, yPercent: -50 });

  const ringTrail = prefersReducedMotion ? 0 : 0.15;
  const ringX = gsap.quickTo(ring, "x", { duration: ringTrail, ease: "power3", overwrite: "auto" });
  const ringY = gsap.quickTo(ring, "y", { duration: ringTrail, ease: "power3", overwrite: "auto" });
  const toggleDuration = prefersReducedMotion ? 0 : 0.3;
  let visible = false;

  const setVisible = (value) => {
    visible = value;
    gsap.to([dot, ring], { opacity: value ? 1 : 0, duration: toggleDuration, overwrite: "auto" });
  };

  const setHover = (value) => {
    ring.dataset.hover = value;
    gsap.to(ring, { scale: value ? 1.5 : 1, duration: toggleDuration });
    gsap.to(dot, { scale: value ? 0 : 1, duration: toggleDuration });
  };

  window.addEventListener("mousemove", (event) => {
    if (!visible) {
      gsap.set(ring, { x: event.clientX, y: event.clientY });
      setVisible(true);
    }
    gsap.set(dot, { x: event.clientX, y: event.clientY });
    ringX(event.clientX);
    ringY(event.clientY);
  });

  document.addEventListener("pointerover", (event) => {
    if (event.target.closest(cursorInteractive)) setHover(true);
  });

  document.addEventListener("pointerout", (event) => {
    const leaving = event.target.closest(cursorInteractive);
    if (leaving && !leaving.contains(event.relatedTarget)) setHover(false);
  });

  document.documentElement.addEventListener("mouseleave", () => setVisible(false));
};

const initLikeButtons = () => {
  const form = document.querySelector("[data-like-form]");
  if (!form) return;

  const button = form.querySelector("[data-like-button]");
  const count = document.querySelector("[data-like-count]");
  if (!button || !count) return;

  form.addEventListener("submit", async (event) => {
    event.preventDefault();
    if (button.disabled) return;
    button.disabled = true;

    try {
      const response = await fetch(form.action, {
        method: "POST",
        body: new FormData(form),
        headers: { Accept: "application/json" },
        credentials: "same-origin",
      });
      if (!response.ok) throw new Error(response.statusText);

      const result = await response.json();
      button.setAttribute("aria-pressed", String(result.liked));
      count.textContent = result.label;
      button.disabled = false;
    } catch {
      form.submit();
    }
  });
};

const contactFieldsMessage = "Revisa els camps marcats.";
const contactConnectionError = "No hem pogut connectar amb el servidor. Comprova la connexió i torna-ho a provar.";

const loadRecaptcha = (siteKey) =>
  new Promise((resolve, reject) => {
    if (window.grecaptcha?.execute) {
      window.grecaptcha.ready(resolve);
      return;
    }

    const script = document.createElement("script");
    script.src = `https://www.google.com/recaptcha/api.js?render=${encodeURIComponent(siteKey)}`;
    script.async = true;
    script.onload = () => window.grecaptcha.ready(resolve);
    script.onerror = reject;
    document.head.append(script);
  });

const initContactForm = () => {
  const form = document.querySelector("[data-contact-form][data-token-url]");
  if (!form) return;

  const button = form.querySelector("button[type='submit']");
  const label = button?.querySelector("[data-ripple-text]");
  const tokenInput = form.querySelector("input[name='csrf_token']");
  if (!button || !label || !tokenInput) return;

  const idleLabel = label.textContent.trim();
  let siteKey = null;
  let tokenRequest = null;

  form.querySelectorAll("[data-error-text]").forEach((text) => {
    text.dataset.default = text.textContent;
  });

  const clearFieldError = (field) => {
    field.removeAttribute("aria-invalid");

    const message = form.querySelector(`#err-${field.name}`);
    if (!message || !message.dataset.serverError) return;

    delete message.dataset.serverError;
    message.classList.add("hidden");
    message.classList.remove("flex");
    const text = message.querySelector("[data-error-text]");
    text.textContent = text.dataset.default;
  };

  const clearFeedback = () => {
    form.querySelectorAll("[aria-invalid]").forEach(clearFieldError);
  };

  const showError = (message) => showToast({ type: "error", message });

  const showFieldErrors = (errors) => {
    const unmapped = [];

    Object.entries(errors).forEach(([name, message]) => {
      const field = form.elements[name];
      const target = form.querySelector(`#err-${name}`);
      field?.setAttribute("aria-invalid", "true");

      if (!target) {
        unmapped.push(message);
        return;
      }

      target.querySelector("[data-error-text]").textContent = message;
      target.dataset.serverError = "true";
      target.classList.remove("hidden");
      target.classList.add("flex");
    });

    return unmapped;
  };

  const loadToken = () => {
    tokenRequest ??= fetch(form.dataset.tokenUrl, { headers: { Accept: "application/json" }, credentials: "same-origin" })
      .then((response) => {
        if (!response.ok) throw new Error(response.statusText);
        return response.json();
      })
      .then(async (data) => {
        siteKey = data.recaptcha ?? null;
        if (siteKey) await loadRecaptcha(siteKey);
        tokenInput.value = data.token;
      })
      .finally(() => {
        tokenRequest = null;
      });

    return tokenRequest;
  };

  const setBusy = (busy) => {
    button.disabled = busy;
    form.setAttribute("aria-busy", String(busy));
    label.textContent = busy ? "Enviant…" : idleLabel;
  };

  const handleFailure = (status, result) => {
    if (status === 403) tokenInput.value = "";

    const unmapped = result?.errors ? showFieldErrors(result.errors) : [];
    showError(result?.errors ? [contactFieldsMessage, ...unmapped].join(" ") : (result?.message ?? contactConnectionError));
    form.querySelector("[aria-invalid='true']")?.focus();
  };

  form.addEventListener("input", (event) => event.target.matches("[aria-invalid]") && clearFieldError(event.target));
  form.addEventListener("change", (event) => event.target.matches("[aria-invalid]") && clearFieldError(event.target));

  form.addEventListener("submit", async (event) => {
    event.preventDefault();
    if (button.disabled) return;
    clearFeedback();
    setBusy(true);

    try {
      if (!tokenInput.value) await loadToken();

      const data = new FormData(form);
      if (siteKey) data.set("recaptcha_response", await window.grecaptcha.execute(siteKey, { action: "contacte" }));

      const response = await fetch(form.action, {
        method: "POST",
        body: data,
        headers: { Accept: "application/json" },
        credentials: "same-origin",
      });
      const result = await response.json().catch(() => null);

      if (response.ok && result?.ok) {
        form.reset();
        tokenInput.value = result.token ?? "";
        showToast({ type: "success", message: result.message });
        return;
      }

      handleFailure(response.status, result);
    } catch {
      showError(contactConnectionError);
    } finally {
      setBusy(false);
    }
  });

  button.disabled = true;
  loadToken()
    .catch(() => showError(contactConnectionError))
    .finally(() => {
      button.disabled = false;
    });
};

if (!prefersReducedMotion) {
  initHeaderAnimation();
  initHeroAnimation();
  initHeroBorderAnimation();
  initSolutionsAnimation();
  initCardsBorder();
  initPlatformAnimation();
  initIntegrationAnimation();
  initAiAnimation();
  initAzureAnimation();
  initContactAnimation();
  initBlogAnimation();
  initFooterAnimation();
}

initHeroVideo();
initCursor();
initToasts();
initLikeButtons();
initContactForm();
