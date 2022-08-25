<?php namespace MasterPopups\Includes;

class PluginLoader {
    public $plugin;
    protected static $instance = null;
    public $options_manager;

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    private function __construct( $plugin ){
        $this->plugin = $plugin;

        $this->constants();
        $this->hooks();
        $this->general_files();
        $this->assets_loader();
        $this->general_settings();
        $this->options_manager();
        $this->include_xbox_framework();
        $this->synchronizations();
        $this->ajax();
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
    | Constants
    |---------------------------------------------------------------------------------------------------
    */
    public function constants(){
        define( 'MPP_VERSION', $this->plugin->arg( 'version' ) );
        define( 'MPP_SLUG', $this->plugin->arg( 'slug' ) );
        define( 'MPP_TEXT_DOMAIN', $this->plugin->arg( 'text_domain' ) );
        define( 'MPP_DIR', trailingslashit( dirname( dirname( __FILE__ ) ) ) );
        define( 'MPP_URL', trailingslashit( plugins_url( '', dirname( __FILE__ ) ) ) );

        define( 'MPP_SOURCE_FORM_SUBMIT_POPUP', 'popups' );
        define( 'MPP_SOURCE_FORM_SUBMIT_SYNCS', 'syncs' );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Hooks
    |---------------------------------------------------------------------------------------------------
    */
    private function hooks(){
        add_action( 'wp_loaded', array( $this, 'load_files_on_plugins_loaded' ) );
        //add_action( 'xbox_init', array( $this, 'general_settings' ), 11 );
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | General files
    |---------------------------------------------------------------------------------------------------
    */
    public function general_files(){
        include MPP_DIR . 'includes/global-functions.php';
        include MPP_DIR . 'includes/options/popup-editor/popup-editor-functions.php';
        include MPP_DIR . 'libs/Mobile-Detect/Mobile_Detect.php';
        include MPP_DIR . 'libs/plugin-update-checker/plugin-update-checker.php';
        include MPP_DIR . 'libs/IronMan/class-iron-man.php';
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Load files on "wp_loaded" hook
    |---------------------------------------------------------------------------------------------------
    */
    public function load_files_on_plugins_loaded(){
        include MPP_DIR . 'includes/debug.php';
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Assets Loader
    |---------------------------------------------------------------------------------------------------
    */
    public function assets_loader(){
        AssetsLoader::get_instance( $this->plugin );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Options Manager
    |---------------------------------------------------------------------------------------------------
    */
    public function options_manager(){
        $this->options_manager = OptionsManager::get_instance( $this->plugin );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | General Settings
    |---------------------------------------------------------------------------------------------------
    */
    public function general_settings(){
        $this->plugin->settings = Settings::get_instance( $this->plugin );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Xbox Framework
    |---------------------------------------------------------------------------------------------------
    */
    public function include_xbox_framework(){
        //No condicionar la inclusión de xbox porque lo puede requerir otros plugins
        if( ! defined( 'XBOX_HIDE_DEMO' ) ){
            define( 'XBOX_HIDE_DEMO', true );
        }
        if( file_exists( MPP_DIR . 'libs/xbox/xbox.php' ) ){
            include MPP_DIR . 'libs/xbox/xbox.php';
        }
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Sincronizaciones
	|---------------------------------------------------------------------------------------------------
	*/
    public function synchronizations(){
        Synchronizations::get_instance( $this->plugin );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Ajax
    |---------------------------------------------------------------------------------------------------
    */
    public function ajax(){
        Ajax::get_instance( $this->plugin );
    }


}
