<?php
if (!defined('ABSPATH')) {
    exit;
}

function bywa_tm_add_team_members_meta_box() {
    add_meta_box(
        'bywa_team_members',
        'Composition de l’équipe',
        'bywa_tm_render_team_members_meta_box',
        'team_section',
        'normal',
        'high'
    );

    add_meta_box(
        'bywa_team_presentation',
        'Présentation de l’équipe',
        'bywa_tm_render_team_presentation_meta_box',
        'team_section',
        'normal',
        'default'
    );

    add_meta_box(
        'bywa_team_banner',
        'Bannière mini',
        'bywa_tm_render_team_banner_meta_box',
        'team_section',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'bywa_tm_add_team_members_meta_box');

function bywa_tm_render_team_members_meta_box($post) {
    wp_nonce_field('bywa_tm_save_team_members', 'bywa_tm_team_members_nonce');

    $members = bywa_tm_get_team_members($post->ID);
    $photos_enabled = bywa_tm_team_member_photos_enabled($post->ID);
    ?>
    <p>Ajoutez ici les collaborateurs affichés dans la grille front. L’ordre le plus petit sort en premier.</p>

    <p>
        <label>
            <input type="checkbox" name="bywa_team_member_photos_enabled" value="1" <?php checked($photos_enabled); ?>>
            Activer les photos des membres
        </label>
        <br>
        <span class="description">Si cette option est désactivée, la grille front s’affiche en version simplifiée sans photo.</span>
    </p>

    <div class="bywa-team-admin" data-bywa-team-admin>
        <div class="bywa-team-admin__list" data-bywa-team-list>
            <?php foreach ($members as $index => $member) : ?>
                <?php bywa_tm_render_member_admin_row($index, $member); ?>
            <?php endforeach; ?>
        </div>

        <button type="button" class="button button-secondary" data-bywa-team-add>Ajouter un employé</button>

        <script type="text/template" id="tmpl-bywa-team-member-row">
            <?php bywa_tm_render_member_admin_row('__INDEX__', array(
                'first_name' => '',
                'last_name'  => '',
                'age'        => '',
                'role'       => '',
                'order'      => '',
                'photo_id'   => 0,
            )); ?>
        </script>
    </div>
    <?php
}

function bywa_tm_render_team_presentation_meta_box($post) {
    $presentation = bywa_tm_get_team_presentation($post->ID);
    ?>
    <p>Le titre et la description de cette section viennent du titre WordPress et du contenu principal. Ajoutez ici le kicker, le bouton et jusqu’à 5 photos d’équipe pour le slider.</p>

    <div class="bywa-team-admin bywa-team-admin--presentation" data-bywa-team-gallery-admin>
        <div class="bywa-team-admin__grid">
            <p>
                <label for="bywa_team_kicker"><strong>Kicker</strong></label>
                <input type="text" class="widefat" id="bywa_team_kicker" name="bywa_team_presentation[kicker]" value="<?php echo esc_attr($presentation['kicker']); ?>">
            </p>

            <p>
                <label for="bywa_team_button_label"><strong>Texte du bouton</strong></label>
                <input type="text" class="widefat" id="bywa_team_button_label" name="bywa_team_presentation[button_label]" value="<?php echo esc_attr($presentation['button_label']); ?>">
            </p>

            <p>
                <label for="bywa_team_button_url"><strong>URL du bouton</strong></label>
                <input type="url" class="widefat" id="bywa_team_button_url" name="bywa_team_presentation[button_url]" value="<?php echo esc_attr($presentation['button_url']); ?>">
            </p>
        </div>

        <div class="bywa-team-admin__gallery-head">
            <strong>Photos d’équipe</strong>
            <span>5 images maximum</span>
        </div>

        <div class="bywa-team-admin__gallery" data-bywa-team-gallery-list>
            <?php foreach ($presentation['gallery_ids'] as $index => $attachment_id) : ?>
                <?php bywa_tm_render_gallery_admin_item($index, $attachment_id); ?>
            <?php endforeach; ?>
        </div>

        <button type="button" class="button button-secondary" data-bywa-team-gallery-add <?php disabled(count($presentation['gallery_ids']) >= 5); ?>>Ajouter une photo d’équipe</button>

        <script type="text/template" id="tmpl-bywa-team-gallery-item">
            <?php bywa_tm_render_gallery_admin_item('__INDEX__', 0); ?>
        </script>
    </div>
    <?php
}

function bywa_tm_render_team_banner_meta_box($post) {
    wp_nonce_field('bywa_tm_save_team_banner', 'bywa_tm_team_banner_nonce');

    $kicker         = get_post_meta($post->ID, '_bywa_hero_kicker', true);
    $title          = get_post_meta($post->ID, '_bywa_hero_title', true);
    $text           = get_post_meta($post->ID, '_bywa_hero_text', true);
    $action_1_label = get_post_meta($post->ID, '_bywa_hero_action_1_label', true);
    $action_1_url   = get_post_meta($post->ID, '_bywa_hero_action_1_url', true);
    $action_2_label = get_post_meta($post->ID, '_bywa_hero_action_2_label', true);
    $action_2_url   = get_post_meta($post->ID, '_bywa_hero_action_2_url', true);
    $points         = get_post_meta($post->ID, '_bywa_hero_points', true);
    ?>
    <p>La bannière mini de l’équipe reprend la même logique que les pages. Elle peut être affichée au-dessus de la section présentation.</p>

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
        <textarea id="bywa_hero_points" name="bywa_hero_points" rows="4" class="widefat"><?php echo esc_textarea($points); ?></textarea>
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
                    <button type="button" class="button button-secondary bywa-hero-upload-button">Choisir une image</button>
                    <button type="button" class="button button-link-delete bywa-hero-remove-button"<?php echo $image_url ? '' : ' style="display:none;"'; ?>>Retirer</button>
                </div>
            </div>
        <?php endfor; ?>
    </div>
    <?php
}

function bywa_tm_filter_team_banner_data($data, $post_id, $post_type) {
    if ('team_section' !== $post_type) {
        return $data;
    }

    $title = get_post_meta($post_id, '_bywa_hero_title', true);
    $text = get_post_meta($post_id, '_bywa_hero_text', true);
    $kicker = get_post_meta($post_id, '_bywa_hero_kicker', true);
    $action_1_label = get_post_meta($post_id, '_bywa_hero_action_1_label', true);
    $action_1_url = get_post_meta($post_id, '_bywa_hero_action_1_url', true);
    $action_2_label = get_post_meta($post_id, '_bywa_hero_action_2_label', true);
    $action_2_url = get_post_meta($post_id, '_bywa_hero_action_2_url', true);
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

    $data['kicker'] = $kicker !== '' ? sanitize_text_field($kicker) : (!empty($data['kicker']) ? $data['kicker'] : __('Entreprise', 'bywa-team-manager'));
    $data['title'] = $title !== '' ? sanitize_text_field($title) : get_the_title($post_id);
    $data['text'] = $text !== '' ? sanitize_textarea_field($text) : (!empty($data['text']) ? $data['text'] : '');
    $data['action_1'] = array(
        'label' => $action_1_label !== '' ? sanitize_text_field($action_1_label) : (!empty($data['action_1']['label']) ? $data['action_1']['label'] : ''),
        'url'   => $action_1_url !== '' ? esc_url_raw($action_1_url) : (!empty($data['action_1']['url']) ? $data['action_1']['url'] : ''),
    );
    $data['action_2'] = array(
        'label' => $action_2_label !== '' ? sanitize_text_field($action_2_label) : (!empty($data['action_2']['label']) ? $data['action_2']['label'] : ''),
        'url'   => $action_2_url !== '' ? esc_url_raw($action_2_url) : (!empty($data['action_2']['url']) ? $data['action_2']['url'] : ''),
    );
    $data['points'] = !empty($points) ? $points : (!empty($data['points']) ? $data['points'] : array());
    $data['slides'] = !empty($slides) ? $slides : (!empty($data['slides']) ? $data['slides'] : array());

    return $data;
}
add_filter('bywa_eco_page_hero_data', 'bywa_tm_filter_team_banner_data', 10, 3);

function bywa_tm_render_member_admin_row($index, $member) {
    $photo_id = !empty($member['photo_id']) ? absint($member['photo_id']) : 0;
    $preview = $photo_id ? wp_get_attachment_image_url($photo_id, 'thumbnail') : '';
    ?>
    <div class="bywa-team-admin__item" data-bywa-team-item>
        <div class="bywa-team-admin__item-head">
            <strong>Employé</strong>
            <button type="button" class="button-link-delete" data-bywa-team-remove>Supprimer</button>
        </div>

        <div class="bywa-team-admin__grid">
            <p>
                <label><strong>Prénom</strong></label>
                <input type="text" class="widefat" name="bywa_team_members[<?php echo esc_attr($index); ?>][first_name]" value="<?php echo esc_attr($member['first_name']); ?>">
            </p>

            <p>
                <label><strong>Nom</strong></label>
                <input type="text" class="widefat" name="bywa_team_members[<?php echo esc_attr($index); ?>][last_name]" value="<?php echo esc_attr($member['last_name']); ?>">
            </p>

            <p>
                <label><strong>Âge</strong></label>
                <input type="number" min="0" class="widefat" name="bywa_team_members[<?php echo esc_attr($index); ?>][age]" value="<?php echo esc_attr($member['age']); ?>">
            </p>

            <p>
                <label><strong>Fonction</strong></label>
                <input type="text" class="widefat" name="bywa_team_members[<?php echo esc_attr($index); ?>][role]" value="<?php echo esc_attr($member['role']); ?>">
            </p>

            <p>
                <label><strong>Ordre</strong></label>
                <input type="number" class="widefat" name="bywa_team_members[<?php echo esc_attr($index); ?>][order]" value="<?php echo esc_attr($member['order']); ?>">
            </p>
        </div>

        <div class="bywa-team-admin__photo">
            <input type="hidden" class="bywa-team-admin__photo-input" name="bywa_team_members[<?php echo esc_attr($index); ?>][photo_id]" value="<?php echo esc_attr($photo_id); ?>">

            <div class="bywa-team-admin__photo-preview">
                <?php if ($preview) : ?>
                    <img src="<?php echo esc_url($preview); ?>" alt="">
                <?php else : ?>
                    <span>Aucune photo sélectionnée</span>
                <?php endif; ?>
            </div>

            <div class="bywa-team-admin__photo-actions">
                <button type="button" class="button" data-bywa-team-photo-upload>Choisir une photo</button>
                <button type="button" class="button button-link-delete" data-bywa-team-photo-remove>Retirer</button>
            </div>
        </div>
    </div>
    <?php
}

function bywa_tm_render_gallery_admin_item($index, $attachment_id) {
    $attachment_id = absint($attachment_id);
    $preview = $attachment_id ? wp_get_attachment_image_url($attachment_id, 'medium') : '';
    ?>
    <div class="bywa-team-admin__gallery-item" data-bywa-team-gallery-item>
        <input type="hidden" class="bywa-team-admin__photo-input" name="bywa_team_presentation[gallery_ids][<?php echo esc_attr($index); ?>]" value="<?php echo esc_attr($attachment_id); ?>">

        <div class="bywa-team-admin__gallery-preview bywa-team-admin__photo-preview">
            <?php if ($preview) : ?>
                <img src="<?php echo esc_url($preview); ?>" alt="">
            <?php else : ?>
                <span>Aucune photo sélectionnée</span>
            <?php endif; ?>
        </div>

        <div class="bywa-team-admin__photo-actions">
            <button type="button" class="button" data-bywa-team-photo-upload>Choisir une photo</button>
            <button type="button" class="button button-link-delete" data-bywa-team-gallery-remove>Retirer</button>
        </div>
    </div>
    <?php
}

function bywa_tm_save_team_members_meta($post_id) {
    if (!isset($_POST['bywa_tm_team_members_nonce']) || !wp_verify_nonce($_POST['bywa_tm_team_members_nonce'], 'bywa_tm_save_team_members')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $photos_enabled = isset($_POST['bywa_team_member_photos_enabled']) ? '1' : '0';
    update_post_meta($post_id, '_bywa_team_member_photos_enabled', $photos_enabled);

    if (isset($_POST['bywa_tm_team_banner_nonce']) && wp_verify_nonce($_POST['bywa_tm_team_banner_nonce'], 'bywa_tm_save_team_banner')) {
        if (isset($_POST['bywa_hero_kicker'])) {
            update_post_meta($post_id, '_bywa_hero_kicker', sanitize_text_field($_POST['bywa_hero_kicker']));
        }

        if (isset($_POST['bywa_hero_title'])) {
            update_post_meta($post_id, '_bywa_hero_title', sanitize_text_field($_POST['bywa_hero_title']));
        }

        if (isset($_POST['bywa_hero_text'])) {
            update_post_meta($post_id, '_bywa_hero_text', sanitize_textarea_field($_POST['bywa_hero_text']));
        }

        if (isset($_POST['bywa_hero_action_1_label'])) {
            update_post_meta($post_id, '_bywa_hero_action_1_label', sanitize_text_field($_POST['bywa_hero_action_1_label']));
        }

        if (isset($_POST['bywa_hero_action_1_url'])) {
            update_post_meta($post_id, '_bywa_hero_action_1_url', esc_url_raw($_POST['bywa_hero_action_1_url']));
        }

        if (isset($_POST['bywa_hero_action_2_label'])) {
            update_post_meta($post_id, '_bywa_hero_action_2_label', sanitize_text_field($_POST['bywa_hero_action_2_label']));
        }

        if (isset($_POST['bywa_hero_action_2_url'])) {
            update_post_meta($post_id, '_bywa_hero_action_2_url', esc_url_raw($_POST['bywa_hero_action_2_url']));
        }

        if (isset($_POST['bywa_hero_points'])) {
            update_post_meta($post_id, '_bywa_hero_points', sanitize_textarea_field($_POST['bywa_hero_points']));
        }

        for ($i = 1; $i <= 5; $i++) {
            $input_name = 'bywa_hero_image_' . $i;
            $meta_key = '_bywa_hero_image_' . $i;

            if (isset($_POST[$input_name]) && $_POST[$input_name] !== '') {
                update_post_meta($post_id, $meta_key, absint($_POST[$input_name]));
            } else {
                delete_post_meta($post_id, $meta_key);
            }
        }
    }

    $members = array();

    if (isset($_POST['bywa_team_members']) && is_array($_POST['bywa_team_members'])) {
        foreach ($_POST['bywa_team_members'] as $member) {
            if (!is_array($member)) {
                continue;
            }

            $members[] = array(
                'first_name' => isset($member['first_name']) ? sanitize_text_field($member['first_name']) : '',
                'last_name'  => isset($member['last_name']) ? sanitize_text_field($member['last_name']) : '',
                'age'        => isset($member['age']) ? absint($member['age']) : 0,
                'role'       => isset($member['role']) ? sanitize_text_field($member['role']) : '',
                'order'      => isset($member['order']) ? intval($member['order']) : 0,
                'photo_id'   => isset($member['photo_id']) ? absint($member['photo_id']) : 0,
            );
        }
    }

    if (!empty($members)) {
        update_post_meta($post_id, '_bywa_team_members', $members);
    } else {
        delete_post_meta($post_id, '_bywa_team_members');
    }

    $presentation_input = isset($_POST['bywa_team_presentation']) && is_array($_POST['bywa_team_presentation']) ? $_POST['bywa_team_presentation'] : array();
    $gallery_ids = array();

    if (!empty($presentation_input['gallery_ids']) && is_array($presentation_input['gallery_ids'])) {
        $gallery_ids = array_slice(array_filter(array_map('absint', $presentation_input['gallery_ids'])), 0, 5);
    }

    update_post_meta($post_id, '_bywa_team_presentation', array(
        'kicker'       => isset($presentation_input['kicker']) ? sanitize_text_field($presentation_input['kicker']) : '',
        'button_label' => isset($presentation_input['button_label']) ? sanitize_text_field($presentation_input['button_label']) : '',
        'button_url'   => isset($presentation_input['button_url']) ? esc_url_raw($presentation_input['button_url']) : '',
        'gallery_ids'  => $gallery_ids,
    ));
}
add_action('save_post_team_section', 'bywa_tm_save_team_members_meta');
