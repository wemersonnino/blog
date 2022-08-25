<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;

class KlaviyoIntegration extends ServiceIntegration {
    private $api_endpoint = 'https://a.klaviyo.com/api/v2/';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $api_key ){
        $this->api_key = trim( $api_key );

        $this->ironman = new IronMan( $this->api_endpoint );
        $this->ironman->set_headers( array(
            'Content-Type' => 'application/json; charset=utf-8',
            'api-key' => $this->api_key,
        ) );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Request
    |---------------------------------------------------------------------------------------------------
    */
    public function new_request( $method, $url, $body = array(), $headers = array(), $options = array() ){
        $success = parent::new_request( $method, $url, $body, $headers, $options );
        $body = $this->get_response_body( true );
        $error_message = isset( $body['message'] ) ? $body['message'] : '';
        $this->error = $this->get_error_message( $error_message );
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        $success = $this->new_request( "GET", "/lists" );
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists( $args = array() ){
        $items = array();
        $success = $this->new_request( "GET", "/lists" );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $lists = $body;
        foreach( $lists as $list ){
            $items[$list['list_id']] = $list['list_name'];
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
        $params[$last_name['name']] = $last_name['value'];

        if( ! empty( $data['custom_fields'] ) ){
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                $params[$cf_name] = $cf_value;
            }
        }

        //Suscribir nuevo usuario
        $this->ironman->set_option( 'encode_body', true );//La petición requiere datos en formato json
        $success = $this->new_request( "POST", "/list/$this->list_id/members", array( 'profiles' => array( $params ) ) );
        return $success;
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Retorna todos los campos por defecto
	|---------------------------------------------------------------------------------------------------
	*/
    public function get_default_fields(){
        return array(
            'first_name',
            'last_name',
            'phone_number',
            'organization',
            'title',
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        return $items;
    }

}
