<?php

function cptui_register_my_cpts_calculadora() {

    /**
     * Post Type: calculadoras.
     */

    $labels = [
        "name" => esc_html__( "calculadoras", "meutudoblog" ),
        "singular_name" => esc_html__( "calculadora", "meutudoblog" ),
        "menu_name" => esc_html__( "Ferramentas Calculadora", "meutudoblog" ),
        "all_items" => esc_html__( "Todas as Calculadoras", "meutudoblog" ),
        "add_new" => esc_html__( "Adicionar nova calculadora", "meutudoblog" ),
        "edit_item" => esc_html__( "Editar Calculadora", "meutudoblog" ),
        "new_item" => esc_html__( "Nova Calculadora", "meutudoblog" ),
        "view_item" => esc_html__( "Ver Calculadora", "meutudoblog" ),
        "search_items" => esc_html__( "Pesquisar Calculadora", "meutudoblog" ),
        "not_found" => esc_html__( "Não foram encontradas calculadoras", "meutudoblog" ),
        "archives" => esc_html__( "Arquivo de Calculadora", "meutudoblog" ),
        "items_list" => esc_html__( "Lista de Calculadoras", "meutudoblog" ),
        "name_admin_bar" => esc_html__( "Calculadora", "meutudoblog" ),
        "item_published" => esc_html__( "Calculadora publicada", "meutudoblog" ),
        "item_reverted_to_draft" => esc_html__( "Calculadora revestida para rascunho", "meutudoblog" ),
        "item_updated" => esc_html__( "Calculadora atualizada", "meutudoblog" ),
    ];

    $args = [
        "label" => esc_html__( "calculadoras", "meutudoblog" ),
        "labels" => $labels,
        "description" => "Criação de tipo post calculadora",
        "public" => true,
        "publicly_queryable" => true,
        "show_ui" => true,
        "show_in_rest" => true,
        "rest_base" => "calculadora",
        "rest_controller_class" => "WP_REST_Posts_Controller",
        "rest_namespace" => "wp/v2",
        "has_archive" => false,
        "show_in_menu" => false,
        "show_in_nav_menus" => true,
        "delete_with_user" => false,
        "exclude_from_search" => false,
        "capability_type" => "post",
        "map_meta_cap" => true,
        "hierarchical" => false,
        "can_export" => true,
        "rewrite" => [ "slug" => "calculadora", "with_front" => true ],
        "query_var" => true,
        "menu_icon" => "dashicons-calculator",
        "supports" => [ "title", "editor", "trackbacks", "revisions", "author", "post-formats" ],
        "taxonomies" => [ "category", "post_tag" ],
        "show_in_graphql" => false,
    ];

    register_post_type( "calculadora", $args );
}

add_action( 'init', 'cptui_register_my_cpts_calculadora' );
