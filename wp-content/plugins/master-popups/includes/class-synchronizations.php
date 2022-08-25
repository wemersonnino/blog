<?php namespace MasterPopups\Includes;

use MasterPopups\Includes\Synchronizations\WpComments;

class Synchronizations {
    public $plugin = null;
    private static $instance = null;

    /*
	|---------------------------------------------------------------------------------------------------
	| Constructor
	|---------------------------------------------------------------------------------------------------
	*/
    private function __construct( $plugin = null ){
        $this->plugin = $plugin;
        $this->hooks();
        $this->include_files();
        $this->init_syncs();
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
	| Plugin hooks
	|---------------------------------------------------------------------------------------------------
	*/
    private function hooks(){

    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Include basic files
    |---------------------------------------------------------------------------------------------------
    */
    public function include_files(){
        include dirname(__FILE__).'/synchronizations/class-base-sync.php';
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Init action
    |---------------------------------------------------------------------------------------------------
    */
    public function init_syncs(){
        WpComments::get_instance( $this->plugin, $this->plugin );
    }

}


