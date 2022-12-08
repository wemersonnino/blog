<?php
/**
 * Plugin Name:       Ferramentas meutudo Calculadora
 * Description:       Ferramentas para criação e edição de CALCULADORAS, SIMULADORES E CONVERSORES.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Wemerson Nino
 * Author URI:        https://github.com/wemersonnino
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ferramentas-calculadora
 *
 * @package           create-block
 */
// Exit if accessed directly
if (!function_exists('add_action') && !defined( 'ABSPATH' ) ){
    echo 'Seems like you stumbled here by accident. :)=';
    exit();
}

//Setup
define('MEUTUDO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MEUTUDO_PLUGIN_URL', plugin_dir_url(__FILE__));



//Includes
$rootFiles =            glob(MEUTUDO_PLUGIN_DIR . 'includes/*.php');
$subDirectoryFiles =    glob(MEUTUDO_PLUGIN_DIR . 'includes/**/*.php');
$allFiles =             array_merge($rootFiles,$subDirectoryFiles);

foreach ($allFiles as $nomeArquivos){
	include_once($nomeArquivos);
}


//Hooks
add_action('init', 'meutudo_register_blocks');
add_action('admin_menu', 'meutudo_admin_menu');
add_shortcode( 'simuladores', 'up_simulador_meutudo_render_cb' );
add_filter( 'manage_simuladores_posts_columns', 'set_custom_edit_simuladores_columns' );
add_action( 'manage_simuladores_posts_custom_columns' , 'custom_simuladores_column', 10, 2 );
