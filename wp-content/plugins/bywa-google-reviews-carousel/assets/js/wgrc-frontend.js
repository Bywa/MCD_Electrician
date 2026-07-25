jQuery(function ($) {
  $('.ntc-carousel').each(function () {
    var $carousel = $(this);
    if ($carousel.hasClass('owl-loaded')) return;

    var config = $carousel.data('ntc-config') || {};
    var cardCount = $carousel.find('.bywa-google-reviews__card').length;
    var visibleItems = Math.max(1, parseInt(config.visibleItems || 4, 10));
    var desktopItems = Math.min(cardCount, visibleItems);
    var tabletItems = Math.min(cardCount, Math.min(visibleItems, 3));
    var smallTabletItems = Math.min(cardCount, Math.min(visibleItems, 2));
    var hasOverflow = cardCount > desktopItems;

    if (!cardCount) return;

    $carousel.owlCarousel({
      items: desktopItems,
      margin: 24,
      loop: hasOverflow,
      nav: hasOverflow,
      dots: hasOverflow,
      autoplay: !!config.autoplay,
      autoplayTimeout: parseInt(config.autoplayTimeout || 5000, 10),
      autoplayHoverPause: true,
      smartSpeed: 700,
      rtl: !!config.rtl,
      navText: ['‹', '›'],
      responsive: {
        0: { items: 1 },
        576: { items: Math.max(1, smallTabletItems) },
        992: { items: Math.max(1, tabletItems) },
        1200: { items: Math.max(1, desktopItems) }
      }
    });
  });
});
