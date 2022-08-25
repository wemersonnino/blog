<?php

/*
|---------------------------------------------------------------------------------------------------
| Kint debug helper. Para evitar errores en producción.
|---------------------------------------------------------------------------------------------------
*/
if( ! function_exists('d') ){
    function d(){
        //do nothing
    }
}

/*
|---------------------------------------------------------------------------------------------------
| Debug
|---------------------------------------------------------------------------------------------------
*/
if( !function_exists('mpp_debug') ){
    function mpp_debug( $var1, $var2 = null ){
        echo '<pre style="margin-left: 200px; margin-top: 20px;">';
        print_r( $var1 );
        echo '</pre>';
        if( $var2 !== null ){
            echo '<pre style="margin-left: 200px; margin-top: 20px;">';
            print_r( $var2 );
            echo '</pre>';
        }
    }
}


/*
|---------------------------------------------------------------------------------------------------
| Funciones generales
|---------------------------------------------------------------------------------------------------
*/
if( ! function_exists('mpp_d') ){
    //Para hacer debug en determinados tipos de usuarios
    function mpp_d( $arg, $user_id = null ){
        if( $user_id != null && $user_id != get_current_user_id() ){
            return;
        }
        if( is_admin() && function_exists( 'd' ) ){
            d( $arg );
        }
    }
}


/*
|---------------------------------------------------------------------------------------------------
| Masterpopups testing
|---------------------------------------------------------------------------------------------------
*/

if( ! function_exists('mpp_testing') ){
    add_action('admin_init', __NAMESPACE__.'\\mpp_testing');
    function mpp_testing(){

    }
}


/*
|---------------------------------------------------------------------------------------------------
| Shortcode Popup templates
|---------------------------------------------------------------------------------------------------
*/
add_shortcode( 'mpp_display_popup_templates', 'mpp_display_popup_templates' );
function mpp_display_popup_templates( $atts = '', $content = null ){
    $atts = shortcode_atts( array(
        'category_filter' => 1,
        'tag_filter' => 1,
        'order' => 'asc',
        'class' => '',
    ), $atts );
    //$plugin = Functions::get_plugin_instance();

    $popup_templates = include MPP_DIR .'includes/data/popup-templates.php';
    //$user_popup_templates = (array) apply_filters( 'mpp_add_popup_templates', array(), $plugin->plugin->arg( 'version' ) );
    //$popup_templates = array_merge( $popup_templates, $user_popup_templates );
    if( $atts['order'] == 'desc' ){
        arsort($popup_templates);
    }

    $data = array();
    $all_types = array();
    $all_categories = array();
    $all_tags = array();

    foreach( $popup_templates as $index => $template ){
        $type = isset( $template['type'] ) ? trim( $template['type'], ',' ) : '';
        $type = array_filter( array_map( 'trim', explode( ',', $type ) ) );

        $category = isset( $template['category'] ) ? trim( $template['category'], ',' ) : '';
        $category = array_filter( array_map( 'trim', explode( ',', $category ) ) );

        $tags = isset( $template['tags'] ) ? trim( $template['tags'], ',' ) : '';
        $tags = array_filter( array_map( 'trim', explode( ',', $tags ) ) );

        $data[$index]['type'] = array_unique( $type );
        $data[$index]['category'] = array_unique( $category );
        $data[$index]['tags'] = array_unique( $tags );

        $all_types = array_merge( $all_types, $type );
        $all_categories = array_merge( $all_categories, $category );
        $all_tags = array_merge( $all_tags, $tags );
    }
    $all_types = array_values( array_unique( $all_types ) );
    $all_categories = array_values( array_unique( $all_categories ) );
    $all_tags = array_values( array_unique( $all_tags ) );

    //Menu popup templates
    $templates_header = "<div class='mpp-control-popup-templates'>";

    if( $atts['category_filter'] == 1 ){
        $templates_header .= "<ul class='mpp-categories-popup-templates xbox-clearfix'>";
        $templates_header .= "<li class='mpp-active' data-filter='all' data-group='category'>All</li>";
        $all_categories = array_merge( $all_categories, $all_types );
        foreach( $all_categories as $cat ){
            $templates_header .= "<li data-filter='$cat' data-group='category'>".ucfirst( str_replace('-', ' ', $cat ) )."</li>";
        }
        $templates_header .= "</ul><!--.mpp-categories-popup-templates-->";
    }

    if( $atts['tag_filter'] == 1 ){
        $templates_header .= "<ul class='mpp-tags-popup-templates xbox-clearfix'>";
        $templates_header .= "<li class='mpp-active' data-filter='all' data-group='tag'>All</li>";
        foreach( $all_tags as $tag ){
            $templates_header .= "<li data-filter='$tag' data-group='tag'>".ucfirst( str_replace('-', ' ', $tag ) )."</li>";
        }
        $templates_header .= "</ul><!--.mpp-tags-popup-templates-->";
    }

    $templates_header .= "</div><!--.mpp-control-popup-templates-->";

    //All popup templates
    $templates_body = '';
    $templates_body .= "<div class='mpp-wrap-popup-templates xbox-clearfix'>";
    foreach( $popup_templates as $index => $template ){
        $category = implode( ',', array_merge( $data[$index]['category'], $data[$index]['type'] ) );
        $tags = implode( ',', $data[$index]['tags'] );

        if( isset( $template['template'] ) ){
            $templates_body .= "<div class='mpp-item-popup-template mpp-scale-1 mpp-trigger-popup-{$template['popup_id']}' data-category='all,$category' data-tags='all,$tags' data-url='{$template['template']}'>";
            $image = isset( $template['image'] ) ? $template['image'] : MPP_URL . 'assets/admin/images/default-popup-template.jpg';
            $templates_body .= "<img src='$image'>";
            $templates_body .= "</div><!--.mpp-item-popup-template-->";
        }
    }
    $templates_body .= "</div><!--.mpp-wrap-popup-templates-->";

    $display_popup_templates = "<div class='public-popup-templates {$atts['class']}'>";
    $display_popup_templates .= $templates_header.$templates_body;
    $display_popup_templates .= "</div>";

    return $display_popup_templates;
}
