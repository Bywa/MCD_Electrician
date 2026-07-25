<?php
if (!defined('ABSPATH')) exit;

function bywa_register_cpt_services() {

    $labels = array(
        'name'                  => 'Services',
        'singular_name'         => 'Service',
        'menu_name'             => 'Services',
        'name_admin_bar'        => 'Service',
        'add_new'               => 'Ajouter',
        'add_new_item'          => 'Ajouter un service',
        'new_item'              => 'Nouveau service',
        'edit_item'             => 'Modifier le service',
        'view_item'             => 'Voir le service',
        'all_items'             => 'Tous les services',
        'search_items'          => 'Rechercher des services',
        'not_found'             => 'Aucun service trouvé',
        'not_found_in_trash'    => 'Aucun service dans la corbeille',
    );

    register_post_type('service', array(
        'labels'             => $labels,
        'public'             => true,
        'menu_icon'          => 'dashicons-lightbulb',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'),
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'services', 'with_front' => false),
        'show_in_rest'       => true,
        'menu_position'      => 20,
        'publicly_queryable' => true,
    ));

    register_taxonomy('service_group', 'service', array(
        'labels' => array(
            'name'              => 'Groupes de services',
            'singular_name'     => 'Groupe de services',
            'search_items'      => 'Rechercher un groupe',
            'all_items'         => 'Tous les groupes',
            'edit_item'         => 'Modifier le groupe',
            'update_item'       => 'Mettre à jour le groupe',
            'add_new_item'      => 'Ajouter un groupe',
            'new_item_name'     => 'Nom du nouveau groupe',
            'menu_name'         => 'Groupes',
        ),
        'hierarchical'      => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => array('slug' => 'service-group', 'with_front' => false),
    ));
}
add_action('init', 'bywa_register_cpt_services');