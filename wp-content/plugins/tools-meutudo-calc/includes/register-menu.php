<?php
/**
 * @return void
 * Monta o menu do panel admin
 * @wordpress
 * @author Wemerson Pereira
 */
function meutudo_admin_menu(){
	add_menu_page(
		'ferramentas-calc',
		'Ferramentas Calculadoras',
		'manage_options',
		'ferramentas-calculadoras',
		'',
		'dashicons-media-spreadsheet',
		'6'
	);
	add_submenu_page(
		'ferramentas-calculadoras',
		'calculadoras',
		'calculadoras',
		'manage_options',
		'edit.php?post_type=calculadora'
	);
	add_submenu_page(
		'ferramentas-calculadoras',
		'simuladores',
		'Simuladores',
		'manage_options',
		'edit.php?post_type=simuladores'
	);
}

