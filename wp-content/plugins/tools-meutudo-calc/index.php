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

//Includes
$rootFiles =            glob(MEUTUDO_PLUGIN_DIR . 'includes/*.php');
$subDirectoryFiles =    glob(MEUTUDO_PLUGIN_DIR . 'includes/**/*.php');
$allFiles =             array_merge($rootFiles,$subDirectoryFiles);

foreach ($allFiles as $nomeArquivos){
	include_once($nomeArquivos);
}


//Hooks
add_action('init','meutudo_register_blocks');
add_action('admin_menu', 'meutudo_admin_menu');
add_shortcode( 'simuladores', 'tools_simuladores_shortcode' );
add_filter( 'manage_simulador_posts_columns', 'set_custom_edit_simuladores_columns' );
add_action( 'manage_simulador_posts_custom_columns' , 'custom_simuladores_column', 10, 2 );
