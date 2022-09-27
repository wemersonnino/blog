<?php

/**
 * @package calculadora
 * @name Calculadora meutudo
 * @author Wemerson Pereira
 * @link https://www.meutudo.com.br/plugin
 * @license   GPL-2.0+
 * @since 1.0.0
 * @copyright 2022 meutudo.app
 * @package Ferramentas meutudo
 *
 * Plugin Name:    Calculadora meutudo
 * Plugin URI:     https://meutudo.blog.com.br/
 * Description:    Ferramenta da meutudo para criação de post customs como calculadoras.
 * Version:        0.0.1
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
//ao ativar o plugin
function ferramentas_meutudo_activate(){

    // Aciona nossa função que registra o plugin de tipo de postagem customizado.
    meutudo_calculadora_front_style();

    // Limpa os permalinks após o tipo de postagem ser registrado.
    flush_rewrite_rules();
}
register_activation_hook(__FILE__,'ferramentas_meutudo_activate');

//Gancho de desativação.
function ferramentas_meutudo_deactivate(){
    // Cancela o registro do tipo de postagem, para que as regras não fiquem mais na memória.
    unregister_post_type('calculadora');

    // Limpe os permalinks para remover as regras do nosso tipo de postagem do banco de dados.
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__,'ferramentas_meutudo_deactivate');

// Define version plugin meutudo-calculadora
const MEUTUDO_PLUGIN_NAME_VERSION = '0.0.1';

// Define path and URL to the ACF plugin.
define( 'MEUTUDO_CALCULADORA_ACF_PATH', plugin_dir_path( __FILE__ ) . 'includes/acf/' );
define( 'MEUTUDO_CALCULADORA_ACF_URL', plugin_dir_url( __FILE__ ) . 'includes/acf/' );

// Include the ACF plugin.
include_once( MEUTUDO_CALCULADORA_ACF_PATH . 'acf.php' );

// Customize the url setting to fix incorrect asset URLs.
add_filter('acf/settings/url', 'my_acf_settings_url');
function my_acf_settings_url( $url ) {
    return MEUTUDO_CALCULADORA_ACF_URL;
}

// (Optional) Hide the ACF admin menu item.
add_filter('acf/settings/show_admin', 'meutudo_calculadora_acf_settings_show_admin');
function meutudo_calculadora_acf_settings_show_admin( $show_admin ) {
    return false;
}

include_once("calculadoraMenu.php");

include_once('fields.php');

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
            echo '[calculadora id="' . $post_id . '"]';
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