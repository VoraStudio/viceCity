// Accordion of the resource cards. Keeps aria-expanded in sync with what the user actually sees:
// - below lg: click/tap toggles the card (CSS reads aria-expanded through group-has-aria-expanded)
// - from lg: the card opens on hover or keyboard focus (pure CSS), so aria-expanded follows hover/focus-within
// Collapsed panels below lg are made inert so their links cannot be focused or read while hidden.
const selectAll = (selector) => [...document.querySelectorAll(selector)];

const desktop = window.matchMedia('(min-width: 64rem)');

const initAccordion = () => {
  const items = selectAll('[data-accordion-toggle]')
    .map((toggle) => ({
      toggle,
      panel: document.getElementById(toggle.getAttribute('aria-controls')),
      card: toggle.closest('article'),
    }))
    .filter((item) => item.panel && item.card);
  if (!items.length) return;

  const setOpen = (item, open) => {
    item.toggle.setAttribute('aria-expanded', String(open));
    item.panel.inert = !open && !desktop.matches;
  };

  const isActive = (item) => item.card.matches(':hover') || item.card.matches(':focus-within');

  const syncAll = () => items.forEach((item) => setOpen(item, desktop.matches && isActive(item)));

  items.forEach((item) => {
    item.toggle.addEventListener('click', () => {
      if (desktop.matches) return;
      setOpen(item, item.toggle.getAttribute('aria-expanded') !== 'true');
    });

    item.card.addEventListener('pointerenter', () => desktop.matches && setOpen(item, true));
    item.card.addEventListener('pointerleave', () => desktop.matches && setOpen(item, item.card.matches(':focus-within')));
    item.card.addEventListener('focusin', () => desktop.matches && setOpen(item, true));
    item.card.addEventListener('focusout', (event) => {
      if (desktop.matches && !item.card.contains(event.relatedTarget)) setOpen(item, item.card.matches(':hover'));
    });
  });

  desktop.addEventListener('change', syncAll);
  syncAll();
};

initAccordion();
