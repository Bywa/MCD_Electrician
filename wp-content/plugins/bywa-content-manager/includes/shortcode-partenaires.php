<?php
if (!defined('ABSPATH')) exit;

function bywa_partenaires_shortcode($atts) {
    $atts = shortcode_atts(array(
        'template' => 'grid',
        'limit'    => 12,
        'ids'      => '',
        'include'  => '',
        'category' => '',
        'featured' => '',
        'orderby'  => 'menu_order',
        'order'    => 'ASC',
    ), $atts, 'bywa_partenaires');

    $allowed_templates = array('logos', 'grid', 'cards');
    $template = in_array($atts['template'], $allowed_templates, true) ? $atts['template'] : 'grid';

    $allowed_orderby = array('menu_order', 'title', 'rand');
    $orderby = in_array($atts['orderby'], $allowed_orderby, true) ? $atts['orderby'] : 'menu_order';

    $order = strtoupper($atts['order']) === 'DESC' ? 'DESC' : 'ASC';

    $ids        = bywa_csv_to_array($atts['ids'], 'int');
    $include    = bywa_csv_to_array($atts['include'], 'string');
    $categories = bywa_csv_to_array($atts['category'], 'string');

    $args = array(
        'post_type'           => 'partenaire',
        'post_status'         => 'publish',
        'posts_per_page'      => intval($atts['limit']),
        'orderby'             => $orderby,
        'order'               => $order,
        'ignore_sticky_posts' => true,
    );

    if (!empty($ids)) {
        $args['post__in'] = $ids;
        $args['orderby']  = 'post__in';
    }

    if (!empty($include)) {
        $args['post_name__in'] = $include;
    }

    if (!empty($categories)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'categorie_partenaire',
                'field'    => 'slug',
                'terms'    => $categories,
            )
        );
    }

    if ($atts['featured'] === 'yes') {
        $args['meta_query'] = array(
            array(
                'key'   => '_bywa_featured',
                'value' => '1',
            )
        );
    }

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        return '';
    }

    ob_start();

    echo '<div class="bywa-partenaires bywa-partenaires--' . esc_attr($template) . '">';

    while ($query->have_posts()) {
        $query->the_post();

        $post_id    = get_the_ID();
        $title      = get_the_title();
        $excerpt    = get_the_excerpt();
        $image_html = bywa_get_post_image_html($post_id, 'medium', 'bywa-card__img bywa-logo__img');

        if ($template === 'cards') {
            echo '<article class="bywa-partenaire-card bywa-partenaire-card--cards">';
                echo '<div class="bywa-partenaire-card__media">' . $image_html . '</div>';
                echo '<div class="bywa-partenaire-card__content">';
                    echo '<h3 class="bywa-card__title">' . esc_html($title) . '</h3>';
                    if (!empty($excerpt)) {
                        echo '<p class="bywa-card__excerpt">' . esc_html($excerpt) . '</p>';
                    }
                echo '</div>';
            echo '</article>';
        } elseif ($template === 'grid') {
            echo '<article class="bywa-partenaire-card bywa-partenaire-card--grid">';
                echo '<div class="bywa-partenaire-card__media">' . $image_html . '</div>';
                echo '<h3 class="bywa-card__title">' . esc_html($title) . '</h3>';
            echo '</article>';
        } else {
            echo '<article class="bywa-partenaire-card bywa-partenaire-card--logos">';
                echo '<div class="bywa-partenaire-card__media">' . $image_html . '</div>';
            echo '</article>';
        }
    }

    echo '</div>';

    wp_reset_postdata();

    return ob_get_clean();
}

add_shortcode('bywa_partenaires', 'bywa_partenaires_shortcode');
