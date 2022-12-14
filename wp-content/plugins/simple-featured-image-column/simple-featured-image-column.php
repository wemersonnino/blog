<?php
/**
 * Plugin Name: Simple Featured Image Column
 * Plugin URI: https://github.com/dedevillela/Simple-Featured-Image-Column/
 * Description: A simple plugin that displays the "Featured Image" column in admin post type listing. Supports Posts, Pages and Custom Posts.
 * Version: 1.0.7
 * Text Domain: simple-featured-image-column
 * Domain Path: /languages
 * Author: Andre Aguiar Villela
 * Author URI: https://profiles.wordpress.org/dedevillela
 * License: GPLv2+
 **/

  if ( !defined( 'ABSPATH' ) || preg_match('#' . basename( __FILE__ ) . '#',  $_SERVER['PHP_SELF'])) {
    die( "Hey, dude! What are you doing here?" );
  }

  if ( !class_exists( 'Simple_Featured_Image_Column' ) ) {

	class Simple_Featured_Image_Column {

		function __construct() {
			add_action('admin_init', array($this, 'init'));
		}

		function init(){

			$post_types = apply_filters('Simple_Featured_Image_Column_post_types', get_post_types(array('public' => true)));
			if(empty($post_types)) return;

			add_action('admin_head', function(){ 
				echo '<style>th#featured-image  { width: 110px; }</style>'."\r\n"; 
			});
			
			foreach($post_types as $post_type){
				if(!post_type_supports($post_type, 'thumbnail')) continue;
				add_filter( "manage_{$post_type}_posts_columns", array($this, 'columns'));
				add_action( "manage_{$post_type}_posts_custom_column", array($this, 'column_data'), 10, 2);
			}
		}

		function columns($columns){
			
			if(!is_array($columns)) $columns = array();
			$new = array();
			foreach($columns as $key => $title){
				if($key == 'title') $new['featured-image'] = __('Featured Image', 'simple-featured-image-column');
				$new[$key] = $title;
			}
			return $new;
		}

		function column_data($column_name, $post_id) {
			
			if('featured-image' != $column_name) return;
			$style = 'display: block; max-width: 110px; height: auto; border: 1px solid #e5e5e5;';
			$style = apply_filters('Simple_Featured_Image_Column_image_style', $style);

			if(has_post_thumbnail($post_id)){
				$size = 'thumbnail';
				echo get_the_post_thumbnail($post_id, $size, 'style='.$style);
			} else {
				echo '<img style="'. $style .'" src="'. esc_url(plugins_url( 'images/default.png', __FILE__ )) .'" />';
			}	
		}
	}

	$featured_image_column = new Simple_Featured_Image_Column;

};
