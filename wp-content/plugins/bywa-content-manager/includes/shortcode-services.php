<?php
if (!defined('ABSPATH')) exit;

function bywa_services_shortcode($atts) {
    $atts = shortcode_atts(array(
        'template' => 'tiles',
        'limit'    => 6,
        'ids'      => '',
        'include'  => '',
        'exclude'  => '',
        'group'    => '',
        'featured' => '',
        'orderby'  => 'menu_order',
        'order'    => 'ASC',
    ), $atts, 'bywa_services');

    $allowed_templates = array('light', 'tiles', 'list', 'large', 'showcase');
    $template = in_array($atts['template'], $allowed_templates, true) ? $atts['template'] : 'tiles';

    $allowed_orderby = array('menu_order', 'title', 'date', 'rand');
    $orderby = in_array($atts['orderby'], $allowed_orderby, true) ? $atts['orderby'] : 'menu_order';

    $order = strtoupper($atts['order']) === 'DESC' ? 'DESC' : 'ASC';

    $ids     = bywa_csv_to_array($atts['ids'], 'int');
    $include = bywa_csv_to_array($atts['include'], 'string');
    $exclude = bywa_csv_to_array($atts['exclude'], 'string');
    $groups  = bywa_csv_to_array($atts['group'], 'string');

    $args = array(
        'post_type'           => 'service',
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

    $tax_query = array();

    if (!empty($groups)) {
        $tax_query[] = array(
            'taxonomy' => 'service_group',
            'field'    => 'slug',
            'terms'    => $groups,
        );
    }

    if (!empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }

    $meta_query = array();

    if ($atts['featured'] === 'yes') {
        $meta_query[] = array(
            'key'   => '_bywa_featured',
            'value' => '1',
        );
    }

    if (!empty($meta_query)) {
        $args['meta_query'] = $meta_query;
    }

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        return '';
    }

    ob_start();

    echo '<div class="bywa-services bywa-services--' . esc_attr($template) . '">';

    $card_index = 0;

    while ($query->have_posts()) {
        $query->the_post();
        $card_index++;

        $post_id         = get_the_ID();
        $title           = get_the_title();
        $permalink       = get_permalink();
        $image_html      = bywa_get_post_image_html($post_id, 'large', 'bywa-card__img');
        $service_icon    = get_post_meta($post_id, '_bywa_service_icon', true);
        $service_excerpt = get_post_meta($post_id, '_bywa_service_short_text', true);

        if (empty($service_excerpt)) {
            $service_excerpt = get_the_excerpt();
        }

        if (empty($service_icon)) {
            $service_icon = 'bi-lightning-charge-fill';
        }

        if ($template === 'showcase') {
            $content = get_post_field('post_content', $post_id);
            $content_preview = wp_strip_all_tags(strip_shortcodes($content), true);

            if (empty($content_preview)) {
                $content_preview = $service_excerpt;
            }

            $card_classes  = 'bywa-service-showcase-card bywa-reveal';
            $card_classes .= ($card_index % 2 === 0) ? ' bywa-service-showcase-card--media-right' : ' bywa-service-showcase-card--media-left';

            echo '<article class="' . esc_attr($card_classes) . '">';
                echo '<div class="bywa-service-showcase-card__media-wrap">';
                    echo '<a class="bywa-service-showcase-card__media" href="' . esc_url($permalink) . '" aria-label="' . esc_attr($title) . '">';
                        echo $image_html;
                        echo '<span class="bywa-service-showcase-card__media-overlay"></span>';
                    echo '</a>';

                    echo '<div class="bywa-service-showcase-card__floating-meta">';
                        echo '<span class="bywa-service-showcase-card__icon"><i class="bi ' . esc_attr($service_icon) . '"></i></span>';
                    echo '</div>';
                echo '</div>';

                echo '<div class="bywa-service-showcase-card__content">';
                    echo '<span class="bywa-service-showcase-card__eyebrow">Serviciu</span>';
                    echo '<h3 class="bywa-card__title">' . esc_html($title) . '</h3>';

                    if (!empty($content_preview)) {
                        echo '<p class="bywa-card__excerpt">' . esc_html(wp_trim_words($content_preview, 36, '…')) . '</p>';
                    }

                    echo '<div class="bywa-service-showcase-card__footer">';
                        echo '<a href="' . esc_url($permalink) . '" class="bywa-read-more">Descoperă serviciul <span class="bi bi-arrow-up-right"></span></a>';
                    echo '</div>';
                echo '</div>';
            echo '</article>';

            continue;
        }

        if ($template === 'light') {
            echo '<article class="bywa-service-card bywa-service-card--light">';
                echo '<div class="bywa-service-card__content">';
                    echo '<h3 class="bywa-card__title">' . esc_html($title) . '</h3>';
                    if (!empty($service_excerpt)) {
                        echo '<p class="bywa-card__excerpt">' . esc_html($service_excerpt) . '</p>';
                    }
                    echo bywa_render_button($permalink, 'Vezi mai mult');
                echo '</div>';
            echo '</article>';
        }

        elseif ($template === 'list') {
            echo '<article class="bywa-service-card bywa-service-card--list">';
                echo '<div class="bywa-service-card__media">' . $image_html . '</div>';
                echo '<div class="bywa-service-card__content">';
                    echo '<h3 class="bywa-card__title">' . esc_html($title) . '</h3>';
                    if (!empty($service_excerpt)) {
                        echo '<p class="bywa-card__excerpt">' . esc_html($service_excerpt) . '</p>';
                    }
                    echo bywa_render_button($permalink, 'Vezi mai mult');
                echo '</div>';
            echo '</article>';
        }

        elseif ($template === 'large') {
            echo '<article class="bywa-service-card bywa-service-card--large">';
                echo '<div class="bywa-service-card__media">' . $image_html . '</div>';
                echo '<div class="bywa-service-card__content">';
                    echo '<h3 class="bywa-card__title">' . esc_html($title) . '</h3>';
                    if (!empty($service_excerpt)) {
                        echo '<p class="bywa-card__excerpt">' . esc_html($service_excerpt) . '</p>';
                    }
                    echo bywa_render_button($permalink, 'Descoperă serviciul');
                echo '</div>';
            echo '</article>';
        }

        else {
            echo '<article class="bywa-service-card bywa-service-card--tiles">';
                echo '<a class="bywa-service-card__link" href="' . esc_url($permalink) . '">';
                    echo '<div class="bywa-service-card__media">';
                        echo $image_html;
                    echo '</div>';

                    echo '<div class="bywa-service-card__icon">';
                        echo '<i class="bi ' . esc_attr($service_icon) . '"></i>';
                    echo '</div>';

                    echo '<div class="bywa-service-card__body">';
                        echo '<h3 class="bywa-card__title">' . esc_html($title) . '</h3>';
                        if (!empty($service_excerpt)) {
                            echo '<p class="bywa-card__excerpt">' . esc_html($service_excerpt) . '</p>';
                        }
                        echo '<span class="bywa-readmore">Vezi mai mult <i class="bi bi-arrow-up-right"></i></span>';
                    echo '</div>';
                echo '</a>';
            echo '</article>';
        }
    }

    echo '</div>';

    wp_reset_postdata();

    return ob_get_clean();
}

add_shortcode('bywa_services', 'bywa_services_shortcode');
