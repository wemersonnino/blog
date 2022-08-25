<?php namespace MasterPopups\Includes;

use MasterPopups\Includes\ServiceIntegration\FluentCRMIntegration;

class Target {
    private $display = false;
    private $plugin = null;
    private $popup = null;
    private $prefix = '';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $plugin = null, $popup = null ){
        $this->plugin = $plugin;
        $this->popup = $popup;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup
    |---------------------------------------------------------------------------------------------------
    */
    public function should_display_popup(){
        $display = false;

        if( is_admin() ){
            return $this->display_on_admin();
        }

        //Display Target
        $display = $this->display_on_all_site();

        if( is_archive() ){
            $display = $this->display_on_archive();
            if( is_category() ){
                $display = $this->display_on_category();
            } else if( is_tag() ){
                $display = $this->display_on_post_tag();
            }
            if( Functions::is_woocommerce_activated() ){
                if( is_tax( 'product_cat' ) ){
                    $display = $this->display_on_taxonomy( 'product_cat' );
                } else if( is_tax( 'product_tag' ) ){
                    $display = $this->display_on_taxonomy( 'product_tag' );
                }
            }
        }

        if( Functions::is_homepage() ){
            $display = $this->display_on_homepage();
        } else if( is_single() ){
            if( is_singular( array( 'post' ) ) ){
                $display = $this->display_on_posts();
            } else{
                $post_types = $this->popup->options_manager->get_not_builtin_post_types();
                if( is_singular( array_keys( $post_types ) ) ){
                    if( is_singular( array( 'product' ) ) && Functions::is_woocommerce_activated() ){
                        $display = $this->display_on_woocommerce();
                    } else{
                        $display = $this->display_on_post_types();
                    }

                }
            }
        } else if( is_page() ){
            $display = $this->display_on_pages();
        }

        if( $this->display_on_specific_urls() ){
            $display = true;
        }
        if( $this->not_display_on_specific_urls() ){
            $display = false;
        }



        //Display Conditions
        if( $display ){
            if( ! $this->display_for_users() || ! $this->display_on_devices() ){
                $display = false;
            }

            if( ! $this->display_by_url_params() || ! $this->display_by_post_content() || ! $this->display_by_referrer_url() ){
                $display = false;
            }
        }

        if( $display && function_exists( 'FluentCrmApi' ) ){
            //FluentCRM Support
            $display = $this->display_by_fluent_crm_tags();
            if( $display ){
                $display = $this->hide_by_fluent_crm_tags();
            }
        }

        return $display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup en el admin
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_admin(){
        $display = false;
        if( is_admin() ){
            $display = true;
        }
        return $display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup en todo el sitio
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_all_site(){
        return 'on' == $this->popup->option( 'display-on-all-site' ) ? true : false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup en la página principal
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_homepage(){
        return 'on' == $this->popup->option( 'display-on-homepage' ) ? true : false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup en páginas de archivos.
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_archive(){
        return 'on' == $this->popup->option( 'display-on-archive' ) ? true : false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup en categorías
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_taxonomy( $taxonomy ){
        $display = false;
        if( 'on' == $this->popup->option( "display-on-taxonomy-{$taxonomy}" ) ){
            $display = true;
        }
        $term = get_queried_object();
        $selected_terms = $this->popup->option( "display-on-taxonomy-{$taxonomy}-terms", array() );
        if( $term && in_array( $term->slug, $selected_terms ) ){
            $display = true;
        }
        return $display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup en categorías
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_category(){
        return $this->display_on_taxonomy( 'category' );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup en etiquetas
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_post_tag(){
        return $this->display_on_taxonomy( 'post_tag' );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup en un post
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_posts(){
        $display = false;
        global $post;

        if( 'on' == $this->popup->option( 'display-on-post' ) ){
            $display = true;
        } else if( in_array( $post->ID, wp_parse_id_list( $this->popup->option( 'display-on-post-include' ) ) ) ){
            $display = true;
        }

        //Mostrar en posts con determinadas categorias o tags
        $show_with_taxonomies = $this->display_on_post_type_with_taxonomies( $post, 'posts', 'category', 'post_tag' );
        if( $show_with_taxonomies ){
            $display = $show_with_taxonomies;
        }

        if( in_array( $post->ID, wp_parse_id_list( $this->popup->option( 'display-on-post-exclude' ) ) ) ){
            $display = false;
        }
        return $display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup en una página
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_pages(){
        $display = false;
        global $post;

        if( 'on' == $this->popup->option( 'display-on-page' ) ){
            $display = true;
        } else if( in_array( $post->ID, wp_parse_id_list( $this->popup->option( 'display-on-page-include' ) ) ) ){
            $display = true;
        }
        if( in_array( $post->ID, wp_parse_id_list( $this->popup->option( 'display-on-page-exclude' ) ) ) ){
            $display = false;
        }
        return $display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup en un post type verificando taxonomias
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_post_type_with_taxonomies( $post, $post_type, $cat_taxonomy, $tag_taxonomy ){
        $display = null;
        $show_by_category = $this->popup->option( "display-on-{$post_type}-with-taxonomy-{$cat_taxonomy}" );
        $show_by_tag = $this->popup->option( "display-on-{$post_type}-with-taxonomy-{$tag_taxonomy}" );

        if( $show_by_category == 'on' || $show_by_tag == 'on' ){
            $post_categories = wp_get_object_terms( $post->ID, $cat_taxonomy, array( 'fields' => 'slugs' ) );
            $post_tags = wp_get_object_terms( $post->ID, $tag_taxonomy, array( 'fields' => 'slugs' ) );

            $selected_categories = $this->popup->option( "display-on-taxonomy-{$cat_taxonomy}-terms", array() );
            $selected_tags = $this->popup->option( "display-on-taxonomy-{$tag_taxonomy}-terms", array() );

            if( $show_by_category == 'on' && array_intersect( $post_categories, $selected_categories ) ){
                $display = true;
            }
            if( $show_by_tag == 'on' && array_intersect( $post_tags, $selected_tags ) ){
                $display = true;
            }
        }
        return $display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | FluentCRM Support. Mostrar por tags de usuarios
    |---------------------------------------------------------------------------------------------------
    */
    public function display_by_fluent_crm_tags(){
        $display = true;
        $show_by_user_tags = $this->popup->option( "display-by-user-tags-enabled-fluent-crm" );
        $selected_user_tags = $this->popup->option( "display-by-user-tags-fluent-crm", array() );

        if( $show_by_user_tags == 'on'  ){
            $display = FluentCRMIntegration::user_has_tags( $selected_user_tags );
        }
        return $display;
    }
    public function hide_by_fluent_crm_tags(){
        $display = true;
        $hide_by_user_tags = $this->popup->option( "hide-by-user-tags-enabled-fluent-crm" );
        $selected_user_tags = $this->popup->option( "hide-by-user-tags-fluent-crm", array() );

        if( $hide_by_user_tags == 'on'  ){
            $display = ! FluentCRMIntegration::user_has_tags( $selected_user_tags );
        }
        return $display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup en un post type
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_woocommerce(){
        $display = false;
        global $post;
        if( ! $post ){
            return false;
        }
        $name = $post->post_type;
        if( 'on' == $this->popup->option( 'display-on-' . $name ) ){
            $display = true;
        } else if( in_array( $post->ID, wp_parse_id_list( $this->popup->option( 'display-on-' . $name . '-include' ) ) ) ){
            $display = true;
        }

        //Mostrar en products con determinadas categorias o tags
        $show_with_taxonomies = $this->display_on_post_type_with_taxonomies( $post, 'products', 'product_cat', 'product_tag' );
        if( $show_with_taxonomies ){
            $display = $show_with_taxonomies;
        }

        if( in_array( $post->ID, wp_parse_id_list( $this->popup->option( 'display-on-' . $name . '-exclude' ) ) ) ){
            $display = false;
        }
        return $display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup en un post type
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_post_types(){
        $display = false;
        global $post;
        if( ! $post ){
            return false;
        }
        $name = $post->post_type;
        if( 'on' == $this->popup->option( 'display-on-' . $name ) ){
            $display = true;
        } else if( in_array( $post->ID, wp_parse_id_list( $this->popup->option( 'display-on-' . $name . '-include' ) ) ) ){
            $display = true;
        }
        if( in_array( $post->ID, wp_parse_id_list( $this->popup->option( 'display-on-' . $name . '-exclude' ) ) ) ){
            $display = false;
        }
        return $display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup en urls espefíficas
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_specific_urls(){
        $display = false;
        $current_url = str_replace( array( 'https://', 'http://' ), '', Functions::current_url( null, true, true ) );
        $specific_urls = str_replace( array( 'https://', 'http://' ), '', $this->popup->option( 'display-on-specific-urls' ) );
        $urls = array_map( 'trim', explode( ',', $specific_urls ) );

        foreach( $urls as $url ){
            if( ! empty( $url ) ){
                if( strpos( $url, '*', strlen( $url ) - 1 ) !== false ){
                    $url = str_replace( '*', '', $url );
                    if( strpos( $current_url, $url ) !== false && strlen( $current_url ) > strlen( $url ) ){
                        $display = true;
                    }
                } else if( $current_url == $url || $current_url == $url . '/' ){
                    $display = true;
                }
            }
        }

        return $display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si no se debe mostrar el popup en urls espefíficas
    |---------------------------------------------------------------------------------------------------
    */
    public function not_display_on_specific_urls(){
        $not_display = false;
        $specific_urls = str_replace( array( 'https://', 'http://' ), '', $this->popup->option( 'display-on-specific-urls' ) );
        $urls = array_map( 'trim', explode( ',', $specific_urls ) );

        //Exclude URL like: -http://domain.com/post
        if( $this->not_show_in_urls( $urls, true ) ){
            $not_display = true;
        }

        //Excluir también en la nueva opción de "Excluir URLs"
        $specific_urls = str_replace( array( 'https://', 'http://' ), '', $this->popup->option( 'display-on-specific-urls-exclude' ) );
        $urls = array_map( 'trim', explode( ',', $specific_urls ) );
        if( $this->not_show_in_urls( $urls, false ) ){
            $not_display = true;
        }
        return $not_display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | No mostrar popup en ciertas urls
    |---------------------------------------------------------------------------------------------------
    */
    public function not_show_in_urls( $urls = array(), $search_minus = false ){
        $not_show = false;
        $current_url = str_replace( array( 'https://', 'http://' ), '', Functions::current_url( null, true, true ) );
        foreach( $urls as $url ){
            if( ! empty( $url ) && ( ! $search_minus || ( $search_minus && strpos( $url, '-' ) === 0 ) ) ){
                if( $search_minus ){
                    $url = ltrim( $url, '-' );
                }
                if( strpos( $url, '*', strlen( $url ) - 1 ) !== false ){
                    $url = str_replace( '*', '', $url );
                    if( strpos( $current_url, $url ) !== false && strlen( $current_url ) > strlen( $url ) ){
                        $not_show = true;
                    }
                } else if( $current_url == $url || $current_url == $url . '/' ){
                    $not_show = true;
                }
            }
        }
        return $not_show;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Comrpueba si se debe mostrar el popup a usuarios registrados/no registrados
    |---------------------------------------------------------------------------------------------------
    */
    public function display_for_users(){
        $display = true;
        $display_for_users = (array) $this->popup->option( 'display-for-users' );
        if( is_user_logged_in() ){
            $display = in_array( 'logged-in', $display_for_users );
        } else{
            $display = in_array( 'not-logged-in', $display_for_users );
        }
        return $display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comrpueba si se debe mostrar el popup en ciertos dispositivos
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_devices(){
        $display = true;
        $display_on_devices = (array) $this->popup->option( 'display-on-devices' );
        $mobile_delect = new \Mobile_Detect_Popup_Master();
        if( $mobile_delect->isMobile() && ! $mobile_delect->isTablet() ){
            $display = in_array( 'mobile', $display_on_devices );
        } else if( $mobile_delect->isTablet() ){
            $display = in_array( 'tablet', $display_on_devices );
        } else{
            $display = in_array( 'desktop', $display_on_devices );
        }
        return $display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup según los parámetros del URL
    |---------------------------------------------------------------------------------------------------
    */
    public function display_by_url_params(){
        $display = true;
        $url_params = $this->popup->option( 'display-by-url-parameters' );

        if( is_array( $url_params ) && ! empty( $url_params ) ){
            foreach( $url_params as $key => $item ){
                $key = $item['mpp_key'];
                $value = $item['mpp_value'];
                $condition = $item['mpp_condition'];
                if( empty( $key ) ){
                    continue;
                }
                $display = false;
                if( isset( $_GET[$key] ) ){
                    if( $condition == 'equal' && $_GET[$key] == $value ){
                        $display = true;
                    } else if( $condition == 'not_equal' && $_GET[$key] != $value ){
                        $display = true;
                    } else if( $condition == 'less' && $_GET[$key] < $value ){
                        $display = true;
                    } else if( $condition == 'less_equal' && $_GET[$key] <= $value ){
                        $display = true;
                    } else if( $condition == 'higher' && $_GET[$key] > $value ){
                        $display = true;
                    } else if( $condition == 'higher_equal' && $_GET[$key] >= $value ){
                        $display = true;
                    }
                }
                //Si alguno de los parámetros cumple, entonces ya no analizar el resto
                if( $display ){
                    break;
                }
            }
        }
        return $display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup siempre que el contenido del post tenga una palabra clave
    |---------------------------------------------------------------------------------------------------
    */
    public function display_by_post_content(){
        $display = true;
        $keyword = $this->popup->option( 'display-by-post-content' );

        global $post;
        if( $keyword !== '' && $post && ( is_single() || is_page() ) ){
            $keyword = trim( $keyword );
            $case_sensitive = substr( $keyword, -2 );
            if( $case_sensitive == '/i' ){
                $case_sensitive = 'i';
                $keyword = ltrim( $keyword, '/' );
                $keyword = str_replace( '/i', '', $keyword );
            } else{
                $case_sensitive = '';
                $keyword = trim( $keyword, '/' );
            }
            $regex = "/$keyword/$case_sensitive";
            $display = ! ! preg_match_all( $regex, $post->post_content, $matches );
        }
        return $display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup a través de el dominio de referencia
    |---------------------------------------------------------------------------------------------------
    */
    public function display_by_referrer_url(){
        $display = true;
        $show_domains = $this->popup->option( 'display-by-referrer-url' );
        $hide_domains = $this->popup->option( 'hide-by-referrer-url' );
        $referrer = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';
        if( $show_domains ){
            $show_domains = array_map( '\MasterPopups\Includes\Functions::url_to_domain', explode( ',', $show_domains ) );
            $display = in_array( Functions::url_to_domain( $referrer ), $show_domains);
        }
        if( $hide_domains ){
            $hide_domains = array_map( '\MasterPopups\Includes\Functions::url_to_domain', explode( ',', $hide_domains ) );
            $display = !in_array( Functions::url_to_domain( $referrer ), $hide_domains);
        }
        return $display;
    }


}
