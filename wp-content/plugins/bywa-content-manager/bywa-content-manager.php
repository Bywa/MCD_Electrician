<?php
/**
 * Plugin Name: Bywa Creations – Content Manager
 * Description: Gestion des CPT, taxonomies, champs admin et shortcodes front.
 * Version: 1.2.4
 * Author: Bywa Creations
 */

if (!defined('ABSPATH')) exit;

define('BYWA_CM_VERSION', '1.2.6');
define('BYWA_CM_PATH', plugin_dir_path(__FILE__));
define('BYWA_CM_URL', plugin_dir_url(__FILE__));

// CPT / TAXO / META
require_once BYWA_CM_PATH . 'includes/cpt-services.php';
require_once BYWA_CM_PATH . 'includes/cpt-realisations.php';
require_once BYWA_CM_PATH . 'includes/cpt-partenaires.php';
require_once BYWA_CM_PATH . 'includes/cpt-domaines.php';
require_once BYWA_CM_PATH . 'includes/meta-fields.php';

// HELPERS
require_once BYWA_CM_PATH . 'includes/helpers.php';

// SHORTCODES
require_once BYWA_CM_PATH . 'includes/shortcode-services.php';
require_once BYWA_CM_PATH . 'includes/shortcode-realisations.php';
require_once BYWA_CM_PATH . 'includes/shortcode-partenaires.php';
require_once BYWA_CM_PATH . 'includes/shortcode-domaines.php';

/**
 * Assets front
 */
function bywa_enqueue_front_assets() {
    if (is_admin()) {
        return;
    }

    wp_enqueue_style(
        'bywa-content-manager-front',
        BYWA_CM_URL . 'assets/css/bywa-front.css',
        array(),
        BYWA_CM_VERSION
    );

    wp_enqueue_script(
        'bywa-content-manager-front',
        BYWA_CM_URL . 'assets/js/bywa-front.js',
        array(),
        BYWA_CM_VERSION,
        true
    );

    wp_localize_script('bywa-content-manager-front', 'bywaContentManager', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('bywa_realisations_filter'),
    ));

    wp_enqueue_style(
        'bootstrap-icons',
        'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
        array(),
        '1.11.3'
    );
}
add_action('wp_enqueue_scripts', 'bywa_enqueue_front_assets');
