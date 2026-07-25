<?php
if (!defined('ABSPATH')) {
    exit;
}

$args    = isset($args) && is_array($args) ? $args : array();
$post_id = !empty($args['post_id']) ? intval($args['post_id']) : get_the_ID();
$variant = !empty($args['variant']) ? sanitize_key($args['variant']) : 'large';

$data = bywa_eco_get_page_hero_data($post_id);

$hero_class = 'bywa-hero bywa-page-hero';
$hero_class .= ($variant === 'mini') ? ' bywa-page-hero--mini' : ' bywa-page-hero--large';

if (empty($data['slides'])) {
    $hero_class .= ' bywa-page-hero--no-media';
}
?>
<section class="<?php echo esc_attr($hero_class); ?>">
    <div class="bywa-hero-media bywa-hero-slider" data-bywa-hero-slider>
        <?php if (!empty($data['slides'])) : ?>
            <?php foreach ($data['slides'] as $index => $slide_url) : ?>
                <div
                    class="bywa-hero-slide<?php echo $index === 0 ? ' is-active' : ''; ?>"
                    style="background-image: url('<?php echo esc_url($slide_url); ?>');"
                    aria-hidden="<?php echo $index === 0 ? 'false' : 'true'; ?>"
                ></div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="bywa-hero-slide is-active bywa-hero-slide--fallback" aria-hidden="false"></div>
        <?php endif; ?>

        <div class="bywa-hero-overlay bywa-hero-overlay--smoke"></div>
        <div class="bywa-hero-overlay bywa-hero-overlay--green"></div>
    </div>

    <div class="container bywa-hero-content">
        <div class="row align-items-center <?php echo $variant === 'mini' ? 'bywa-hero-row-mini' : 'min-vh-100'; ?>">
            <div class="col-xl-8 col-lg-10">
                <?php if (!empty($data['kicker'])) : ?>
                    <span class="bywa-hero-kicker bywa-reveal"><?php echo esc_html($data['kicker']); ?></span>
                <?php endif; ?>

                <h1 class="bywa-reveal"><?php echo esc_html($data['title']); ?></h1>

                <?php if (!empty($data['text'])) : ?>
                    <p class="bywa-reveal"><?php echo esc_html($data['text']); ?></p>
                <?php endif; ?>

                <?php if (
                    (!empty($data['action_1']['label']) && !empty($data['action_1']['url'])) ||
                    (!empty($data['action_2']['label']) && !empty($data['action_2']['url']))
                ) : ?>
                    <div class="bywa-hero-actions bywa-reveal">
                        <?php if (!empty($data['action_1']['label']) && !empty($data['action_1']['url'])) : ?>
                            <a class="bywa-btn bywa-btn-primary" href="<?php echo esc_url($data['action_1']['url']); ?>">
                                <?php echo esc_html($data['action_1']['label']); ?>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($data['action_2']['label']) && !empty($data['action_2']['url'])) : ?>
                            <a class="bywa-btn bywa-btn-outline" href="<?php echo esc_url($data['action_2']['url']); ?>">
                                <?php echo esc_html($data['action_2']['label']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($data['points'])) : ?>
                    <div class="bywa-hero-points bywa-reveal">
                        <?php foreach ($data['points'] as $point) : ?>
                            <span><i class="bi bi-check-circle-fill"></i> <?php echo esc_html($point); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>