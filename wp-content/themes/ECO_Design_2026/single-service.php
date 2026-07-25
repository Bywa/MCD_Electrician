<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="primary" class="site-main bywa-page-layout bywa-page-layout--hero-mini bywa-single-service-page">
    <?php
    while (have_posts()) :
        the_post();

        $post_id = get_the_ID();
        $service_icon = get_post_meta($post_id, '_bywa_service_icon', true);
        $service_short_text = get_post_meta($post_id, '_bywa_service_short_text', true);
        $service_groups = get_the_terms($post_id, 'service_group');
        $cta_label = bywa_eco_get_theme_mod('bywa_eco_cta_label', 'Demander un devis');
        $cta_url = bywa_eco_get_contact_url();
        $company_phone = bywa_eco_get_contact_phone();
        $company_phone_secondary = bywa_eco_get_contact_phone_secondary();
        $company_email = bywa_eco_get_contact_email();
        $phone_link = bywa_eco_get_tel_link($company_phone);
        $phone_secondary_link = bywa_eco_get_tel_link($company_phone_secondary);

        if (empty($service_icon)) {
            $service_icon = 'bi-lightning-charge-fill';
        }

        bywa_eco_render_page_hero($post_id, 'mini');
        ?>

        <section class="bywa-page-content bywa-single-service">
            <div class="container">
                <article <?php post_class('bywa-single-service__article'); ?>>
                    <section class="bywa-single-service__intro">
                        <div class="row g-4 align-items-stretch">
                            <div class="col-lg-7">
                                <div class="bywa-single-service__panel bywa-single-service__panel--content bywa-reveal">
                                    <?php
                                    bywa_eco_render_breadcrumbs(array(
                                        array(
                                            'label' => __('Accueil', 'eco-design-2026'),
                                            'url'   => home_url('/'),
                                        ),
                                        array(
                                            'label' => __('Services', 'eco-design-2026'),
                                            'url'   => get_post_type_archive_link('service'),
                                        ),
                                        array(
                                            'label' => get_the_title(),
                                            'url'   => '',
                                        ),
                                    ));
                                    ?>
                                    <div class="bywa-single-service__badge-wrap">
                                        <span class="bywa-single-service__icon"><i class="bi <?php echo esc_attr($service_icon); ?>"></i></span>
                                        <span class="bywa-section-kicker">Service</span>
                                    </div>

                                    <h1 class="bywa-single-service__title"><?php the_title(); ?></h1>

                                    <?php if (!empty($service_short_text)) : ?>
                                        <p class="bywa-single-service__lead"><?php echo esc_html($service_short_text); ?></p>
                                    <?php elseif (has_excerpt()) : ?>
                                        <p class="bywa-single-service__lead"><?php echo esc_html(get_the_excerpt()); ?></p>
                                    <?php endif; ?>

                                    <div class="bywa-entry-content bywa-single-service__content">
                                        <?php the_content(); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-5">
                                <div class="bywa-single-service__stack">
                                    <aside class="bywa-single-service__panel bywa-single-service__panel--visual bywa-reveal">
                                        <div class="bywa-single-service__visual">
                                            <?php if (has_post_thumbnail()) : ?>
                                                <?php the_post_thumbnail('full', array('class' => 'bywa-single-service__image')); ?>
                                            <?php else : ?>
                                                <div class="bywa-no-image bywa-single-service__image"></div>
                                            <?php endif; ?>
                                            <div class="bywa-single-service__visual-overlay"></div>
                                        </div>
                                    </aside>

                                    <aside class="bywa-single-service__panel bywa-single-service__panel--meta bywa-reveal">
                                        <div class="bywa-single-service__meta-grid">
                                            <div class="bywa-single-service__meta-card">
                                                <span class="bywa-single-service__meta-label">Intervention</span>
                                                <strong>Étude, réalisation et accompagnement</strong>
                                            </div>

                                            <?php if (!empty($service_groups) && !is_wp_error($service_groups)) : ?>
                                                <div class="bywa-single-service__meta-card">
                                                    <span class="bywa-single-service__meta-label">Catégorie</span>
                                                    <strong><?php echo esc_html(implode(', ', wp_list_pluck($service_groups, 'name'))); ?></strong>
                                                </div>
                                            <?php endif; ?>

                                            <div class="bywa-single-service__meta-card">
                                                <span class="bywa-single-service__meta-label">Zone</span>
                                                <strong>Jura bernois, Bienne et région</strong>
                                            </div>
                                        </div>
                                    </aside>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="bywa-single-service__cta bywa-reveal">
                        <div class="bywa-single-service__cta-box">
                            <div class="bywa-single-service__cta-content">
                                <span class="bywa-section-kicker">Passer à l’action</span>
                                <h2>Besoin d’un accompagnement pour ce service ?</h2>
                                <p>Présentez votre besoin et obtenez rapidement une proposition claire, adaptée à votre bâtiment et à votre budget.</p>
                            </div>

                            <div class="bywa-single-service__cta-actions">
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
                </article>
            </div>
        </section>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
