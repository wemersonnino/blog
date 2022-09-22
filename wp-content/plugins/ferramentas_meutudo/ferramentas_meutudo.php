<?php
/**
 * @return void
 * @name Ferramentas meutudo
 * @author Wemerson Pereira
 * @link https://www.meutudo.com.br/plugin
 * @license   GPL-2.0+
 * @since 1.0.0
 * @copyright 2022 meutudo.app
 * @package Ferramentas meutudo
 *
 * @wordpress-plugin
 * Plugin Name:     Ferramentas meutudo
 * Plugin URI:      https://meutudo.blog.com.br
 * Description:     Ferramenta da meutudo para criação de post customs como calculadoras, conversores e simuladores
 * Version:         1.0.0
 * Author:          Wemerson Pereira
 * Author URi:      https://www.github.com/wemersonnino
 * License:         GPL-2.0+
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:     ferramentas-meutudo-plugin
 * Domain Path:     /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

//registrar o type post calculadora
function ferramentas_meutudo_setup_post_type(){
	register_post_type('calculadora',[
		'labels' =>[
			'name'                  => __('calculadoras','ferramentas-meutudo-plugin'),
			'singular_name'         => __('calculadora','ferramentas-meutudo-plugin'),
			'add_new'               => __('Adicionar nova','ferramentas-meutudo-plugin'),
			'add_new_item'          => __('Adicionar nova calculadora','ferramentas-meutudo-plugin'),
			'edit_item'             => __('Editar Calculadora','ferramentas-meutudo-plugin'),
			'new_item'              => __('Criar Nova Calculadora','ferramentas-meutudo-plugin'),
			'view_item'             => __('Ver Calculadora','ferramentas-meutudo-plugin'),
			'menu_name'             => __('Calculadora'),
			'description'           => __('Criar as calculadoras aqui','ferramentas-meutudo-plugin'),
		],
		'map_meta_cap'          => true,
		'hierarchical'          => true,
		'public'                => true,
		'with_front'            => true,
		'pages'                 => true,
		'has_archive'           => true,
		'query_var'             =>'calculadora',
		'supports'              => ['title','editor','revisions','trackbacks','author','excerpt','page-attributes','custom-fields','post-formats'],
		'show_ui'               => true,
		'menu_icon'             => 'dashicons-calculator',
		'show_in_nav_menus'     => true,
		'show_in_menu'          => true,
		'delete_with_user'      => true,

	]);
}
add_action('init','ferramentas_meutudo_setup_post_type');

//custom function to register the "calculadora" taxonomy
function ferramentas_meutudo_register_taxonomy_subject(){
	register_taxonomy(
		'ferramentas_calculadoras',
		'calculadora',
		[
			'labels'        => [
				'name'          => 'ferramentas_calculadoras',
				'singular_name' => 'ferramenta_calculadora',
				'all_items'     => 'Todas as categorias',
				'Edit_item'     => 'Editar Categoria Calculadora',
			],
			'hierarchical'          => true,
			'public'                => true,
			'publicly_queryable'    => true,
			'show_ui'               => true,
			'show_in_nav_menus'     => true,
			'show_admin_colum'      => true,
			'capabilitties'         => ['manage_terms','edit_terms','delete_terms','assign_terms']
		]
	);
}
add_action('init','ferramentas_meutudo_register_taxonomy_subject');

//tornar a taxonomy criado em default para este plugin
function ferramentas_meutudo_register_taxonomy_for_object_type_calculadora(){
	register_taxonomy_for_object_type('ferramentas_calculadoras','calculadora');
}

function wporg_options_page_html() {
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			// output security fields for the registered setting "wporg_options"
			settings_fields( 'wporg_options' );
			// output setting sections and their fields
			// (sections are registered for "wporg", each field is registered to a specific section)
			do_settings_sections( 'wporg' );
			// output save settings button
			submit_button( __( 'Save Settings', 'textdomain' ) );
			?>
		</form>
	</div>
	<?php
}


//ao ativar o plugin
function ferramentas_meutudo_activate(){

	// Aciona nossa função que registra o plugin de tipo de postagem customizado.
	ferramentas_meutudo_setup_post_type();
	ferramentas_meutudo_register_taxonomy_subject();
	wporg_options_page();

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



