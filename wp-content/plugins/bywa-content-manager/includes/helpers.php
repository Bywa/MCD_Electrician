<?php
if (!defined('ABSPATH')) exit;

/**
 * Nettoie une liste CSV en tableau.
 */
function bywa_csv_to_array($value, $type = 'string') {
    if (empty($value)) {
        return array();
    }

    $items = array_map('trim', explode(',', $value));
    $items = array_filter($items);

    if ($type === 'int') {
        return array_map('intval', $items);
    }

    return array_map('sanitize_title', $items);
}

/**
 * Rend un bouton standard Bywa.
 */
function bywa_render_button($url, $label = 'Vezi mai mult', $class = '') {
    $url   = esc_url($url);
    $label = esc_html($label);
    $class = esc_attr(trim($class));

    return '<a class="bywa-btn ' . $class . '" href="' . $url . '">' . $label . '</a>';
}

/**
 * Image du post avec fallback.
 */
function bywa_get_post_image_html($post_id, $size = 'large', $class = '') {
    $class = esc_attr($class);

    if (has_post_thumbnail($post_id)) {
        return get_the_post_thumbnail($post_id, $size, array('class' => $class));
    }

    return '<div class="bywa-no-image ' . $class . '"></div>';
}

/**
 * Normalise un nom de client pour l'affichage.
 */
function bywa_normalize_client_name($name) {
    $name = trim(preg_replace('/\s+/u', ' ', wp_strip_all_tags((string) $name)));

    if ($name === '') {
        return 'Client';
    }

    if (function_exists('mb_convert_case')) {
        return mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
    }

    return ucwords(strtolower($name));
}
