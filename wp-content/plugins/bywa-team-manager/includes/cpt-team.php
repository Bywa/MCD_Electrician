<?php
if (!defined('ABSPATH')) {
    exit;
}

function bywa_register_cpt_team_sections() {
    $labels = array(
        'name'               => 'Équipes',
        'singular_name'      => 'Équipe',
        'menu_name'          => 'Équipe',
        'name_admin_bar'     => 'Équipe',
        'add_new'            => 'Ajouter',
        'add_new_item'       => 'Ajouter une équipe',
        'new_item'           => 'Nouvelle équipe',
        'edit_item'          => 'Modifier l’équipe',
        'view_item'          => 'Voir l’équipe',
        'all_items'          => 'Toutes les équipes',
        'search_items'       => 'Rechercher des équipes',
        'not_found'          => 'Aucune équipe trouvée',
        'not_found_in_trash' => 'Aucune équipe dans la corbeille',
    );

    register_post_type('team_section', array(
        'labels'             => $labels,
        'public'             => true,
        'menu_icon'          => 'dashicons-groups',
        'supports'           => array('title', 'editor', 'page-attributes'),
        'has_archive'        => false,
        'show_in_rest'       => true,
        'publicly_queryable' => false,
        'rewrite'            => false,
        'menu_position'      => 22,
    ));
}
add_action('init', 'bywa_register_cpt_team_sections');
