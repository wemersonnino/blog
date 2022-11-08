<?php
/**
 * Plugin Name:       Simuladores
 * Plugin URI:        https://meutudo.com.br/blog/
 * Description:       Criação e edição de simuladores para calculadoras
 * Requires at least: 5.9
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Wemerson Pereira
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       calculadora-simulador
 * Domain Path:       simuladores
 * Update URI:        https://meutudo.com.br/blog/
 *
 * @package           simuladores
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */


function simuladores_calculadora_simulador_block_init() {
	register_block_type(
		__DIR__ . '/build',
		array(
			'render_callback' => 'simuladores_calculadora_simulador_render_callback',
		)
	);
}
add_action( 'init', 'simuladores_calculadora_simulador_block_init' );

/**
 * Render callback function.
 *
 * @param array    $attributes The block attributes.
 * @param string   $content    The block content.
 * @param WP_Block $block      Block instance.
 *
 * @return string The rendered output.
 */
function simuladores_calculadora_simulador_render_callback( $attributes, $content, $block ) {
	ob_start();
	require plugin_dir_path( __FILE__ ) . 'build/template.php';
	return ob_get_clean();
}


/** Manage post columns */
add_filter( 'manage_calculadora_posts_columns', 'set_custom_edit_simuladores_columns' );
add_action( 'manage_calculadora_posts_custom_column' , 'custom_simuladores_column', 10, 2 );

function set_custom_edit_simuladores_columns($columns) {
	unset( $columns['date'] );
	$columns['shortcode'] = __( 'Shortcode', 'Calculadora' );
	$columns['date'] = __( 'Date', 'Calculadora' );

	return $columns;
}

function custom_simuladores_column( $column, $post_id ) {
	switch ( $column ) {
		case 'shortcode' :
			_e( '[calculadora id="' . $post_id . '"]');
			break;
	}
}
