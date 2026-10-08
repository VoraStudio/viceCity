const selectAll = (selector) => [...document.querySelectorAll(selector)];

const desktop = window.matchMedia("(min-width: 64rem)");

const initAccordion = () => {
  const items = selectAll("[data-accordion-toggle]")
    .map((toggle) => ({
      toggle,
      panel: document.getElementById(toggle.getAttribute("aria-controls")),
      card: toggle.closest("article"),
    }))
    .filter((item) => item.panel && item.card);
  if (!items.length) return;

  const setOpen = (item, open) => {
    item.toggle.setAttribute("aria-expanded", String(open));
    item.panel.inert = !open && !desktop.matches;
  };

  const isActive = (item) => item.card.matches(":hover") || item.card.matches(":focus-within");

  const syncAll = () =>
    items.forEach((item, index) => {
      const othersActive = items.some((other) => other !== item && isActive(other));
      const open = desktop.matches ? (index === 0 ? !othersActive : isActive(item)) : index === 0;
      setOpen(item, open);
    });

  items.forEach((item) => {
    item.toggle.addEventListener("click", () => {
      if (desktop.matches) return;
      setOpen(item, item.toggle.getAttribute("aria-expanded") !== "true");
    });

    ["pointerenter", "pointerleave", "focusin", "focusout"].forEach((type) => item.card.addEventListener(type, () => desktop.matches && syncAll()));
  });

  desktop.addEventListener("change", syncAll);
  syncAll();
};

initAccordion();
