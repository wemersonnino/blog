<?php
/**
 * Plugin Name:       Ferramentas Calculadoras meutudo
 * Plugin URI:        https://meutudo.com.br/blog/
 * Description:       Um plugin que contém as ferramentas necessárias para criar CALCULADORAS, SIMULADORES e CONVERSORES.
 * Version:           1.0.0
 * Author:            Wemerson Nino
 * Author URI:        https://github.com/wemersonnino
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tools-meutudo
 * Domain Path:       /languages
 */

// Exit if accessed directly
if (!function_exists('add_action') && !defined( 'ABSPATH' ) ){
    echo 'Seems like you stumbled here by accident. :)=';
    exit();
}


//Setup
define('MEUTUDO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MEUTUDO_PLUGIN_PATH', plugin_dir_path(__DIR__));

//Includes
//$rootFiles =            glob(MEUTUDO_PLUGIN_DIR . 'includes/*.php');
//$subDirectoryFiles =    glob(MEUTUDO_PLUGIN_DIR . 'includes/**/*.php');
//$allFiles =             array_merge($rootFiles,$subDirectoryFiles);
//
//foreach ($allFiles as $nomeArquivos){
//    include_once($nomeArquivos);
//}
include (MEUTUDO_PLUGIN_DIR . 'includes/register-blocks-meutudo.php');
include (MEUTUDO_PLUGIN_DIR . 'includes/register-menu.php');
include (MEUTUDO_PLUGIN_DIR . 'includes/simulador-colum-shotcod-post.php');
include (MEUTUDO_PLUGIN_DIR . 'includes/blocks/simulador-meutudo.php');


//Hooks
//add_action('init','meutudo_register_blocks');
add_action('init', 'meutudo_register_blocks');
add_action('admin_menu', 'meutudo_admin_menu');
add_shortcode( 'simuladores', 'tools_simuladores_shortcode' );
add_filter( 'manage_simuladores_posts_columns', 'set_custom_edit_simuladores_columns' );
add_action( 'manage_simuladores_posts_custom_columns' , 'custom_simuladores_column', 10, 2 );
