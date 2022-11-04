<?php
define( "WP_DEBUG", true );
add_theme_support('post-thumbnails');
add_theme_support('title-tag');

function custom_image_sizes() {
  add_image_size('postagem-460x330', 460, 330, true);
  add_image_size('postagem-400x280', 400, 280, true);
  add_image_size('postagem-1200x675', 1200, 675, true); //1110x490
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
  wp_enqueue_style('theme_author', get_template_directory_uri() . '/css/author.css');
  wp_enqueue_style('wp-block-library'. get_site_url() . 'wp-includes/css/dist/block-library/style.min.css');

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
  register_nav_menu('rodape-institucional', 'Rodapé (Institucional)');
  //register_nav_menu('rodape-seu-credito', 'Rodapé (Crédito)');
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

function calculadora_sidebar(){
    register_sidebar([
        'name'          => 'Calculadora Front Sidebar',
        'id'            => 'calculadora-meutudo-sidebar',
        'description'   => 'Sidebar das paginas de calculadora',
        'before_widget' => '<div id="%1$s" class="widget %2$s widget-wrapper">',
        'after_widget'  => '</div>',
    ]);
}
add_action('widgets_init', 'calculadora_sidebar');

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

// Corrigindo problema de canonical duplicada por web story
// https://wordpress.org/support/topic/canonical-link-error-on-web-stories/
function prefix_filter_canonical_web_story($canonical) {
  if(is_singular('web-story')) {
    return false;
  }

  return $canonical;
}

add_filter('wpseo_canonical', 'prefix_filter_canonical_web_story');

// Breadcrumb do Yoast Customizado
// https://fellowtuts.com/wordpress/custom-breadcrumb-navigation-yoast-seo/
function get_yoast_breadcrumb_array(){
    global $request;

	$crumb = array();
	
	// Get all preceding links before the current page
	$dom = new DOMDocument();
	$dom->loadHTML(yoast_breadcrumb('', '', false));
	$items = $dom->getElementsByTagName('a');
	
	foreach ($items as $tag)
		$crumb[] =  array('text' => utf8_decode($tag->nodeValue), 'href' => $tag->getAttribute('href'));			

    // Author
    if (is_archive() && is_author()) {
        $authorCrumb = ['text' => 'Autor', 'href' => get_permalink(get_page_by_path('autor'))];
        if ($items->length == 1) $crumb[] = $authorCrumb;
        else array_splice($crumb, 1, 0, [$authorCrumb]);
    }
	
	// Get the current page text and href 
	$items = new DOMXpath($dom);
	$dom = $items->query('//*[contains(@class, "breadcrumb_last")]');
    $wp = $request;
	$crumb[] = array('text' =>  utf8_decode($dom->item(0)->nodeValue), 'href' => trailingslashit(home_url($wp)));
	
	return $crumb;
}

function custom_yoast_breadcrumb($crumb){
	$html = '';
	
	if($crumb) {
		$items = count($crumb) - 1;
		$html = '<nav class="breadcrumb">';
		$html .= '<div class="container">';
		$html .= '<ol itemscope itemtype="http://schema.org/BreadcrumbList">';
		
		$i = 1;
		foreach($crumb as $k => $v){
			$html .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
			
			if($k == $items) // If it's the last item then output the text only
				$html .= sprintf('<span itemprop="name">%s</span><meta itemprop="position" content="%s" />', $v['text'], $i);
			else // Preceding items with URLs
				$html .= sprintf('<a itemprop="item" typeof="WebPage" href="%s"><span itemprop="name">%s</span></a><meta itemprop="position" content="%s" />', $v['href'], $v['text'], $i);
			
			$html .= '</li>';
			
			$i += 1;
		}
		$html .=  '</ol>';
		$html .= '</div>';
		$html .= '</nav>';
	}
	
	return $html;
}

function mt_banner_shortcode($atts) {
    $bannerID = $atts['id'];
    
   if(!empty($bannerID)) {
        $alt = get_the_title($bannerID);
        $link = get_field('link', $bannerID);
        $desktopImage = get_field('imagem-desktop', $bannerID);
        $mobileImage = get_field('imagem-mobile', $bannerID);
        
        if(!empty($desktopImage) && !empty($mobileImage)) {
            $style = '<style>.mt-banner .mt-banner-desktop { display: block; width: 100%; margin: 30px 0 0; } .mt-banner .mt-banner-mobile { display: none; width: 100%; } @media (max-width: 767.98px) { .mt-banner .mt-banner-desktop { display: none; } .mt-banner .mt-banner-mobile { display: block; } }</style>';
            
            $html = $style . '<div class="mt-banner mt-banner-' . $bannerID . '"><a href="' . $link['url'] . '" target="' . $link['target'] . '" title="' . $link['title'] . '"><img src="' . $desktopImage['url'] . '" alt="' . $alt . '" class="mt-banner-desktop"><img src="' . $mobileImage['url'] . '" alt="' . $alt . '" class="mt-banner-mobile"></a></div>';
            
            return $html;
        }
    }
    return ''; 
} 
add_shortcode('mt_banner', 'mt_banner_shortcode'); 

function faq_after_content($content) {
    if (is_single()) {
        ob_start();
        get_template_part('partials/blocos/perguntas-frequentes');
        $content .= ob_get_clean();
    }
    return $content;
}
add_filter('the_content', 'faq_after_content');

// Author permanlink
function author_new_base() {
    global $wp_rewrite;
    $wp_rewrite->author_base = 'autor';
}
add_action('init', 'author_new_base');

// Author information post
function author_after_content ($content) {
    if (is_single()) {
        ob_start();
        get_template_part('partials/author/profile-post');
        $content .= ob_get_clean();
    }
    return $content;
}
add_filter('the_content', 'author_after_content');