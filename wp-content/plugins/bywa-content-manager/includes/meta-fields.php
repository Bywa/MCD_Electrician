<?php
if (!defined('ABSPATH')) exit;

/**
 * Meta box featured
 */
function bywa_add_featured_meta() {
    add_meta_box(
        'bywa_featured',
        'Mise en avant',
        'bywa_featured_callback',
        array('service', 'realisation', 'partenaire', 'domaine_activite'),
        'side'
    );
}
add_action('add_meta_boxes', 'bywa_add_featured_meta');

function bywa_add_partenaire_order_meta() {
    add_meta_box(
        'bywa_partenaire_order',
        'Ordre d\'affichage',
        'bywa_partenaire_order_callback',
        'partenaire',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'bywa_add_partenaire_order_meta');

function bywa_partenaire_order_callback($post) {
    wp_nonce_field('bywa_save_partenaire_order', 'bywa_partenaire_order_nonce');
    ?>
    <p>
        <label for="bywa_partenaire_order"><strong>Ordre</strong></label><br>
        <input
            type="number"
            id="bywa_partenaire_order"
            name="bywa_partenaire_order"
            value="<?php echo esc_attr((int) $post->menu_order); ?>"
            class="widefat"
            min="0"
            step="1"
        />
    </p>
    <p class="description">Les partenaires avec l'ordre le plus petit s'affichent en premier.</p>
    <?php
}

function bywa_add_banner_meta() {
    add_meta_box(
        'bywa_banner_details',
        'Bannière mini',
        'bywa_banner_details_callback',
        array('service', 'realisation', 'partenaire', 'domaine_activite'),
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'bywa_add_banner_meta');

function bywa_featured_callback($post) {
    wp_nonce_field('bywa_save_meta_fields', 'bywa_meta_nonce');
    $value = get_post_meta($post->ID, '_bywa_featured', true);
    ?>
    <label>
        <input type="checkbox" name="bywa_featured" value="1" <?php checked($value, '1'); ?> />
        Mettre en avant
    </label>
    <?php
}

/**
 * Meta box service details
 */
function bywa_add_service_details_meta() {
    add_meta_box(
        'bywa_service_details',
        'Détails du service',
        'bywa_service_details_callback',
        'service',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'bywa_add_service_details_meta');

function bywa_service_details_callback($post) {
    wp_nonce_field('bywa_save_meta_fields', 'bywa_meta_nonce');

    $icon       = get_post_meta($post->ID, '_bywa_service_icon', true);
    $short_text = get_post_meta($post->ID, '_bywa_service_short_text', true);
    ?>
    <p>
        <label for="bywa_service_icon"><strong>Icône Bootstrap Icons</strong></label><br>
        <input
            type="text"
            id="bywa_service_icon"
            name="bywa_service_icon"
            value="<?php echo esc_attr($icon); ?>"
            class="widefat"
            placeholder="Ex: bi-lightning-charge-fill"
        />
        <small>
            Exemples : <code>bi-lightning-charge-fill</code>, <code>bi-house-gear-fill</code>, <code>bi-sun-fill</code>, <code>bi-ev-front-fill</code>
        </small>
    </p>

    <p>
        <label for="bywa_service_short_text"><strong>Texte court carte service</strong></label><br>
        <textarea
            id="bywa_service_short_text"
            name="bywa_service_short_text"
            rows="5"
            class="widefat"
            placeholder="Texte court affiché dans les cartes services."
        ><?php echo esc_textarea($short_text); ?></textarea>
        <small>
            Conseil : 2 à 4 lignes maximum pour garder la même hauteur visuelle.
        </small>
    </p>
    <?php
}

/**
 * Meta box domaine details
 */
function bywa_add_domaine_details_meta() {
    add_meta_box(
        'bywa_domaine_details',
        'Détails du domaine d’activité',
        'bywa_domaine_details_callback',
        'domaine_activite',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'bywa_add_domaine_details_meta');

function bywa_domaine_details_callback($post) {
    wp_nonce_field('bywa_save_meta_fields', 'bywa_meta_nonce');

    $short_text = get_post_meta($post->ID, '_bywa_domaine_short_text', true);
    $link_label = get_post_meta($post->ID, '_bywa_domaine_link_label', true);

    ?>
    <p>
        <label for="bywa_domaine_short_text"><strong>Texte court carte domaine</strong></label><br>
        <textarea
            id="bywa_domaine_short_text"
            name="bywa_domaine_short_text"
            rows="5"
            class="widefat"
            placeholder="Texte court affiché dans la carte domaine."
        ><?php echo esc_textarea($short_text); ?></textarea>
        <small>
            Conseil : 2 à 3 lignes maximum pour garder un rendu homogène.
        </small>
    </p>

    <p>
        <label for="bywa_domaine_link_label"><strong>Libellé du lien</strong></label><br>
        <input
            type="text"
            id="bywa_domaine_link_label"
            name="bywa_domaine_link_label"
            value="<?php echo esc_attr($link_label); ?>"
            class="widefat"
            placeholder="Ex: Lire plus"
        />
    </p>
    <?php
}

function bywa_banner_details_callback($post) {
    wp_nonce_field('bywa_save_banner_meta', 'bywa_banner_nonce');

    $kicker         = get_post_meta($post->ID, '_bywa_hero_kicker', true);
    $title          = get_post_meta($post->ID, '_bywa_hero_title', true);
    $text           = get_post_meta($post->ID, '_bywa_hero_text', true);
    $action_1_label = get_post_meta($post->ID, '_bywa_hero_action_1_label', true);
    $action_1_url   = get_post_meta($post->ID, '_bywa_hero_action_1_url', true);
    $action_2_label = get_post_meta($post->ID, '_bywa_hero_action_2_label', true);
    $action_2_url   = get_post_meta($post->ID, '_bywa_hero_action_2_url', true);
    $points         = get_post_meta($post->ID, '_bywa_hero_points', true);
    ?>
    <p>Utilise la même logique que la bannière mini des pages. Le titre reste optionnel si tu veux laisser le titre WordPress.</p>

    <p>
        <label for="bywa_hero_kicker"><strong>Kicker</strong></label>
        <input type="text" id="bywa_hero_kicker" name="bywa_hero_kicker" class="widefat" value="<?php echo esc_attr($kicker); ?>">
    </p>

    <p>
        <label for="bywa_hero_title"><strong>Titre bannière</strong></label>
        <input type="text" id="bywa_hero_title" name="bywa_hero_title" class="widefat" value="<?php echo esc_attr($title); ?>" placeholder="Si vide, le titre du contenu sera utilisé">
    </p>

    <p>
        <label for="bywa_hero_text"><strong>Texte</strong></label>
        <textarea id="bywa_hero_text" name="bywa_hero_text" rows="4" class="widefat"><?php echo esc_textarea($text); ?></textarea>
    </p>

    <hr>

    <p>
        <label for="bywa_hero_action_1_label"><strong>Bouton principal - texte</strong></label>
        <input type="text" id="bywa_hero_action_1_label" name="bywa_hero_action_1_label" class="widefat" value="<?php echo esc_attr($action_1_label); ?>">
    </p>

    <p>
        <label for="bywa_hero_action_1_url"><strong>Bouton principal - URL</strong></label>
        <input type="text" id="bywa_hero_action_1_url" name="bywa_hero_action_1_url" class="widefat" value="<?php echo esc_attr($action_1_url); ?>">
    </p>

    <p>
        <label for="bywa_hero_action_2_label"><strong>Bouton secondaire - texte</strong></label>
        <input type="text" id="bywa_hero_action_2_label" name="bywa_hero_action_2_label" class="widefat" value="<?php echo esc_attr($action_2_label); ?>">
    </p>

    <p>
        <label for="bywa_hero_action_2_url"><strong>Bouton secondaire - URL</strong></label>
        <input type="text" id="bywa_hero_action_2_url" name="bywa_hero_action_2_url" class="widefat" value="<?php echo esc_attr($action_2_url); ?>">
    </p>

    <hr>

    <p>
        <label for="bywa_hero_points"><strong>Points clés</strong></label>
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
                <p><strong>Image bannière <?php echo $i; ?></strong></p>

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
    <?php
}

/**
 * Meta box réalisation details
 */
function bywa_add_realisation_details_meta() {
    add_meta_box(
        'bywa_realisation_details',
        'Détails de la réalisation',
        'bywa_realisation_details_callback',
        'realisation',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'bywa_add_realisation_details_meta');

function bywa_realisation_details_callback($post) {
    wp_nonce_field('bywa_save_meta_fields', 'bywa_meta_nonce');

    $location   = get_post_meta($post->ID, '_bywa_realisation_location', true);
    $client     = get_post_meta($post->ID, '_bywa_realisation_client', true);
    $architecte = get_post_meta($post->ID, '_bywa_realisation_architecte', true);
    $date       = get_post_meta($post->ID, '_bywa_realisation_date', true);
    $short_text = get_post_meta($post->ID, '_bywa_realisation_short_text', true);

    $gallery_ids = array();
    for ($i = 1; $i <= 5; $i++) {
        $gallery_ids[$i] = get_post_meta($post->ID, '_bywa_realisation_gallery_' . $i, true);
    }
    ?>
    <p>
        <label for="bywa_realisation_location"><strong>Lieu / ville</strong></label><br>
        <input
            type="text"
            id="bywa_realisation_location"
            name="bywa_realisation_location"
            value="<?php echo esc_attr($location); ?>"
            class="widefat"
            placeholder="Ex: Reconvilier"
        />
    </p>

    <p>
        <label for="bywa_realisation_client"><strong>Client</strong></label><br>
        <input
            type="text"
            id="bywa_realisation_client"
            name="bywa_realisation_client"
            value="<?php echo esc_attr($client); ?>"
            class="widefat"
            placeholder="Ex: PPE Les Tilleuls"
        />
    </p>

    <p>
        <label for="bywa_realisation_architecte"><strong>Architecte</strong></label><br>
        <input
            type="text"
            id="bywa_realisation_architecte"
            name="bywa_realisation_architecte"
            value="<?php echo esc_attr($architecte); ?>"
            class="widefat"
            placeholder="Ex: Atelier XYZ"
        />
    </p>

    <p>
        <label for="bywa_realisation_date"><strong>Date du projet</strong></label><br>
        <input
            type="text"
            id="bywa_realisation_date"
            name="bywa_realisation_date"
            value="<?php echo esc_attr($date); ?>"
            class="widefat"
            placeholder="Ex: 2025"
        />
    </p>

    <p>
        <label for="bywa_realisation_short_text"><strong>Texte court carte réalisation</strong></label><br>
        <textarea
            id="bywa_realisation_short_text"
            name="bywa_realisation_short_text"
            rows="5"
            class="widefat"
            placeholder="Texte court affiché dans les cartes réalisations."
        ><?php echo esc_textarea($short_text); ?></textarea>
        <small>
            Conseil : 2 à 4 lignes maximum pour garder un rendu homogène.
        </small>
    </p>

    <hr>

    <p><strong>Mini galerie (jusqu’à 5 images)</strong><br>
    <small>Utiliser la médiathèque WordPress. La première image servira de fallback si aucune image mise en avant n’est définie.</small></p>

    <div class="bywa-gallery-admin-wrap">
        <?php for ($i = 1; $i <= 5; $i++) :
            $attachment_id = !empty($gallery_ids[$i]) ? intval($gallery_ids[$i]) : 0;
            $preview = $attachment_id ? wp_get_attachment_image_url($attachment_id, 'medium') : '';
        ?>
            <div class="bywa-gallery-admin-item" style="margin-bottom:20px;padding:16px;border:1px solid #ddd;">
                <p><strong>Image <?php echo intval($i); ?></strong></p>

                <input
                    type="hidden"
                    class="bywa-media-field"
                    id="bywa_realisation_gallery_<?php echo intval($i); ?>"
                    name="bywa_realisation_gallery_<?php echo intval($i); ?>"
                    value="<?php echo esc_attr($attachment_id); ?>"
                />

                <div class="bywa-media-preview" style="margin-bottom:10px;">
                    <?php if ($preview) : ?>
                        <img src="<?php echo esc_url($preview); ?>" alt="" style="max-width:180px;height:auto;display:block;">
                    <?php endif; ?>
                </div>

                <button type="button" class="button bywa-media-upload" data-target="#bywa_realisation_gallery_<?php echo intval($i); ?>">
                    Choisir une image
                </button>

                <button type="button" class="button bywa-media-remove" data-target="#bywa_realisation_gallery_<?php echo intval($i); ?>">
                    Retirer
                </button>
            </div>
        <?php endfor; ?>
    </div>
    <?php
}

function bywa_save_featured_meta($post_id) {
    if (!isset($_POST['bywa_meta_nonce']) || !wp_verify_nonce($_POST['bywa_meta_nonce'], 'bywa_save_meta_fields')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['bywa_featured'])) {
        update_post_meta($post_id, '_bywa_featured', '1');
    } else {
        delete_post_meta($post_id, '_bywa_featured');
    }

    if (isset($_POST['bywa_service_icon'])) {
        update_post_meta(
            $post_id,
            '_bywa_service_icon',
            sanitize_text_field($_POST['bywa_service_icon'])
        );
    }

    if (isset($_POST['bywa_service_short_text'])) {
        update_post_meta(
            $post_id,
            '_bywa_service_short_text',
            sanitize_textarea_field($_POST['bywa_service_short_text'])
        );
    }

    if (isset($_POST['bywa_domaine_short_text'])) {
        update_post_meta(
            $post_id,
            '_bywa_domaine_short_text',
            sanitize_textarea_field($_POST['bywa_domaine_short_text'])
        );
    }

    if (isset($_POST['bywa_domaine_link_label'])) {
        update_post_meta(
            $post_id,
            '_bywa_domaine_link_label',
            sanitize_text_field($_POST['bywa_domaine_link_label'])
        );
    }

    if (isset($_POST['bywa_banner_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['bywa_banner_nonce'])), 'bywa_save_banner_meta')) {
        if (isset($_POST['bywa_hero_kicker'])) {
            update_post_meta($post_id, '_bywa_hero_kicker', sanitize_text_field(wp_unslash($_POST['bywa_hero_kicker'])));
        }

        if (isset($_POST['bywa_hero_title'])) {
            update_post_meta($post_id, '_bywa_hero_title', sanitize_text_field(wp_unslash($_POST['bywa_hero_title'])));
        }

        if (isset($_POST['bywa_hero_text'])) {
            update_post_meta($post_id, '_bywa_hero_text', sanitize_textarea_field(wp_unslash($_POST['bywa_hero_text'])));
        }

        if (isset($_POST['bywa_hero_action_1_label'])) {
            update_post_meta($post_id, '_bywa_hero_action_1_label', sanitize_text_field(wp_unslash($_POST['bywa_hero_action_1_label'])));
        }

        if (isset($_POST['bywa_hero_action_1_url'])) {
            update_post_meta($post_id, '_bywa_hero_action_1_url', esc_url_raw(wp_unslash($_POST['bywa_hero_action_1_url'])));
        }

        if (isset($_POST['bywa_hero_action_2_label'])) {
            update_post_meta($post_id, '_bywa_hero_action_2_label', sanitize_text_field(wp_unslash($_POST['bywa_hero_action_2_label'])));
        }

        if (isset($_POST['bywa_hero_action_2_url'])) {
            update_post_meta($post_id, '_bywa_hero_action_2_url', esc_url_raw(wp_unslash($_POST['bywa_hero_action_2_url'])));
        }

        if (isset($_POST['bywa_hero_points'])) {
            update_post_meta($post_id, '_bywa_hero_points', sanitize_textarea_field(wp_unslash($_POST['bywa_hero_points'])));
        }

        for ($i = 1; $i <= 5; $i++) {
            $input_name = 'bywa_hero_image_' . $i;
            $meta_key   = '_bywa_hero_image_' . $i;

            if (isset($_POST[$input_name]) && $_POST[$input_name] !== '') {
                update_post_meta($post_id, $meta_key, absint($_POST[$input_name]));
            } else {
                delete_post_meta($post_id, $meta_key);
            }
        }
    }

    if (isset($_POST['bywa_realisation_location'])) {
        update_post_meta(
            $post_id,
            '_bywa_realisation_location',
            sanitize_text_field($_POST['bywa_realisation_location'])
        );
    }

    if (isset($_POST['bywa_realisation_client'])) {
        $client_name = sanitize_text_field($_POST['bywa_realisation_client']);

        if (function_exists('bywa_normalize_client_name')) {
            $client_name = bywa_normalize_client_name($client_name);
        }

        if ($client_name !== '') {
            update_post_meta(
                $post_id,
                '_bywa_realisation_client',
                $client_name
            );
        } else {
            delete_post_meta($post_id, '_bywa_realisation_client');
        }
    }

    if (isset($_POST['bywa_realisation_architecte'])) {
        update_post_meta(
            $post_id,
            '_bywa_realisation_architecte',
            sanitize_text_field($_POST['bywa_realisation_architecte'])
        );
    }

    if (isset($_POST['bywa_realisation_date'])) {
        update_post_meta(
            $post_id,
            '_bywa_realisation_date',
            sanitize_text_field($_POST['bywa_realisation_date'])
        );
    }

    if (isset($_POST['bywa_realisation_short_text'])) {
        update_post_meta(
            $post_id,
            '_bywa_realisation_short_text',
            sanitize_textarea_field($_POST['bywa_realisation_short_text'])
        );
    }

    if (isset($_POST['bywa_partenaire_order_nonce']) && wp_verify_nonce($_POST['bywa_partenaire_order_nonce'], 'bywa_save_partenaire_order')) {
        if (isset($_POST['bywa_partenaire_order'])) {
            global $wpdb;

            $wpdb->update(
                $wpdb->posts,
                array('menu_order' => intval($_POST['bywa_partenaire_order'])),
                array('ID' => $post_id),
                array('%d'),
                array('%d')
            );

            clean_post_cache($post_id);
        }
    }

    for ($i = 1; $i <= 5; $i++) {
        $field = 'bywa_realisation_gallery_' . $i;
        $meta_key = '_bywa_realisation_gallery_' . $i;

        if (isset($_POST[$field]) && $_POST[$field] !== '') {
            update_post_meta($post_id, $meta_key, intval($_POST[$field]));
        } else {
            delete_post_meta($post_id, $meta_key);
        }
    }
}
add_action('save_post', 'bywa_save_featured_meta');

/**
 * Media uploader admin
 */
function bywa_admin_media_uploader_scripts($hook) {
    global $post_type;

    if (!in_array($post_type, array('realisation', 'service', 'domaine_activite', 'partenaire'), true)) {
        return;
    }

    wp_enqueue_media();

    wp_enqueue_style(
        'bywa-content-manager-admin',
        BYWA_CM_URL . 'assets/admin.css',
        array(),
        BYWA_CM_VERSION
    );

    wp_add_inline_script('jquery-core', "
        jQuery(function($){
            var bywaFrame;

            $(document).on('click', '.bywa-media-upload', function(e){
                e.preventDefault();

                var button = $(this);
                var target = $(button.data('target'));
                var preview = button.closest('.bywa-gallery-admin-item').find('.bywa-media-preview');

                bywaFrame = wp.media({
                    title: 'Choisir une image',
                    button: { text: 'Utiliser cette image' },
                    multiple: false
                });

                bywaFrame.on('select', function(){
                    var attachment = bywaFrame.state().get('selection').first().toJSON();
                    target.val(attachment.id);
                    preview.html('<img src=\"' + attachment.url + '\" alt=\"\" style=\"max-width:180px;height:auto;display:block;\">');
                });

                bywaFrame.open();
            });

            $(document).on('click', '.bywa-media-remove', function(e){
                e.preventDefault();

                var button = $(this);
                var target = $(button.data('target'));
                var preview = button.closest('.bywa-gallery-admin-item').find('.bywa-media-preview');

                target.val('');
                preview.html('');
            });

            $(document).on('click', '.bywa-hero-upload-button', function(e){
                e.preventDefault();

                var button = $(this);
                var card = button.closest('.bywa-hero-admin-image-card');
                var input = card.find('.bywa-hero-image-input');
                var preview = card.find('.bywa-hero-image-preview');
                var removeButton = card.find('.bywa-hero-remove-button');

                bywaFrame = wp.media({
                    title: 'Choisir une image pour la bannière',
                    button: { text: 'Utiliser cette image' },
                    multiple: false
                });

                bywaFrame.on('select', function(){
                    var attachment = bywaFrame.state().get('selection').first().toJSON();
                    input.val(attachment.id);
                    preview
                        .addClass('has-image')
                        .html('<img src=\"' + (attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url) + '\" alt=\"\">');
                    removeButton.show();
                });

                bywaFrame.open();
            });

            $(document).on('click', '.bywa-hero-remove-button', function(e){
                e.preventDefault();

                var button = $(this);
                var card = button.closest('.bywa-hero-admin-image-card');
                var input = card.find('.bywa-hero-image-input');
                var preview = card.find('.bywa-hero-image-preview');

                input.val('');
                preview
                    .removeClass('has-image')
                    .html('<span>Aucune image sélectionnée</span>');
                button.hide();
            });
        });
    ");
}
add_action('admin_enqueue_scripts', 'bywa_admin_media_uploader_scripts');
