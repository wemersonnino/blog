<?php /* start AceIDE restore code */
if ( $_POST["restorewpnonce"] === "169182e214a689db7414d258723f35293cddd8e407" ) {
if ( file_put_contents ( "/var/www/site/blog/wp-content/themes/meutudoblog/functions.php" ,  preg_replace( "#<\?php /\* start AceIDE restore code(.*)end AceIDE restore code \* \?>/#s", "", file_get_contents( "/var/www/site/blog/wp-content/plugins/aceide/backups/themes/meutudoblog/functions_2021-09-30-22-53-52.php" ) ) ) ) {
	echo __( "Your file has been restored, overwritting the recently edited file! \n\n The active editor still contains the broken or unwanted code. If you no longer need that content then close the tab and start fresh with the restored file." );
}
} else {
echo "-1";
}
die();
/* end AceIDE restore code */ ?><?php
add_theme_support('post-thumbnails');
add_theme_support('title-tag');

function custom_image_sizes() {
  add_image_size('postagem-460x330', 460, 330, true);
  add_image_size('postagem-400x280', 400, 280, true);
  add_image_size('postagem-1110x490', 1110, 490, true);
}
add_action('init', 'custom_image_sizes');

function enqueue_scripts() {
  /* Stylesheets */
  wp_enqueue_style('default', get_template_directory_uri() . '/style.css');
  wp_enqueue_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.custom.min.css');
  wp_enqueue_style('lity', get_template_directory_uri() . '/css/lity.min.css');
  wp_enqueue_style('swiper', get_template_directory_uri() . '/css/swiper-bundle.min.css');
  wp_enqueue_style('hamburgers', get_template_directory_uri() . '/css/hamburgers.min.css');
  wp_enqueue_style('theme', get_template_directory_uri() . '/css/theme.css');

  /* Javascripts */
  wp_deregister_script('jquery');
  wp_enqueue_script('jquery', get_template_directory_uri() . '/js/jquery.min.js', array(), null, false);
  wp_enqueue_script('jquery.mask', get_template_directory_uri() . '/js/jquery.mask.min.js', array(), null, true);
  wp_enqueue_script('lity', get_template_directory_uri() . '/js/lity.min.js', array(), null, true);
  wp_enqueue_script('swiper', get_template_directory_uri() . '/js/swiper-bundle.min.js', array(), null, true);
  wp_enqueue_script('scripts', get_template_directory_uri() . '/js/scripts.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_scripts');

function register_menus() {
  register_nav_menu('principal', 'Principal');
  register_nav_menu('rodape', 'Rodapé');
  register_nav_menu('rodape-auxiliar', 'Rodapé Auxiliar');
  register_nav_menu('mobile', 'Mobile');
}
add_action('init', 'register_menus');

function custom_widgets_init() {
  // register_sidebar(array(
  //   'name' => 'Post',
  //   'id'  => 'single-post',
  //   'before_widget' => '<div class="widget %2$s">',
  //   'after_widget' => '</div>',
  //   'before_title' => '<h3>',
  //   'after_title' => '</h3>',
  // ));
}
add_action('widgets_init', 'custom_widgets_init');

/* ACF Google Maps Api Key */
// function my_acf_google_map_api($api){
//   $api['key'] = '';
//   return $api; 
// }
// add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');

function limit_excerpt_length($length) {
  return 35;
}
add_filter('excerpt_length', 'limit_excerpt_length', 999);

// Search Highlight
function search_excerpt_highlight() {
  $excerpt = get_the_excerpt();
  $keys = implode('|', explode(' ', get_search_query()));
  $excerpt = preg_replace('/(' . $keys .')/iu', '<strong class="search-highlight">\0</strong>', $excerpt);

  echo $excerpt;
}

// Query vars
function add_state_var($vars){
    $vars[] = 'utm_source';
    $vars[] = 'utm_medium';
    $vars[] = 'utm_campaign';
    $vars[] = 'utm_term';
  return $vars;
}
add_filter('query_vars', 'add_state_var', 0, 1);

// Custom search URL redirection
function change_search_url() {
  if(is_search() && !empty($_GET['s'])) {
    wp_redirect(home_url('/busca/') . urlencode(get_query_var('s')));
    exit();
  }   
}
add_action('template_redirect', 'change_search_url');

// Custom pagination base
function custom_pagination_base() {
  global $wp_rewrite;
  $wp_rewrite->pagination_base = 'p';
  $wp_rewrite->flush_rules();
}
add_action('init', 'custom_pagination_base', 1);

// Rewrites
function custom_rewrite_rule() {
  add_rewrite_rule('^busca/([^/]*)/p/([^/]*)/?', 'index.php?s=$matches[1]&paged=$matches[2]', 'top');
  add_rewrite_rule('^busca/([^/]*)/?', 'index.php?s=$matches[1]', 'top');
}
add_action('init', 'custom_rewrite_rule', 10, 0);

// Remover versão do Wordpress
remove_action('wp_head', 'wp_generator');
function remove_wp_version() {
  return '';
}
add_filter('the_generator', 'remove_wp_version');

// Remover WP Emoji
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');

// Remover comentarios HTML do Yoast
add_action('wp_head', function() {
  ob_start(function($o) {
    return preg_replace('/^\n?<!--.*?[Y]oast.*?-->\n?$/mi', '', $o);
  });
}, ~PHP_INT_MAX);

// Categoria/Taxonomia primaria do Yoast
function get_primary_taxonomy_term($post = 0, $taxonomy = 'category') {
  if(!$post) {
    $post = get_the_ID();
  }

  $terms = get_the_terms( $post, $taxonomy );
  $primary_term = array();

  if($terms) {
    $term_display = '';
    $term_slug = '';
    $term_link = '';

    if(class_exists('WPSEO_Primary_Term')) {
      $wpseo_primary_term = new WPSEO_Primary_Term($taxonomy, $post);
      $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
      $term = get_term( $wpseo_primary_term );
      
      if(is_wp_error($term)) {
        $term_display = $terms[0]->name;
        $term_slug = $terms[0]->slug;
        $term_link = get_term_link($terms[0]->term_id);
      } else {
        $term_display = $term->name;
        $term_slug = $term->slug;
        $term_link = get_term_link($term->term_id);
      }
    } else {
      $term_display = $terms[0]->name;
      $term_slug = $terms[0]->slug;
      $term_link = get_term_link( $terms[0]->term_id );
    }

    $primary_term['url'] = $term_link;
    $primary_term['slug'] = $term_slug;
    $primary_term['title'] = $term_display;
  }

  return $primary_term;
}