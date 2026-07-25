<?php
if (!defined('ABSPATH')) exit;

function bywa_get_realisation_gallery_ids($post_id) {
    $ids = array();

    if (has_post_thumbnail($post_id)) {
        $ids[] = get_post_thumbnail_id($post_id);
    }

    for ($i = 1; $i <= 5; $i++) {
        $gallery_id = intval(get_post_meta($post_id, '_bywa_realisation_gallery_' . $i, true));
        if (!empty($gallery_id) && !in_array($gallery_id, $ids, true)) {
            $ids[] = $gallery_id;
        }
    }

    return array_values(array_filter($ids));
}

function bywa_get_realisation_cover_html($post_id, $size = 'large', $class = '') {
    $gallery_ids = bywa_get_realisation_gallery_ids($post_id);

    if (!empty($gallery_ids)) {
        return wp_get_attachment_image($gallery_ids[0], $size, false, array(
            'class' => $class,
            'loading' => 'lazy',
        ));
    }

    return '<div class="bywa-no-image ' . esc_attr($class) . '"></div>';
}

function bywa_get_realisation_cover_attachment_id($post_id) {
    $gallery_ids = bywa_get_realisation_gallery_ids($post_id);

    if (empty($gallery_ids)) {
        return 0;
    }

    return intval($gallery_ids[0]);
}

function bywa_render_realisation_cover_carousel($post_id, $size = 'large', $class = '') {
    $gallery_ids = bywa_get_realisation_gallery_ids($post_id);

    if (empty($gallery_ids)) {
        return '<div class="bywa-no-image ' . esc_attr($class) . '"></div>';
    }

    if (count($gallery_ids) === 1) {
        return wp_get_attachment_image($gallery_ids[0], $size, false, array(
            'class' => $class,
            'loading' => 'lazy',
        ));
    }

    $html = '<div class="bywa-project-carousel" data-bywa-carousel data-interval="4200">';
    $html .= '<div class="bywa-project-carousel__slides">';

    foreach ($gallery_ids as $index => $attachment_id) {
        $slide_class = 'bywa-project-carousel__slide';

        if ($index === 0) {
            $slide_class .= ' is-active';
        }

        $html .= '<div class="' . esc_attr($slide_class) . '" data-bywa-slide aria-hidden="' . ($index === 0 ? 'false' : 'true') . '">';
        $html .= wp_get_attachment_image($attachment_id, $size, false, array(
            'class' => $class,
            'loading' => $index === 0 ? 'eager' : 'lazy',
        ));
        $html .= '</div>';
    }

    $html .= '</div>';
    $html .= '<div class="bywa-project-carousel__dots" aria-hidden="true">';

    foreach ($gallery_ids as $index => $attachment_id) {
        $dot_class = 'bywa-project-carousel__dot';

        if ($index === 0) {
            $dot_class .= ' is-active';
        }

        $html .= '<span class="' . esc_attr($dot_class) . '" data-bywa-dot></span>';
    }

    $html .= '</div>';
    $html .= '</div>';

    return $html;
}

function bywa_render_realisation_mini_gallery($post_id) {
    $gallery_ids = bywa_get_realisation_gallery_ids($post_id);

    if (count($gallery_ids) <= 1) {
        return '';
    }

    $html = '<div class="bywa-realisation-mini-gallery">';

    foreach (array_slice($gallery_ids, 0, 5) as $attachment_id) {
        $thumb = wp_get_attachment_image($attachment_id, 'thumbnail', false, array(
            'class' => 'bywa-realisation-mini-gallery__img',
            'loading' => 'lazy',
        ));

        $html .= '<div class="bywa-realisation-mini-gallery__item">' . $thumb . '</div>';
    }

    $html .= '</div>';

    return $html;
}

function bywa_render_realisation_list_gallery($post_id, $limit = 4) {
    $gallery_ids = bywa_get_realisation_gallery_ids($post_id);
    $limit = max(1, intval($limit));

    if (count($gallery_ids) <= 1) {
        return '';
    }

    $secondary_ids = array_slice($gallery_ids, 1, $limit);
    $remaining = max(0, count($gallery_ids) - 1 - count($secondary_ids));
    $html = '<div class="bywa-realisation-gallery-grid">';

    foreach ($secondary_ids as $index => $attachment_id) {
        $item_class = 'bywa-realisation-gallery-grid__item';

        if ($index === 0) {
            $item_class .= ' is-large';
        }

        $full_image_url = wp_get_attachment_image_url($attachment_id, 'full');
        $image_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);

        if (empty($image_alt)) {
            $image_alt = get_the_title($post_id);
        }

        $html .= '<button type="button" class="' . esc_attr($item_class) . '" data-bywa-modal-trigger data-bywa-modal-image="' . esc_url($full_image_url) . '" data-bywa-modal-alt="' . esc_attr($image_alt) . '">';
        $html .= wp_get_attachment_image($attachment_id, 'medium_large', false, array(
            'class' => 'bywa-realisation-gallery-grid__img',
            'loading' => 'lazy',
        ));

        if ($remaining > 0 && $index === count($secondary_ids) - 1) {
            $html .= '<span class="bywa-realisation-gallery-grid__more">+' . intval($remaining) . '</span>';
        }

        $html .= '</button>';
    }

    $html .= '</div>';

    return $html;
}

function bywa_realisations_get_allowed_per_page_options() {
    return array(5, 10, 20);
}

function bywa_realisations_normalize_per_page($value, $default = 10) {
    $value = absint($value);

    if (in_array($value, bywa_realisations_get_allowed_per_page_options(), true)) {
        return $value;
    }

    return in_array($default, bywa_realisations_get_allowed_per_page_options(), true) ? $default : 10;
}

function bywa_realisations_get_query_args($atts, $paged = 1, $per_page = 10, $type_filter = '') {
    $allowed_orderby = array('date', 'menu_order', 'title', 'rand');
    $orderby = in_array($atts['orderby'], $allowed_orderby, true) ? $atts['orderby'] : 'date';
    $order = strtoupper($atts['order']) === 'ASC' ? 'ASC' : 'DESC';
    $ids     = bywa_csv_to_array($atts['ids'], 'int');
    $include = bywa_csv_to_array($atts['include'], 'string');
    $exclude = bywa_csv_to_array($atts['exclude'], 'string');
    $types   = bywa_csv_to_array($atts['type'], 'string');

    $args = array(
        'post_type'           => 'realisation',
        'post_status'         => 'publish',
        'posts_per_page'      => $per_page,
        'paged'               => max(1, absint($paged)),
        'orderby'             => $orderby,
        'order'               => $order,
        'ignore_sticky_posts' => true,
        'no_found_rows'       => false,
    );

    if (!empty($ids)) {
        $args['post__in'] = $ids;
        $args['orderby'] = 'post__in';
    }

    if (!empty($include)) {
        $args['post_name__in'] = $include;
    }

    if (!empty($exclude)) {
        $args['post_name__not_in'] = $exclude;
    }

    if (!empty($types)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'type_realisation',
                'field'    => 'slug',
                'terms'    => $types,
            )
        );
    }

    if ($type_filter !== '') {
        $args['tax_query'] = isset($args['tax_query']) ? $args['tax_query'] : array();
        $args['tax_query'][] = array(
            'taxonomy' => 'type_realisation',
            'field'    => 'slug',
            'terms'    => array(sanitize_title($type_filter)),
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

    return $args;
}

function bywa_realisations_get_type_options($atts) {
    $allowed_types = bywa_csv_to_array($atts['type'], 'string');

    $term_args = array(
        'taxonomy'   => 'type_realisation',
        'hide_empty'  => true,
    );

    $terms = get_terms($term_args);

    if (is_wp_error($terms) || empty($terms)) {
        return array();
    }

    $options = array();

    foreach ($terms as $term) {
        if (!empty($allowed_types) && !in_array($term->slug, $allowed_types, true)) {
            continue;
        }

        $options[] = array(
            'slug'  => $term->slug,
            'label' => $term->name,
        );
    }

    return $options;
}

function bywa_realisations_build_pagination_html($current_page, $max_pages) {
    $current_page = max(1, absint($current_page));
    $max_pages = max(1, absint($max_pages));

    if ($max_pages <= 1) {
        return '';
    }

    $pages = array();
    $start = max(1, $current_page - 1);
    $end = min($max_pages, $current_page + 1);

    if ($start > 1) {
        $pages[] = 1;
        if ($start > 2) {
            $pages[] = '...';
        }
    }

    for ($i = $start; $i <= $end; $i++) {
        $pages[] = $i;
    }

    if ($end < $max_pages) {
        if ($end < $max_pages - 1) {
            $pages[] = '...';
        }
        $pages[] = $max_pages;
    }

    ob_start();
    ?>
    <nav class="bywa-realisations__pagination" aria-label="<?php esc_attr_e('Pagination des réalisations', 'bywa-content-manager'); ?>">
        <button type="button" class="bywa-realisations__page-btn" data-bywa-realisations-page="<?php echo esc_attr(max(1, $current_page - 1)); ?>" <?php disabled($current_page <= 1); ?>>
            <?php esc_html_e('Précédent', 'bywa-content-manager'); ?>
        </button>

        <div class="bywa-realisations__pages">
            <?php foreach ($pages as $page) : ?>
                <?php if ($page === '...') : ?>
                    <span class="bywa-realisations__page-ellipsis" aria-hidden="true">…</span>
                <?php else : ?>
                    <button type="button" class="bywa-realisations__page-btn<?php echo $page === $current_page ? ' is-active' : ''; ?>" data-bywa-realisations-page="<?php echo esc_attr($page); ?>" <?php echo $page === $current_page ? 'aria-current="page"' : ''; ?>>
                        <?php echo esc_html($page); ?>
                    </button>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <button type="button" class="bywa-realisations__page-btn" data-bywa-realisations-page="<?php echo esc_attr(min($max_pages, $current_page + 1)); ?>" <?php disabled($current_page >= $max_pages); ?>>
            <?php esc_html_e('Suivant', 'bywa-content-manager'); ?>
        </button>
    </nav>
    <?php

    return ob_get_clean();
}

function bywa_realisations_render_list_results($atts, $paged = 1, $per_page = 10, $type_filter = '', $view = 'list') {
    $per_page = bywa_realisations_normalize_per_page($per_page, 10);
    $gallery_limit = max(1, intval($atts['gallery_limit'] ?? 4));
    $excerpt_length = max(40, intval($atts['excerpt_length'] ?? 220));
    $link_label = !empty($atts['link_label']) ? sanitize_text_field($atts['link_label']) : 'Voir la réalisation';
    $allowed_views = array('list', 'grid', 'simple');
    $view = in_array($view, $allowed_views, true) ? $view : 'list';
    $query = new WP_Query(bywa_realisations_get_query_args($atts, $paged, $per_page, $type_filter));

    if (!$query->have_posts()) {
        wp_reset_postdata();

        return array(
            'items_html'      => '<div class="bywa-realisations bywa-realisations--' . esc_attr($view) . '"><div class="bywa-realisations__empty"><p>Aucune réalisation trouvée.</p></div></div>',
            'pagination_html' => '',
            'max_pages'       => 0,
            'found_posts'     => 0,
            'per_page'        => $per_page,
            'current_page'    => max(1, absint($paged)),
            'view'            => $view,
        );
    }

    ob_start();
    ?>
    <div class="bywa-realisations bywa-realisations--<?php echo esc_attr($view); ?>">
        <?php
        $card_index = 0;

        while ($query->have_posts()) {
            $query->the_post();
            $card_index++;

            $post_id       = get_the_ID();
            $title         = wp_strip_all_tags(get_the_title());
            $permalink     = get_permalink();
            $location      = get_post_meta($post_id, '_bywa_realisation_location', true);
            $cover_html    = bywa_get_realisation_cover_html($post_id, 'large', 'bywa-card__img');
            $gallery_html  = bywa_render_realisation_list_gallery($post_id, $gallery_limit);
            $meta_label    = bywa_get_realisation_meta_label($post_id);
            $cover_id      = bywa_get_realisation_cover_attachment_id($post_id);
            $cover_url     = $cover_id ? wp_get_attachment_image_url($cover_id, 'full') : '';
            $cover_alt     = $cover_id ? get_post_meta($cover_id, '_wp_attachment_image_alt', true) : '';
            $content_parts = bywa_get_realisation_list_content_parts($post_id, $excerpt_length);

            if (empty($location)) {
                $terms = get_the_terms($post_id, 'type_realisation');
                if (!empty($terms) && !is_wp_error($terms)) {
                    $location = $terms[0]->name;
                } else {
                    $location = 'Réalisation';
                }
            }

            if (empty($cover_alt)) {
                $cover_alt = $title;
            }

            if ($view === 'grid') {
                $grid_excerpt = !empty($content_parts['short']) ? wp_trim_words(wp_strip_all_tags($content_parts['short']), 22, '…') : '';

                echo '<article class="bywa-realisation-compact-card bywa-reveal">';
                    echo '<a class="bywa-realisation-compact-card__link" href="' . esc_url($permalink) . '">';
                        echo '<div class="bywa-realisation-compact-card__media">' . $cover_html . '</div>';
                        echo '<div class="bywa-realisation-compact-card__content">';
                            echo '<span class="bywa-realisation-compact-card__eyebrow">' . esc_html__('Réalisation locale', 'bywa-content-manager') . '</span>';
                            echo '<span class="bywa-project-city">' . esc_html($location) . '</span>';
                            if (!empty($meta_label)) {
                                echo '<span class="bywa-realisation-compact-card__meta-chip">' . esc_html($meta_label) . '</span>';
                            }
                            echo '<h3 class="bywa-card__title">' . esc_html($title) . '</h3>';
                            if (!empty($grid_excerpt)) {
                                echo '<p class="bywa-card__excerpt">' . esc_html($grid_excerpt) . '</p>';
                            }
                            echo '<span class="bywa-read-more">' . esc_html($link_label) . ' <span class="bi bi-arrow-up-right" aria-hidden="true"></span></span>';
                        echo '</div>';
                    echo '</a>';
                echo '</article>';
            } elseif ($view === 'simple') {
                $simple_excerpt = !empty($content_parts['short']) ? wp_trim_words(wp_strip_all_tags($content_parts['short']), 28, '…') : '';

                echo '<article class="bywa-realisation-simple-card bywa-reveal">';
                    echo '<div class="bywa-realisation-simple-card__content">';
                        echo '<span class="bywa-realisation-simple-card__eyebrow">' . esc_html__('Réalisation locale', 'bywa-content-manager') . '</span>';
                        echo '<div class="bywa-realisation-simple-card__meta-row">';
                            echo '<span class="bywa-project-city">' . esc_html($location) . '</span>';
                            if (!empty($meta_label)) {
                                echo '<span class="bywa-realisation-simple-card__meta-chip">' . esc_html($meta_label) . '</span>';
                            }
                        echo '</div>';
                        echo '<h3 class="bywa-card__title">' . esc_html($title) . '</h3>';
                        if (!empty($simple_excerpt)) {
                            echo '<p class="bywa-card__excerpt">' . esc_html($simple_excerpt) . '</p>';
                        }
                        echo '<div class="bywa-realisation-simple-card__footer">';
                            echo '<a href="' . esc_url($permalink) . '" class="bywa-read-more">' . esc_html($link_label) . ' <span class="bi bi-arrow-up-right" aria-hidden="true"></span></a>';
                        echo '</div>';
                    echo '</div>';
                echo '</article>';
            } else {
                $card_classes  = 'bywa-realisation-list-card';
                $card_classes .= ($card_index % 2 === 0) ? ' bywa-realisation-list-card--media-right' : ' bywa-realisation-list-card--media-left';
                $card_classes .= ' bywa-reveal';

                echo '<article class="' . esc_attr($card_classes) . '">';
                    echo '<div class="bywa-realisation-list-card__media-wrap">';
                        echo '<button type="button" class="bywa-realisation-list-card__media" aria-label="' . esc_attr(sprintf(__('Agrandir l’image de %s', 'bywa-content-manager'), $title)) . '"';
                        if (!empty($cover_url)) {
                            echo ' data-bywa-modal-trigger data-bywa-modal-image="' . esc_url($cover_url) . '" data-bywa-modal-alt="' . esc_attr($cover_alt) . '"';
                        } else {
                            echo ' disabled';
                        }
                        echo '>';
                            echo $cover_html;
                            echo '<span class="bywa-realisation-list-card__media-overlay"></span>';
                        echo '</button>';

                        echo '<div class="bywa-realisation-list-card__floating-meta">';
                            echo '<span class="bywa-project-city">' . esc_html($location) . '</span>';
                            if (!empty($meta_label)) {
                                echo '<span class="bywa-realisation-list-card__meta-chip">' . esc_html($meta_label) . '</span>';
                            }
                        echo '</div>';
                    echo '</div>';

                    echo '<div class="bywa-realisation-list-card__content">';
                        echo '<span class="bywa-realisation-list-card__eyebrow">Réalisation locale</span>';
                        echo '<h3 class="bywa-card__title">' . esc_html($title) . '</h3>';

                        if (!empty($content_parts['short'])) {
                            echo '<div class="bywa-card__excerpt bywa-realisation-list-card__excerpt" data-bywa-readmore>';
                                echo '<div class="bywa-realisation-list-card__excerpt-short">' . wpautop(esc_html($content_parts['short'])) . '</div>';

                                if (!empty($content_parts['is_truncated'])) {
                                    echo '<div class="bywa-realisation-list-card__excerpt-full bywa-entry-content" hidden>' . $content_parts['full'] . '</div>';
                                    echo ' <button type="button" class="bywa-read-more bywa-realisation-list-card__readmore-toggle" data-bywa-readmore-toggle aria-expanded="false">' . esc_html__('Lire plus', 'bywa-content-manager') . ' <span class="bi bi-plus-lg" aria-hidden="true"></span></button>';
                                } else {
                                    echo '<div class="bywa-realisation-list-card__excerpt-full bywa-entry-content" hidden>' . $content_parts['full'] . '</div>';
                                }
                            echo '</div>';
                        }

                        if (!empty($gallery_html)) {
                            echo '<div class="bywa-realisation-list-card__gallery-wrap">';
                                echo '<span class="bywa-realisation-list-card__gallery-label">Vues du chantier</span>';
                                echo $gallery_html;
                            echo '</div>';
                        }
                    echo '</div>';
                echo '</article>';
            }
        }
        ?>
    </div>
    <?php
    wp_reset_postdata();

    return array(
        'items_html'      => ob_get_clean(),
        'pagination_html' => bywa_realisations_build_pagination_html($paged, $query->max_num_pages),
        'max_pages'       => (int) $query->max_num_pages,
        'found_posts'     => (int) $query->found_posts,
        'per_page'        => $per_page,
        'current_page'    => max(1, absint($paged)),
        'view'            => $view,
    );
}

function bywa_get_realisation_meta_label($post_id) {
    $date = trim((string) get_post_meta($post_id, '_bywa_realisation_date', true));
    $client = trim((string) get_post_meta($post_id, '_bywa_realisation_client', true));

    if (!empty($date)) {
        return $date;
    }

    if (!empty($client)) {
        return $client;
    }

    return '';
}

function bywa_get_realisation_excerpt_parts($text, $length = 220) {
    $text = wp_strip_all_tags((string) $text, true);
    $text = trim(preg_replace('/\s+/', ' ', $text));
    $length = max(40, intval($length));

    if ($text === '') {
        return array(
            'short' => '',
            'full' => '',
            'is_truncated' => false,
        );
    }

    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        $is_truncated = mb_strlen($text) > $length;

        if (!$is_truncated) {
            return array(
                'short' => $text,
                'full' => $text,
                'is_truncated' => false,
            );
        }

        $short = mb_substr($text, 0, $length);
        $last_space = mb_strrpos($short, ' ');

        if ($last_space !== false) {
            $short = mb_substr($short, 0, $last_space);
        }
    } else {
        $is_truncated = strlen($text) > $length;

        if (!$is_truncated) {
            return array(
                'short' => $text,
                'full' => $text,
                'is_truncated' => false,
            );
        }

        $short = substr($text, 0, $length);
        $last_space = strrpos($short, ' ');

        if ($last_space !== false) {
            $short = substr($short, 0, $last_space);
        }
    }

    return array(
        'short' => rtrim($short, " \t\n\r\0\x0B.,;:!?") . '…',
        'full' => $text,
        'is_truncated' => true,
    );
}

function bywa_get_realisation_list_content_parts($post_id, $length = 220) {
    $raw_content = get_post_field('post_content', $post_id);
    $full_html = apply_filters('the_content', $raw_content);
    $preview_text = wp_strip_all_tags(strip_shortcodes($raw_content), true);
    $preview_parts = bywa_get_realisation_excerpt_parts($preview_text, $length);

    if ($preview_parts['short'] === '' && $full_html !== '') {
        $preview_parts['short'] = wp_strip_all_tags($full_html, true);
        $preview_parts['full'] = $full_html;
        $preview_parts['is_truncated'] = false;
    } elseif ($preview_parts['short'] === '') {
        $fallback_text = get_post_meta($post_id, '_bywa_realisation_short_text', true);

        if ($fallback_text === '') {
            $fallback_text = get_the_excerpt($post_id);
        }

        $preview_parts = bywa_get_realisation_excerpt_parts($fallback_text, $length);
        $preview_parts['full'] = wpautop(esc_html($fallback_text));
    } else {
        $preview_parts['full'] = $full_html;
    }

    return $preview_parts;
}

function bywa_realisations_shortcode($atts) {
    $atts = shortcode_atts(array(
        'template' => 'projects',
        'limit'    => 6,
        'ids'      => '',
        'include'  => '',
        'exclude'  => '',
        'type'     => '',
        'featured' => '',
        'orderby'  => 'date',
        'order'    => 'DESC',
        'gallery_limit' => 4,
        'link_label' => 'Voir la réalisation',
        'excerpt_length' => 220,
        'archive_link' => '',
        'view' => 'list',
    ), $atts, 'bywa_realisations');

    $allowed_templates = array('projects', 'list', 'grid', 'tiles', 'featured');
    $template = in_array($atts['template'], $allowed_templates, true) ? $atts['template'] : 'projects';

    $allowed_orderby = array('date', 'menu_order', 'title', 'rand');
    $orderby = in_array($atts['orderby'], $allowed_orderby, true) ? $atts['orderby'] : 'date';

    $order = strtoupper($atts['order']) === 'ASC' ? 'ASC' : 'DESC';
    $gallery_limit = max(1, intval($atts['gallery_limit']));
    $excerpt_length = max(40, intval($atts['excerpt_length']));
    $link_label = !empty($atts['link_label']) ? sanitize_text_field($atts['link_label']) : 'Voir la réalisation';
    $show_archive_link = ($atts['archive_link'] === 'yes');
    $allowed_views = array('list', 'grid', 'simple');
    $default_view = in_array($atts['view'], $allowed_views, true) ? $atts['view'] : 'list';

    $ids     = bywa_csv_to_array($atts['ids'], 'int');
    $include = bywa_csv_to_array($atts['include'], 'string');
    $exclude = bywa_csv_to_array($atts['exclude'], 'string');
    $types   = bywa_csv_to_array($atts['type'], 'string');

    $args = array(
        'post_type'           => 'realisation',
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

    if (!empty($types)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'type_realisation',
                'field'    => 'slug',
                'terms'    => $types,
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

    if ($template !== 'list') {
        $query = new WP_Query($args);

        if (!$query->have_posts()) {
            return '';
        }
    }

    ob_start();

    if ($template === 'list') {
        $current_per_page = bywa_realisations_normalize_per_page(!empty($atts['per_page']) ? $atts['per_page'] : $atts['limit'], 10);
        $type_options = bywa_realisations_get_type_options($atts);
        $results = bywa_realisations_render_list_results($atts, 1, $current_per_page, '', $default_view);
        $ajax_config = array(
            'template'       => $template,
            'ids'            => $atts['ids'],
            'include'        => $atts['include'],
            'exclude'        => $atts['exclude'],
            'type'           => $atts['type'],
            'featured'       => $atts['featured'],
            'orderby'        => $atts['orderby'],
            'order'          => $atts['order'],
            'gallery_limit'   => $atts['gallery_limit'],
            'link_label'     => $link_label,
            'excerpt_length' => $excerpt_length,
            'archive_link'   => $show_archive_link ? 'yes' : '',
            'view'           => $default_view,
        );
        ?>
        <div class="bywa-realisations-browser" data-bywa-realisations-root data-bywa-realisations-view="<?php echo esc_attr($default_view); ?>" data-bywa-realisations-current-page="<?php echo esc_attr((int) $results['current_page']); ?>" data-bywa-realisations-config="<?php echo esc_attr(wp_json_encode($ajax_config)); ?>">
            <div class="bywa-realisations-browser__filters">
                <div class="bywa-realisations-browser__field">
                    <label for="bywa-realisations-per-page"><?php esc_html_e('Résultats par page', 'bywa-content-manager'); ?></label>
                    <select id="bywa-realisations-per-page" data-bywa-realisations-per-page>
                        <?php foreach (bywa_realisations_get_allowed_per_page_options() as $option) : ?>
                            <option value="<?php echo esc_attr($option); ?>" <?php selected($current_per_page, $option); ?>><?php echo esc_html($option); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="bywa-realisations-browser__field bywa-realisations-browser__view-switch" role="group" aria-label="<?php esc_attr_e('Changer de vue', 'bywa-content-manager'); ?>">
                    <span class="bywa-realisations-browser__field-label"><?php esc_html_e('Vue', 'bywa-content-manager'); ?></span>
                    <div class="bywa-realisations-browser__view-switch-inner">
                        <button type="button" class="bywa-realisations-browser__view-btn" data-bywa-realisations-view-btn="list" aria-pressed="<?php echo $default_view === 'list' ? 'true' : 'false'; ?>">
                            <span class="bi bi-list-ul" aria-hidden="true"></span>
                            <span class="screen-reader-text"><?php esc_html_e('Vue actuelle avec image', 'bywa-content-manager'); ?></span>
                        </button>
                        <button type="button" class="bywa-realisations-browser__view-btn" data-bywa-realisations-view-btn="grid" aria-pressed="<?php echo $default_view === 'grid' ? 'true' : 'false'; ?>">
                            <span class="bi bi-grid-3x3-gap-fill" aria-hidden="true"></span>
                            <span class="screen-reader-text"><?php esc_html_e('Vue grille compacte', 'bywa-content-manager'); ?></span>
                        </button>
                        <button type="button" class="bywa-realisations-browser__view-btn" data-bywa-realisations-view-btn="simple" aria-pressed="<?php echo $default_view === 'simple' ? 'true' : 'false'; ?>">
                            <span class="bi bi-card-list" aria-hidden="true"></span>
                            <span class="screen-reader-text"><?php esc_html_e('Vue liste simple sans image', 'bywa-content-manager'); ?></span>
                        </button>
                    </div>
                </div>

                <?php if (!empty($type_options)) : ?>
                    <div class="bywa-realisations-browser__field">
                        <label for="bywa-realisations-type"><?php esc_html_e('Type de réalisation', 'bywa-content-manager'); ?></label>
                        <select id="bywa-realisations-type" data-bywa-realisations-type>
                            <option value=""><?php esc_html_e('Tous les types', 'bywa-content-manager'); ?></option>
                            <?php foreach ($type_options as $type_option) : ?>
                                <option value="<?php echo esc_attr($type_option['slug']); ?>"><?php echo esc_html($type_option['label']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
            </div>

            <div class="bywa-realisations-browser__results" data-bywa-realisations-results>
                <div class="bywa-realisations-browser__list" data-bywa-realisations-list>
                    <?php echo $results['items_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>

                <div class="bywa-realisations-browser__pagination" data-bywa-realisations-pagination>
                    <?php echo $results['pagination_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            </div>

            <div class="bywa-media-modal" data-bywa-media-modal hidden aria-hidden="true">
                <button type="button" class="bywa-media-modal__backdrop" data-bywa-modal-close aria-label="<?php esc_attr_e('Fermer la fenêtre', 'bywa-content-manager'); ?>"></button>
                <div class="bywa-media-modal__dialog" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Image agrandie', 'bywa-content-manager'); ?>">
                    <button type="button" class="bywa-media-modal__close" data-bywa-modal-close aria-label="<?php esc_attr_e('Fermer la fenêtre', 'bywa-content-manager'); ?>"><span class="bi bi-x-lg" aria-hidden="true"></span></button>
                    <img class="bywa-media-modal__image" data-bywa-modal-image-target src="" alt="">
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    if ($template === 'projects') {
        echo '<div class="bywa-projects">';
        echo '<div class="row g-4">';

        while ($query->have_posts()) {
            $query->the_post();

            $post_id    = get_the_ID();
            $title      = get_the_title();
            $permalink  = get_permalink();
            $excerpt    = get_post_meta($post_id, '_bywa_realisation_short_text', true);
            $location   = get_post_meta($post_id, '_bywa_realisation_location', true);
            $cover_html = bywa_render_realisation_cover_carousel($post_id, 'large', 'bywa-card__img');

            if (empty($excerpt)) {
                $excerpt = get_the_excerpt();
            }

            if (empty($location)) {
                $terms = get_the_terms($post_id, 'type_realisation');
                if (!empty($terms) && !is_wp_error($terms)) {
                    $location = $terms[0]->name;
                } else {
                    $location = 'Réalisation';
                }
            }

            echo '<div class="col-lg-4">';
                echo '<article class="bywa-project-card">';
                    echo '<div class="bywa-project-thumb">';
                        echo '<a href="' . esc_url($permalink) . '" aria-label="' . esc_attr($title) . '">';
                            echo $cover_html;
                        echo '</a>';
                    echo '</div>';

                    echo '<div class="bywa-project-body">';
                        echo '<span class="bywa-project-city">' . esc_html($location) . '</span>';
                        echo '<h3>' . esc_html($title) . '</h3>';

                        if (!empty($excerpt)) {
                            echo '<p>' . esc_html($excerpt) . '</p>';
                        }

                        echo '<a href="' . esc_url($permalink) . '" class="bywa-read-more">' . esc_html($link_label) . ' <span class="bi bi-arrow-up-right"></span></a>';
                    echo '</div>';
                echo '</article>';
            echo '</div>';
        }

        echo '</div>';
        echo '</div>';
    } elseif ($template === 'list') {
        echo '<div class="bywa-realisations bywa-realisations--list">';

        $card_index = 0;

        while ($query->have_posts()) {
            $query->the_post();
            $card_index++;

            $post_id       = get_the_ID();
            $title         = wp_strip_all_tags(get_the_title());
            $permalink     = get_permalink();
            $location      = get_post_meta($post_id, '_bywa_realisation_location', true);
            $cover_html    = bywa_get_realisation_cover_html($post_id, 'large', 'bywa-card__img');
            $gallery_html  = bywa_render_realisation_list_gallery($post_id, $gallery_limit);
            $meta_label    = bywa_get_realisation_meta_label($post_id);
            $cover_id      = bywa_get_realisation_cover_attachment_id($post_id);
            $cover_url     = $cover_id ? wp_get_attachment_image_url($cover_id, 'full') : '';
            $cover_alt     = $cover_id ? get_post_meta($cover_id, '_wp_attachment_image_alt', true) : '';
            $content_parts = bywa_get_realisation_list_content_parts($post_id, $excerpt_length);
            $card_classes  = 'bywa-realisation-list-card';
            $card_classes .= ($card_index % 2 === 0) ? ' bywa-realisation-list-card--media-right' : ' bywa-realisation-list-card--media-left';
            $card_classes .= ' bywa-reveal';

            if (empty($location)) {
                $terms = get_the_terms($post_id, 'type_realisation');
                if (!empty($terms) && !is_wp_error($terms)) {
                    $location = $terms[0]->name;
                } else {
                    $location = 'Réalisation';
                }
            }

            if (empty($cover_alt)) {
                $cover_alt = $title;
            }

            echo '<article class="' . esc_attr($card_classes) . '">';
                echo '<div class="bywa-realisation-list-card__media-wrap">';
                    echo '<button type="button" class="bywa-realisation-list-card__media" aria-label="' . esc_attr(sprintf(__('Agrandir l’image de %s', 'bywa-content-manager'), $title)) . '"';
                    if (!empty($cover_url)) {
                        echo ' data-bywa-modal-trigger data-bywa-modal-image="' . esc_url($cover_url) . '" data-bywa-modal-alt="' . esc_attr($cover_alt) . '"';
                    } else {
                        echo ' disabled';
                    }
                    echo '>';
                        echo $cover_html;
                        echo '<span class="bywa-realisation-list-card__media-overlay"></span>';
                    echo '</button>';

                    echo '<div class="bywa-realisation-list-card__floating-meta">';
                        echo '<span class="bywa-project-city">' . esc_html($location) . '</span>';
                        if (!empty($meta_label)) {
                            echo '<span class="bywa-realisation-list-card__meta-chip">' . esc_html($meta_label) . '</span>';
                        }
                    echo '</div>';
                echo '</div>';

                echo '<div class="bywa-realisation-list-card__content">';
                    echo '<span class="bywa-realisation-list-card__eyebrow">Réalisation locale</span>';
                    echo '<h3 class="bywa-card__title">' . esc_html($title) . '</h3>';

                    if (!empty($content_parts['short'])) {
                        echo '<div class="bywa-card__excerpt bywa-realisation-list-card__excerpt" data-bywa-readmore>';
                            echo '<div class="bywa-realisation-list-card__excerpt-short">' . wpautop(esc_html($content_parts['short'])) . '</div>';

                            if (!empty($content_parts['is_truncated'])) {
                                echo '<div class="bywa-realisation-list-card__excerpt-full bywa-entry-content" hidden>' . $content_parts['full'] . '</div>';
                                echo ' <button type="button" class="bywa-read-more bywa-realisation-list-card__readmore-toggle" data-bywa-readmore-toggle aria-expanded="false">' . esc_html__('Lire plus', 'bywa-content-manager') . ' <span class="bi bi-plus-lg" aria-hidden="true"></span></button>';
                            } else {
                                echo '<div class="bywa-realisation-list-card__excerpt-full bywa-entry-content" hidden>' . $content_parts['full'] . '</div>';
                            }
                        echo '</div>';
                    }

                    if (!empty($gallery_html)) {
                        echo '<div class="bywa-realisation-list-card__gallery-wrap">';
                            echo '<span class="bywa-realisation-list-card__gallery-label">Vues du chantier</span>';
                            echo $gallery_html;
                        echo '</div>';
                    }

                    if ($show_archive_link) {
                        echo '<div class="bywa-realisation-list-card__footer">';
                            echo '<a href="' . esc_url($permalink) . '" class="bywa-read-more">' . esc_html($link_label) . ' <span class="bi bi-arrow-up-right"></span></a>';
                        echo '</div>';
                    }
                echo '</div>';
            echo '</article>';
        }

        echo '</div>';
        echo '<div class="bywa-media-modal" data-bywa-media-modal hidden aria-hidden="true">';
            echo '<button type="button" class="bywa-media-modal__backdrop" data-bywa-modal-close aria-label="' . esc_attr__('Fermer la fenêtre', 'bywa-content-manager') . '"></button>';
            echo '<div class="bywa-media-modal__dialog" role="dialog" aria-modal="true" aria-label="' . esc_attr__('Image agrandie', 'bywa-content-manager') . '">';
                echo '<button type="button" class="bywa-media-modal__close" data-bywa-modal-close aria-label="' . esc_attr__('Fermer la fenêtre', 'bywa-content-manager') . '"><span class="bi bi-x-lg" aria-hidden="true"></span></button>';
                echo '<img class="bywa-media-modal__image" data-bywa-modal-image-target src="" alt="">';
            echo '</div>';
        echo '</div>';
    } else {
        echo '<div class="bywa-realisations bywa-realisations--' . esc_attr($template) . '">';

        $index = 0;

        while ($query->have_posts()) {
            $query->the_post();
            $index++;

            $post_id    = get_the_ID();
            $title      = get_the_title();
            $permalink  = get_permalink();
            $excerpt    = get_post_meta($post_id, '_bywa_realisation_short_text', true);
            $cover_html = bywa_get_realisation_cover_html($post_id, 'large', 'bywa-card__img');

            if (empty($excerpt)) {
                $excerpt = get_the_excerpt();
            }

            if ($template === 'featured' && $index === 1) {
                echo '<article class="bywa-realisation-card bywa-realisation-card--featured">';
                    echo '<div class="bywa-realisation-card__media">' . $cover_html . '</div>';
                    echo '<div class="bywa-realisation-card__content">';
                        echo '<h3 class="bywa-card__title">' . esc_html($title) . '</h3>';
                        if (!empty($excerpt)) {
                            echo '<p class="bywa-card__excerpt">' . esc_html($excerpt) . '</p>';
                        }
                        echo bywa_render_button($permalink, 'Voir le projet');
                    echo '</div>';
                echo '</article>';
                continue;
            }

            if ($template === 'tiles') {
                echo '<article class="bywa-realisation-card bywa-realisation-card--tiles">';
                    echo '<a class="bywa-realisation-card__link" href="' . esc_url($permalink) . '">';
                        echo '<div class="bywa-realisation-card__media">' . $cover_html . '</div>';
                        echo '<div class="bywa-realisation-card__overlay">';
                            echo '<h3 class="bywa-card__title">' . esc_html($title) . '</h3>';
                            echo '<span class="bywa-readmore">Voir le projet</span>';
                        echo '</div>';
                    echo '</a>';
                echo '</article>';
            } else {
                echo '<article class="bywa-realisation-card bywa-realisation-card--grid">';
                    echo '<div class="bywa-realisation-card__media">' . $cover_html . '</div>';
                    echo '<div class="bywa-realisation-card__content">';
                        echo '<h3 class="bywa-card__title">' . esc_html($title) . '</h3>';
                        if (!empty($excerpt)) {
                            echo '<p class="bywa-card__excerpt">' . esc_html($excerpt) . '</p>';
                        }
                        echo bywa_render_button($permalink, 'Voir le projet');
                    echo '</div>';
                echo '</article>';
            }
        }

        echo '</div>';
    }

    wp_reset_postdata();

    return ob_get_clean();
}

add_shortcode('bywa_realisations', 'bywa_realisations_shortcode');

function bywa_realisations_list_shortcode($atts) {
    $atts = shortcode_atts(array(
        'template' => 'list',
    ), $atts, 'bywa_realisations_list');

    $atts['template'] = 'list';

    return bywa_realisations_shortcode($atts);
}

add_shortcode('bywa_realisations_list', 'bywa_realisations_list_shortcode');

function bywa_realisations_ajax_filter() {
    check_ajax_referer('bywa_realisations_filter', 'nonce');

    $config = array();
    if (!empty($_POST['config'])) {
        $decoded = json_decode(wp_unslash($_POST['config']), true);
        if (is_array($decoded)) {
            $config = $decoded;
        }
    }

    $atts = shortcode_atts(array(
        'template' => 'list',
        'limit'    => 10,
        'ids'      => '',
        'include'  => '',
        'exclude'  => '',
        'type'     => '',
        'featured' => '',
        'orderby'  => 'date',
        'order'    => 'DESC',
        'gallery_limit' => 4,
        'link_label' => 'Voir la réalisation',
        'excerpt_length' => 220,
        'archive_link' => '',
        'view' => 'list',
    ), $config, 'bywa_realisations');

    $page = isset($_POST['page']) ? absint($_POST['page']) : 1;
    $per_page = isset($_POST['per_page']) ? absint($_POST['per_page']) : 10;
    $type_filter = isset($_POST['type_filter']) ? sanitize_title(wp_unslash($_POST['type_filter'])) : '';
    $view = isset($_POST['view']) ? sanitize_key(wp_unslash($_POST['view'])) : 'list';

    $results = bywa_realisations_render_list_results($atts, $page, $per_page, $type_filter, $view);

    wp_send_json_success(array(
        'items_html'      => $results['items_html'],
        'pagination_html' => $results['pagination_html'],
        'current_page'    => $results['current_page'],
        'per_page'        => $results['per_page'],
        'max_pages'       => $results['max_pages'],
        'view'            => $results['view'],
    ));
}
add_action('wp_ajax_bywa_realisations_filter', 'bywa_realisations_ajax_filter');
add_action('wp_ajax_nopriv_bywa_realisations_filter', 'bywa_realisations_ajax_filter');
