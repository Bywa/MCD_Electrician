<?php
if (!defined('ABSPATH')) exit;

function bywa_register_cpt_domaines() {

    $labels = array(
        'name'                  => 'Domaines d’activité',
        'singular_name'         => 'Domaine d’activité',
        'menu_name'             => 'Domaines d’activité',
        'name_admin_bar'        => 'Domaine',
        'add_new'               => 'Ajouter',
        'add_new_item'          => 'Ajouter un domaine',
        'new_item'              => 'Nouveau domaine',
        'edit_item'             => 'Modifier le domaine',
        'view_item'             => 'Voir le domaine',
        'all_items'             => 'Tous les domaines',
        'search_items'          => 'Rechercher des domaines',
        'not_found'             => 'Aucun domaine trouvé',
        'not_found_in_trash'    => 'Aucun domaine dans la corbeille',
    );

    register_post_type('domaine_activite', array(
        'labels'             => $labels,
        'public'             => true,
        'menu_icon'          => 'dashicons-screenoptions',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'),
        'has_archive'        => false,
        'rewrite'            => array('slug' => 'domaines-activite', 'with_front' => false),
        'show_in_rest'       => true,
        'menu_position'      => 21,
        'publicly_queryable' => true,
    ));

    register_taxonomy('domaine_group', 'domaine_activite', array(
        'labels' => array(
            'name'              => 'Groupes de domaines',
            'singular_name'     => 'Groupe de domaines',
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
        'rewrite'           => array('slug' => 'domaine-group', 'with_front' => false),
    ));
}
add_action('init', 'bywa_register_cpt_domaines');
