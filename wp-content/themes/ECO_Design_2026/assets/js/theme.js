document.addEventListener('DOMContentLoaded', function () {
  const header = document.querySelector('.bywa-site-header');

  const handleHeaderScroll = () => {
    if (!header) return;
    if (window.scrollY > 20) {
      header.classList.add('is-scrolled');
    } else {
      header.classList.remove('is-scrolled');
    }
  };

  handleHeaderScroll();
  window.addEventListener('scroll', handleHeaderScroll, { passive: true });

  const revealElements = document.querySelectorAll('.bywa-reveal');

  if ('IntersectionObserver' in window && revealElements.length) {
    const revealObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach((entry, index) => {
        if (entry.isIntersecting) {
          entry.target.style.transitionDelay = `${Math.min(index * 90, 360)}ms`;
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.16,
      rootMargin: '0px 0px -40px 0px'
    });

    revealElements.forEach((el) => revealObserver.observe(el));
  } else {
    revealElements.forEach((el) => el.classList.add('is-visible'));
  }

  const heroSliders = document.querySelectorAll('[data-bywa-hero-slider]');

  heroSliders.forEach((slider) => {
    const slides = slider.querySelectorAll('.bywa-hero-slide');
    if (slides.length <= 1) return;

    let current = 0;

    const activateSlide = (nextIndex) => {
      slides.forEach((slide, index) => {
        const isActive = index === nextIndex;
        slide.classList.toggle('is-active', isActive);
        slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
      });
    };

    setInterval(() => {
      current = (current + 1) % slides.length;
      activateSlide(current);
    }, 5000);
  });
});