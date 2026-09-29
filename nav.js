// Mobile menu: the hamburger button opens/closes the slide-in nav panel.
// Same behaviour as the live site's main.js.
document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('.mobile-menu-toggle');
  const nav = document.querySelector('.nav-links');
  if (!toggle || !nav) return;

  toggle.addEventListener('click', e => {
    e.stopPropagation();
    const open = nav.classList.toggle('active');
    toggle.setAttribute('aria-expanded', open);
  });

  // Close when tapping outside the panel
  document.addEventListener('click', e => {
    if (nav.classList.contains('active') && !nav.contains(e.target) && !toggle.contains(e.target)) {
      nav.classList.remove('active');
      toggle.setAttribute('aria-expanded', 'false');
    }
  });
});
