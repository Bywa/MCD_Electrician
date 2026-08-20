<?php
if (!defined('ABSPATH')) {
    exit;
}

function bywa_tm_get_shortcode_query($atts) {
    $allowed_orderby = array('menu_order', 'title', 'date', 'rand');
    $orderby = in_array($atts['orderby'], $allowed_orderby, true) ? $atts['orderby'] : 'menu_order';
    $order = strtoupper($atts['order']) === 'DESC' ? 'DESC' : 'ASC';

    $ids = bywa_tm_csv_to_array($atts['ids'], 'int');
    $include = bywa_tm_csv_to_array($atts['include'], 'string');

    $args = array(
        'post_type'           => 'team_section',
        'post_status'         => 'publish',
        'posts_per_page'      => intval($atts['limit']),
        'orderby'             => $orderby,
        'order'               => $order,
        'ignore_sticky_posts' => true,
    );

    if (!empty($ids)) {
        $args['post__in'] = $ids;
        $args['orderby'] = 'post__in';
    }

    if (!empty($include)) {
        $args['post_name__in'] = $include;
    }

    return new WP_Query($args);
}

function bywa_tm_render_gallery($gallery_ids) {
    if (empty($gallery_ids)) {
        ob_start();
        ?>
        <div class="bywa-team-showcase__visual bywa-reveal">
            <div class="bywa-team-slider bywa-team-slider--empty">
                <div class="bywa-team-slider__empty">
                    <span><?php esc_html_e('Adaugă până la 5 fotografii ale echipei', 'bywa-team-manager'); ?></span>
                    <strong><?php esc_html_e('Galeria echipei', 'bywa-team-manager'); ?></strong>
                </div>
            </div>
        </div>
        <?php

        return ob_get_clean();
    }

    ob_start();
    ?>
    <div class="bywa-team-showcase__visual bywa-reveal">
        <div class="bywa-team-slider" data-bywa-team-slider data-autoplay-delay="5000">
            <div class="bywa-team-slider__viewport">
                <?php foreach ($gallery_ids as $index => $attachment_id) : ?>
                    <figure class="bywa-team-slider__slide<?php echo 0 === $index ? ' is-active' : ''; ?>" data-bywa-team-slide>
                        <?php
                        echo wp_get_attachment_image(
                            $attachment_id,
                            'large',
                            false,
                            array(
                                'class'   => 'bywa-team-slider__image',
                                'loading' => 0 === $index ? 'eager' : 'lazy',
                            )
                        );
                        ?>
                    </figure>
                <?php endforeach; ?>
            </div>

            <?php if (count($gallery_ids) > 1) : ?>
                <div class="bywa-team-slider__controls">
                    <button type="button" class="bywa-team-slider__arrow bywa-team-slider__arrow--prev" data-bywa-team-prev aria-label="<?php esc_attr_e('Fotografia anterioară', 'bywa-team-manager'); ?>">‹</button>
                    <div class="bywa-team-slider__dots" aria-label="<?php esc_attr_e('Navigarea sliderului echipei', 'bywa-team-manager'); ?>">
                        <?php foreach ($gallery_ids as $index => $attachment_id) : ?>
                            <button type="button" class="bywa-team-slider__dot<?php echo 0 === $index ? ' is-active' : ''; ?>" data-bywa-team-dot="<?php echo esc_attr($index); ?>" aria-label="<?php echo esc_attr(sprintf(__('Mergi la fotografia %d', 'bywa-team-manager'), $index + 1)); ?>"></button>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="bywa-team-slider__arrow bywa-team-slider__arrow--next" data-bywa-team-next aria-label="<?php esc_attr_e('Fotografia următoare', 'bywa-team-manager'); ?>">›</button>
                </div>
            <?php endif; ?>
        </div>

    </div>
    <?php

    return ob_get_clean();
}

function bywa_tm_render_presentation_section($post_id, $args = array()) {
    $title = get_the_title($post_id);
    $content = get_post_field('post_content', $post_id);
    $presentation = bywa_tm_get_team_presentation($post_id);
    $members = bywa_tm_get_team_members($post_id);
    $gallery_ids = $presentation['gallery_ids'];
    $use_member_gallery_fallback = empty($gallery_ids);
    $company_phone = function_exists('bywa_eco_get_contact_phone') ? bywa_eco_get_contact_phone() : get_theme_mod('bywa_eco_phone', '0759 670 711');
    $company_phone_secondary = function_exists('bywa_eco_get_contact_phone_secondary') ? bywa_eco_get_contact_phone_secondary() : '';
    $company_email = function_exists('bywa_eco_get_contact_email') ? bywa_eco_get_contact_email() : 'info@mcdelectrician.ro';
    $phone_link = function_exists('bywa_eco_get_tel_link') ? bywa_eco_get_tel_link($company_phone) : preg_replace('/[^0-9+]/', '', (string) $company_phone);
    $phone_secondary_link = function_exists('bywa_eco_get_tel_link') ? bywa_eco_get_tel_link($company_phone_secondary) : preg_replace('/[^0-9+]/', '', (string) $company_phone_secondary);
    $contact_url = home_url('/contact');

    foreach ($members as $member) {
        if ($use_member_gallery_fallback && !empty($member['photo_id'])) {
            $gallery_ids[] = absint($member['photo_id']);
        }
    }

    $gallery_ids = array_slice(array_values(array_unique(array_filter($gallery_ids))), 0, 5);
    $button_label = !empty($presentation['button_label']) ? $presentation['button_label'] : '';
    $button_url = !empty($presentation['button_url']) ? $presentation['button_url'] : '';
    $show_button = !empty($button_url);
    $showcase_class = !empty($args['standalone']) ? ' bywa-team-showcase--standalone' : '';
    $has_banner = false;

    for ($i = 1; $i <= 5; $i++) {
        if ((int) get_post_meta($post_id, '_bywa_hero_image_' . $i, true) > 0) {
            $has_banner = true;
            break;
        }
    }

    if (!$has_banner) {
        foreach (array('_bywa_hero_kicker', '_bywa_hero_title', '_bywa_hero_text', '_bywa_hero_action_1_label', '_bywa_hero_action_1_url', '_bywa_hero_action_2_label', '_bywa_hero_action_2_url', '_bywa_hero_points') as $meta_key) {
            if (trim((string) get_post_meta($post_id, $meta_key, true)) !== '') {
                $has_banner = true;
                break;
            }
        }
    }

    ob_start();
    ?>
    <?php if ($has_banner && function_exists('bywa_eco_render_page_hero')) : ?>
        <?php bywa_eco_render_page_hero($post_id, 'mini'); ?>
    <?php endif; ?>

    <section class="bywa-team-showcase<?php echo esc_attr($showcase_class); ?>">
        <div class="container">
            <div class="row align-items-stretch g-5">
                <div class="col-lg-4">
                    <div class="bywa-team-showcase__content">
                        <span class="bywa-section-kicker"><?php echo esc_html($presentation['kicker']); ?></span>
                        <h2 class="bywa-team-showcase__title"><?php echo esc_html($title); ?></h2>

                        <?php if (!empty($content)) : ?>
                            <div class="bywa-team-showcase__text" data-bywa-team-description>
                                <div class="bywa-team-showcase__text-inner" data-bywa-team-description-inner>
                                    <?php echo apply_filters('the_content', $content); ?>
                                </div>
                            </div>
                            <button type="button" class="bywa-team-showcase__more" data-bywa-team-description-toggle><?php esc_html_e('Vezi mai mult', 'bywa-team-manager'); ?></button>
                        <?php endif; ?>

                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="bywa-team-showcase__media-wrap">
                        <?php echo bywa_tm_render_gallery($gallery_ids); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <div class="bywa-team-showcase__actions">
                            <?php if (!empty($phone_link)) : ?>
                                <a class="bywa-btn bywa-btn-outline-dark" href="<?php echo esc_url($phone_link); ?>"><?php echo esc_html($company_phone); ?></a>
                            <?php endif; ?>

                            <?php if (!empty($company_email)) : ?>
                                <a class="bywa-btn bywa-btn-outline-dark" href="mailto:<?php echo esc_attr($company_email); ?>"><?php echo esc_html($company_email); ?></a>
                            <?php endif; ?>
                            <?php if ($show_button) : ?>
                                <a class="bywa-btn bywa-btn-primary" href="<?php echo esc_url($button_url); ?>"><?php echo esc_html($button_label); ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php

    return ob_get_clean();
}

function bywa_tm_render_team_grid($members, $show_photos = true) {
    if (empty($members)) {
        return '';
    }

    ob_start();
    ?>
    <div class="bywa-team__grid<?php echo $show_photos ? '' : ' bywa-team__grid--no-photos'; ?>">
        <?php foreach ($members as $member) : ?>
            <?php
            $full_name = trim($member['first_name'] . ' ' . $member['last_name']);
            ?>
            <article class="bywa-team-card<?php echo $show_photos ? ' bywa-team-card--with-photo' : ' bywa-team-card--no-photo'; ?> bywa-reveal">
                <?php if ($show_photos) : ?>
                    <div class="bywa-team-card__media">
                        <?php
                        echo !empty($member['photo_id'])
                            ? wp_get_attachment_image($member['photo_id'], 'large', false, array(
                                'class'   => 'bywa-team-card__image',
                                'loading' => 'lazy',
                            ))
                            : '<div class="bywa-no-image bywa-team-card__image"></div>';
                        ?>
                    </div>
                <?php endif; ?>

                <div class="bywa-team-card__body">
                    <?php if (!empty($member['role'])) : ?>
                        <span class="bywa-team-card__badge"><span class="bywa-team-card__badge-icon" aria-hidden="true"></span> <?php echo esc_html($member['role']); ?></span>
                    <?php endif; ?>

                    <h3 class="bywa-team-card__name"><?php echo esc_html($full_name); ?></h3>

                    <?php if (!empty($member['age'])) : ?>
                        <p class="bywa-team-card__meta"><?php echo esc_html(sprintf(__('%s ani', 'bywa-team-manager'), intval($member['age']))); ?></p>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    <?php

    return ob_get_clean();
}

function bywa_tm_render_team_section($post_id, $mode = 'full') {
    $members = bywa_tm_get_team_members($post_id);
    $show_photos = bywa_tm_team_member_photos_enabled($post_id);

    ob_start();

    if ('presentation' === $mode) {
        echo bywa_tm_render_presentation_section($post_id, array('standalone' => true)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    } else {
        echo '<section class="bywa-team bywa-team--full">';
            echo bywa_tm_render_presentation_section($post_id); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo '<div class="bywa-team__members">';
                echo '<div class="container">';
                    echo bywa_tm_render_team_grid($members, $show_photos); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo '</div>';
            echo '</div>';
        echo '</section>';
    }

    return ob_get_clean();
}

function bywa_team_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit'    => -1,
        'ids'      => '',
        'include'  => '',
        'orderby'  => 'menu_order',
        'order'    => 'ASC',
    ), $atts, 'bywa_team');

    $query = bywa_tm_get_shortcode_query($atts);

    if (!$query->have_posts()) {
        return '';
    }

    ob_start();

    while ($query->have_posts()) {
        $query->the_post();
        echo bywa_tm_render_team_section(get_the_ID(), 'full'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode('bywa_team', 'bywa_team_shortcode');

function bywa_team_presentation_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit'    => 1,
        'ids'      => '',
        'include'  => '',
        'orderby'  => 'menu_order',
        'order'    => 'ASC',
    ), $atts, 'bywa_team_presentation');

    $query = bywa_tm_get_shortcode_query($atts);

    if (!$query->have_posts()) {
        return '';
    }

    ob_start();

    while ($query->have_posts()) {
        $query->the_post();
        echo bywa_tm_render_team_section(get_the_ID(), 'presentation'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode('bywa_team_presentation', 'bywa_team_presentation_shortcode');
