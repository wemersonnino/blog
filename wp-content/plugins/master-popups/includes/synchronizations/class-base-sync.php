<?php namespace MasterPopups\Includes\Synchronizations;

use MasterPopups\Includes\Functions;
use MasterPopups\Includes\Settings;
use MasterPopups\Includes\Subscription;
use MasterPopups\Includes\Lista;

abstract class BaseSync {
    public $masterpopups = null;
    public $sync_name = '';
    public $list = null;
    public $email = '';
    public $fields = array();
    public $custom_fields = array();
    public $subscription = null;
    protected $checkbox_name = 'masterpopups_should_subscribe';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    protected function __construct( $masterpopups, $sync_name = '' ){
        $this->masterpopups = $masterpopups;
        $this->sync_name = $sync_name;
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Get option
	|---------------------------------------------------------------------------------------------------
	*/
    public function option( $option_name ){
        if( $this->masterpopups ){
            if( $this->masterpopups->settings ){
                return $this->masterpopups->settings->option( 'sync-'.$this->sync_name.'-'.$option_name );
            } else {
                return Settings::get_value( 'sync-'.$this->sync_name.'-'.$option_name );
            }
        }
        return null;
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Plugin hooks
	|---------------------------------------------------------------------------------------------------
	*/
    public function set_field( $name, $value ){
        $field_name = $name;
        if( $name == 'email' ){
            $field_name = 'field_email';
            $this->email = trim( $value );
        } else if( $name == 'first_name' ){
            $field_name = 'field_first_name';
        } else if( $name == 'last_name' ){
            $field_name = 'field_last_name';
        }
        $this->fields[$name] = array(
            'field_name' => $field_name,//Xbox option "Field name"
            'value' => trim($value),
            'index' => -1,//Popup element index
            'type' => '',//Popup element type
            'required' => 'off',//Popup element required
        );

        if( ! in_array( $name, array( 'email', 'first_name', 'last_name' ) ) ){
            $this->custom_fields[$name] = trim($value);
        }
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Valida la Lista
	|---------------------------------------------------------------------------------------------------
	*/
    public function is_valid_list( $the_list_id = null ){
        $list_id = $the_list_id == null ? $this->option( 'list-id' ) : $the_list_id;

        $this->list = new Lista( array( 'id' => $list_id ) );
        if( get_post_status( $this->list->ID ) != 'publish' ){
            return false;
        }
        return true;
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Comprueba si la sincronización está habilitada
	|---------------------------------------------------------------------------------------------------
	*/
    public function is_sync_enabled(){
        return $this->option( 'enabled' ) == 'on';
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Comprueba si se debe agregar el checkbox
	|---------------------------------------------------------------------------------------------------
	*/
    public function should_add_checkbox(){
        return $this->is_sync_enabled() && $this->option( 'use-checkbox' ) == 'on';
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Sincronizar
	|---------------------------------------------------------------------------------------------------
	*/
    public function sync( $post_data = array() ){
        if( ! Functions::is_email( $this->email ) ){
            return false;
        }
        $this->subscription = new Subscription( $this->masterpopups, $post_data, MPP_SOURCE_FORM_SUBMIT_SYNCS );
        $this->subscription->set_list( $this->list->id );
        $this->subscription->set_fields( $this->fields, $this->custom_fields );
        $result = $this->subscription->execute();
        return $result['success'];
    }

}