document.addEventListener('DOMContentLoaded', function () {
  const config = window.bywaContentManager || {};
  const revealRootSelector = '.bywa-reveal';
  let revealObserver = null;
  const viewStoragePrefix = 'bywaRealisationsView:';
  const mobileViewQuery = window.matchMedia('(max-width: 767px)');

  const bindReadmore = (root = document) => {
    root.querySelectorAll('[data-bywa-readmore]').forEach((block) => {
      if (block.dataset.bywaReadmoreBound === '1') {
        return;
      }

      const toggle = block.querySelector('[data-bywa-readmore-toggle]');
      const shortText = block.querySelector('.bywa-realisation-list-card__excerpt-short');
      const fullText = block.querySelector('.bywa-realisation-list-card__excerpt-full');

      if (!toggle || !shortText || !fullText) {
        return;
      }

      block.dataset.bywaReadmoreBound = '1';

      toggle.addEventListener('click', () => {
        const isExpanded = toggle.getAttribute('aria-expanded') === 'true';

        toggle.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
        toggle.innerHTML = isExpanded
          ? 'Citește mai puțin <span class="bi bi-plus-lg" aria-hidden="true"></span>'
          : 'Citește mai mult <span class="bi bi-plus-lg" aria-hidden="true"></span>';

        shortText.hidden = !isExpanded;
        fullText.hidden = isExpanded;
      });
    });
  };

  const observeReveal = (root = document) => {
    const elements = Array.from(root.querySelectorAll(revealRootSelector));

    if (!elements.length) {
      return;
    }

    if (!('IntersectionObserver' in window)) {
      elements.forEach((el) => el.classList.add('is-visible'));
      return;
    }

    if (!revealObserver) {
      revealObserver = new IntersectionObserver((entries, obs) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            obs.unobserve(entry.target);
          }
        });
      }, {
        threshold: 0.16,
        rootMargin: '0px 0px -40px 0px'
      });
    }

    elements.forEach((el, index) => {
      if (el.dataset.bywaRevealBound === '1') {
        return;
      }

      el.dataset.bywaRevealBound = '1';
      el.style.setProperty('--bywa-reveal-delay', `${index * 70}ms`);
      revealObserver.observe(el);
    });
  };

  const getViewStorageKey = (root) => {
    const slug = root.dataset.bywaRealisationsViewKey || window.location.pathname;
    const viewport = mobileViewQuery.matches ? 'mobile' : 'desktop';
    return `${viewStoragePrefix}${slug}:${viewport}`;
  };

  const normalizeRealisationsView = (view) => {
    return ['list', 'grid', 'simple'].includes(view) ? view : 'list';
  };

  const applyRealisationsView = (root, view, persist = true, animate = true) => {
    const normalizedView = normalizeRealisationsView(view);
    const buttons = root.querySelectorAll('[data-bywa-realisations-view-btn]');
    const previousView = root.dataset.bywaRealisationsView || 'list';

    root.dataset.bywaRealisationsView = normalizedView;
    root.classList.toggle('is-view-grid', normalizedView === 'grid');
    root.classList.toggle('is-view-list', normalizedView !== 'grid');

    buttons.forEach((button) => {
      const isActive = button.dataset.bywaRealisationsViewBtn === normalizedView;
      button.classList.toggle('is-active', isActive);
      button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });

    if (persist) {
      try {
        window.localStorage.setItem(getViewStorageKey(root), normalizedView);
      } catch (error) {
        // localStorage indisponible
      }
    }

    if (animate && previousView !== normalizedView) {
      root.classList.add('is-switching');
      window.clearTimeout(root._bywaViewSwitchTimer);
      root._bywaViewSwitchTimer = window.setTimeout(() => {
        root.classList.remove('is-switching');
      }, 220);
    }
  };

  const bindRealisationsView = (root) => {
    const savedView = (() => {
      try {
        return window.localStorage.getItem(getViewStorageKey(root));
      } catch (error) {
        return null;
      }
    })();

    const initialView = normalizeRealisationsView(savedView || root.dataset.bywaRealisationsView || 'list');
    const serverView = root.dataset.bywaRealisationsView || 'list';

    applyRealisationsView(root, initialView, false, false);

    const handleViewChange = (event) => {
      const button = event.target.closest('[data-bywa-realisations-view-btn]');

      if (!button || !root.contains(button)) {
        return;
      }

      event.preventDefault();

      const nextView = normalizeRealisationsView(button.dataset.bywaRealisationsViewBtn || 'list');
      const currentView = normalizeRealisationsView(root.dataset.bywaRealisationsView || 'list');

      if (nextView === currentView) {
        return;
      }

      const currentPage = Number(root.dataset.bywaRealisationsCurrentPage || 1);

      applyRealisationsView(root, nextView);
      loadRealisations(root, currentPage);
    };

    root.addEventListener('click', handleViewChange);

    if ('PointerEvent' in window) {
      root.addEventListener('pointerup', handleViewChange);
    }

    if (initialView !== serverView) {
      loadRealisations(root, Number(root.dataset.bywaRealisationsCurrentPage || 1));
    }
  };

  const mediaModal = document.querySelector('[data-bywa-media-modal]');
  const mediaModalImage = mediaModal ? mediaModal.querySelector('[data-bywa-modal-image-target]') : null;
  let lastTrigger = null;

  if (mediaModal && mediaModalImage) {
    const closeModal = () => {
      mediaModal.hidden = true;
      mediaModal.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('bywa-modal-open');
      mediaModalImage.setAttribute('src', '');
      mediaModalImage.setAttribute('alt', '');

      if (lastTrigger) {
        lastTrigger.focus();
      }
    };

    const openModal = (trigger) => {
      const imageUrl = trigger.dataset.bywaModalImage;

      if (!imageUrl) {
        return;
      }

      mediaModalImage.setAttribute('src', imageUrl);
      mediaModalImage.setAttribute('alt', trigger.dataset.bywaModalAlt || '');
      mediaModal.hidden = false;
      mediaModal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('bywa-modal-open');
      lastTrigger = trigger;

      const closeButton = mediaModal.querySelector('.bywa-media-modal__close');

      if (closeButton) {
        closeButton.focus();
      }
    };

    document.addEventListener('click', (event) => {
      const trigger = event.target.closest('[data-bywa-modal-trigger]');
      const closeButton = event.target.closest('[data-bywa-modal-close]');

      if (trigger) {
        openModal(trigger);
        return;
      }

      if (closeButton) {
        closeModal();
      }
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && !mediaModal.hidden) {
        closeModal();
      }
    });
  }

  const carousels = document.querySelectorAll('[data-bywa-carousel]');
  carousels.forEach((carousel) => {
    const slides = carousel.querySelectorAll('[data-bywa-slide]');
    const dots = carousel.querySelectorAll('[data-bywa-dot]');
    const interval = Number(carousel.dataset.interval || 4200);

    if (slides.length <= 1) {
      return;
    }

    let current = 0;
    let timerId = null;

    const setActive = (index) => {
      slides.forEach((slide, slideIndex) => {
        const isActive = slideIndex === index;
        slide.classList.toggle('is-active', isActive);
        slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
      });

      dots.forEach((dot, dotIndex) => {
        dot.classList.toggle('is-active', dotIndex === index);
      });
    };

    const start = () => {
      if (timerId) {
        return;
      }

      timerId = window.setInterval(() => {
        current = (current + 1) % slides.length;
        setActive(current);
      }, interval);
    };

    const stop = () => {
      if (!timerId) {
        return;
      }

      window.clearInterval(timerId);
      timerId = null;
    };

    setActive(current);
    start();

    carousel.addEventListener('mouseenter', stop);
    carousel.addEventListener('mouseleave', start);
  });

  const loadRealisations = async (root, page = 1) => {
    const list = root.querySelector('[data-bywa-realisations-list]');
    const pagination = root.querySelector('[data-bywa-realisations-pagination]');
    const perPageSelect = root.querySelector('[data-bywa-realisations-per-page]');
    const typeSelect = root.querySelector('[data-bywa-realisations-type]');
    const ajaxUrl = config.ajaxUrl || root.dataset.bywaRealisationsAjaxUrl || window.ajaxurl;
    const currentConfig = root.dataset.bywaRealisationsConfig || '{}';
    const currentView = root.dataset.bywaRealisationsView || 'list';

    if (!list || !pagination || !ajaxUrl) {
      return;
    }

    const formData = new URLSearchParams({
      action: 'bywa_realisations_filter',
      nonce: config.nonce || '',
      config: currentConfig,
      page: String(page),
      per_page: String(perPageSelect ? perPageSelect.value : 10),
      type_filter: typeSelect ? typeSelect.value : '',
      view: currentView,
    });

    root.classList.add('is-loading');
    root.setAttribute('aria-busy', 'true');

    try {
      const response = await fetch(ajaxUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
        },
        body: formData.toString(),
      });

      const payload = await response.json();

      if (!payload || !payload.success || !payload.data) {
        return;
      }

      list.innerHTML = payload.data.items_html || '';
      pagination.innerHTML = payload.data.pagination_html || '';
      if (payload.data.view) {
        root.dataset.bywaRealisationsView = payload.data.view;
      }
      if (payload.data.current_page) {
        root.dataset.bywaRealisationsCurrentPage = String(payload.data.current_page);
      }
      bindReadmore(list);
      observeReveal(list);
    } catch (error) {
      // no-op: on garde le rendu courant si l'AJAX échoue
    } finally {
      root.classList.remove('is-loading');
      root.removeAttribute('aria-busy');
    }
  };

  document.querySelectorAll('[data-bywa-realisations-root]').forEach((root) => {
    const perPageSelect = root.querySelector('[data-bywa-realisations-per-page]');
    const typeSelect = root.querySelector('[data-bywa-realisations-type]');

    bindRealisationsView(root);

    root.addEventListener('click', (event) => {
      const button = event.target.closest('[data-bywa-realisations-page]');

      if (!button || !root.contains(button)) {
        return;
      }

      event.preventDefault();
      const nextPage = Number(button.dataset.bywaRealisationsPage || 1);
      loadRealisations(root, nextPage);
    });

    if (perPageSelect) {
      perPageSelect.addEventListener('change', () => {
        loadRealisations(root, 1);
      });
    }

    if (typeSelect) {
      typeSelect.addEventListener('change', () => {
        loadRealisations(root, 1);
      });
    }
  });

  bindReadmore(document);
  observeReveal(document);
});
