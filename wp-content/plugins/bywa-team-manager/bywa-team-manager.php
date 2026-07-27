<?php
/**
 * Plugin Name: Bywa Creations – Team Manager
 * Description: Gestion réutilisable des sections équipe et de leurs collaborateurs.
 * Version: 1.0.0
 * Author: Bywa Creations
 */

if (!defined('ABSPATH')) {
    exit;
}

define('BYWA_TM_VERSION', '1.0.3');
define('BYWA_TM_PATH', plugin_dir_path(__FILE__));
define('BYWA_TM_URL', plugin_dir_url(__FILE__));

require_once BYWA_TM_PATH . 'includes/cpt-team.php';
require_once BYWA_TM_PATH . 'includes/meta-team.php';
require_once BYWA_TM_PATH . 'includes/helpers.php';
require_once BYWA_TM_PATH . 'includes/shortcode-team.php';

function bywa_tm_enqueue_front_assets() {
    if (is_admin()) {
        return;
    }

    wp_enqueue_style(
        'bywa-team-manager-front',
        BYWA_TM_URL . 'assets/css/bywa-team-front.css',
        array(),
        BYWA_TM_VERSION
    );

    wp_enqueue_script(
        'bywa-team-manager-front',
        BYWA_TM_URL . 'assets/js/bywa-team-front.js',
        array(),
        BYWA_TM_VERSION,
        true
    );
}
add_action('wp_enqueue_scripts', 'bywa_tm_enqueue_front_assets');

function bywa_tm_enqueue_admin_assets($hook) {
    global $post_type;

    if (($hook !== 'post.php' && $hook !== 'post-new.php') || $post_type !== 'team_section') {
        return;
    }

    wp_enqueue_media();

    wp_enqueue_style(
        'bywa-team-manager-admin',
        BYWA_TM_URL . 'assets/css/bywa-team-admin.css',
        array(),
        BYWA_TM_VERSION
    );

    wp_enqueue_script(
        'bywa-team-manager-admin',
        BYWA_TM_URL . 'assets/js/bywa-team-admin.js',
        array(),
        BYWA_TM_VERSION,
        true
    );
}
add_action('admin_enqueue_scripts', 'bywa_tm_enqueue_admin_assets');
