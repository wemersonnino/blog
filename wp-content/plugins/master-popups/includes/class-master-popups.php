<?php

use MasterPopups\Includes\ClassAutoloader;
use MasterPopups\Includes\PluginLoader;
use MasterPopups\Includes\OptionsManager;
use MasterPopups\Includes\Functions;
use MasterPopups\Includes\Popup;
use MasterPopups\Includes\Popups;
use MasterPopups\Includes\Settings;
use MasterPopups\Includes\Services;

class MasterPopups {
    public static $args = array();
    protected static $instance = null;
    public $plugin = null;
    public $settings = null;
    public $options_manager = null;
    public $plugin_loader = null;
    public $post_types = array();
    public $xbox_ids = array();
    public $settings_url = '';
    public $main_menu_item = '';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    private function __construct( $args = array() ){
        self::$args = $args;
        $this->plugin = $this;
        $this->post_types = $this->arg( 'post_types' );
        $this->xbox_ids = $this->arg( 'xbox_ids' );
        $this->main_menu_item = 'edit.php?post_type=' . $this->post_types['popups'];

        $this->plugin_loader();

        $this->hooks();
        $this->settings_url = Functions::post_type_url( $this->post_types['popups'], 'edit', array( 'page' => 'settings-master-popups' ) );

        if( Settings::plugin_status() ){
            $update_checker = Puc_v4_Factory::buildUpdateChecker(
                'http://masterpopups.com/plugin/updates/?action=get_metadata&slug=master-popups',
                MPP_DIR . 'master-popups.php',
                'master-popups'
            );
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Singleton
    |---------------------------------------------------------------------------------------------------
    */
    private function __clone(){
    }//Stopping Clonning of Object

    public function __wakeup(){
    }//Stopping unserialize of object

    public static function get_instance( $args = array() ){
        if( null === self::$instance ){
            self::$instance = new self( $args );
        }
        return self::$instance;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Plugin arguments
    |---------------------------------------------------------------------------------------------------
    */
    public function arg( $name = '', $key = '' ){
        if( isset( self::$args[$name] ) ){
            if( $key ){
                if( isset( self::$args[$name][$key] ) ){
                    return self::$args[$name][$key];
                } else{
                    return null;
                }
            }
            return self::$args[$name];
        }
        return null;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Plugin loader
    |---------------------------------------------------------------------------------------------------
    */
    private function plugin_loader(){
        include dirname( __FILE__ ) . '/class-autoloader.php';
        ClassAutoloader::run();

        $this->plugin_loader = PluginLoader::get_instance( $this );
        $this->options_manager = $this->plugin_loader->options_manager;

    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Plugin hooks
    |---------------------------------------------------------------------------------------------------
    */
    private function hooks(){
        $popups = $this->post_types['popups'];
        $lists = $this->post_types['lists'];
        add_action( 'init', array( $this, 'create_post_types' ) );
        add_action( 'admin_menu', array( $this, 'add_submenu_pages' ), 10 );
        add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );

        if( ! $this->should_continue() ){
            return;
        }

        add_action( 'wp_loaded', array( $this, 'register_popups' ) );
        add_shortcode( 'mpp_popup', array( $this, 'trigger_popup' ) );
        add_shortcode( 'mpp_inline', array( $this, 'inline_popup' ) );
        add_shortcode( 'mpp_content_locker', array( $this, 'content_locker' ) );
        add_action( 'admin_notices', array( $this, 'create_top_bar' ), 1 );
        add_action( 'admin_notices', array( $this, 'show_message_to_activate_plugin' ) );
        add_action( 'admin_notices', array( $this, 'check_version' ) );

        add_filter( "manage_edit-{$popups}_columns", array( $this, 'set_columns_popups' ) );
        add_action( "manage_{$popups}_posts_custom_column", array( $this, 'set_content_columns_popups' ), 10, 2 );
        add_action( "post_row_actions", array( $this, 'add_duplicate_popup_link' ), 10, 2 );

        add_filter( "manage_edit-{$lists}_columns", array( $this, 'set_columns_audience' ) );
        add_action( "manage_{$lists}_posts_custom_column", array( $this, 'set_content_columns_audience' ), 10, 2 );

    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Should continue
    |---------------------------------------------------------------------------------------------------
    */
    public function should_continue(){
        // si no es el backend inicializar normalmente
        if( !is_admin() ) return true;

        // ajax requiere inicialización completa
        if( defined( 'DOING_AJAX' ) && DOING_AJAX ) return true;

        $post_type = Functions::get_current_post_type();
        if( in_array( $post_type, $this->post_types ) ){
            return true;
        }
        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Plugins loaded hook
    |---------------------------------------------------------------------------------------------------
    */
    public function plugins_loaded(){
        $plugin_rel_path = trailingslashit( plugin_basename( MPP_DIR ) );
        $loaded = load_plugin_textdomain( 'masterpopups', false, $plugin_rel_path . 'languages/' );

        if( ! $loaded ){
            load_textdomain( 'masterpopups', MPP_DIR . 'languages/masterpopups-' . get_locale() . '.mo' );
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Register Popups
    |---------------------------------------------------------------------------------------------------
    */
    public function register_popups(){
        Popups::init( $this );
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Labels for custom post types
    |---------------------------------------------------------------------------------------------------
    */
    public static function get_post_type_labels( $menu_name, $plural, $singular, $all_items ){
        return array(
            'singular_name' => $singular,
            'name' => $plural,
            'menu_name' => $menu_name,
            'add_new' => sprintf( __( 'New %s', 'masterpopups' ), $singular ),
            'name_admin_bar' => sprintf( '%s', $singular ) . ' (' . $menu_name . ')',
            'all_items' => $all_items,
            'add_new_item' => sprintf( __( 'Add %s', 'masterpopups' ), $singular ),
            'new_item' => sprintf( __( 'New %s', 'masterpopups' ), $singular ),
            'edit_item' => sprintf( __( 'Edit %s', 'masterpopups' ), $singular ),
            'update_item' => sprintf( __( 'Update %s', 'masterpopups' ), $singular ),
            'item_updated' => sprintf( __( '%s updated', 'masterpopups' ), $plural ),
            'item_published' => sprintf( __( '%s published', 'masterpopups' ), $plural ),
            'view_item' => sprintf( __( 'View %s', 'masterpopups' ), $singular ),
            'view_items' => sprintf( __( 'View %s', 'masterpopups' ), $plural ),
            'search_items' => sprintf( __( 'Search %s', 'masterpopups' ), $plural ),
            'not_found' => sprintf( __( 'No %s found', 'masterpopups' ), $plural ),
            'not_found_in_trash' => sprintf( __( 'No %s found in Trash', 'masterpopups' ), $plural ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Labels for custom post types
    |---------------------------------------------------------------------------------------------------
    */
    public function get_post_type_args( $args ){
        if( is_null( $args['show_ui'] ) ){
            $args['show_ui'] = true;
            $settings = get_option( 'settings-master-popups' );
            $disable_roles = isset( $settings['disable-user-roles'] ) ? (array) $settings['disable-user-roles'] : array();

            //Excluir roles de usuarios que no pueden gestionar el custom post type
            if( is_user_logged_in() ){
                $user = wp_get_current_user();
                $role = false;
                if( is_array( $user->roles ) && isset( $user->roles[0] ) ){
                    $role = $user->roles[0];
                }
                if( in_array( $role, $disable_roles ) ){
                    $args['show_ui'] = false;
                }
            }
        }

        return array(
            'labels' => $args['labels'],
            'description' => '',
            'supports' => $args['supports'],
            'hierarchical' => false,
            'capability_type' => 'post',

            'public' => $args['public'],//dejar como falso
            'publicly_queryable' => $args['public'],//Permite que sea visible en el front-end. url.com/post-type/popup-slug
            'exclude_from_search' => !$args['public'],//true
            'show_ui' => $args['show_ui'],
            'show_in_menu' => isset( $args['show_in_menu'] ) ? $args['show_in_menu'] : $args['show_ui'],
            'show_in_nav_menus' => $args['public'],//false. Permite seleccionar desde menús de navegación

            'menu_position' => isset( $args['menu_position'] ) ? $args['menu_position'] : null,
            'menu_icon' => isset( $args['menu_icon'] ) ? $args['menu_icon'] : null,
            'show_in_admin_bar' => isset( $args['show_in_menu'] ) ? $args['show_in_menu'] : $args['show_ui'],
            'can_export' => true,//Si permitir que este tipo de publicación se exporte
            'has_archive' => false,
            'rewrite' => false,//para ocultar permalink al editar
            'delete_with_user' => false
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Add submenu pages
    |---------------------------------------------------------------------------------------------------
    */
    public function add_submenu_pages(){
        $singular = __( 'List', 'masterpopups' );
        $page_title = sprintf( __( 'New %s', 'masterpopups' ), $singular );
        $menu_title = $page_title;
        $menu_slug = Functions::post_type_url( $this->post_types['lists'], 'new' );
        add_submenu_page( $this->plugin->main_menu_item, $page_title, $menu_title, 'manage_options', $menu_slug );

    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Create custom post types
    |---------------------------------------------------------------------------------------------------
    */
    public function create_post_types(){
        $this->create_post_type_popups();
        $this->create_post_type_lists();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Post Type: popups
    |---------------------------------------------------------------------------------------------------
    */
    public function create_post_type_popups(){
        if ( post_type_exists( $this->post_types['popups'] ) ) {
            return;
        }

        //Popups
        $singular = __( 'Popup', 'masterpopups' );
        $plural = __( 'Popups', 'masterpopups' );
        $all_items = sprintf( __( 'All %s', 'masterpopups' ), $plural );
        $labels = $this->get_post_type_labels( $this->arg( 'menu_name' ), $plural, $singular, $all_items );
        $args = $this->get_post_type_args( array(
            'labels' => $labels,
            'public' => false,
            'supports' => array( 'title' ),
            'show_ui' => null,//Si se debe generar y permitir una interfaz de usuario de administración
            'show_in_menu' => true,//Si se debe mostrar en el menú de administración o como submenú (agregar parent)
            'menu_position' => 20,
            'menu_icon' => MPP_URL . 'assets/admin/images/icon-plugin2.png',
        ) );
        register_post_type( $this->post_types['popups'], $args );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Post Type: popups
    |---------------------------------------------------------------------------------------------------
    */
    public function create_post_type_lists(){
        if ( post_type_exists( $this->post_types['lists'] ) ) {
            return;
        }


        //Lists
        $singular = __( 'List', 'masterpopups' );
        $plural = __( 'Lists', 'masterpopups' );
        $all_items = sprintf( __( 'All %s', 'masterpopups' ), $plural );
        $labels = $this->get_post_type_labels( $this->arg( 'name' ), $plural, $singular, $all_items );
        $args = $this->get_post_type_args( array(
            'labels' => $labels,
            'public' => false,
            'supports' => array( 'title' ),
            'show_ui' => null,//Si se debe generar y permitir una interfaz de usuario de administración
            'show_in_menu' => $this->main_menu_item,//Si se debe mostrar en el menú de administración o como submenú (agregar parent)
            'menu_position' => null,
            'menu_icon' => null,
        ) );
        register_post_type( $this->post_types['lists'], $args );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Toolbar Menu
    |---------------------------------------------------------------------------------------------------
    */
    public function create_top_bar(){
        $return = '';
        if( ! Functions::in_admin_post_type( $this->post_types['popups'] ) ){
            return;
        }

        $return .= "<div class='ampp-topbar'>";
        $return .= "<ul class='ampp-topbar-menu'>";
        $return .= "<li class='ampp-topbar-item'>";
        $return .= "<a href='" . Functions::post_type_url( $this->post_types['popups'] ) . "'><i class='xbox-icon xbox-icon-folder-open'></i>" . __( 'All Popups', 'masterpopups' ) . "</a>";
        $return .= "</li>";

        $return .= "<li class='ampp-topbar-item'>";
        $return .= "<a href='" . Functions::post_type_url( $this->post_types['popups'], 'new' ) . "'><i class='xbox-icon xbox-icon-plus'></i>" . __( 'New Popup', 'masterpopups' ) . "</a>";
        $return .= "</li>";

        $return .= "<li class='ampp-topbar-item'>";
        $return .= "<a href='" . Functions::post_type_url( $this->post_types['lists'], 'edit' ) . "'><i class='xbox-icon xbox-icon-address-book'></i>" . __( 'All Lists', 'masterpopups' ) . "</a>";
        $return .= "</li>";

        $return .= "<li class='ampp-topbar-item'>";
        $return .= "<a href='" . Functions::post_type_url( $this->post_types['lists'], 'new' ) . "'><i class='xbox-icon xbox-icon-list'></i>" . __( 'New List', 'masterpopups' ) . "</a>";
        $return .= "</li>";

        $return .= "<li class='ampp-topbar-item'>";
        $return .= "<a href='" . Functions::post_type_url( $this->post_types['popups'], 'edit', array( 'page' => 'settings-master-popups' ) ) . "'><i class='xbox-icon xbox-icon-cog'></i>" . __( 'General Settings', 'masterpopups' ) . "</a>";
        $return .= "</li>";

        $return .= "<li class='ampp-topbar-item'>";
        $return .= "<a href='http://masterpopups.com/documentation/' target='_blank'><i class='xbox-icon xbox-icon-file-text'></i>" . __( 'Documentation', 'masterpopups' ) . "</a>";
        $return .= "</li>";
        $return .= "</ul>";
        $return .= "</div>";
        echo $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Muestra mensaje para activación
    |---------------------------------------------------------------------------------------------------
    */
    public function show_message_to_activate_plugin(){
        $header = __( 'License activation required.', 'masterpopups' );
        $message = sprintf( __( 'Please activate your license from %shere%s. Tab "Plugin Activation"', 'masterpopups' ), '<a href="' . $this->settings_url . '" target="_blank">', '</a>' );
        if( ! Settings::plugin_status() ){
            echo "<div class='notice notice-warning'><p><strong>".$this->arg( 'name' ).": $header</strong> $message</p></div>";
        }
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Comprueba la version del plugin
	|---------------------------------------------------------------------------------------------------
	*/
    public function check_version(){
        if( version_compare( MPP_VERSION, '2.2.9', '>=' ) ){
            $link_powered_by = $this->settings->option( 'link-powered-by-enabled' );
            $option = get_option( 'mpp_version' );
            if( ! $option ){
                update_option( 'mpp_version', array(
                    'version' => MPP_VERSION,
                    'link_powered_by' => $link_powered_by
                ) );
            } else{
                if( $link_powered_by == 'off' && isset( $option['link_powered_by'] ) && $option['link_powered_by'] == 'on' ){
                    update_option( 'mpp_version', array(
                        'version' => MPP_VERSION,
                        'link_powered_by' => 'off'
                    ) );
                }
            }
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | On Activate
    |---------------------------------------------------------------------------------------------------
    */
    public static function on_activate(){

    }

    /*
    |---------------------------------------------------------------------------------------------------
    | On Deactivate
    |---------------------------------------------------------------------------------------------------
    */
    public static function on_deactivate(){

    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Shortcode: Trigger Popup
    |---------------------------------------------------------------------------------------------------
    */
    public function trigger_popup( $atts = array(), $content = null ){
        $atts = shortcode_atts( array(
            'id' => 0,
            'tag' => 'span',
            'class' => '',
        ), $atts );

        if( ! $this->is_published_popup( $atts['id'] ) ){
            return "";
        }

        $popup = Popups::get( $atts['id'] );
        $trigger_content = '';
        if( $popup ){
            if( $popup->is_on() ){
                if( $popup->should_display() ){
                    $trigger_content = $popup->get_trigger_content( $content, $atts );
                }
            } else{
                //$trigger_content = __( 'Popup status is off', 'masterpopups' );
            }
        } else{
            $trigger_content = __( 'Popup not found', 'masterpopups' );
        }
        return $trigger_content;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Shortcode: Inline Popup
    |---------------------------------------------------------------------------------------------------
    */
    public function inline_popup( $atts = array(), $content = null ){
        $atts = shortcode_atts( array(
            'id' => 0,
        ), $atts );

        if( ! $this->is_published_popup( $atts['id'] ) ){
            return "";
        }

        $popup = Popups::get( $atts['id'] );
        $inline_popup = '';
        if( $popup ){
            if( $popup->is_on() ){
                if( $popup->should_display() ){
                    $inline_popup = $popup->build_inline();
                }
            } else{
                //$inline_popup = '<br>'.__( 'Popup status is off', 'masterpopups' );
            }
        } else{
            $inline_popup = __( 'Popup not found', 'masterpopups' );
        }
        return $inline_popup;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Shortcode: Content Locker
    |---------------------------------------------------------------------------------------------------
    */
    public function content_locker( $atts = array(), $content = null ){
        $atts = shortcode_atts( array(
            'popup_id' => 0,
        ), $atts );

        if( ! $this->is_published_popup( $atts['popup_id'] ) ){
            return "";
        }
        $popup = Popups::get( $atts['popup_id'] );
        if( $popup && $popup->option( 'content-locker' ) == 'on' && $popup->option( 'content-locker-type' ) == 'shortcode' ){
            $inline_popup = '';
            if( $popup->is_on() && $popup->should_display() ){
                $inline_popup = $popup->build_inline();
            }
            $return = '';
            $return .= '<div class="mpp-content-locker">';
            $return .= do_shortcode( $content );
            $return .= '</div>';
            return $return.$inline_popup;
        }
        return $content;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si el popup es válido
    |---------------------------------------------------------------------------------------------------
    */
    public function is_valid_popup( $id = 0 ){
        $popup = get_post( $id );
        if( $popup && $popup->post_type == $this->post_types['popups'] ){
            return true;
        }
        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si el popup está publicado
    |---------------------------------------------------------------------------------------------------
    */
    public function is_published_popup( $id = 0 ){
        return $this->is_valid_popup( $id ) && get_post_status( $id ) == 'publish';
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Columnas para la lista de popups
    |---------------------------------------------------------------------------------------------------
    */
    public function set_columns_popups( $columns ){
        $columns = array(
            "cb" => "<input type=\"checkbox\" />",
            "title" => __( 'Title', 'masterpopups' ),
            "status" => __( 'Status', 'masterpopups' ),
            "popup-shortcode" => "Popup Shortcode",
            "inline-shortcode" => "Inline Shortcode",
            "impressions" => __( 'Impressions', 'masterpopups' ),
            "submits" => __( 'Submits', 'masterpopups' ),
            "ctr" => __( 'Conversion (CTR)', 'masterpopups' ),
            "date" => 'Date',
        );
        return $columns;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Add "Duplicate post" link to Row actions
    |---------------------------------------------------------------------------------------------------
    */
    public function add_duplicate_popup_link( $actions, $post ){
        if( $post->post_type == $this->post_types['popups'] ){
            $actions['duplicate_popup'] = '<a href="#" class="ampp-action ampp-duplicate-popup" data-popup_id="' . $post->ID . '">' . __( 'Duplicate Popup', 'masterpopups' ) . '</a>';
            if( $this->is_published_popup( $post->ID ) ){
                $actions['change_popup_status'] = '<a href="#" class="ampp-action ampp-change-status" data-popup_id="' . $post->ID . '">' . __( 'Change Status', 'masterpopups' ) . '</a>';
            }
        }
        return $actions;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Contenido para las columnas de la lista de popups
    |---------------------------------------------------------------------------------------------------
    */
    public function set_content_columns_popups( $column, $popup_id ){
        $impressions = (int) get_post_meta( $popup_id, 'mpp_impressions', true );
        $submits = (int) get_post_meta( $popup_id, 'mpp_submits', true );
        $ctr = 0;
        if( get_post_status( $popup_id ) != 'publish' ){
            switch( $column ){
                case 'status':
                    echo '-';
                    break;
                case 'popup-shortcode':
                case 'inline-shortcode':
                    echo __( 'Please, publish popup', 'masterpopups' );
                    break;
            }
        } else{
            $popup = Popups::get( $popup_id );
            $status_class = 'ampp-status-off';
            $status_text_on = __( 'On', 'masterpopups' );
            $status_text_off = __( 'Off', 'masterpopups' );
            $status_text = $status_text_off;
            if( $popup->is_on() ){
                $status_class = 'ampp-status-on';
                $status_text = $status_text_on;
            }
            $status = "<span class='ampp-status $status_class' data-text-on='$status_text_on' data-text-off='$status_text_off'>$status_text</span>";
            switch( $column ){
                case 'status':
                    echo $status;
                    break;

                case 'popup-shortcode':
                    $popup_shortcode = '[mpp_popup id="' . $popup_id . '"]Open popup[/mpp_popup]';
                    echo "<input type='text' class='ampp-input-popup-shortcode' value='$popup_shortcode' onfocus='this.select()' readonly>";
                    break;

                case 'inline-shortcode':
                    $inline_shortcode = '[mpp_inline id="' . $popup_id . '"]';
                    echo "<input type='text' class='ampp-input-inline-shortcode' value='$inline_shortcode' onfocus='this.select()' readonly>";
                    break;

                case 'impressions':
                    echo $impressions;
                    break;

                case 'submits':
                    echo $submits;
                    break;

                case 'ctr':
                    if( $popup && $popup->option( 'form-submission-type' ) != 'none' ){
                        if( $impressions >= 1 ){
                            $ctr = $submits * 100 / $impressions;
                        }
                        echo round( (float) $ctr, 2 ) . '%';
                    } else{
                        echo '-';
                    }
                    break;
            }
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Columnas para la lista de audiencia
    |---------------------------------------------------------------------------------------------------
    */
    public function set_columns_audience( $columns ){
        $columns = array(
            "cb" => "<input type=\"checkbox\" />",
            "title" => __( 'Title', 'masterpopups' ),
            "service" => __( 'Service', 'masterpopups' ),
            "subscribers" => __( 'Total Subscribers', 'masterpopups' ),
            "date" => 'Date',
        );
        return $columns;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Contenido para las columnas de la lista de audiencia
    |---------------------------------------------------------------------------------------------------
    */
    public function set_content_columns_audience( $column, $audience_id ){
        $service = get_post_meta( $audience_id, 'mpp_service', true );
        $subscribers = (int) get_post_meta( $audience_id, 'mpp_total-subscribers', true );
        $integrated_services = $this->options_manager->get_integrated_services( true, false );
        switch( $column ){
            case 'service':
                if( $service == 'master_popups' ){
                    echo "<img src='" . MPP_URL . "assets/admin/images/logo-short.png' class='ampp-service-logo'>";
                    echo 'MasterPopups';
                } else if( isset( $integrated_services[$service] ) ){
                    $services = Services::get_all();
                    if( isset( $services[$service]['image_url'] ) ){
                        echo "<img src='{$services[$service]['image_url']}' class='ampp-service-logo'>";
                    }
                    echo $integrated_services[$service];
                } else{
                    echo __( 'Service not Connected', 'masterpopups' );
                }
                break;

            case 'subscribers':
                echo $subscribers;
                break;
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Cookies usadas en el plugin
    |---------------------------------------------------------------------------------------------------
    */
    public function get_plugin_cookies( $popup_id = null ){
        if( is_null( $popup_id ) ){
            $popup_id = Functions::post_id();
        }
        return array(
            'unlockWithPassword' => 'mpp_unlock_password',
            'unlockWithForm' => 'mpp_unlock_form',
            'loadCounter' => "mpp_load_counter_{$popup_id}",
            'onLoad' => "mpp_on_load_{$popup_id}",
            'onExit' => "mpp_on_exit_{$popup_id}",
            'onInactivity' => "mpp_on_inactivity_{$popup_id}",
            'onScroll' => "mpp_on_scroll_{$popup_id}",
            'onConversion' => "mpp_on_conversion_{$popup_id}",
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Cookies usadas en el plugin
    |---------------------------------------------------------------------------------------------------
    */
    public function get_cookie_name( $key, $popup_id = null ){
        $cookies = $this->get_plugin_cookies( $popup_id );
        return isset( $cookies[$key] ) ? $cookies[$key] : '';
    }
}


