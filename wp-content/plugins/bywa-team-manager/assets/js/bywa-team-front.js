document.addEventListener('DOMContentLoaded', function () {
  const descriptions = document.querySelectorAll('[data-bywa-team-description]');

  descriptions.forEach((description) => {
    const inner = description.querySelector('[data-bywa-team-description-inner]');
    const toggle = description.parentElement.querySelector('[data-bywa-team-description-toggle]');

    if (!inner || !toggle) {
      return;
    }

    const updateToggleVisibility = () => {
      if (inner.scrollHeight <= description.clientHeight + 4) {
        toggle.hidden = true;
      } else {
        toggle.hidden = false;
      }
    };

    toggle.addEventListener('click', () => {
      const isExpanded = description.classList.toggle('is-expanded');
      toggle.textContent = isExpanded ? 'Vezi mai puțin' : 'Vezi mai mult';
    });

    updateToggleVisibility();
    window.addEventListener('resize', updateToggleVisibility);
  });

  const sliders = document.querySelectorAll('[data-bywa-team-slider]');

  sliders.forEach((slider) => {
    const slides = Array.from(slider.querySelectorAll('[data-bywa-team-slide]'));
    const dots = Array.from(slider.querySelectorAll('[data-bywa-team-dot]'));
    const prevButton = slider.querySelector('[data-bywa-team-prev]');
    const nextButton = slider.querySelector('[data-bywa-team-next]');
    const autoplayDelay = parseInt(slider.getAttribute('data-autoplay-delay') || '5000', 10);
    let activeIndex = 0;
    let timerId = null;

    if (slides.length < 2) {
      return;
    }

    const goTo = (index) => {
      activeIndex = (index + slides.length) % slides.length;

      slides.forEach((slide, slideIndex) => {
        slide.classList.toggle('is-active', slideIndex === activeIndex);
      });

      dots.forEach((dot, dotIndex) => {
        dot.classList.toggle('is-active', dotIndex === activeIndex);
      });
    };

    const restartAutoplay = () => {
      if (timerId) {
        window.clearInterval(timerId);
      }

      timerId = window.setInterval(() => {
        goTo(activeIndex + 1);
      }, autoplayDelay);
    };

    if (prevButton) {
      prevButton.addEventListener('click', () => {
        goTo(activeIndex - 1);
        restartAutoplay();
      });
    }

    if (nextButton) {
      nextButton.addEventListener('click', () => {
        goTo(activeIndex + 1);
        restartAutoplay();
      });
    }

    dots.forEach((dot, index) => {
      dot.addEventListener('click', () => {
        goTo(index);
        restartAutoplay();
      });
    });

    slider.addEventListener('mouseenter', () => {
      if (timerId) {
        window.clearInterval(timerId);
      }
    });

    slider.addEventListener('mouseleave', restartAutoplay);
    window.addEventListener('focus', restartAutoplay);

    goTo(0);
    restartAutoplay();
  });
});
