<?php
/**
 * Post Type: Simuladores.
 */

$labels = [
    "name" => __( "Simuladores", "meutudoblog" ),
    "singular_name" => __( "Simulador", "meutudoblog" ),
];

$args = [
    "label" => __( "Simuladores", "meutudoblog" ),
    "labels" => $labels,
    "description" => "Simuladores",
    "public" => true,
    "publicly_queryable" => true,
    "show_ui" => true,
    "show_in_rest" => true,
    "rest_base" => "",
    "rest_controller_class" => "WP_REST_Posts_Controller",
    "rest_namespace" => "wp/v2",
    "has_archive" => false,
    "show_in_menu" => true,
    "show_in_nav_menus" => true,
    "delete_with_user" => false,
    "exclude_from_search" => false,
    "capability_type" => "post",
    "map_meta_cap" => true,
    "hierarchical" => false,
    "can_export" => true,
    "rewrite" => [ "slug" => "mt-Simulador", "with_front" => true ],
    "query_var" => true,
    "menu_icon" => "dashicons-format-gallery",
    "supports" => [ "title" ],
    "show_in_graphql" => false,
];

register_post_type( "mt-Simulador", $args );