document.addEventListener('DOMContentLoaded', () => {
  const heroVideo = document.querySelector('.video-hero-media');
  const toggle = document.querySelector('[data-video-toggle]');
  const videos = document.querySelectorAll('.video-hero-media, .facility-video');
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Videos the visitor paused themselves stay paused while scrolling
  const pausedByUser = new WeakSet();

  function setToggleState(paused) {
    if (!toggle) return;
    toggle.classList.toggle('is-paused', paused);
    toggle.setAttribute('aria-label', paused ? 'Play background video' : 'Pause background video');
  }

  // Respect reduced-motion: show the poster frames instead of autoplaying
  if (reduceMotion) {
    videos.forEach(v => {
      v.removeAttribute('autoplay');
      v.pause();
      pausedByUser.add(v);
    });
    setToggleState(true);
  }

  if (toggle && heroVideo) {
    toggle.addEventListener('click', () => {
      if (heroVideo.paused) {
        pausedByUser.delete(heroVideo);
        heroVideo.play().catch(() => {});
        setToggleState(false);
      } else {
        pausedByUser.add(heroVideo);
        heroVideo.pause();
        setToggleState(true);
      }
    });
  }

  // Only play videos while they are on screen
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        const v = entry.target;
        if (entry.isIntersecting) {
          if (!pausedByUser.has(v)) v.play().catch(() => {});
        } else {
          v.pause();
        }
      });
    }, { threshold: 0.25 });

    videos.forEach(v => observer.observe(v));
  }
});
