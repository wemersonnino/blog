<?php
/**
 * Post Type: Calculadoras.
 */
function cptui_register_meutudo_calculadora_cpts() {

	$labels = [
		"name"                  => esc_html__( "Calculadoras", "meutudo-calculadora" ),
		"singular_name"         => esc_html__( "Calculadora", "meutudo-calculadora" ),
        'add_new'               => esc_html__('Adicionar nova','meutudo-calculadora'),
        'add_new_item'          => esc_html__('Adicionar nova calculadora','meutudo-calculadora'),
        'edit_item'             => esc_html__('Editar Calculadora','meutudo-calculadora'),
        'new_item'              => esc_html__('Criar Nova Calculadora','meutudo-calculadora'),
        'view_item'             => esc_html__('Ver Calculadora','meutudo-calculadora'),
        'menu_name'             => esc_html__('Calculadoras','meutudo-calculadora'),
        'description'           => esc_html__('Criar as calculadoras aqui','ferramentas-meutudo-plugin'),
	];

	$args = [
		"label"                 => esc_html__( "Calculadoras", "storefront" ),
		"labels"                => $labels,
		"description"           => "Criação de calculadoras da meutudo",
		"public"                => true,
		"publicly_queryable"    => true,
		"show_ui"               => true,
		"show_in_rest"          => true,
		"rest_base"             => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace"        => "wp/v2",
		"has_archive"           => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"delete_with_user"      => false,
		"exclude_from_search"   => false,
		"capability_type"       => "post",
		"map_meta_cap"          => true,
		"hierarchical"          => false,
		"can_export"            => true,
		"rewrite"               => [ "slug" => "calculadora", "with_front" => true ],
		"query_var"             => true,
		"menu_position"         => 10,
        "menu_icon"             => "dashicons-calculator",
		"supports"              => [ 'title','editor','revisions','trackbacks','author','excerpt','page-attributes','custom-fields','post-formats' ],
		"show_in_graphql"       => false,
	];
    register_post_type( "calculadora", $args );

}
add_action( 'init', 'cptui_register_meutudo_calculadora_cpts' );