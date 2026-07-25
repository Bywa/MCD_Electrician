<?php
if (!defined('ABSPATH')) exit;

function bywa_register_cpt_realisations() {

    $labels = array(
        'name'                  => 'Réalisations',
        'singular_name'         => 'Réalisation',
        'menu_name'             => 'Réalisations',
        'name_admin_bar'        => 'Réalisation',
        'add_new'               => 'Ajouter',
        'add_new_item'          => 'Ajouter une réalisation',
        'new_item'              => 'Nouvelle réalisation',
        'edit_item'             => 'Modifier la réalisation',
        'view_item'             => 'Voir la réalisation',
        'all_items'             => 'Toutes les réalisations',
        'search_items'          => 'Rechercher des réalisations',
        'not_found'             => 'Aucune réalisation trouvée',
        'not_found_in_trash'    => 'Aucune réalisation dans la corbeille',
    );

    register_post_type('realisation', array(
        'labels'             => $labels,
        'public'             => true,
        'menu_icon'          => 'dashicons-building',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'),
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'realisations', 'with_front' => false),
        'show_in_rest'       => true,
        'menu_position'      => 21,
        'publicly_queryable' => true,
    ));

    register_taxonomy('type_realisation', 'realisation', array(
        'labels' => array(
            'name'              => 'Types de réalisations',
            'singular_name'     => 'Type de réalisation',
            'search_items'      => 'Rechercher un type',
            'all_items'         => 'Tous les types',
            'edit_item'         => 'Modifier le type',
            'update_item'       => 'Mettre à jour le type',
            'add_new_item'      => 'Ajouter un type',
            'new_item_name'     => 'Nom du nouveau type',
            'menu_name'         => 'Types',
        ),
        'hierarchical'      => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => array('slug' => 'type-realisation', 'with_front' => false),
    ));
}
add_action('init', 'bywa_register_cpt_realisations');