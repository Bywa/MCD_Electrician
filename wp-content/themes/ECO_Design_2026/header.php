<?php
if (!defined('ABSPATH')) {
    exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="bywa-site-header">
    <div class="container-fluid bywa-header-wrap">
        <nav class="navbar navbar-expand-xl bywa-navbar">
            <div class="container bywa-navbar-inner">
                <a class="navbar-brand bywa-brand" href="<?php echo esc_url(home_url('/')); ?>">
                    <?php echo bywa_eco_get_brand_logo_html('bywa-site-brand-logo'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#bywaMainNav" aria-controls="bywaMainNav" aria-expanded="false" aria-label="<?php esc_attr_e('Ouvrir le menu', 'eco-design-2026'); ?>">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="bywaMainNav">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'container'      => false,
                        'menu_class'     => 'navbar-nav ms-auto align-items-xl-center bywa-primary-menu',
                        'fallback_cb'    => 'bywa_eco_primary_fallback',
                        'depth'          => 2,
                    ));
                    ?>

                    <a class="bywa-header-cta" href="<?php echo esc_url(bywa_eco_get_contact_url()); ?>">
                        <span class="bi bi-arrow-up-right"></span>
                        <?php echo esc_html(bywa_eco_get_theme_mod('bywa_eco_cta_label', 'Demander un devis')); ?>
                    </a>
                </div>
            </div>
        </nav>
    </div>
</header>
