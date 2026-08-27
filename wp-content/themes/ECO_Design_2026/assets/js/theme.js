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

  const cf7Forms = document.querySelectorAll('.wpcf7 form');

  if (cf7Forms.length && window.bootstrap && window.bootstrap.Modal) {
    document.body.classList.add('bywa-cf7-modal-enabled');

    let modalElement = document.getElementById('bywa-cf7-feedback-modal');

    if (!modalElement) {
      modalElement = document.createElement('div');
      modalElement.id = 'bywa-cf7-feedback-modal';
      modalElement.className = 'modal fade bywa-cf7-modal';
      modalElement.tabIndex = -1;
      modalElement.setAttribute('aria-hidden', 'true');
      modalElement.innerHTML = `
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <div>
                <p class="bywa-cf7-modal__eyebrow mb-1">Formulaire de contact</p>
                <h2 class="modal-title h4 mb-0" data-bywa-cf7-modal-title></h2>
              </div>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body" data-bywa-cf7-modal-body></div>
            <div class="modal-footer">
              <button type="button" class="bywa-btn bywa-btn-primary" data-bs-dismiss="modal">Fermer</button>
            </div>
          </div>
        </div>
      `;
      document.body.appendChild(modalElement);
    }

    const modalTitle = modalElement.querySelector('[data-bywa-cf7-modal-title]');
    const modalBody = modalElement.querySelector('[data-bywa-cf7-modal-body]');
    const modal = window.bootstrap.Modal.getOrCreateInstance(modalElement);

    const setModalState = (state) => {
      modalElement.classList.remove('is-success', 'is-warning', 'is-error');
      modalElement.classList.add(`is-${state}`);
    };

    const clearModalBody = () => {
      modalBody.replaceChildren();
    };

    const appendParagraph = (text) => {
      const paragraph = document.createElement('p');
      paragraph.className = 'mb-0';
      paragraph.textContent = text;
      modalBody.appendChild(paragraph);
    };

    const appendFieldList = (fields) => {
      const list = document.createElement('ul');
      list.className = 'bywa-cf7-modal__list';

      fields.forEach((field) => {
        const item = document.createElement('li');
        item.textContent = field.message || field.field || 'Champ invalide';
        list.appendChild(item);
      });

      modalBody.appendChild(list);
    };

    const openModal = (title, message, state, fields = []) => {
      modalTitle.textContent = title;
      clearModalBody();
      setModalState(state);

      if (message) {
        appendParagraph(message);
      }

      if (fields.length) {
        appendFieldList(fields);
      }

      modal.show();
    };

    const normalizeStatus = (status) => {
      if (status === 'invalid') return 'warning';
      if (status === 'mail_sent' || status === 'sent') return 'success';
      return 'error';
    };

    document.addEventListener('wpcf7mailsent', (event) => {
      openModal(
        'Demande envoyée',
        event.detail?.apiResponse?.message || 'Votre demande a bien été envoyée.',
        'success'
      );
    });

    document.addEventListener('wpcf7invalid', (event) => {
      openModal(
        'Vérification nécessaire',
        event.detail?.apiResponse?.message || 'Merci de corriger les champs signalés.',
        'warning',
        event.detail?.apiResponse?.invalid_fields || []
      );
    });

    document.addEventListener('wpcf7mailfailed', (event) => {
      openModal(
        'Erreur d’envoi',
        event.detail?.apiResponse?.message || 'Le message n’a pas pu être envoyé. Réessaie dans un instant.',
        'error'
      );
    });

    document.addEventListener('wpcf7spam', (event) => {
      openModal(
        'Envoi bloqué',
        event.detail?.apiResponse?.message || 'L’envoi a été bloqué par la protection anti-spam.',
        'error'
      );
    });

    document.addEventListener('wpcf7aborted', (event) => {
      openModal(
        'Envoi interrompu',
        event.detail?.apiResponse?.message || 'La demande a été interrompue avant son envoi.',
        'error'
      );
    });

    document.addEventListener('wpcf7submit', (event) => {
      const status = event.detail?.apiResponse?.status;

      if (status && !['mail_sent', 'sent', 'invalid', 'spam', 'mail_failed', 'aborted'].includes(status)) {
        setModalState(normalizeStatus(status));
      }
    });
  }
});
