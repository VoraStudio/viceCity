const selectAll = (selector) => [...document.querySelectorAll(selector)];

const initAccordion = () => {
  const toggles = selectAll('[data-accordion-toggle]');
  if (!toggles.length) return;

  toggles.forEach((toggle) => {
    toggle.addEventListener('click', () => {
      const isOpen = toggle.getAttribute('aria-expanded') === 'true';
      toggle.setAttribute('aria-expanded', String(!isOpen));
    });
  });
};

initAccordion();