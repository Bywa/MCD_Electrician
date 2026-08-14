<?php
get_header();

$archive_hero = bywa_eco_get_archive_hero_data('service');
?>
<main id="primary" class="site-main bywa-page-layout bywa-page-layout--hero-mini">
    <section class="bywa-page-hero bywa-page-hero--mini bywa-cpt-archive-hero">
        <div class="bywa-hero-media bywa-hero-slider" data-bywa-hero-slider>
            <?php if (!empty($archive_hero['slides'])) : ?>
                <?php foreach ($archive_hero['slides'] as $index => $slide_url) : ?>
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
            <div class="row align-items-center bywa-hero-row-mini">
                <div class="col-xl-8 col-lg-10">
                    <?php
                    bywa_eco_render_breadcrumbs(array(
                        array(
                            'label' => __('Acasă', 'eco-design-2026'),
                            'url'   => home_url('/'),
                        ),
                        array(
                            'label' => $archive_hero['title'],
                            'url'   => '',
                        ),
                    ));
                    ?>
                    <span class="bywa-hero-kicker bywa-reveal"><?php echo esc_html($archive_hero['kicker']); ?></span>
                    <h1 class="bywa-reveal"><?php echo esc_html($archive_hero['title']); ?></h1>
                    <?php
                    $archive_description = !empty($archive_hero['text']) ? $archive_hero['text'] : get_the_archive_description();
                    if (!empty($archive_description)) :
                    ?>
                        <div class="bywa-reveal"><?php echo wp_kses_post($archive_description); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="bywa-page-content">
        <div class="container py-5">
            <?php echo do_shortcode('[bywa_services template="showcase" orderby="menu_order" order="ASC"]'); ?>
        </div>
    </section>
</main>
<?php get_footer(); ?>
