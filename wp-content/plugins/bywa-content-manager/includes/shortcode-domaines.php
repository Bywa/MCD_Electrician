<?php
if (!defined('ABSPATH')) exit;

function bywa_domaines_shortcode($atts) {
    $atts = shortcode_atts(array(
        'template' => 'visual',
        'limit'    => 4,
        'ids'      => '',
        'include'  => '',
        'exclude'  => '',
        'group'    => '',
        'featured' => '',
        'orderby'  => 'menu_order',
        'order'    => 'ASC',
    ), $atts, 'bywa_domaines');

    $allowed_templates = array('visual', 'grid', 'simple');
    $template = in_array($atts['template'], $allowed_templates, true) ? $atts['template'] : 'visual';

    $allowed_orderby = array('menu_order', 'title', 'date', 'rand');
    $orderby = in_array($atts['orderby'], $allowed_orderby, true) ? $atts['orderby'] : 'menu_order';

    $order = strtoupper($atts['order']) === 'DESC' ? 'DESC' : 'ASC';

    $ids     = bywa_csv_to_array($atts['ids'], 'int');
    $include = bywa_csv_to_array($atts['include'], 'string');
    $exclude = bywa_csv_to_array($atts['exclude'], 'string');
    $groups  = bywa_csv_to_array($atts['group'], 'string');

    $args = array(
        'post_type'           => 'domaine_activite',
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

    if (!empty($exclude)) {
        $args['post_name__not_in'] = $exclude;
    }

    if (!empty($groups)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'domaine_group',
                'field'    => 'slug',
                'terms'    => $groups,
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

    echo '<div class="bywa-domaines bywa-domaines--' . esc_attr($template) . '">';

    $index = 0;

    while ($query->have_posts()) {
        $query->the_post();
        $index++;

        $post_id      = get_the_ID();
        $title        = get_the_title();
        $permalink    = get_permalink();
        $image_html   = bywa_get_post_image_html($post_id, 'large', 'bywa-card__img');
        $short_text   = get_post_meta($post_id, '_bywa_domaine_short_text', true);
        $link_label   = get_post_meta($post_id, '_bywa_domaine_link_label', true);
        $excerpt      = get_the_excerpt();

        if (empty($short_text)) {
            $short_text = $excerpt;
        }

        if (empty($link_label)) {
            $link_label = 'Lire plus';
        }

        $number = str_pad((string) $index, 2, '0', STR_PAD_LEFT);

        if ($template === 'simple') {
            echo '<article class="bywa-domaine-card bywa-domaine-card--simple bywa-reveal">';
                echo '<h3 class="bywa-card__title">' . esc_html($title) . '</h3>';
                if (!empty($short_text)) {
                    echo '<p class="bywa-card__excerpt">' . esc_html($short_text) . '</p>';
                }
                echo '<a class="bywa-readmore" href="' . esc_url($permalink) . '">' . esc_html($link_label) . ' <i class="bi bi-arrow-up-right"></i></a>';
            echo '</article>';
        } elseif ($template === 'grid') {
            echo '<article class="bywa-domaine-card bywa-domaine-card--grid bywa-reveal">';
                echo '<a class="bywa-domaine-card__link" href="' . esc_url($permalink) . '">';
                    echo '<div class="bywa-domaine-card__media">' . $image_html . '</div>';
                    echo '<div class="bywa-domaine-card__content">';
                        echo '<div class="bywa-domaine-card__number">' . esc_html($number) . '</div>';
                        echo '<h3 class="bywa-card__title">' . esc_html($title) . '</h3>';
                        if (!empty($short_text)) {
                            echo '<p class="bywa-card__excerpt">' . esc_html($short_text) . '</p>';
                        }
                    echo '</div>';
                echo '</a>';
            echo '</article>';
        } else {
            echo '<article class="bywa-domaine-card bywa-domaine-card--visual bywa-reveal">';
                echo '<a class="bywa-domaine-card__link" href="' . esc_url($permalink) . '">';
                    echo '<div class="bywa-domaine-card__media">' . $image_html . '</div>';
                    echo '<div class="bywa-domaine-card__overlay"></div>';
                    echo '<div class="bywa-domaine-card__number">' . esc_html($number) . '</div>';
                    echo '<div class="bywa-domaine-card__content">';
                        echo '<h3 class="bywa-card__title">' . esc_html($title) . '</h3>';
                        if (!empty($short_text)) {
                            echo '<p class="bywa-card__excerpt">' . esc_html($short_text) . '</p>';
                        }
                        echo '<span class="bywa-readmore">' . esc_html($link_label) . ' <i class="bi bi-arrow-up-right"></i></span>';
                    echo '</div>';
                echo '</a>';
            echo '</article>';
        }
    }

    echo '</div>';

    wp_reset_postdata();

    return ob_get_clean();
}

add_shortcode('bywa_domaines', 'bywa_domaines_shortcode');