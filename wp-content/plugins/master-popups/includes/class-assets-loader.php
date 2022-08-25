<?php namespace MasterPopups\Includes;

class AssetsLoader {
    public $plugin;
    public $admin_url = '';
    public $public_url = '';
    public $version = '1.0.0';
    protected static $instance = null;

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    private function __construct( $plugin ){
        $this->plugin = $plugin;
        $this->admin_url = MPP_URL . 'assets/admin/';
        $this->public_url = MPP_URL . 'assets/public/';
        $this->libs_url = MPP_URL . 'libs/';

        add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'add_public_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'add_public_styles' ) );
        add_filter( 'script_loader_tag', array( $this, 'add_async_attributes' ), 10, 3 );

        add_filter( "autoptimize_filter_js_exclude", array( $this, 'autoptimize_filter_js_exclude' ), 10, 1 );
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

    public static function get_instance( $plugin = null ){
        if( null === self::$instance ){
            self::$instance = new self( $plugin );
        }
        return self::$instance;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Add admin scripts
    |---------------------------------------------------------------------------------------------------
    */
    public function add_admin_scripts( $hook ){
        if( ! $this->should_add() ){
            return;
        }

        //Wordpress scripts
        $deps_scripts = array( 'xbox', 'jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-resizable', 'jquery-ui-draggable' );
        if( function_exists( 'wp_enqueue_media' ) ){
            wp_enqueue_media();
        } else{
            wp_enqueue_script( 'media-upload' );
        }


        //Plugin scripts
        wp_register_script( 'mpp-admin', $this->admin_url . 'js/mpp-admin.js', $deps_scripts, Functions::get_plugin_version() );
        wp_enqueue_script( 'mpp-admin' );

        wp_register_script( 'mc-editor', $this->admin_url . 'js/mc-editor.js', array( 'mpp-admin' ), Functions::get_plugin_version() );
        wp_enqueue_script( 'mc-editor' );

        wp_register_script( 'mpp-element-content', $this->admin_url . 'js/mpp-element-content.js', array( 'mpp-admin' ), Functions::get_plugin_version() );
        wp_enqueue_script( 'mpp-element-content' );

        wp_register_script( 'mpp-popup-editor', $this->admin_url . 'js/mpp-popup-editor.js', array( 'mpp-admin', 'mc-editor', 'mpp-element-content' ), Functions::get_plugin_version() );
        wp_enqueue_script( 'mpp-popup-editor' );

        wp_register_script( 'mpp-onchange', $this->admin_url . 'js/mpp-onchange.js', array( 'mpp-admin', 'mpp-element-content' ), Functions::get_plugin_version() );
        wp_enqueue_script( 'mpp-onchange' );

        wp_register_script( 'mpp-integrations', $this->admin_url . 'js/mpp-integrations.js', array( 'mpp-admin' ), Functions::get_plugin_version() );
        wp_enqueue_script( 'mpp-integrations' );

        wp_register_script( 'mpp-audience-lists', $this->admin_url . 'js/mpp-audience-lists.js', array( 'mpp-admin' ), Functions::get_plugin_version() );
        wp_enqueue_script( 'mpp-audience-lists' );

        wp_register_script( 'mpp-datatable', $this->libs_url . 'dataTables/datatables.min.js', array( 'mpp-admin' ), Functions::get_plugin_version() );
        wp_enqueue_script( 'mpp-datatable' );

        wp_register_script( 'mpp-filter', $this->libs_url . 'Filterizr/jquery.filterizr.min.js', array( 'mpp-admin' ), Functions::get_plugin_version() );
        wp_enqueue_script( 'mpp-filter' );

        wp_register_script( 'mpp-selectable', $this->libs_url . 'selectable/selectable.min.js', array( 'mpp-admin' ), Functions::get_plugin_version() );
        wp_enqueue_script( 'mpp-selectable' );

        wp_localize_script( 'mpp-admin', 'MPP_ADMIN_JS', $this->admin_localization() );

    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Add admin styles
    |---------------------------------------------------------------------------------------------------
    */
    public function add_admin_styles( $hook ){
        wp_register_style( 'ampp-general', $this->admin_url . 'css/ampp-general.css', array(), Functions::get_plugin_version() );
        wp_enqueue_style( 'ampp-general' );

        if( ! $this->should_add() ){
            return;
        }

        wp_register_style( 'ampp', $this->admin_url . 'css/ampp.css', array(), Functions::get_plugin_version() );
        wp_enqueue_style( 'ampp' );

        wp_register_style( 'mpp-datatable', $this->libs_url . 'dataTables/css/datatables.min.css', array(), Functions::get_plugin_version() );
        wp_enqueue_style( 'mpp-datatable' );

        $this->add_public_styles();
        $this->add_public_scripts();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe agregar scripts y estilos
    |---------------------------------------------------------------------------------------------------
    */
    private function should_add(){
        return $this->plugin->should_continue();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Add the "async" attributes
    |---------------------------------------------------------------------------------------------------
    */
    public function add_async_attributes( $tag, $handle, $src ){
        if ( 'mpp-google-recaptcha' == $handle ) {
            $tag = str_replace( ' src', ' data-cfasync="false" async="async" defer="defer" src', $tag );
        }
        return $tag;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Add public scripts
    |---------------------------------------------------------------------------------------------------
    */
    public function add_public_scripts(){
        //Plugin scripts
        //d(Settings::get_value('minify-js'));
        if( $this->plugin->settings->option( 'minify-js' ) == 'on' ){
            wp_register_script( 'master-popups-main', $this->public_url . 'js/master-popups.min.js', array( 'jquery' ), Functions::get_plugin_version(), true );
            wp_enqueue_script( 'master-popups-main' );
        } else {
            wp_register_script( 'master-popups-main', $this->public_url . 'js/master-popups.js', array( 'jquery' ), Functions::get_plugin_version(), true );
            wp_enqueue_script( 'master-popups-main' );

            wp_register_script( 'master-popups-countdown', $this->public_url . 'js/master-popups-countdown.js', array( 'jquery', 'master-popups-main' ), Functions::get_plugin_version(), true );
            wp_enqueue_script( 'master-popups-countdown' );
        }

        wp_localize_script( 'master-popups-main', 'MPP_PUBLIC_JS', $this->public_localization() );

        //Libs
        wp_register_script( 'master-popups-libs', $this->public_url . 'js/master-popups-libs.min.js', array( 'jquery' ), Functions::get_plugin_version(), true );
        wp_enqueue_script( 'master-popups-libs' );

        if(  $this->plugin->settings->option( 'load-videojs' ) == 'on' ){
            wp_register_script( 'mpp-videojs',$this->libs_url . 'videojs/videojs.min.js', array( 'jquery', 'master-popups-main' ), Functions::get_plugin_version(), true );
            wp_enqueue_script( 'mpp-videojs' );
        }

        //Google reCaptcha
        //https://developers.google.com/recaptcha/docs/faq
        //las 2 url son correctas. Google recomienda usar la segunda para sitios en los que google.com está bloqueado
        //https://www.google.com/recaptcha/api.js
        //https://www.recaptcha.net/recaptcha/api.js
        $recaptcha_site_key = $this->plugin->settings->option('recaptcha-site-key');
        if( $recaptcha_site_key ){
            $recaptcha_api_url = "https://www.recaptcha.net/recaptcha/api.js";
            if( $this->plugin->settings->option('recaptcha-version') == 'v3' ){
                $recaptcha_api_url = "$recaptcha_api_url?render={$recaptcha_site_key}";
            } else {
                $recaptcha_api_url = "$recaptcha_api_url?render=explicit";
            }
            wp_register_script( 'mpp-google-recaptcha', $recaptcha_api_url, array( 'jquery', 'master-popups-main' ), Functions::get_plugin_version(), true );
            wp_enqueue_script( 'mpp-google-recaptcha' );
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Add public styles
    |---------------------------------------------------------------------------------------------------
    */
    public function add_public_styles(){
        wp_register_style( 'master-popups', $this->public_url . 'css/master-popups.min.css', array(), Functions::get_plugin_version() );
        wp_enqueue_style( 'master-popups' );

        if( 'on' == $this->plugin->settings->option( 'load-font-awesome' ) || is_admin() ){
            wp_register_style( 'mpp-font-awesome', $this->public_url . 'css/font-awesome.css', array(), Functions::get_plugin_version() );
            wp_enqueue_style( 'mpp-font-awesome' );

            wp_register_style( 'mpp-fontface-fontawesome', $this->public_url . 'css/mpp-fontface-fontawesome.css', array(), Functions::get_plugin_version() );
            wp_enqueue_style( 'mpp-fontface-fontawesome' );
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | WP admin_Localization
    |---------------------------------------------------------------------------------------------------
    */
    public function admin_localization(){
        $l10n = array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'ajax_nonce' => wp_create_nonce( 'mpp_admin_ajax_nonce' ),
            'settings_url' => Functions::get_plugin_instance()->settings_url,
            'local_fonts' => array_values( Assets::local_fonts() ),
            'google_fonts' => array_values( Assets::google_fonts() ),
            'cookies' => Functions::get_plugin_instance()->get_plugin_cookies(),
            'text' => array(
                'import' => __( 'Import', 'masterpopups' ),
                'saving_changes' => __( 'Saving changes', 'masterpopups' ),
                'please_wait' => __( 'Please wait a moment', 'masterpopups' ),
                'replacing_styles' => __( 'Replacing Styles', 'masterpopups' ),
                'styles_copied' => __( 'Styles copied successfully', 'masterpopups' ),
                'object_library' => __( 'Object Library', 'masterpopups' ),
                'service_status' => array(
                    'on' => __( 'Connected', 'masterpopups' ),
                    'off' => __( 'Disconnected', 'masterpopups' ),
                ),
                'service' => array(
                    'please_connect' => __( 'Please connect with the service', 'masterpopups' ),
                    'integrated' => __( 'Integrated', 'masterpopups' ),
                    'integrate' => __( 'Integrate', 'masterpopups' ),
                    'status_on' => __( 'Connected', 'masterpopups' ),
                    'status_off' => __( 'Disconnected', 'masterpopups' ),
                    'disconnect_title' => __( 'Disconnect Service', 'masterpopups' ),
                    'disconnect_content' => __( 'Are you sure you want to disconnect account? If you disconnect, your previous campaigns syncing will be disconnected as well.', 'masterpopups' ),
                    'title_popup_get_lists' => _x( 'Lists', 'On search service lists', 'masterpopups' ),
                ),
            )
        );
        return $l10n;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | WP public_Localization
    |---------------------------------------------------------------------------------------------------
    */
    public function public_localization(){
        $l10n = array(
            'version' => MPP_VERSION,
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'ajax_nonce' => wp_create_nonce( 'mpp_ajax_nonce' ),
            'plugin_url' => MPP_URL,
            'is_admin' => is_admin(),
            'debug_mode' => $this->plugin->settings->option('debug-mode'),
            'debug_ip' => $this->plugin->settings->option('debug-ip'),
            'integrated_services' => $this->plugin->settings->get_status_integrated_services(),
            'popups_z_index' => $this->plugin->settings->option('popups-z-index'),
            'sticky_z_index' => $this->plugin->settings->option('sticky-z-index'),
            'enable_enqueue_popups' => $this->plugin->settings->option('enable-enqueue-popups'),
            'target'=> array(
                'is_homepage' => Functions::is_homepage(),
                'is_single' => is_single(),
                'is_singular_post' => is_singular( array( 'post' ) ),
                'is_page' => is_page(),
                'is_archive' => is_archive(),
                'is_category' => is_category(),
                'is_tag' => is_tag(),
            ),
            'google_recaptcha' => array(
                'site_key' => $this->plugin->settings->option('recaptcha-site-key'),
                'version' => $this->plugin->settings->option('recaptcha-version'),
            ),
            'strings' => array(
                'back_to_form' => $this->plugin->settings->option('form-submission-back-to-form-text'),
                'close_popup' => $this->plugin->settings->option('form-submission-close-popup-text'),
                'validation' => array(
                    'general' => $this->plugin->settings->option('validation-msg-general'),
                    'email' => $this->plugin->settings->option('validation-msg-email'),
                    'checkbox' => $this->plugin->settings->option('validation-msg-checkbox'),
                    'dropdown' => $this->plugin->settings->option('validation-msg-dropdown'),
                    'min_length' => $this->plugin->settings->option('validation-msg-minlength'),
                ),

            )
        );
        return $l10n;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Exclude js files for Autoptimize plugin
    |---------------------------------------------------------------------------------------------------
    */
    public function autoptimize_filter_js_exclude( $exclude ){
        return $exclude.", js/master-popups.js, js/master-popups.min.js, js/master-popups-countdown.js, js/master-popups-libs.min.js";
    }

}
