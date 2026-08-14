<?php
if (!defined('ABSPATH')) {
    exit;
}

function bywa_tm_csv_to_array($value, $type = 'string') {
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

function bywa_tm_get_team_members($post_id) {
    $members = get_post_meta($post_id, '_bywa_team_members', true);

    if (!is_array($members)) {
        return array();
    }

    $normalized = array();

    foreach ($members as $member) {
        if (!is_array($member)) {
            continue;
        }

        $first_name = isset($member['first_name']) ? sanitize_text_field($member['first_name']) : '';
        $last_name = isset($member['last_name']) ? sanitize_text_field($member['last_name']) : '';
        $age = isset($member['age']) ? absint($member['age']) : 0;
        $role = isset($member['role']) ? sanitize_text_field($member['role']) : '';
        $order = isset($member['order']) ? intval($member['order']) : 0;
        $photo_id = isset($member['photo_id']) ? absint($member['photo_id']) : 0;

        if ($first_name === '' && $last_name === '' && $role === '' && $photo_id === 0) {
            continue;
        }

        $normalized[] = array(
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'age'        => $age,
            'role'       => $role,
            'order'      => $order,
            'photo_id'   => $photo_id,
        );
    }

    usort($normalized, function ($a, $b) {
        if ($a['order'] === $b['order']) {
            return strcmp($a['last_name'] . $a['first_name'], $b['last_name'] . $b['first_name']);
        }

        return $a['order'] <=> $b['order'];
    });

    return $normalized;
}

function bywa_tm_team_member_photos_enabled($post_id) {
    $value = get_post_meta($post_id, '_bywa_team_member_photos_enabled', true);

    if ($value === '' || $value === null) {
        return true;
    }

    return (string) $value === '1';
}

function bywa_tm_get_team_presentation($post_id) {
    $defaults = array(
        'kicker'       => 'Echipă',
        'button_label' => '',
        'button_url'   => '',
        'gallery_ids'  => array(),
    );

    $presentation = get_post_meta($post_id, '_bywa_team_presentation', true);

    if (!is_array($presentation)) {
        $presentation = array();
    }

    $gallery_ids = array();
    if (!empty($presentation['gallery_ids']) && is_array($presentation['gallery_ids'])) {
        $gallery_ids = array_slice(array_filter(array_map('absint', $presentation['gallery_ids'])), 0, 5);
    }

    return array(
        'kicker'       => !empty($presentation['kicker']) ? sanitize_text_field($presentation['kicker']) : $defaults['kicker'],
        'button_label' => !empty($presentation['button_label']) ? sanitize_text_field($presentation['button_label']) : $defaults['button_label'],
        'button_url'   => !empty($presentation['button_url']) ? esc_url_raw($presentation['button_url']) : $defaults['button_url'],
        'gallery_ids'  => $gallery_ids,
    );
}

function bywa_tm_get_team_summary($post_id) {
    $members = bywa_tm_get_team_members($post_id);
    $count = count($members);

    return array(
        'members' => $members,
        'count'   => $count,
        'label'   => sprintf(_n('%s persoană', '%s persoane', $count, 'bywa-team-manager'), number_format_i18n($count)),
    );
}
