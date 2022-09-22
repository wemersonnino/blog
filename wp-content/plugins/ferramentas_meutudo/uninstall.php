<?php
//se desinstalar o plugin
// se uninstall.php não for chamado pelo WordPress, morre
if (!define('WP_UNINSTALL_PLUGIN')){
	die;
}

$option_name = 'ferramentas_meutudo';

delete_option($option_name);

// descarta tabelas de banco de dados personalizada criadas pelo plugin
global $wpdb;

$wpdb->query("DROP TABLE nome_tablea_se_existir {$wpdb->prepare}nome_tabela");