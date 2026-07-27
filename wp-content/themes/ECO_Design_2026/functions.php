<?php
if (!defined('ABSPATH')) {
    exit;
}

define('BYWA_ECO_THEME_VERSION', '1.3.0');

action_if_theme_setup();

add_action('send_headers', function() {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
});


function action_if_theme_setup() {
    add_action('after_setup_theme', 'bywa_eco_setup');
    add_action('wp_enqueue_scripts', 'bywa_eco_enqueue_assets');
    add_action('customize_register', 'bywa_eco_customize_register');
    add_filter('document_title_parts', 'bywa_eco_filter_document_title_parts');

    add_action('add_meta_boxes', 'bywa_eco_add_page_hero_meta_box');
    add_action('save_post_page', 'bywa_eco_save_page_hero_meta');
    add_action('admin_enqueue_scripts', 'bywa_eco_admin_enqueue_assets');
}

function bywa_eco_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script'));
    add_theme_support('custom-logo', array(
        'height'      => 80,
        'width'       => 280,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    register_nav_menus(array(
        'primary' => __('Menu principal', 'eco-design-2026'),
        'footer_services' => __('Footer - Services', 'eco-design-2026'),
        'footer_company'  => __('Footer - Entreprise', 'eco-design-2026'),
    ));
}

function bywa_eco_enqueue_assets() {
    wp_enqueue_style('bywa-google-fonts', 'https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap', array(), null);
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(), '5.3.3');
    wp_enqueue_style('bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', array(), '1.11.3');
    wp_enqueue_style('bywa-eco-style', get_template_directory_uri() . '/assets/css/theme.css', array('bootstrap'), BYWA_ECO_THEME_VERSION);

    wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array(), '5.3.3', true);
    wp_enqueue_script('bywa-eco-theme', get_template_directory_uri() . '/assets/js/theme.js', array(), BYWA_ECO_THEME_VERSION, true);
}

function bywa_eco_admin_enqueue_assets($hook) {
    if ($hook !== 'post.php' && $hook !== 'post-new.php') {
        return;
    }

    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'page') {
        return;
    }

    wp_enqueue_media();

    wp_enqueue_script(
        'bywa-eco-admin-hero',
        get_template_directory_uri() . '/assets/js/admin-hero.js',
        array('jquery'),
        BYWA_ECO_THEME_VERSION,
        true
    );

    wp_enqueue_style(
        'bywa-eco-admin-hero',
        get_template_directory_uri() . '/assets/css/admin-hero.css',
        array(),
        BYWA_ECO_THEME_VERSION
    );
}

function bywa_eco_customize_register($wp_customize) {
    $wp_customize->add_section('bywa_eco_company', array(
        'title'    => __('ECO Electricite - Infos entreprise', 'eco-design-2026'),
        'priority' => 30,
    ));

    $settings = array(
        'bywa_eco_phone' => array('label' => 'Téléphone principal', 'default' => '032 481 14 45'),
        'bywa_eco_phone_secondary' => array('label' => 'Téléphone secondaire', 'default' => '079 786 70 94'),
        'bywa_eco_email' => array('label' => 'E-mail', 'default' => 'info@eco-electricite.ch'),
        'bywa_eco_address' => array('label' => 'Adresse', 'default' => 'Rue de Bel-Air 22, 2732 Reconvilier'),
        'bywa_eco_cta_label' => array('label' => 'Texte CTA hero 1', 'default' => 'Demander un devis'),
        'bywa_eco_cta_url' => array('label' => 'URL CTA hero 1', 'default' => '/contact/'),
        'bywa_eco_cta_secondary_label' => array('label' => 'Texte CTA hero 2', 'default' => 'Voir nos services'),
        'bywa_eco_cta_secondary_url' => array('label' => 'URL CTA hero 2', 'default' => '/services'),
    );

    foreach ($settings as $key => $data) {
        $wp_customize->add_setting($key, array(
            'default'           => $data['default'],
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control($key, array(
            'label'   => __($data['label'], 'eco-design-2026'),
            'section' => 'bywa_eco_company',
            'type'    => 'text',
        ));
    }

    $wp_customize->add_section('bywa_eco_footer', array(
        'title'    => __('ECO Electricite - Footer', 'eco-design-2026'),
        'priority' => 31,
    ));

    $wp_customize->add_setting('bywa_eco_footer_logo_id', array(
        'default'           => '',
        'sanitize_callback' => 'bywa_eco_sanitize_footer_logo',
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'bywa_eco_footer_logo_id', array(
        'label'   => __('Logo du footer', 'eco-design-2026'),
        'section' => 'bywa_eco_footer',
        'settings' => 'bywa_eco_footer_logo_id',
    )));

    $social_defaults = array(
        1 => array('label' => 'Facebook', 'icon' => 'bi-facebook', 'url' => ''),
        2 => array('label' => 'Instagram', 'icon' => 'bi-instagram', 'url' => ''),
        3 => array('label' => 'LinkedIn', 'icon' => 'bi-linkedin', 'url' => ''),
        4 => array('label' => 'YouTube', 'icon' => 'bi-youtube', 'url' => ''),
    );

    foreach ($social_defaults as $index => $social) {
        $wp_customize->add_setting('bywa_eco_social_' . $index . '_icon', array(
            'default'           => $social['icon'],
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('bywa_eco_social_' . $index . '_icon', array(
            'label'   => sprintf(__('Réseau %d - icône Bootstrap', 'eco-design-2026'), $index),
            'section' => 'bywa_eco_footer',
            'type'    => 'text',
        ));

        $wp_customize->add_setting('bywa_eco_social_' . $index . '_url', array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));

        $wp_customize->add_control('bywa_eco_social_' . $index . '_url', array(
            'label'   => sprintf(__('Réseau %d - lien', 'eco-design-2026'), $index),
            'section' => 'bywa_eco_footer',
        'type'    => 'url',
        ));
    }

    $wp_customize->add_section('bywa_eco_archive_heroes', array(
        'title'    => __('ECO Electricite - Bannières d’archives', 'eco-design-2026'),
        'priority' => 32,
    ));

    $archive_hero_images = array(
        'bywa_eco_archive_service_hero_image' => array(
            'label' => 'Image bannière archive Services',
        ),
        'bywa_eco_archive_realisation_hero_image' => array(
            'label' => 'Image bannière archive Réalisations',
        ),
    );

    foreach ($archive_hero_images as $key => $data) {
        $wp_customize->add_setting($key, array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));

        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, $key, array(
            'label'    => __($data['label'], 'eco-design-2026'),
            'section'  => 'bywa_eco_archive_heroes',
            'settings' => $key,
        )));
    }
}

function bywa_eco_get_theme_mod($key, $default = '') {
    $value = get_theme_mod($key, $default);
    return is_string($value) ? $value : $default;
}

function bywa_eco_get_contact_url() {
    return home_url('/contact/');
}

function bywa_eco_get_contact_phone() {
    return bywa_eco_get_theme_mod('bywa_eco_phone', '032 481 14 45');
}

function bywa_eco_get_contact_phone_secondary() {
    return bywa_eco_get_theme_mod('bywa_eco_phone_secondary', '079 786 70 94');
}

function bywa_eco_get_contact_email() {
    return sanitize_email(bywa_eco_get_theme_mod('bywa_eco_email', 'info@eco-electricite.ch'));
}

function bywa_eco_get_tel_link($phone) {
    return 'tel:' . preg_replace('/[^0-9+]/', '', (string) $phone);
}

function bywa_eco_get_footer_logo_id() {
    return absint(get_theme_mod('bywa_eco_footer_logo_id', 0));
}

function bywa_eco_sanitize_footer_logo($value) {
    $value = trim((string) $value);

    if ($value === '' || $value === '0') {
        return '';
    }

    if (ctype_digit($value)) {
        $attachment_url = wp_get_attachment_image_url(absint($value), 'full');

        if (!empty($attachment_url)) {
            return esc_url_raw($attachment_url);
        }

        return '';
    }

    return esc_url_raw($value);
}

function bywa_eco_get_footer_logo_html() {
    $logo_value = trim((string) get_theme_mod('bywa_eco_footer_logo_id', ''));

    if ($logo_value !== '' && $logo_value !== '0') {
        $logo_id = attachment_url_to_postid($logo_value);

        if ($logo_id > 0) {
            return wp_get_attachment_image($logo_id, 'medium', false, array(
                'class' => 'bywa-footer-brand-logo',
                'loading' => 'lazy',
            ));
        }

        return '<img src="' . esc_url($logo_value) . '" alt="' . esc_attr__('ECO Electricite', 'eco-design-2026') . '" class="bywa-footer-brand-logo" loading="lazy">';
    }

    return '<span class="bywa-footer-badge"><i class="bi bi-lightning-charge-fill" aria-hidden="true"></i></span>';
}

function bywa_eco_get_footer_social_links() {
    $socials = array();
    $labels = array(
        1 => 'Facebook',
        2 => 'Instagram',
        3 => 'LinkedIn',
        4 => 'YouTube',
    );
    $defaults = array(
        1 => 'bi-facebook',
        2 => 'bi-instagram',
        3 => 'bi-linkedin',
        4 => 'bi-youtube',
    );

    for ($i = 1; $i <= 4; $i++) {
        $icon = sanitize_text_field((string) get_theme_mod('bywa_eco_social_' . $i . '_icon', $defaults[$i]));
        $url = esc_url_raw((string) get_theme_mod('bywa_eco_social_' . $i . '_url', ''));

        if ($url === '') {
            continue;
        }

        $socials[] = array(
            'icon'  => bywa_eco_normalize_bootstrap_icon_class($icon, $defaults[$i]),
            'url'   => $url,
            'label' => $labels[$i] ?? __('Réseau social', 'eco-design-2026'),
        );
    }

    return $socials;
}

function bywa_eco_get_archive_hero_label($post_type) {
    $labels = array(
        'service'     => __('Services', 'eco-design-2026'),
        'realisation' => __('Réalisations', 'eco-design-2026'),
    );

    if (isset($labels[$post_type])) {
        return $labels[$post_type];
    }

    $archive_title = post_type_archive_title('', false);
    $archive_title = trim((string) $archive_title);

    if ($archive_title === '') {
        return ucfirst(str_replace(array('_', '-'), ' ', (string) $post_type));
    }

    $archive_title = preg_replace('/\s*archive\s*$/i', '', $archive_title);

    return trim($archive_title);
}

function bywa_eco_get_archive_hero_image_url($post_type) {
    $map = array(
        'service'     => 'bywa_eco_archive_service_hero_image',
        'realisation' => 'bywa_eco_archive_realisation_hero_image',
    );

    if (!isset($map[$post_type])) {
        return '';
    }

    return esc_url_raw((string) get_theme_mod($map[$post_type], ''));
}

function bywa_eco_get_archive_hero_data($post_type) {
    $label = bywa_eco_get_archive_hero_label($post_type);
    $image = bywa_eco_get_archive_hero_image_url($post_type);

    return array(
        'kicker' => $label,
        'title'  => $label,
        'text'   => get_the_archive_description(),
        'slides' => $image !== '' ? array($image) : array(),
    );
}

function bywa_eco_filter_document_title_parts($parts) {
    if (is_post_type_archive('service')) {
        $parts['title'] = bywa_eco_get_archive_hero_label('service');
    } elseif (is_post_type_archive('realisation')) {
        $parts['title'] = bywa_eco_get_archive_hero_label('realisation');
    }

    return $parts;
}

function bywa_eco_render_footer_menu($location, $fallback_items) {
    if (has_nav_menu($location)) {
        wp_nav_menu(array(
            'theme_location' => $location,
            'container'      => false,
            'menu_class'     => 'bywa-footer-menu',
            'depth'          => 1,
            'fallback_cb'    => false,
        ));
        return;
    }

    echo '<ul class="bywa-footer-menu">';
    foreach ($fallback_items as $item) {
        echo '<li><a href="' . esc_url($item['url']) . '">' . esc_html($item['label']) . '</a></li>';
    }
    echo '</ul>';
}

function bywa_eco_normalize_bootstrap_icon_class($icon, $default = 'bi-link-45deg') {
    $icon = trim(preg_replace('/\s+/', ' ', sanitize_text_field((string) $icon)));

    if ($icon === '') {
        return $default;
    }

    $parts = preg_split('/\s+/', $icon);
    $parts = array_filter(array_map('trim', $parts));
    $classes = array();

    foreach ($parts as $part) {
        if ($part === 'bi') {
            continue;
        }

        if (strpos($part, 'bi-') === 0) {
            $classes[] = $part;
        }
    }

    if (empty($classes)) {
        $classes[] = $default;
    }

    return implode(' ', array_unique($classes));
}

function bywa_eco_primary_fallback() {
    echo '<ul class="navbar-nav ms-auto align-items-xl-center bywa-primary-menu">';
    echo '<li class="menu-item current-menu-item"><a href="' . esc_url(home_url('/')) . '">Accueil</a></li>';
    echo '<li class="menu-item"><a href="' . esc_url(home_url('/services')) . '">Services</a></li>';
    echo '<li class="menu-item"><a href="' . esc_url(home_url('/realisations')) . '">Realisations</a></li>';
    echo '<li class="menu-item"><a href="' . esc_url(home_url('/entreprise')) . '">Entreprise</a></li>';
    echo '<li class="menu-item"><a href="' . esc_url(home_url('/contact/')) . '">Contact</a></li>';
    echo '</ul>';
}

/**
 * ===== HERO PAGE META =====
 */
function bywa_eco_add_page_hero_meta_box() {
    add_meta_box(
        'bywa_eco_page_hero',
        'Bannière page Bywa',
        'bywa_eco_page_hero_meta_box_callback',
        'page',
        'normal',
        'high'
    );
}

function bywa_eco_page_hero_meta_box_callback($post) {
    wp_nonce_field('bywa_eco_save_page_hero_meta', 'bywa_eco_page_hero_nonce');

    $kicker         = get_post_meta($post->ID, '_bywa_hero_kicker', true);
    $title          = get_post_meta($post->ID, '_bywa_hero_title', true);
    $text           = get_post_meta($post->ID, '_bywa_hero_text', true);
    $action_1_label = get_post_meta($post->ID, '_bywa_hero_action_1_label', true);
    $action_1_url   = get_post_meta($post->ID, '_bywa_hero_action_1_url', true);
    $action_2_label = get_post_meta($post->ID, '_bywa_hero_action_2_label', true);
    $action_2_url   = get_post_meta($post->ID, '_bywa_hero_action_2_url', true);
    $points         = get_post_meta($post->ID, '_bywa_hero_points', true);
    ?>
    <p>
        <label for="bywa_hero_kicker"><strong>Hero kicker</strong></label>
        <input type="text" id="bywa_hero_kicker" name="bywa_hero_kicker" class="widefat" value="<?php echo esc_attr($kicker); ?>">
    </p>

    <p>
        <label for="bywa_hero_title"><strong>Titre hero</strong></label>
        <input type="text" id="bywa_hero_title" name="bywa_hero_title" class="widefat" value="<?php echo esc_attr($title); ?>" placeholder="Si vide, le titre de la page sera utilisé">
    </p>

    <p>
        <label for="bywa_hero_text"><strong>Paragraphe explicatif</strong></label>
        <textarea id="bywa_hero_text" name="bywa_hero_text" rows="4" class="widefat"><?php echo esc_textarea($text); ?></textarea>
    </p>

    <hr>

    <p>
        <label for="bywa_hero_action_1_label"><strong>Hero action 1 - texte</strong></label>
        <input type="text" id="bywa_hero_action_1_label" name="bywa_hero_action_1_label" class="widefat" value="<?php echo esc_attr($action_1_label); ?>">
    </p>

    <p>
        <label for="bywa_hero_action_1_url"><strong>Hero action 1 - URL</strong></label>
        <input type="text" id="bywa_hero_action_1_url" name="bywa_hero_action_1_url" class="widefat" value="<?php echo esc_attr($action_1_url); ?>">
    </p>

    <p>
        <label for="bywa_hero_action_2_label"><strong>Hero action 2 - texte</strong></label>
        <input type="text" id="bywa_hero_action_2_label" name="bywa_hero_action_2_label" class="widefat" value="<?php echo esc_attr($action_2_label); ?>">
    </p>

    <p>
        <label for="bywa_hero_action_2_url"><strong>Hero action 2 - URL</strong></label>
        <input type="text" id="bywa_hero_action_2_url" name="bywa_hero_action_2_url" class="widefat" value="<?php echo esc_attr($action_2_url); ?>">
    </p>

    <hr>

    <p>
        <label for="bywa_hero_points"><strong>Hero points</strong></label>
        <textarea id="bywa_hero_points" name="bywa_hero_points" rows="4" class="widefat" placeholder="Une ligne = un point"><?php echo esc_textarea($points); ?></textarea>
    </p>

    <hr>

    <div class="bywa-hero-admin-images">
        <?php for ($i = 1; $i <= 5; $i++) : ?>
            <?php
            $image_id  = (int) get_post_meta($post->ID, '_bywa_hero_image_' . $i, true);
            $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
            ?>
            <div class="bywa-hero-admin-image-card">
                <p><strong>Image fond hero <?php echo $i; ?></strong></p>

                <input
                    type="hidden"
                    name="bywa_hero_image_<?php echo $i; ?>"
                    id="bywa_hero_image_<?php echo $i; ?>"
                    value="<?php echo esc_attr($image_id); ?>"
                    class="bywa-hero-image-input"
                >

                <div class="bywa-hero-image-preview<?php echo $image_url ? ' has-image' : ''; ?>">
                    <?php if ($image_url) : ?>
                        <img src="<?php echo esc_url($image_url); ?>" alt="">
                    <?php else : ?>
                        <span>Aucune image sélectionnée</span>
                    <?php endif; ?>
                </div>

                <div class="bywa-hero-image-actions">
                    <button type="button" class="button button-secondary bywa-hero-upload-button">
                        Choisir une image
                    </button>
                    <button type="button" class="button button-link-delete bywa-hero-remove-button"<?php echo $image_url ? '' : ' style="display:none;"'; ?>>
                        Retirer
                    </button>
                </div>
            </div>
        <?php endfor; ?>
    </div>

    <p><em>Si aucune image n’est définie, la bannière utilisera l’image mise en avant de la page.</em></p>
    <?php
}

function bywa_eco_save_page_hero_meta($post_id) {
    if (!isset($_POST['bywa_eco_page_hero_nonce']) || !wp_verify_nonce($_POST['bywa_eco_page_hero_nonce'], 'bywa_eco_save_page_hero_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_page', $post_id)) {
        return;
    }

    $text_fields = array(
        'bywa_hero_kicker'         => '_bywa_hero_kicker',
        'bywa_hero_title'          => '_bywa_hero_title',
        'bywa_hero_action_1_label' => '_bywa_hero_action_1_label',
        'bywa_hero_action_1_url'   => '_bywa_hero_action_1_url',
        'bywa_hero_action_2_label' => '_bywa_hero_action_2_label',
        'bywa_hero_action_2_url'   => '_bywa_hero_action_2_url',
    );

    foreach ($text_fields as $input_name => $meta_key) {
        if (isset($_POST[$input_name])) {
            update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$input_name]));
        }
    }

    $textarea_fields = array(
        'bywa_hero_text'   => '_bywa_hero_text',
        'bywa_hero_points' => '_bywa_hero_points',
    );

    foreach ($textarea_fields as $input_name => $meta_key) {
        if (isset($_POST[$input_name])) {
            update_post_meta($post_id, $meta_key, sanitize_textarea_field($_POST[$input_name]));
        }
    }

    for ($i = 1; $i <= 5; $i++) {
        $input_name = 'bywa_hero_image_' . $i;
        $meta_key   = '_bywa_hero_image_' . $i;

        if (isset($_POST[$input_name])) {
            $attachment_id = absint($_POST[$input_name]);

            if ($attachment_id > 0) {
                update_post_meta($post_id, $meta_key, $attachment_id);
            } else {
                delete_post_meta($post_id, $meta_key);
            }
        }
    }
}

/**
 * ===== HERO HELPERS =====
 */
function bywa_eco_get_page_hero_data($post_id = 0) {
    $post_id = $post_id ? intval($post_id) : get_the_ID();
    $post_type = get_post_type($post_id);

    $title = get_post_meta($post_id, '_bywa_hero_title', true);
    if (empty($title)) {
        $title = get_the_title($post_id);
    }

    $points_raw = get_post_meta($post_id, '_bywa_hero_points', true);
    $points = array();

    if (!empty($points_raw)) {
        $lines = preg_split('/\r\n|\r|\n/', $points_raw);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line !== '') {
                $points[] = $line;
            }
        }
    }

    $slides = array();
    for ($i = 1; $i <= 5; $i++) {
        $image_id = (int) get_post_meta($post_id, '_bywa_hero_image_' . $i, true);
        if ($image_id > 0) {
            $image_url = wp_get_attachment_image_url($image_id, 'full');
            if ($image_url) {
                $slides[] = $image_url;
            }
        }
    }

    if (empty($slides) && has_post_thumbnail($post_id)) {
        $fallback = get_the_post_thumbnail_url($post_id, 'full');
        if ($fallback) {
            $slides[] = $fallback;
        }
    }

    $hero_text = get_post_meta($post_id, '_bywa_hero_text', true);

    if ($post_type === 'realisation') {
        $realisation_short_text = get_post_meta($post_id, '_bywa_realisation_short_text', true);

        if (!empty($realisation_short_text)) {
            $hero_text = $realisation_short_text;
        } elseif (empty($hero_text)) {
            $hero_text = get_the_excerpt($post_id);
        }
    } elseif ($post_type === 'service') {
        $service_short_text = get_post_meta($post_id, '_bywa_service_short_text', true);

        if (!empty($service_short_text)) {
            $hero_text = $service_short_text;
        } elseif (empty($hero_text)) {
            $hero_text = get_the_excerpt($post_id);
        }
    }

    $data = array(
        'kicker'   => get_post_meta($post_id, '_bywa_hero_kicker', true),
        'title'    => $title,
        'text'     => $hero_text,
        'action_1' => array(
            'label' => get_post_meta($post_id, '_bywa_hero_action_1_label', true),
            'url'   => get_post_meta($post_id, '_bywa_hero_action_1_url', true),
        ),
        'action_2' => array(
            'label' => get_post_meta($post_id, '_bywa_hero_action_2_label', true),
            'url'   => get_post_meta($post_id, '_bywa_hero_action_2_url', true),
        ),
        'points'   => $points,
        'slides'   => $slides,
    );

    return apply_filters('bywa_eco_page_hero_data', $data, $post_id, $post_type);
}

function bywa_eco_render_page_hero($post_id = 0, $variant = 'large') {
    $post_id = $post_id ? intval($post_id) : get_the_ID();

    get_template_part('template-parts/hero-page', null, array(
        'post_id' => $post_id,
        'variant' => $variant,
    ));
}

function bywa_eco_page_has_team_shortcode($post_id = 0) {
    $post_id = $post_id ? intval($post_id) : get_the_ID();
    $content = (string) get_post_field('post_content', $post_id);

    if ($content === '') {
        return false;
    }

    return has_shortcode($content, 'bywa_team') || has_shortcode($content, 'bywa_team_presentation');
}

function bywa_eco_render_breadcrumbs($items = array()) {
    if (empty($items) || !is_array($items)) {
        return;
    }

    echo '<nav class="bywa-breadcrumbs" aria-label="' . esc_attr__('Fil d’Ariane', 'eco-design-2026') . '">';
    echo '<ol class="bywa-breadcrumbs__list">';

    $last_index = count($items) - 1;

    foreach ($items as $index => $item) {
        $label = isset($item['label']) ? wp_strip_all_tags((string) $item['label']) : '';
        $url = isset($item['url']) ? (string) $item['url'] : '';

        if ($label === '') {
            continue;
        }

        echo '<li class="bywa-breadcrumbs__item">';

        if ($url !== '' && $index !== $last_index) {
            echo '<a href="' . esc_url($url) . '">' . esc_html($label) . '</a>';
        } else {
            echo '<span aria-current="page">' . esc_html($label) . '</span>';
        }

        echo '</li>';
    }

    echo '</ol>';
    echo '</nav>';
}
