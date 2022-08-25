<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;

class ConvertkitIntegration extends ServiceIntegration {
    private $api_endpoint = 'https://api.convertkit.com/v3/';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $api_key ){
        $this->api_key = trim( $api_key );

        $this->ironman = new IronMan( $this->api_endpoint );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Request
    |---------------------------------------------------------------------------------------------------
    */
    public function new_request( $method, $url, $body = array(), $headers = array(), $options = array() ){
        if( $method == 'GET' ){
            return parent::new_request( $method, $url, array_merge( $body, array( 'api_key' => $this->api_key ) ), $headers, $options );
        } else{
            return parent::new_request( $method, $url . "?api_key={$this->api_key}", $body, $headers, $options );
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        $success = $this->new_request( "GET", "/forms" );
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists( $args = array() ){
        $items = array();
        $success = $this->new_request( "GET", "/forms" );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $lists = isset( $body['forms'] ) ? $body['forms'] : array();
        foreach( $lists as $list ){
            $items[$list['id']] = $list['name'];
        }
        return $items;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Agrega un suscriptor a una lista
    |---------------------------------------------------------------------------------------------------
    */
    public function add_subscriber( $email, $data = array() ){
        $first_name = $data['first_name'];
        $first_name['value'] = ! empty( $first_name['value'] ) ? $first_name['value'] : '';
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'first_name';

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : 'last_name';

        //Datos necesarios para la suscripción
        $params = array();
        $params['email'] = $email;
        $params[$first_name['name']] = $first_name['value'];
        $params['fields'] = array();
        if( ! empty( $last_name['value'] ) ){
            $params['fields'][$last_name['name']] = $last_name['value'];
        }

        if( ! empty( $data['custom_fields'] ) ){
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                $params['fields'][$cf_name] = $cf_value;//los campos deben estar en minúscula
            }
        }

        //Suscribir nuevo usuario
        $success = $this->new_request( "POST", "/forms/$this->list_id/subscribe", $params );

        return $success;
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Retorna todos los campos por defecto
	|---------------------------------------------------------------------------------------------------
	*/
    public function get_default_fields(){
        return array(
            'email',
            'first_name'
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        $success = $this->new_request( "GET", "/custom_fields" );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $fields = isset( $body['custom_fields'] ) ? $body['custom_fields'] : array();
        foreach( $fields as $field ){
            $items[] = $field['key'];
        }
        return $items;
    }

}
