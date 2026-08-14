<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="primary" class="site-main bywa-page-layout bywa-page-layout--hero-mini bywa-single-realisation-page">
    <?php
    while (have_posts()) :
        the_post();

        $post_id = get_the_ID();
        $location = get_post_meta($post_id, '_bywa_realisation_location', true);
        $client = trim((string) get_post_meta($post_id, '_bywa_realisation_client', true));
        $architecte = get_post_meta($post_id, '_bywa_realisation_architecte', true);
        $project_date = get_post_meta($post_id, '_bywa_realisation_date', true);
        $gallery_ids = function_exists('bywa_get_realisation_gallery_ids') ? bywa_get_realisation_gallery_ids($post_id) : array();
        $types = get_the_terms($post_id, 'type_realisation');
        $cta_label = bywa_eco_get_theme_mod('bywa_eco_cta_label', 'Cere o ofertă');
        $cta_url = bywa_eco_get_contact_url();
        $company_phone = bywa_eco_get_contact_phone();
        $company_phone_secondary = bywa_eco_get_contact_phone_secondary();
        $company_email = bywa_eco_get_contact_email();
        $phone_link = bywa_eco_get_tel_link($company_phone);
        $phone_secondary_link = bywa_eco_get_tel_link($company_phone_secondary);

        if (empty($location) && !empty($types) && !is_wp_error($types)) {
            $location = $types[0]->name;
        }

        bywa_eco_render_page_hero($post_id, 'mini');
        ?>

        <section class="bywa-page-content bywa-single-realisation">
            <div class="container">
                <article <?php post_class('bywa-single-realisation__article'); ?>>
                    <section class="bywa-single-realisation__intro bywa-reveal">
                        <div class="row g-4 align-items-stretch">
                            <div class="col-lg-7">
                                <div class="bywa-single-realisation__panel bywa-single-realisation__panel--content">
                                    <?php
                                    bywa_eco_render_breadcrumbs(array(
                                        array(
                                            'label' => __('Acasă', 'eco-design-2026'),
                                            'url'   => home_url('/'),
                                        ),
                                        array(
                                            'label' => __('Lucrări', 'eco-design-2026'),
                                            'url'   => get_post_type_archive_link('realisation'),
                                        ),
                                        array(
                                            'label' => get_the_title(),
                                            'url'   => '',
                                        ),
                                    ));
                                    ?>
                                    <span class="bywa-section-kicker">Lucrare</span>
                                    <h1 class="bywa-single-realisation__title"><?php the_title(); ?></h1>

                                    <?php if (has_excerpt()) : ?>
                                        <p class="bywa-single-realisation__lead"><?php echo esc_html(get_the_excerpt()); ?></p>
                                    <?php endif; ?>

                                    <div class="bywa-entry-content bywa-single-realisation__content">
                                        <?php the_content(); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-5">
                                <aside class="bywa-single-realisation__panel bywa-single-realisation__panel--meta">
                                    <div class="bywa-single-realisation__meta-grid">
                                        <?php if (!empty($location)) : ?>
                                            <div class="bywa-single-realisation__meta-card">
                                                <span class="bywa-single-realisation__meta-label">Loc</span>
                                                <strong><?php echo esc_html($location); ?></strong>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($project_date)) : ?>
                                            <div class="bywa-single-realisation__meta-card">
                                                <span class="bywa-single-realisation__meta-label">Dată</span>
                                                <strong><?php echo esc_html($project_date); ?></strong>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($client)) : ?>
                                            <div class="bywa-single-realisation__meta-card">
                                                <span class="bywa-single-realisation__meta-label">Client</span>
                                                <strong><?php echo esc_html(function_exists('bywa_normalize_client_name') ? bywa_normalize_client_name($client) : $client); ?></strong>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($architecte)) : ?>
                                            <div class="bywa-single-realisation__meta-card">
                                                <span class="bywa-single-realisation__meta-label">Arhitect</span>
                                                <strong><?php echo esc_html($architecte); ?></strong>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($types) && !is_wp_error($types)) : ?>
                                            <div class="bywa-single-realisation__meta-card">
                                                <span class="bywa-single-realisation__meta-label">Tip</span>
                                                <strong><?php echo esc_html(implode(', ', wp_list_pluck($types, 'name'))); ?></strong>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </aside>
                            </div>
                        </div>
                    </section>

                    <section class="bywa-single-realisation__cta bywa-reveal">
                        <div class="bywa-single-realisation__cta-box">
                            <div class="bywa-single-realisation__cta-content">
                                <span class="bywa-section-kicker">Proiectul tău</span>
                                <h2>Ai un proiect similar pentru clădirea ta?</h2>
                                <p>Spune-ne ce ai nevoie și primești rapid o ofertă clară pentru lucrări de electricitate, renovare sau fotovoltaic.</p>
                            </div>

                            <div class="bywa-single-realisation__cta-actions">
                                <a class="bywa-btn bywa-btn-primary" href="<?php echo esc_url($cta_url); ?>">
                                    <?php echo esc_html($cta_label); ?>
                                </a>

                                <?php if (!empty($phone_link)) : ?>
                                    <a class="bywa-btn bywa-btn-outline-dark" href="<?php echo esc_url($phone_link); ?>">
                                        <?php echo esc_html($company_phone); ?>
                                    </a>
                                <?php endif; ?>

                                <?php if (!empty($phone_secondary_link)) : ?>
                                    <a class="bywa-btn bywa-btn-outline-dark" href="<?php echo esc_url($phone_secondary_link); ?>">
                                        <?php echo esc_html($company_phone_secondary); ?>
                                    </a>
                                <?php endif; ?>

                                <?php if (!empty($company_email)) : ?>
                                    <a class="bywa-btn bywa-btn-outline-dark" href="mailto:<?php echo esc_attr($company_email); ?>">
                                        <?php echo esc_html($company_email); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>

                    <?php if (!empty($gallery_ids)) : ?>
                        <?php
                        $cover_id = (int) $gallery_ids[0];
                        $cover_url = wp_get_attachment_image_url($cover_id, 'full');
                        $cover_alt = get_post_meta($cover_id, '_wp_attachment_image_alt', true);

                        if (empty($cover_alt)) {
                            $cover_alt = get_the_title();
                        }
                        ?>
                        <section class="bywa-single-realisation__gallery bywa-reveal">
                            <div class="bywa-single-realisation__gallery-head">
                                <span class="bywa-section-kicker">Galerie</span>
                                <h2>Imagini de pe șantier</h2>
                            </div>

                            <div class="bywa-single-realisation__gallery-layout">
                                <button type="button" class="bywa-single-realisation__gallery-main" data-bywa-modal-trigger data-bywa-modal-image="<?php echo esc_url($cover_url); ?>" data-bywa-modal-alt="<?php echo esc_attr($cover_alt); ?>">
                                    <?php echo wp_get_attachment_image($cover_id, 'full', false, array('class' => 'bywa-single-realisation__gallery-main-image', 'loading' => 'eager')); ?>
                                </button>

                                <?php if (count($gallery_ids) > 1) : ?>
                                    <div class="bywa-single-realisation__gallery-grid">
                                        <?php foreach (array_slice($gallery_ids, 1) as $attachment_id) : ?>
                                            <?php
                                            $image_url = wp_get_attachment_image_url($attachment_id, 'full');
                                            $image_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);

                                            if (empty($image_alt)) {
                                                $image_alt = get_the_title();
                                            }
                                            ?>
                                            <button type="button" class="bywa-single-realisation__gallery-thumb" data-bywa-modal-trigger data-bywa-modal-image="<?php echo esc_url($image_url); ?>" data-bywa-modal-alt="<?php echo esc_attr($image_alt); ?>">
                                                <?php echo wp_get_attachment_image($attachment_id, 'large', false, array('class' => 'bywa-single-realisation__gallery-thumb-image', 'loading' => 'lazy')); ?>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </section>

                        <div class="bywa-media-modal" data-bywa-media-modal hidden aria-hidden="true">
                            <button type="button" class="bywa-media-modal__backdrop" data-bywa-modal-close aria-label="<?php echo esc_attr__('Închide fereastra', 'eco-design-2026'); ?>"></button>
                            <div class="bywa-media-modal__dialog" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr__('Imagine mărită', 'eco-design-2026'); ?>">
                                <button type="button" class="bywa-media-modal__close" data-bywa-modal-close aria-label="<?php echo esc_attr__('Închide fereastra', 'eco-design-2026'); ?>">
                                    <span class="bi bi-x-lg" aria-hidden="true"></span>
                                </button>
                                <img class="bywa-media-modal__image" data-bywa-modal-image-target src="" alt="">
                            </div>
                        </div>
                    <?php endif; ?>
                </article>
            </div>
        </section>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
