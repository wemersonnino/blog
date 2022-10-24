<?php

/**
 * @package calculadora
 * @name Calculadora meutudo
 * @author Wemerson Pereira
 * @license   private
 * @version 0.0.3
 * @copyright 2022 meutudo.app
 * @package acf
 */
/*
 * Plugin Name:    Calculadora meutudo
 * Plugin URI:     https://meutudo.blog.com.br/
 * Description:    Ferramenta da meutudo para criação de post customs como calculadoras.
 * Version:        0.0.3
 * Author:         Wemerson Pereira
 * Author URI:     https://www.github.com/wemersonnino
 * License:        Private
 * Text Domain:    meutudo-calculadora
 * Domain Path:    /languages
*/

// Shortcode: https://wordpress.stackexchange.com/questions/318711/load-custom-post-type-with-id-in-a-shortcode
// ACF: https://www.advancedcustomfields.com/resources/including-acf-within-a-plugin-or-theme/
// Plugin custom-post-type-ui: https://wordpress.org/plugins/custom-post-type-ui/



// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

/**
 * Constants
 */
define("CALCULADORA_PATH", plugin_dir_path(__FILE__));
define("CALCULADORA_URL", plugin_dir_url(__FILE__));

include_once("calculadoraMenu.php");

//include_once('fields.php');

include_once('calculadoraShortcode.php');

/** Manage post columns */
add_filter( 'manage_calculadora_posts_columns', 'set_custom_edit_calculadora_columns' );
add_action( 'manage_calculadora_posts_custom_column' , 'custom_calculadora_column', 10, 2 );

function set_custom_edit_calculadora_columns($columns) {
    unset( $columns['date'] );
    $columns['shortcode'] = __( 'Shortcode', 'Calculadora' );
    $columns['date'] = __( 'Date', 'Calculadora' );

    return $columns;
}

function custom_calculadora_column( $column, $post_id ) {
    switch ( $column ) {
        case 'shortcode' :
            _e( '[calculadora id="' . $post_id . '"]');
            break;
    }
}

function meutudo_calculadora_front_style(){
    wp_enqueue_style(
        'meutudo-calculadora-bootstrap-front',
        plugins_url('/assets/css/bootstrap/bootstrap.min.css',__FILE__),
        [],
        '5.2.0',
        'all'
    );
}
add_action('wp_enqueue_scripts','meutudo_calculadora_front_style');

function meutudo_calculadora_front_script(){
    wp_enqueue_script(
        'meutudo-calculadora-bundle-front',
        plugins_url('/assets/js/bootstrap.bundle.min.js',__FILE__),
        [],
        '5.2.0'
    );
}
add_action('wp_enqueue_scripts','meutudo_calculadora_front_script');