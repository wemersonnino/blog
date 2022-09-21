<?php
/**
 * Ferramentas Calculadoras meutudo Plugin
 *
 * @package     FERRAMENTAS-CALC
 * @subpackage  Loader
 * @author      Wemerson Pereira
 * @copyright   2022 meutudo app
 * @since       0.1.0.0
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 */

/**
 * Plugin Name: Ferramentas Calculadoras meutudo Plugin
 * Plugin URI: https://meutudo.com.br
 * Description: Plugin para criar e editar calculadoras financeiras da meutudo
 * Version: 1.0
 * Author: Wemerson Pereira
 * Author URI: https://github.com/wemersonnino
 * Text Domain: ferramentas-calc
 * Domain Path: /languages
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

function ferramentas_page($content){
    //require_once dirname(__FILE__) . "/build/frontend.asset.php";
    $content .= '<p>Thank you for reading!</p>';
    return $content;
}
function calculadoras_page(){
//echo '<h1>Calculadora</h1>';
    require_once dirname(__FILE__). "/pages/calculadoras/index.php";
}

function ferramentas_admin_menu(){
    add_menu_page(
        'Ferramentas Calculadora',
        'Ferramentas',
        'manage_options',
        'ferramentas_reports',
        'ferramentas_page',
        'dashicons-hammer',
        '20',
    );
    add_submenu_page(
        'ferramentas_reports',
        'Ferramentas Calculadoras',
        'Calculadoras',
        'manage_options',
        'calculadora',
        'calculadoras_page',
    );
    add_submenu_page(
        'ferramentas_reports',
        'Ferramentas Calculadoras',
        'Simuladores',
        'manage_options',
        'simuladores_reports',
        'simuladores_page',
    );
    add_submenu_page(
        'ferramentas_reports',
        'Ferramentas Calculadoras',
        'Conversores',
        'manage_options',
        'conversores_reports',
        'conversores_page',
    );
}
add_action('admin_menu','ferramentas_admin_menu');



