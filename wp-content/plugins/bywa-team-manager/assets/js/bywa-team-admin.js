document.addEventListener('DOMContentLoaded', function () {
  const bindHeroPicker = (card) => {
    const uploadButton = card.querySelector('.bywa-hero-upload-button');
    const removeButton = card.querySelector('.bywa-hero-remove-button');
    const input = card.querySelector('.bywa-hero-image-input');
    const preview = card.querySelector('.bywa-hero-image-preview');

    if (uploadButton) {
      uploadButton.addEventListener('click', (event) => {
        event.preventDefault();

        const frame = wp.media({
          title: 'Choisir une image pour la bannière',
          button: { text: 'Utiliser cette image' },
          multiple: false
        });

        frame.on('select', () => {
          const attachment = frame.state().get('selection').first().toJSON();
          input.value = attachment.id;
          preview.classList.add('has-image');
          preview.innerHTML = `<img src="${attachment.sizes?.medium?.url || attachment.url}" alt="">`;
          if (removeButton) {
            removeButton.style.display = '';
          }
        });

        frame.open();
      });
    }

    if (removeButton) {
      removeButton.addEventListener('click', (event) => {
        event.preventDefault();
        input.value = '';
        preview.classList.remove('has-image');
        preview.innerHTML = '<span>Aucune image sélectionnée</span>';
        removeButton.style.display = 'none';
      });
    }
  };

  const bindImagePicker = (scope) => {
    const input = scope.querySelector('.bywa-team-admin__photo-input');
    const preview = scope.querySelector('.bywa-team-admin__photo-preview');
    const uploadButton = scope.querySelector('[data-bywa-team-photo-upload]');

    if (uploadButton) {
      uploadButton.addEventListener('click', (event) => {
        event.preventDefault();

        const frame = wp.media({
          title: 'Choisir une photo',
          button: { text: 'Utiliser cette photo' },
          multiple: false
        });

        frame.on('select', () => {
          const attachment = frame.state().get('selection').first().toJSON();
          input.value = attachment.id;
          preview.innerHTML = `<img src="${attachment.sizes?.thumbnail?.url || attachment.url}" alt="">`;
        });

        frame.open();
      });
    }
  };

  const teamRoot = document.querySelector('[data-bywa-team-admin]');

  document.querySelectorAll('.bywa-hero-admin-image-card').forEach(bindHeroPicker);

  if (teamRoot) {
    const photosToggle = teamRoot.closest('.postbox')?.querySelector('input[name="bywa_team_member_photos_enabled"]');
    const list = teamRoot.querySelector('[data-bywa-team-list]');
    const addButton = teamRoot.querySelector('[data-bywa-team-add]');
    const template = document.getElementById('tmpl-bywa-team-member-row');

    const nextIndex = () => list.querySelectorAll('[data-bywa-team-item]').length;

    const bindRowActions = (item) => {
      const removeButton = item.querySelector('[data-bywa-team-remove]');
      const photoRemoveButton = item.querySelector('[data-bywa-team-photo-remove]');

      if (removeButton) {
        removeButton.addEventListener('click', (event) => {
          event.preventDefault();
          item.remove();
        });
      }

      if (photoRemoveButton) {
        photoRemoveButton.addEventListener('click', (event) => {
          event.preventDefault();
          const input = item.querySelector('.bywa-team-admin__photo-input');
          const preview = item.querySelector('.bywa-team-admin__photo-preview');
          input.value = '';
          preview.innerHTML = '<span>Aucune photo sélectionnée</span>';
        });
      }

      bindImagePicker(item);
    };

    const updatePhotoVisibility = () => {
      if (!photosToggle) {
        return;
      }

      const enabled = photosToggle.checked;

      list.querySelectorAll('[data-bywa-team-item] .bywa-team-admin__photo').forEach((photoBox) => {
        photoBox.classList.toggle('is-hidden', !enabled);
      });
    };

    list.querySelectorAll('[data-bywa-team-item]').forEach(bindRowActions);
    updatePhotoVisibility();

    if (photosToggle) {
      photosToggle.addEventListener('change', updatePhotoVisibility);
    }

    if (addButton && template) {
      addButton.addEventListener('click', (event) => {
        event.preventDefault();

        const markup = template.innerHTML.replaceAll('__INDEX__', String(nextIndex()));
        list.insertAdjacentHTML('beforeend', markup);
        const item = list.lastElementChild;

        if (item) {
          bindRowActions(item);
          updatePhotoVisibility();
        }
      });
    }
  }

  const galleryRoot = document.querySelector('[data-bywa-team-gallery-admin]');

  if (galleryRoot) {
    const list = galleryRoot.querySelector('[data-bywa-team-gallery-list]');
    const addButton = galleryRoot.querySelector('[data-bywa-team-gallery-add]');
    const template = document.getElementById('tmpl-bywa-team-gallery-item');
    const maxItems = 5;

    const refreshAddState = () => {
      if (!addButton) {
        return;
      }

      addButton.disabled = list.querySelectorAll('[data-bywa-team-gallery-item]').length >= maxItems;
    };

    const nextIndex = () => list.querySelectorAll('[data-bywa-team-gallery-item]').length;

    const createGalleryItem = (attachment) => {
      const markup = template.innerHTML.replaceAll('__INDEX__', String(nextIndex()));
      list.insertAdjacentHTML('beforeend', markup);
      const item = list.lastElementChild;

      if (!item) {
        return null;
      }

      const input = item.querySelector('.bywa-team-admin__photo-input');
      const preview = item.querySelector('.bywa-team-admin__photo-preview');

      if (attachment && input && preview) {
        input.value = attachment.id;
        preview.innerHTML = `<img src="${attachment.sizes?.thumbnail?.url || attachment.url}" alt="">`;
      }

      bindGalleryItem(item);
      refreshAddState();

      return item;
    };

    const bindGalleryItem = (item) => {
      const removeButton = item.querySelector('[data-bywa-team-gallery-remove]');

      if (removeButton) {
        removeButton.addEventListener('click', (event) => {
          event.preventDefault();
          item.remove();
          refreshAddState();
        });
      }

      bindImagePicker(item);
    };

    list.querySelectorAll('[data-bywa-team-gallery-item]').forEach(bindGalleryItem);
    refreshAddState();

    if (addButton && template) {
      addButton.addEventListener('click', (event) => {
        event.preventDefault();

        if (list.querySelectorAll('[data-bywa-team-gallery-item]').length >= maxItems) {
          refreshAddState();
          return;
        }

        const frame = wp.media({
          title: 'Choisir une photo d’équipe',
          button: { text: 'Utiliser cette photo' },
          multiple: false
        });

        frame.on('select', () => {
          const attachment = frame.state().get('selection').first().toJSON();
          createGalleryItem(attachment);
        });

        frame.open();
      });
    }
  }
});
