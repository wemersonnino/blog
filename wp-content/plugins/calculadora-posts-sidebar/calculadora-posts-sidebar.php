<?php
/**
 * Plugin Name:       Calculadora Posts Show
 * Plugin URI:        https://meutudo.com.br/blog/
 * Description:       Show posts type calculadora
 * Requires at least: 5.9
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Wemerson Pereira
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       calculadora-posts-sidebar
 * Domain Path:       posts-calculadora-show
 * Update URI:        https://meutudo.com.br/blog/
 *
 * @package           posts-sidebar
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function posts_sidebar_calculadora_posts_sidebar_block_init() {
	register_block_type(
		__DIR__ . '/build',
		array(
			'render_callback' => 'posts_sidebar_calculadora_posts_sidebar_render_callback',
		)
	);
}
add_action( 'init', 'posts_sidebar_calculadora_posts_sidebar_block_init' );

/**
 * Render callback function.
 *
 * @param array    $attributes The block attributes.
 * @param string   $content    The block content.
 * @param WP_Block $block      Block instance.
 *
 * @return string The rendered output.
 */
function posts_sidebar_calculadora_posts_sidebar_render_callback( $attributes, $content, $block ) {
	ob_start();
	require plugin_dir_path( __FILE__ ) . 'build/template.php';
	return ob_get_clean();
}
