<?php
/**
 * Plugin Name: Bywa Creations – Google Reviews Carousel
 * Plugin URI: https://bywacreations.com/
 * Description: Importe les avis Google d'un établissement via Places API (New), permet de les cacher/réordonner dans l'admin, puis les affiche en carousel dans un style Bywa Creations.
 * Version: 1.0.0
 * Author: Bywa Creations
 * License: GPL2+
 * Text Domain: bywa-google-reviews-carousel
 */

if (!defined('ABSPATH')) {
    exit;
}

final class BYWA_Google_Reviews_Carousel_Plugin {
    const VERSION = '1.0.1';
    const OPTION_KEY = 'wgrc_settings';
    const CPT = 'wgrc_review';
    const SHORTCODE = 'bywa_google_reviews';
    const NONCE_ACTION = 'wgrc_admin_action';

    public function __construct() {
        add_action('init', [$this, 'register_post_type']);
        add_action('admin_menu', [$this, 'register_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_post_wgrc_save_settings', [$this, 'handle_save_settings']);
        add_action('admin_post_wgrc_find_place', [$this, 'handle_find_place']);
        add_action('admin_post_wgrc_import_reviews', [$this, 'handle_import_reviews']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_' . self::CPT, [$this, 'save_review_meta']);
        add_filter('manage_' . self::CPT . '_posts_columns', [$this, 'admin_columns']);
        add_action('manage_' . self::CPT . '_posts_custom_column', [$this, 'render_admin_columns'], 10, 2);
        add_action('wp_enqueue_scripts', [$this, 'register_assets']);
        add_shortcode(self::SHORTCODE, [$this, 'render_shortcode']);

        add_filter('cron_schedules', [$this, 'add_cron_schedules']);
        add_action('wgrc_auto_refresh_reviews', [$this, 'run_scheduled_import']);
        add_action('update_option_' . self::OPTION_KEY, [$this, 'maybe_reschedule_cron'], 10, 2);
    }

    public function get_settings() {
        $defaults = [
            'api_key' => '',
            'place_id' => '',
            'search_query' => '',
            'google_url' => '',
            'section_title' => 'Avis Google',
            'autoplay' => 1,
            'autoplay_timeout' => 5000,
            'show_rating' => 1,
            'show_relative_date' => 1,
            'show_google_link' => 1,
            'open_in_new_tab' => 1,
            'auto_refresh_enabled' => 0,
            'auto_refresh_frequency' => 'weekly',
        ];

        $settings = get_option(self::OPTION_KEY, []);
        if (!is_array($settings)) {
            $settings = [];
        }

        return wp_parse_args($settings, $defaults);
    }

    public function register_post_type() {
        $labels = [
            'name'               => __('Avis Google', 'bywa-google-reviews-carousel'),
            'singular_name'      => __('Avis Google', 'bywa-google-reviews-carousel'),
            'menu_name'          => __('Avis importés', 'bywa-google-reviews-carousel'),
            'name_admin_bar'     => __('Avis Google', 'bywa-google-reviews-carousel'),
            'add_new'            => __('Ajouter', 'bywa-google-reviews-carousel'),
            'add_new_item'       => __('Ajouter un avis', 'bywa-google-reviews-carousel'),
            'new_item'           => __('Nouvel avis', 'bywa-google-reviews-carousel'),
            'edit_item'          => __('Modifier l’avis', 'bywa-google-reviews-carousel'),
            'view_item'          => __('Voir l’avis', 'bywa-google-reviews-carousel'),
            'all_items'          => __('Tous les avis', 'bywa-google-reviews-carousel'),
            'search_items'       => __('Rechercher des avis', 'bywa-google-reviews-carousel'),
            'not_found'          => __('Aucun avis trouvé.', 'bywa-google-reviews-carousel'),
            'not_found_in_trash' => __('Aucun avis dans la corbeille.', 'bywa-google-reviews-carousel'),
        ];

        register_post_type(self::CPT, [
            'labels'             => $labels,
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'show_in_rest'       => false,
            'menu_icon'          => 'dashicons-star-filled',
            'supports'           => ['title', 'page-attributes'],
            'has_archive'        => false,
            'rewrite'            => false,
            'publicly_queryable' => false,
        ]);
    }

    public function register_admin_menu() {
        add_menu_page(
            __('Google Reviews', 'bywa-google-reviews-carousel'),
            __('Google Reviews', 'bywa-google-reviews-carousel'),
            'manage_options',
            'wgrc-settings',
            [$this, 'render_settings_page'],
            'dashicons-star-filled',
            58
        );

        add_submenu_page(
            'wgrc-settings',
            __('Réglages', 'bywa-google-reviews-carousel'),
            __('Réglages', 'bywa-google-reviews-carousel'),
            'manage_options',
            'wgrc-settings',
            [$this, 'render_settings_page']
        );

        add_submenu_page(
            'wgrc-settings',
            __('Avis importés', 'bywa-google-reviews-carousel'),
            __('Avis importés', 'bywa-google-reviews-carousel'),
            'edit_posts',
            'edit.php?post_type=' . self::CPT
        );
    }

    public function register_settings() {
        register_setting(self::OPTION_KEY, self::OPTION_KEY, [$this, 'sanitize_settings']);
    }

    public function sanitize_settings($input) {
        $input = is_array($input) ? $input : [];

        $allowed_frequencies = ['hourly', 'twicedaily', 'daily', 'weekly'];
        $frequency = isset($input['auto_refresh_frequency']) ? sanitize_key($input['auto_refresh_frequency']) : 'weekly';

        if (!in_array($frequency, $allowed_frequencies, true)) {
            $frequency = 'weekly';
        }

        return [
            'api_key' => isset($input['api_key']) ? sanitize_text_field($input['api_key']) : '',
            'place_id' => isset($input['place_id']) ? sanitize_text_field($input['place_id']) : '',
            'search_query' => isset($input['search_query']) ? sanitize_text_field($input['search_query']) : '',
            'google_url' => isset($input['google_url']) ? esc_url_raw($input['google_url']) : '',
            'section_title' => isset($input['section_title']) ? sanitize_text_field($input['section_title']) : 'Avis Google',
            'autoplay' => empty($input['autoplay']) ? 0 : 1,
            'autoplay_timeout' => isset($input['autoplay_timeout']) ? max(2000, absint($input['autoplay_timeout'])) : 5000,
            'show_rating' => empty($input['show_rating']) ? 0 : 1,
            'show_relative_date' => empty($input['show_relative_date']) ? 0 : 1,
            'show_google_link' => empty($input['show_google_link']) ? 0 : 1,
            'open_in_new_tab' => empty($input['open_in_new_tab']) ? 0 : 1,
            'auto_refresh_enabled' => empty($input['auto_refresh_enabled']) ? 0 : 1,
            'auto_refresh_frequency' => $frequency,
        ];
    }

    private function verify_admin_request() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Accès refusé.', 'bywa-google-reviews-carousel'));
        }

        check_admin_referer(self::NONCE_ACTION);
    }

    public function handle_save_settings() {
        $this->verify_admin_request();
        $settings = isset($_POST[self::OPTION_KEY]) ? $this->sanitize_settings(wp_unslash($_POST[self::OPTION_KEY])) : $this->get_settings();
        update_option(self::OPTION_KEY, $settings);
        $this->redirect_settings(['wgrc_notice' => 'saved']);
    }

    public function handle_find_place() {
        $this->verify_admin_request();

        $settings = isset($_POST[self::OPTION_KEY]) ? $this->sanitize_settings(wp_unslash($_POST[self::OPTION_KEY])) : $this->get_settings();
        update_option(self::OPTION_KEY, $settings);

        $result = $this->find_place_by_text_search($settings['search_query'], $settings['api_key']);

        if (is_wp_error($result)) {
            $this->redirect_settings([
                'wgrc_notice' => 'error',
                'wgrc_message' => rawurlencode($result->get_error_message()),
            ]);
        }

        $settings['place_id'] = !empty($result['id']) ? sanitize_text_field($result['id']) : '';
        if (!empty($result['googleMapsUri'])) {
            $settings['google_url'] = esc_url_raw($result['googleMapsUri']);
        }
        update_option(self::OPTION_KEY, $settings);

        $this->redirect_settings([
            'wgrc_notice' => 'place_found',
            'wgrc_message' => rawurlencode(sprintf(
                __('Place ID détecté : %s', 'bywa-google-reviews-carousel'),
                $settings['place_id']
            )),
        ]);
    }

    public function handle_import_reviews() {
        $this->verify_admin_request();

        $settings = isset($_POST[self::OPTION_KEY]) ? $this->sanitize_settings(wp_unslash($_POST[self::OPTION_KEY])) : $this->get_settings();
        update_option(self::OPTION_KEY, $settings);

        $details = $this->get_place_details($settings['place_id'], $settings['api_key']);
        if (is_wp_error($details)) {
            $this->redirect_settings([
                'wgrc_notice' => 'error',
                'wgrc_message' => rawurlencode($details->get_error_message()),
            ]);
        }

        $count = $this->import_reviews_from_place($details, $settings);
        if (is_wp_error($count)) {
            $this->redirect_settings([
                'wgrc_notice' => 'error',
                'wgrc_message' => rawurlencode($count->get_error_message()),
            ]);
        }

        $this->redirect_settings([
            'wgrc_notice' => 'imported',
            'wgrc_message' => rawurlencode(sprintf(
                __('%d avis importé(s) ou mis à jour.', 'bywa-google-reviews-carousel'),
                (int) $count
            )),
        ]);
    }

    private function redirect_settings($args = []) {
        $url = add_query_arg($args, admin_url('admin.php?page=wgrc-settings'));
        wp_safe_redirect($url);
        exit;
    }

    private function get_api_headers($api_key, $field_mask) {
        return [
            'Content-Type' => 'application/json',
            'X-Goog-Api-Key' => trim((string) $api_key),
            'X-Goog-FieldMask' => $field_mask,
        ];
    }

    private function validate_api_key($api_key) {
        $api_key = trim((string) $api_key);
        if ('' === $api_key) {
            return new WP_Error('wgrc_missing_api_key', __('Ajoute une clé API Google Places dans les réglages.', 'bywa-google-reviews-carousel'));
        }
        return true;
    }

    public function find_place_by_text_search($query, $api_key) {
        $valid = $this->validate_api_key($api_key);
        if (is_wp_error($valid)) {
            return $valid;
        }

        $query = trim((string) $query);
        if ('' === $query) {
            return new WP_Error('wgrc_missing_query', __('Ajoute une requête de recherche Google, par ex. “MCD Electrician Bucuresti”.', 'bywa-google-reviews-carousel'));
        }

        $response = wp_remote_post('https://places.googleapis.com/v1/places:searchText', [
            'timeout' => 20,
            'headers' => $this->get_api_headers($api_key, 'places.id,places.displayName,places.formattedAddress,places.googleMapsUri'),
            'body'    => wp_json_encode([
                'textQuery' => $query,
                'languageCode' => 'fr',
            ]),
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $code = (int) wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if ($code < 200 || $code >= 300) {
            $message = !empty($body['error']['message']) ? $body['error']['message'] : __('Erreur Google Places durant la recherche.', 'bywa-google-reviews-carousel');
            return new WP_Error('wgrc_google_search_failed', $message);
        }

        if (empty($body['places'][0])) {
            return new WP_Error('wgrc_no_place', __('Aucun établissement trouvé avec cette recherche.', 'bywa-google-reviews-carousel'));
        }

        return $body['places'][0];
    }

    public function get_place_details($place_id, $api_key) {
        $valid = $this->validate_api_key($api_key);
        if (is_wp_error($valid)) {
            return $valid;
        }

        $place_id = trim((string) $place_id);
        if ('' === $place_id) {
            return new WP_Error('wgrc_missing_place_id', __('Ajoute un Place ID ou utilise le bouton “Trouver le lieu”.', 'bywa-google-reviews-carousel'));
        }

        $endpoint = 'https://places.googleapis.com/v1/places/' . rawurlencode($place_id);
        $response = wp_remote_get($endpoint, [
            'timeout' => 20,
            'headers' => $this->get_api_headers($api_key, 'id,displayName,formattedAddress,googleMapsUri,rating,userRatingCount,reviews'),
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $code = (int) wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if ($code < 200 || $code >= 300) {
            $message = !empty($body['error']['message']) ? $body['error']['message'] : __('Erreur Google Places durant la récupération des avis.', 'bywa-google-reviews-carousel');
            return new WP_Error('wgrc_google_details_failed', $message);
        }

        return is_array($body) ? $body : [];
    }

    private function get_review_unique_key($review) {
        $author = !empty($review['authorAttribution']['displayName']) ? $review['authorAttribution']['displayName'] : '';
        $text = '';
        if (!empty($review['originalText']['text'])) {
            $text = $review['originalText']['text'];
        } elseif (!empty($review['text']['text'])) {
            $text = $review['text']['text'];
        }
        $published = !empty($review['publishTime']) ? $review['publishTime'] : '';
        return md5($author . '|' . $text . '|' . $published);
    }

    private function translate_relative_time($relative_time) {
        $relative_time = trim((string) $relative_time);

        if ('' === $relative_time) {
            return '';
        }

        $direct_map = [
            'today' => __('aujourd’hui', 'bywa-google-reviews-carousel'),
            'yesterday' => __('hier', 'bywa-google-reviews-carousel'),
            'just now' => __('à l’instant', 'bywa-google-reviews-carousel'),
            'a moment ago' => __('à l’instant', 'bywa-google-reviews-carousel'),
            'an hour ago' => __('il y a 1 heure', 'bywa-google-reviews-carousel'),
            'a day ago' => __('il y a 1 jour', 'bywa-google-reviews-carousel'),
            'a week ago' => __('il y a 1 semaine', 'bywa-google-reviews-carousel'),
            'a month ago' => __('il y a 1 mois', 'bywa-google-reviews-carousel'),
            'a year ago' => __('il y a 1 an', 'bywa-google-reviews-carousel'),
        ];

        $normalized = strtolower($relative_time);
        if (isset($direct_map[$normalized])) {
            return $direct_map[$normalized];
        }

        $patterns = [
            '/^(\d+)\s+seconds?\s+ago$/i' => __('il y a %d secondes', 'bywa-google-reviews-carousel'),
            '/^(\d+)\s+minutes?\s+ago$/i' => __('il y a %d minutes', 'bywa-google-reviews-carousel'),
            '/^(\d+)\s+hours?\s+ago$/i' => __('il y a %d heures', 'bywa-google-reviews-carousel'),
            '/^(\d+)\s+days?\s+ago$/i' => __('il y a %d jours', 'bywa-google-reviews-carousel'),
            '/^(\d+)\s+weeks?\s+ago$/i' => __('il y a %d semaines', 'bywa-google-reviews-carousel'),
            '/^(\d+)\s+months?\s+ago$/i' => __('il y a %d mois', 'bywa-google-reviews-carousel'),
            '/^(\d+)\s+years?\s+ago$/i' => __('il y a %d ans', 'bywa-google-reviews-carousel'),
        ];

        foreach ($patterns as $pattern => $translation) {
            if (preg_match($pattern, $relative_time, $matches)) {
                return sprintf($translation, (int) $matches[1]);
            }
        }

        return $relative_time;
    }

    private function normalize_author_name($name) {
        $name = trim(preg_replace('/\s+/u', ' ', wp_strip_all_tags((string) $name)));

        if ($name === '') {
            return __('Client vérifié', 'bywa-google-reviews-carousel');
        }

        if (function_exists('mb_convert_case')) {
            $name = mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
        } else {
            $name = ucwords(strtolower($name));
        }

        return $name;
    }

    private function find_existing_review_post($review_key) {
        $posts = get_posts([
            'post_type' => self::CPT,
            'post_status' => ['publish', 'draft', 'private'],
            'posts_per_page' => 1,
            'fields' => 'ids',
            'meta_key' => '_wgrc_review_key',
            'meta_value' => $review_key,
        ]);

        return !empty($posts[0]) ? (int) $posts[0] : 0;
    }

    public function import_reviews_from_place($place, $settings) {
        if (empty($place['reviews']) || !is_array($place['reviews'])) {
            return new WP_Error('wgrc_no_reviews', __('Aucun avis Google n’a été renvoyé par l’API pour ce lieu.', 'bywa-google-reviews-carousel'));
        }

        $imported = 0;
        foreach ($place['reviews'] as $index => $review) {
            $review_key = $this->get_review_unique_key($review);
            $existing_id = $this->find_existing_review_post($review_key);
            $author_name = !empty($review['authorAttribution']['displayName']) ? sanitize_text_field($review['authorAttribution']['displayName']) : __('Client vérifié', 'bywa-google-reviews-carousel');
            $author_name = $this->normalize_author_name($author_name);
            $review_text = '';
            if (!empty($review['originalText']['text'])) {
                $review_text = sanitize_textarea_field($review['originalText']['text']);
            } elseif (!empty($review['text']['text'])) {
                $review_text = sanitize_textarea_field($review['text']['text']);
            }

            $title = $author_name;
            if ($review_text !== '') {
                $words = preg_split('/\s+/', wp_strip_all_tags($review_text));
                $excerpt = implode(' ', array_slice((array) $words, 0, 6));
                if ($excerpt !== '') {
                    $title = $excerpt;
                    if (count((array) $words) > 6) {
                        $title .= '…';
                    }
                }
            }

            $postarr = [
                'post_type' => self::CPT,
                'post_status' => 'publish',
                'post_title' => $title,
                'menu_order' => $existing_id ? (int) get_post_field('menu_order', $existing_id) : ($index + 1),
            ];
            if ($existing_id) {
                $postarr['ID'] = $existing_id;
            }

            $post_id = wp_insert_post(wp_slash($postarr), true);
            if (is_wp_error($post_id)) {
                continue;
            }

            update_post_meta($post_id, '_wgrc_review_key', $review_key);
            update_post_meta($post_id, '_wgrc_author_name', $author_name);
            update_post_meta($post_id, '_wgrc_author_url', !empty($review['authorAttribution']['uri']) ? esc_url_raw($review['authorAttribution']['uri']) : '');
            update_post_meta($post_id, '_wgrc_author_photo', !empty($review['authorAttribution']['photoUri']) ? esc_url_raw($review['authorAttribution']['photoUri']) : '');
            update_post_meta($post_id, '_wgrc_review_text', $review_text);
            update_post_meta($post_id, '_wgrc_rating', isset($review['rating']) ? max(1, min(5, absint($review['rating']))) : 5);
            $relative_time = !empty($review['relativePublishTimeDescription']) ? sanitize_text_field($review['relativePublishTimeDescription']) : '';
            update_post_meta($post_id, '_wgrc_relative_time', $this->translate_relative_time($relative_time));
            update_post_meta($post_id, '_wgrc_publish_time', !empty($review['publishTime']) ? sanitize_text_field($review['publishTime']) : '');
            update_post_meta($post_id, '_wgrc_google_maps_uri', !empty($place['googleMapsUri']) ? esc_url_raw($place['googleMapsUri']) : '');
            update_post_meta($post_id, '_wgrc_place_name', !empty($place['displayName']['text']) ? sanitize_text_field($place['displayName']['text']) : '');
            update_post_meta($post_id, '_wgrc_place_address', !empty($place['formattedAddress']) ? sanitize_text_field($place['formattedAddress']) : '');
            if (!metadata_exists('post', $post_id, '_wgrc_hidden')) {
                update_post_meta($post_id, '_wgrc_hidden', 0);
            }

            $imported++;
        }

        return $imported;
    }

    public function add_meta_boxes() {
        add_meta_box(
            'wgrc_review_details',
            __('Détails de l’avis Google', 'bywa-google-reviews-carousel'),
            [$this, 'render_review_meta_box'],
            self::CPT,
            'normal',
            'high'
        );
    }

    public function render_review_meta_box($post) {
        wp_nonce_field('wgrc_save_review', 'wgrc_review_nonce');

        $author_name = get_post_meta($post->ID, '_wgrc_author_name', true);
        $review_text = get_post_meta($post->ID, '_wgrc_review_text', true);
        $rating = (int) get_post_meta($post->ID, '_wgrc_rating', true);
        $relative_time = get_post_meta($post->ID, '_wgrc_relative_time', true);
        $author_url = get_post_meta($post->ID, '_wgrc_author_url', true);
        $google_maps_uri = get_post_meta($post->ID, '_wgrc_google_maps_uri', true);
        $hidden = (int) get_post_meta($post->ID, '_wgrc_hidden', true);
        ?>
        <style>
            .wgrc-admin-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}.wgrc-admin-field{margin-bottom:16px}.wgrc-admin-field--full{grid-column:1/-1}.wgrc-admin-field label{display:block;font-weight:600;margin-bottom:6px}.wgrc-admin-field input,.wgrc-admin-field textarea,.wgrc-admin-field select{width:100%;max-width:100%}.wgrc-admin-note{margin:8px 0 0;color:#666}
        </style>
        <div class="wgrc-admin-grid">
            <div class="wgrc-admin-field">
                <label for="wgrc_author_name"><?php esc_html_e('Nom affiché', 'bywa-google-reviews-carousel'); ?></label>
                <input type="text" id="wgrc_author_name" name="wgrc_author_name" value="<?php echo esc_attr($author_name); ?>">
            </div>
            <div class="wgrc-admin-field">
                <label for="wgrc_rating"><?php esc_html_e('Note', 'bywa-google-reviews-carousel'); ?></label>
                <select id="wgrc_rating" name="wgrc_rating">
                    <?php for ($i = 5; $i >= 1; $i--) : ?>
                        <option value="<?php echo esc_attr($i); ?>" <?php selected($rating, $i); ?>><?php echo esc_html($i . '/5'); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="wgrc-admin-field">
                <label for="wgrc_relative_time"><?php esc_html_e('Date relative', 'bywa-google-reviews-carousel'); ?></label>
                <input type="text" id="wgrc_relative_time" name="wgrc_relative_time" value="<?php echo esc_attr($relative_time); ?>">
            </div>
            <div class="wgrc-admin-field">
                <label for="wgrc_hidden">
                    <input type="checkbox" id="wgrc_hidden" name="wgrc_hidden" value="1" <?php checked($hidden, 1); ?>>
                    <?php esc_html_e('Cacher cet avis au front', 'bywa-google-reviews-carousel'); ?>
                </label>
            </div>
            <div class="wgrc-admin-field wgrc-admin-field--full">
                <label for="wgrc_review_text"><?php esc_html_e('Texte', 'bywa-google-reviews-carousel'); ?></label>
                <textarea id="wgrc_review_text" name="wgrc_review_text" rows="8"><?php echo esc_textarea($review_text); ?></textarea>
            </div>
        </div>
        <p class="wgrc-admin-note"><strong><?php esc_html_e('Ordre manuel :', 'bywa-google-reviews-carousel'); ?></strong> <?php esc_html_e('Utilise le champ “Ordre” de WordPress pour réordonner les cartes.', 'bywa-google-reviews-carousel'); ?></p>
        <?php if ($author_url) : ?>
            <p><a href="<?php echo esc_url($author_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Voir le profil auteur', 'bywa-google-reviews-carousel'); ?></a></p>
        <?php endif; ?>
        <?php if ($google_maps_uri) : ?>
            <p><a href="<?php echo esc_url($google_maps_uri); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Voir la fiche Google', 'bywa-google-reviews-carousel'); ?></a></p>
        <?php endif; ?>
        <?php
    }

    public function save_review_meta($post_id) {
        if (!isset($_POST['wgrc_review_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wgrc_review_nonce'])), 'wgrc_save_review')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $author_name = isset($_POST['wgrc_author_name']) ? sanitize_text_field(wp_unslash($_POST['wgrc_author_name'])) : '';
        update_post_meta($post_id, '_wgrc_author_name', $this->normalize_author_name($author_name));
        update_post_meta($post_id, '_wgrc_rating', isset($_POST['wgrc_rating']) ? max(1, min(5, absint(wp_unslash($_POST['wgrc_rating'])))) : 5);
        $relative_time = isset($_POST['wgrc_relative_time']) ? sanitize_text_field(wp_unslash($_POST['wgrc_relative_time'])) : '';
        update_post_meta($post_id, '_wgrc_relative_time', $this->translate_relative_time($relative_time));
        update_post_meta($post_id, '_wgrc_review_text', isset($_POST['wgrc_review_text']) ? sanitize_textarea_field(wp_unslash($_POST['wgrc_review_text'])) : '');
        update_post_meta($post_id, '_wgrc_hidden', isset($_POST['wgrc_hidden']) ? 1 : 0);
    }

    public function admin_columns($columns) {
        $new = [];
        foreach ($columns as $key => $label) {
            $new[$key] = $label;
            if ('title' === $key) {
                $new['wgrc_author'] = __('Auteur', 'bywa-google-reviews-carousel');
                $new['wgrc_rating'] = __('Note', 'bywa-google-reviews-carousel');
                $new['wgrc_relative_time'] = __('Date', 'bywa-google-reviews-carousel');
                $new['wgrc_hidden'] = __('Visible', 'bywa-google-reviews-carousel');
                $new['menu_order'] = __('Ordre', 'bywa-google-reviews-carousel');
            }
        }
        return $new;
    }

    public function render_admin_columns($column, $post_id) {
        if ('wgrc_author' === $column) {
            echo esc_html((string) get_post_meta($post_id, '_wgrc_author_name', true));
        }

        if ('wgrc_rating' === $column) {
            $rating = (int) get_post_meta($post_id, '_wgrc_rating', true);
            echo esc_html(str_repeat('★', max(1, min(5, $rating ?: 5))));
        }

        if ('wgrc_relative_time' === $column) {
            echo esc_html((string) get_post_meta($post_id, '_wgrc_relative_time', true));
        }

        if ('wgrc_hidden' === $column) {
            $hidden = (int) get_post_meta($post_id, '_wgrc_hidden', true);
            echo $hidden ? esc_html__('Non', 'bywa-google-reviews-carousel') : esc_html__('Oui', 'bywa-google-reviews-carousel');
        }

        if ('menu_order' === $column) {
            echo esc_html((string) get_post_field('menu_order', $post_id));
        }
    }

    public function register_assets() {
        wp_register_style('wgrc-owl-carousel', plugin_dir_url(__FILE__) . 'assets/lib/owl.carousel.min.css', [], self::VERSION);
        wp_register_style('wgrc-frontend', plugin_dir_url(__FILE__) . 'assets/css/wgrc-frontend.css', ['wgrc-owl-carousel'], self::VERSION);
        wp_register_script('wgrc-owl-carousel', plugin_dir_url(__FILE__) . 'assets/lib/owl.carousel.min.js', ['jquery'], self::VERSION, true);
        wp_register_script('wgrc-frontend', plugin_dir_url(__FILE__) . 'assets/js/wgrc-frontend.js', ['jquery', 'wgrc-owl-carousel'], self::VERSION, true);
    }

    private function get_shortcode_defaults() {
        $settings = $this->get_settings();
        return [
            'title' => $settings['section_title'],
            'posts_per_page' => -1,
            'autoplay' => !empty($settings['autoplay']) ? 'true' : 'false',
            'autoplay_timeout' => (int) $settings['autoplay_timeout'],
            'show_rating' => !empty($settings['show_rating']) ? 'true' : 'false',
            'show_relative_date' => !empty($settings['show_relative_date']) ? 'true' : 'false',
            'show_google_link' => !empty($settings['show_google_link']) ? 'true' : 'false',
            'open_in_new_tab' => !empty($settings['open_in_new_tab']) ? 'true' : 'false',
            'only_visible' => 'true',
        ];
    }

    public function render_shortcode($atts) {
        $atts = shortcode_atts($this->get_shortcode_defaults(), $atts, self::SHORTCODE);
        $visible_items = max(1, (int) $atts['posts_per_page']);

        $meta_query = [];
        if (filter_var($atts['only_visible'], FILTER_VALIDATE_BOOLEAN)) {
            $meta_query[] = [
                'relation' => 'OR',
                [
                    'key' => '_wgrc_hidden',
                    'compare' => 'NOT EXISTS',
                ],
                [
                    'key' => '_wgrc_hidden',
                    'value' => '0',
                    'compare' => '=',
                ],
            ];
        }

        $query = new WP_Query([
            'post_type' => self::CPT,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => ['menu_order' => 'ASC', 'date' => 'DESC'],
            'order' => 'ASC',
            'meta_query' => $meta_query,
        ]);

        if (!$query->have_posts()) {
            return '';
        }

        wp_enqueue_style('wgrc-frontend');
        wp_enqueue_script('wgrc-frontend');

        $uid = 'wgrc-carousel-' . wp_rand(1000, 999999);
        $config = [
            'autoplay' => filter_var($atts['autoplay'], FILTER_VALIDATE_BOOLEAN),
            'autoplayTimeout' => (int) $atts['autoplay_timeout'],
            'visibleItems' => $visible_items,
            'rtl' => is_rtl(),
        ];
        $show_rating = filter_var($atts['show_rating'], FILTER_VALIDATE_BOOLEAN);
        $show_relative_date = filter_var($atts['show_relative_date'], FILTER_VALIDATE_BOOLEAN);
        $show_google_link = filter_var($atts['show_google_link'], FILTER_VALIDATE_BOOLEAN);
        $open_in_new_tab = filter_var($atts['open_in_new_tab'], FILTER_VALIDATE_BOOLEAN);

        ob_start();
        ?>
        <section class="bywa-google-reviews bywa-section" aria-label="<?php echo esc_attr($atts['title']); ?>">
            <div class="container">
                <?php if (!empty($atts['title'])) : ?>
                    <div class="bywa-section-head bywa-section-head-light">
                        <span class="bywa-section-kicker"><?php esc_html_e('Témoignages', 'bywa-google-reviews-carousel'); ?></span>
                        <h2><?php echo esc_html($atts['title']); ?></h2>
                    </div>
                <?php endif; ?>

                <div class="bywa-google-reviews__carousel ntc-carousel owl-carousel" id="<?php echo esc_attr($uid); ?>" data-ntc-config="<?php echo esc_attr(wp_json_encode($config)); ?>">
                    <?php while ($query->have_posts()) : $query->the_post(); ?>
                        <?php
                        $post_id = get_the_ID();
                        $author_name = $this->normalize_author_name((string) get_post_meta($post_id, '_wgrc_author_name', true));
                        $review_text = (string) get_post_meta($post_id, '_wgrc_review_text', true);
                        $rating = (int) get_post_meta($post_id, '_wgrc_rating', true);
                        $rating = max(1, min(5, $rating ?: 5));
                        $relative_time = $this->translate_relative_time((string) get_post_meta($post_id, '_wgrc_relative_time', true));
                        $google_maps_uri = (string) get_post_meta($post_id, '_wgrc_google_maps_uri', true);
                        $link_target = $open_in_new_tab ? ' target="_blank" rel="noopener noreferrer"' : '';
                        ?>
                        <article class="bywa-google-reviews__card">
                            <div class="bywa-google-reviews__inner">
                                <div class="bywa-google-reviews__quote-icon">&#10077;</div>

                                <?php if ($show_rating) : ?>
                                    <div class="bywa-google-reviews__rating" aria-label="<?php echo esc_attr(sprintf(__('Note de %d sur 5', 'bywa-google-reviews-carousel'), $rating)); ?>">
                                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                                            <span class="bywa-google-reviews__star<?php echo $i <= $rating ? ' is-active' : ''; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($review_text)) : ?>
                                    <p><?php echo esc_html($review_text); ?></p>
                                <?php endif; ?>

                                <?php if (!empty($author_name)) : ?>
                                    <h3><?php echo esc_html($author_name); ?></h3>
                                <?php endif; ?>

                                <?php if ($show_relative_date && !empty($relative_time)) : ?>
                                    <span><?php echo esc_html($relative_time); ?></span>
                                <?php endif; ?>

                                <?php if ($show_google_link && !empty($google_maps_uri)) : ?>
                                    <div class="bywa-google-reviews__link">
                                        <a href="<?php echo esc_url($google_maps_uri); ?>"<?php echo $link_target; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
                                            <?php esc_html_e('Voir sur Google', 'bywa-google-reviews-carousel'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
        <?php
        wp_reset_postdata();

        return ob_get_clean();
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $settings = $this->get_settings();
        $notice = isset($_GET['wgrc_notice']) ? sanitize_key(wp_unslash($_GET['wgrc_notice'])) : '';
        $message = isset($_GET['wgrc_message']) ? sanitize_text_field(wp_unslash($_GET['wgrc_message'])) : '';
        $review_count = (int) wp_count_posts(self::CPT)->publish;
        $google_url = !empty($settings['google_url']) ? $settings['google_url'] : (!empty($settings['search_query']) ? 'https://www.google.com/search?q=' . rawurlencode($settings['search_query']) : '');
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Bywa Creations – Google Reviews Carousel', 'bywa-google-reviews-carousel'); ?></h1>

            <?php if ($notice) : ?>
                <?php
                $class = 'notice notice-success';
                if ('error' === $notice) {
                    $class = 'notice notice-error';
                }
                ?>
                <div class="<?php echo esc_attr($class); ?> is-dismissible"><p><?php echo esc_html($message ? $message : __('Réglages enregistrés.', 'bywa-google-reviews-carousel')); ?></p></div>
            <?php endif; ?>

            <p><?php esc_html_e('Ce plugin importe les avis Google dans WordPress. Tu peux ensuite les cacher, corriger le texte, changer l’ordre, puis les afficher avec un shortcode.', 'bywa-google-reviews-carousel'); ?></p>

            <div style="display:grid;grid-template-columns:minmax(320px,780px) minmax(260px,1fr);gap:24px;align-items:start;">
                <div style="background:#fff;border:1px solid #dcdcde;padding:24px;">
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <?php wp_nonce_field(self::NONCE_ACTION); ?>
                        <input type="hidden" name="action" value="wgrc_save_settings">
                        <table class="form-table" role="presentation">
                            <tbody>
                                <tr>
                                    <th scope="row"><label for="wgrc_api_key"><?php esc_html_e('Clé API Google', 'bywa-google-reviews-carousel'); ?></label></th>
                                    <td>
                                        <input type="text" class="regular-text" id="wgrc_api_key" name="<?php echo esc_attr(self::OPTION_KEY); ?>[api_key]" value="<?php echo esc_attr($settings['api_key']); ?>">
                                        <p class="description"><?php esc_html_e('Utilise une clé Google Maps Platform avec Places API (New) activée.', 'bywa-google-reviews-carousel'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="wgrc_search_query"><?php esc_html_e('Recherche Google / nom établissement', 'bywa-google-reviews-carousel'); ?></label></th>
                                    <td>
                                        <input type="text" class="regular-text" id="wgrc_search_query" name="<?php echo esc_attr(self::OPTION_KEY); ?>[search_query]" value="<?php echo esc_attr($settings['search_query']); ?>" placeholder="MCD Electrician Bucuresti">
                                        <p class="description"><?php esc_html_e('Sert à retrouver automatiquement le Place ID.', 'bywa-google-reviews-carousel'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="wgrc_place_id"><?php esc_html_e('Place ID Google', 'bywa-google-reviews-carousel'); ?></label></th>
                                    <td>
                                        <input type="text" class="regular-text" id="wgrc_place_id" name="<?php echo esc_attr(self::OPTION_KEY); ?>[place_id]" value="<?php echo esc_attr($settings['place_id']); ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="wgrc_google_url"><?php esc_html_e('Lien Google', 'bywa-google-reviews-carousel'); ?></label></th>
                                    <td>
                                        <input type="url" class="regular-text" id="wgrc_google_url" name="<?php echo esc_attr(self::OPTION_KEY); ?>[google_url]" value="<?php echo esc_attr($settings['google_url']); ?>">
                                        <p class="description"><?php esc_html_e('Optionnel. Utilisé pour le lien admin et comme fallback.', 'bywa-google-reviews-carousel'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="wgrc_section_title"><?php esc_html_e('Titre par défaut de la section', 'bywa-google-reviews-carousel'); ?></label></th>
                                    <td><input type="text" class="regular-text" id="wgrc_section_title" name="<?php echo esc_attr(self::OPTION_KEY); ?>[section_title]" value="<?php echo esc_attr($settings['section_title']); ?>"></td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php esc_html_e('Options front', 'bywa-google-reviews-carousel'); ?></th>
                                    <td>
                                        <label><input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY); ?>[autoplay]" value="1" <?php checked(!empty($settings['autoplay'])); ?>> <?php esc_html_e('Autoplay', 'bywa-google-reviews-carousel'); ?></label><br>
                                        <label><input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY); ?>[show_rating]" value="1" <?php checked(!empty($settings['show_rating'])); ?>> <?php esc_html_e('Afficher la note', 'bywa-google-reviews-carousel'); ?></label><br>
                                        <label><input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY); ?>[show_relative_date]" value="1" <?php checked(!empty($settings['show_relative_date'])); ?>> <?php esc_html_e('Afficher la date relative', 'bywa-google-reviews-carousel'); ?></label><br>
                                        <label><input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY); ?>[show_google_link]" value="1" <?php checked(!empty($settings['show_google_link'])); ?>> <?php esc_html_e('Afficher le lien Google', 'bywa-google-reviews-carousel'); ?></label><br>
                                        <label><input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY); ?>[open_in_new_tab]" value="1" <?php checked(!empty($settings['open_in_new_tab'])); ?>> <?php esc_html_e('Ouvrir le lien dans un nouvel onglet', 'bywa-google-reviews-carousel'); ?></label>
                                        <p style="margin-top:10px;">
                                            <label for="wgrc_autoplay_timeout"><?php esc_html_e('Délai autoplay (ms)', 'bywa-google-reviews-carousel'); ?></label><br>
                                            <input type="number" min="2000" step="500" id="wgrc_autoplay_timeout" name="<?php echo esc_attr(self::OPTION_KEY); ?>[autoplay_timeout]" value="<?php echo esc_attr((string) $settings['autoplay_timeout']); ?>">
                                        </p>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row"><?php esc_html_e('Auto-refresh des avis', 'bywa-google-reviews-carousel'); ?></th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY); ?>[auto_refresh_enabled]" value="1" <?php checked(!empty($settings['auto_refresh_enabled'])); ?>>
                                            <?php esc_html_e('Activer la mise à jour automatique', 'bywa-google-reviews-carousel'); ?>
                                        </label>

                                        <p style="margin-top:10px;">
                                            <label for="wgrc_auto_refresh_frequency"><?php esc_html_e('Fréquence', 'bywa-google-reviews-carousel'); ?></label><br>
                                            <select id="wgrc_auto_refresh_frequency" name="<?php echo esc_attr(self::OPTION_KEY); ?>[auto_refresh_frequency]">
                                                <option value="hourly" <?php selected($settings['auto_refresh_frequency'], 'hourly'); ?>><?php esc_html_e('Toutes les heures', 'bywa-google-reviews-carousel'); ?></option>
                                                <option value="twicedaily" <?php selected($settings['auto_refresh_frequency'], 'twicedaily'); ?>><?php esc_html_e('Deux fois par jour', 'bywa-google-reviews-carousel'); ?></option>
                                                <option value="daily" <?php selected($settings['auto_refresh_frequency'], 'daily'); ?>><?php esc_html_e('Tous les jours', 'bywa-google-reviews-carousel'); ?></option>
                                                <option value="weekly" <?php selected($settings['auto_refresh_frequency'], 'weekly'); ?>><?php esc_html_e('Toutes les semaines', 'bywa-google-reviews-carousel'); ?></option>
                                            </select>
                                        </p>

                                        <?php $last_refresh = get_option('wgrc_last_auto_refresh'); ?>
                                        <?php if (!empty($last_refresh)) : ?>
                                            <p class="description">
                                                <?php echo esc_html(sprintf(__('Dernier auto-refresh : %s', 'bywa-google-reviews-carousel'), $last_refresh)); ?>
                                            </p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <p>
                            <button type="submit" class="button button-primary"><?php esc_html_e('Enregistrer', 'bywa-google-reviews-carousel'); ?></button>
                        </p>
                    </form>

                    <hr>

                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                            <?php wp_nonce_field(self::NONCE_ACTION); ?>
                            <input type="hidden" name="action" value="wgrc_find_place">
                            <?php foreach ($settings as $key => $value) : ?>
                                <?php if (is_scalar($value)) : ?>
                                    <input type="hidden" name="<?php echo esc_attr(self::OPTION_KEY); ?>[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr((string) $value); ?>">
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <button type="submit" class="button button-secondary"><?php esc_html_e('Trouver le lieu', 'bywa-google-reviews-carousel'); ?></button>
                        </form>

                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                            <?php wp_nonce_field(self::NONCE_ACTION); ?>
                            <input type="hidden" name="action" value="wgrc_import_reviews">
                            <?php foreach ($settings as $key => $value) : ?>
                                <?php if (is_scalar($value)) : ?>
                                    <input type="hidden" name="<?php echo esc_attr(self::OPTION_KEY); ?>[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr((string) $value); ?>">
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <button type="submit" class="button button-primary"><?php esc_html_e('Importer / rafraîchir les avis', 'bywa-google-reviews-carousel'); ?></button>
                        </form>

                        <?php if ($google_url) : ?>
                            <a href="<?php echo esc_url($google_url); ?>" target="_blank" rel="noopener noreferrer" class="button"><?php esc_html_e('Ouvrir Google', 'bywa-google-reviews-carousel'); ?></a>
                        <?php endif; ?>
                    </div>
                </div>

                <div style="background:#fff;border:1px solid #dcdcde;padding:24px;">
                    <h2 style="margin-top:0;"><?php esc_html_e('Utilisation', 'bywa-google-reviews-carousel'); ?></h2>
                    <p><strong><?php esc_html_e('Shortcode de base :', 'bywa-google-reviews-carousel'); ?></strong></p>
                    <code>[bywa_google_reviews]</code>

                    <p style="margin-top:16px;"><strong><?php esc_html_e('Exemple avec options :', 'bywa-google-reviews-carousel'); ?></strong></p>
                    <code>[bywa_google_reviews title="Avis Google" posts_per_page="4" autoplay="true" autoplay_timeout="5000"]</code>

                    <p style="margin-top:16px;"><strong><?php esc_html_e('Avis importés :', 'bywa-google-reviews-carousel'); ?></strong> <?php echo esc_html((string) $review_count); ?></p>
                    <p><a href="<?php echo esc_url(admin_url('edit.php?post_type=' . self::CPT)); ?>"><?php esc_html_e('Gérer les avis importés', 'bywa-google-reviews-carousel'); ?></a></p>

                    <hr>
                    <p><strong><?php esc_html_e('Conseil', 'bywa-google-reviews-carousel'); ?> :</strong> <?php esc_html_e('Après import, va dans “Avis importés” pour cacher certains avis, renommer les titres des cartes et régler l’ordre.', 'bywa-google-reviews-carousel'); ?></p>
                </div>
            </div>
        </div>
        <?php
    }

        public function add_cron_schedules($schedules) {
        if (!isset($schedules['weekly'])) {
            $schedules['weekly'] = [
                'interval' => 7 * DAY_IN_SECONDS,
                'display'  => __('Une fois par semaine', 'bywa-google-reviews-carousel'),
            ];
        }

        return $schedules;
    }

    public function ensure_cron_scheduled() {
        $settings = $this->get_settings();

        wp_clear_scheduled_hook('wgrc_auto_refresh_reviews');

        if (empty($settings['auto_refresh_enabled'])) {
            return;
        }

        $frequency = !empty($settings['auto_refresh_frequency']) ? $settings['auto_refresh_frequency'] : 'weekly';

        if (!wp_next_scheduled('wgrc_auto_refresh_reviews')) {
            wp_schedule_event(time() + 300, $frequency, 'wgrc_auto_refresh_reviews');
        }
    }

    public function maybe_reschedule_cron($old_value, $value) {
        $this->ensure_cron_scheduled();
    }

    public function run_scheduled_import() {
        $settings = $this->get_settings();

        if (empty($settings['auto_refresh_enabled'])) {
            return;
        }

        if (empty($settings['api_key']) || empty($settings['place_id'])) {
            return;
        }

        $details = $this->get_place_details($settings['place_id'], $settings['api_key']);
        if (is_wp_error($details)) {
            return;
        }

        $result = $this->import_reviews_from_place($details, $settings);
        if (is_wp_error($result)) {
            return;
        }

        update_option('wgrc_last_auto_refresh', current_time('mysql'));
    }
}

$wgrc_plugin_instance = new BYWA_Google_Reviews_Carousel_Plugin();

register_activation_hook(__FILE__, function() use ($wgrc_plugin_instance) {
    if ($wgrc_plugin_instance instanceof BYWA_Google_Reviews_Carousel_Plugin) {
        $wgrc_plugin_instance->ensure_cron_scheduled();
    }
});

register_deactivation_hook(__FILE__, function() {
    wp_clear_scheduled_hook('wgrc_auto_refresh_reviews');
});
