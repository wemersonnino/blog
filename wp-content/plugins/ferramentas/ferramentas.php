<?php
function ferramentas_page(){
    require_once dirname(__FILE__) . "/build/frontend.asset.php";
}
function calculadoras_page(){
    //echo '<h1>Calculadora</h1>';
    require_once dirname(__FILE__). "/pages/calculadoras/index.php";
}

function ferramentas_admin_menu(){
    add_menu_page(
        'Ferramentas Calculadora',
        'Ferramentas Calculadoras',
        'manage_options',
        'ferramentas_reports',
        'ferramentas_page',
        'dashicons-hammer',
        '20'
    );
    add_submenu_page(
        'ferramentas_reports',
        'Ferramentas Calculadoras',
        'Calculadoras',
        'manage_options',
        'calculadora_reports',
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