<?php
if (!defined('ABSPATH')) exit;

function bywa_register_cpt_partenaires() {

    register_post_type('partenaire', array(
        'labels' => array(
            'name' => 'Partenaires',
            'singular_name' => 'Partenaire',
        ),
        'public' => true,
        'menu_icon' => 'dashicons-groups',
        'supports' => array('title', 'thumbnail', 'page-attributes'),
        'has_archive' => false,
        'show_in_rest' => true,
        'publicly_queryable' => false,
        'exclude_from_search' => true,
        'query_var' => false,
    ));

    register_taxonomy('categorie_partenaire', 'partenaire', array(
        'label' => 'Catégories',
        'hierarchical' => true,
        'show_in_rest' => true,
    ));
}
add_action('init', 'bywa_register_cpt_partenaires');
