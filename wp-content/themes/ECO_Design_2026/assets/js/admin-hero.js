jQuery(function ($) {
  $('.bywa-hero-upload-button').on('click', function (e) {
    e.preventDefault();

    const button = $(this);
    const card = button.closest('.bywa-hero-admin-image-card');
    const input = card.find('.bywa-hero-image-input');
    const preview = card.find('.bywa-hero-image-preview');
    const removeButton = card.find('.bywa-hero-remove-button');

    const frame = wp.media({
      title: 'Choisir une image pour la bannière',
      button: {
        text: 'Utiliser cette image'
      },
      multiple: false,
      library: {
        type: 'image'
      }
    });

    frame.on('select', function () {
      const attachment = frame.state().get('selection').first().toJSON();

      input.val(attachment.id);

      let previewUrl = attachment.sizes && attachment.sizes.medium
        ? attachment.sizes.medium.url
        : attachment.url;

      preview
        .addClass('has-image')
        .html('<img src="' + previewUrl + '" alt="">');

      removeButton.show();
    });

    frame.open();
  });

  $('.bywa-hero-remove-button').on('click', function (e) {
    e.preventDefault();

    const button = $(this);
    const card = button.closest('.bywa-hero-admin-image-card');
    const input = card.find('.bywa-hero-image-input');
    const preview = card.find('.bywa-hero-image-preview');

    input.val('');
    preview
      .removeClass('has-image')
      .html('<span>Aucune image sélectionnée</span>');

    button.hide();
  });
});