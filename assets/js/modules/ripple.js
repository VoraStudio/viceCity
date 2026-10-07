const initRipple = () => {
  const buttons = [...document.querySelectorAll('[data-ripple]')];
  if (!buttons.length) return;
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  buttons.forEach((button) => {
    const fill = button.querySelector('[data-ripple-fill]');
    const text = button.querySelector('[data-ripple-text]');
    const hoverColor = button.dataset.rippleTextColor;
    const baseColor = getComputedStyle(button).color;

    const getCursorOffset = (event) => {
      const rect = button.getBoundingClientRect();
      return { x: event.clientX - rect.left, y: event.clientY - rect.top };
    };

    gsap.set(fill, { scale: 0 });

    button.addEventListener('mouseenter', (event) => {
      const { x, y } = getCursorOffset(event);
      gsap.fromTo(fill, { x, y, scale: 0 }, { scale: 60, duration: 1.5, ease: 'power2.out', overwrite: 'auto' });
      gsap.to(text, { color: hoverColor, duration: 0.25, delay: 0.15, overwrite: true });
    });

    button.addEventListener('mouseleave', (event) => {
      const { x, y } = getCursorOffset(event);
      gsap.to(fill, { x, y, scale: 0, duration: 0.70, ease: 'power3.out', overwrite: 'auto' });
      gsap.to(text, { color: baseColor, duration: 0.25, delay: 0.1, overwrite: true });
    });
  });
};

initRipple();
