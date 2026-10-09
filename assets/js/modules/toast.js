const MAX_TOASTS = 3;
const DURATIONS = { success: 6000, error: 8000 };
const MIN_RESUME = 1500;
const CLOSE_LABEL = "Tanca l'avís";
const SVG_NS = "http://www.w3.org/2000/svg";

const icons = {
  success: ["M5 13l4 4L19 7"],
  error: ["M12 9v4", "M12 17h.01", "M10.3 3.9L2.4 18a2 2 0 001.7 3h15.8a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"],
  close: ["M6 6l12 12", "M18 6L6 18"],
};

const toastClasses = {
  success: { border: "border-success", icon: "text-success" },
  error: { border: "border-error", icon: "text-error" },
};

const active = [];
let container = null;
let regions = null;

const reducedMotion = () => window.matchMedia("(prefers-reduced-motion: reduce)").matches;

const createIcon = (name, className) => {
  const svg = document.createElementNS(SVG_NS, "svg");
  svg.setAttribute("viewBox", "0 0 24 24");
  svg.setAttribute("fill", "none");
  svg.setAttribute("stroke", "currentColor");
  svg.setAttribute("stroke-width", "2");
  svg.setAttribute("stroke-linecap", "round");
  svg.setAttribute("stroke-linejoin", "round");
  svg.setAttribute("aria-hidden", "true");
  svg.setAttribute("class", className);
  icons[name].forEach((d) => {
    const path = document.createElementNS(SVG_NS, "path");
    path.setAttribute("d", d);
    svg.append(path);
  });
  return svg;
};

const createRegion = (role, className) => {
  const region = document.createElement("div");
  region.setAttribute("role", role);
  region.className = `flex flex-col gap-3 ${className}`;
  return region;
};

const ensureRegions = () => {
  if (container?.isConnected) return false;

  container = document.createElement("div");
  container.className =
    "pointer-events-none fixed right-4 bottom-4 z-[90] flex w-[calc(100%-2rem)] max-w-sm flex-col md:right-6 md:bottom-6";
  regions = { success: createRegion("status", "[&:has(+div:not(:empty))]:mb-3"), error: createRegion("alert", "") };
  container.append(regions.success, regions.error);
  document.body.append(container);
  return true;
};

const createTimer = (duration, onDone) => {
  let remaining = duration;
  let startedAt = 0;
  let id = null;

  return {
    start() {
      if (id !== null) return;
      startedAt = performance.now();
      id = setTimeout(onDone, remaining);
    },
    stop() {
      if (id === null) return;
      clearTimeout(id);
      id = null;
      remaining = Math.max(remaining - (performance.now() - startedAt), MIN_RESUME);
    },
  };
};

const dismiss = (entry) => {
  if (entry.closing) return;
  entry.closing = true;
  entry.timer.stop();
  active.splice(active.indexOf(entry), 1);

  if (entry.toast.contains(document.activeElement) && entry.returnFocus?.isConnected) entry.returnFocus.focus();

  const remove = () => entry.toast.remove();
  if (reducedMotion() || !window.gsap) {
    remove();
    return;
  }
  gsap.to(entry.toast, { x: 48, autoAlpha: 0, duration: 0.3, ease: "power2.in", onComplete: remove });
};

const buildToast = (type, message) => {
  const styles = toastClasses[type];

  const toast = document.createElement("div");
  toast.setAttribute("aria-atomic", "true");
  toast.className = `pointer-events-auto relative flex items-start gap-3 rounded-xl border bg-white p-4 pr-12 text-base text-ink shadow-lg shadow-purple-700/10 ${styles.border}`;

  const text = document.createElement("p");
  text.className = "min-w-0 break-words";
  text.textContent = message;

  const close = document.createElement("button");
  close.type = "button";
  close.setAttribute("aria-label", CLOSE_LABEL);
  close.className =
    "absolute top-2 right-2 inline-flex size-8 items-center justify-center rounded-full text-ink transition-colors hover:text-purple-700 motion-reduce:transition-none focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30";
  close.append(createIcon("close", "size-4"));

  toast.append(createIcon(type, `mt-0.5 size-5 shrink-0 ${styles.icon}`), text, close);
  return { toast, close };
};

const insertToast = (type, message) => {
  const { toast, close } = buildToast(type, message);
  const entry = { toast, closing: false, returnFocus: null, timer: null };
  entry.timer = createTimer(DURATIONS[type], () => dismiss(entry));

  let hovered = false;
  let focused = false;
  const sync = () => (hovered || focused ? entry.timer.stop() : entry.timer.start());

  toast.addEventListener("mouseenter", () => {
    hovered = true;
    sync();
  });
  toast.addEventListener("mouseleave", () => {
    hovered = false;
    sync();
  });
  toast.addEventListener("focusin", (event) => {
    if (!toast.contains(event.relatedTarget) && event.relatedTarget) entry.returnFocus = event.relatedTarget;
    focused = true;
    sync();
  });
  toast.addEventListener("focusout", (event) => {
    focused = toast.contains(event.relatedTarget);
    sync();
  });
  toast.addEventListener("keydown", (event) => event.key === "Escape" && dismiss(entry));
  close.addEventListener("click", () => dismiss(entry));

  while (active.length >= MAX_TOASTS) dismiss(active[0]);
  active.push(entry);
  regions[type].append(toast);

  if (!reducedMotion() && window.gsap) {
    gsap.from(toast, { x: 48, autoAlpha: 0, duration: 0.4, ease: "power3.out", clearProps: "transform,opacity,visibility" });
  }
  sync();
};

export const initToasts = () => {
  ensureRegions();
};

export const showToast = ({ type = "success", message }) => {
  if (!message) return;

  const kind = type === "error" ? "error" : "success";
  if (ensureRegions()) {
    setTimeout(() => insertToast(kind, message), 150);
    return;
  }
  insertToast(kind, message);
};
