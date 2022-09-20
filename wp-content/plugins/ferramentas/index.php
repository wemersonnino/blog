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

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class BradsBoilerplate {
  function __construct() {
    add_action('init', array($this, 'onInit'));
  }

  function onInit() {
    wp_register_script('makeUpANameHereScript', plugin_dir_url(__FILE__) . 'build/index.js', array('wp-blocks', 'wp-element', 'wp-editor'));
    wp_register_style('makeUpANameHereStyle', plugin_dir_url(__FILE__) . 'build/index.css');
    
    register_block_type('makeupnamespace/make-up-block-name', array(
      'render_callback' => array($this, 'renderCallback'),
      'editor_script' => 'makeUpANameHereScript',
      'editor_style' => 'makeUpANameHereStyle'
    ));
  }

  function renderCallback($attributes) {
    if (!is_admin()) {
      wp_enqueue_script('boilerplateFrontendScript', plugin_dir_url(__FILE__) . 'build/frontend.js', array('wp-element'));
      wp_enqueue_style('boilerplateFrontendStyles', plugin_dir_url(__FILE__) . 'build/index.css');
    }

    ob_start(); ?>
    <div class="boilerplate-update-me my-unique-plugin-wrapper-class"><pre style="display: none;"><?php echo wp_json_encode($attributes) ?></pre></div>
    <?php return ob_get_clean();
    
  }

  function renderCallbackBasic($attributes) {
    return '<div class="boilerplate-frontend">Hello, the sky is ' . $attributes['skyColor'] . ' and the grass is ' . $attributes['grassColor'] . '.</div>';
  }
}





$bradsBoilerplate = new BradsBoilerplate();