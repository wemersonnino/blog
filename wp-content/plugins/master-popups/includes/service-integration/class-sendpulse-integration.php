<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;

class SendpulseIntegration extends ServiceIntegration {
    private $api_endpoint = 'https://api.sendpulse.com';
    private $url_get_token = 'https://api.sendpulse.com/oauth/access_token';

    private $client_id = '';
    private $client_secret = '';
    private $access_token = '';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $api_key, $token = '' ){
        $this->api_key = trim( $api_key );
        $this->client_id = $this->api_key;
        $this->client_secret = $token;

        $this->ironman = new IronMan( $this->api_endpoint );
        $this->ironman->set_option( 'reset_body_after_request', true );
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
        $success = $this->new_request( "POST", $this->url_get_token, array(
            'grant_type' => 'client_credentials',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
        ) );
        if( $success ){
            $body = $this->get_response_body( true );
            $this->access_token = isset( $body['access_token'] ) ? $body['access_token'] : $this->access_token;
            $this->ironman->set_headers( array(
                'Authorization' => "Bearer $this->access_token",
            ) );
        }
        return $success;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists( $args = array() ){
        $items = array();
        $success = $this->new_request( "GET", "/addressbooks" );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $lists = $body;
        foreach( $lists as $list ){
            $items[$list['id']] = $list['name'];
        }
        return $items;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verificar si el contacto está en la lista indicada
    |---------------------------------------------------------------------------------------------------
    */
    private function subscriber_exists( $email ){
        $success = $this->new_request( "GET", "/addressbooks/$this->list_id/emails/$email" );
        if( ! $success ){
            return false;
        }
        $body = $this->get_response_body( true );
        $contact = isset( $body['email']) ? $body : false;
        return $contact;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Agrega un suscriptor a una lista
    |---------------------------------------------------------------------------------------------------
    */
    public function add_subscriber( $email, $data = array() ){
        $first_name = $data['first_name'];
        $first_name['value'] = ! empty( $first_name['value'] ) ? $first_name['value'] : '';
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'Name';

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : false;

        //Datos necesarios para la suscripción
        $params = array();
        $params['email'] = $email;
        $params['variables'] = array();
        $params['variables'][$first_name['name']] = $first_name['value'];

        if( $last_name['name'] !== false ){
            $params['variables'][$last_name['name']] = $last_name['value'];
        }

        if( ! empty( $data['custom_fields'] ) ){
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                $params['variables'][$cf_name] = $cf_value;//Acepta cualquier campo
            }
        }

        //Comprobamos si el usuario ya está registrado
        $overwrite = isset( $data['overwrite'] ) && $data['overwrite'] == 'on' ? true : false;
        if( ! $overwrite && $this->subscriber_exists( $email ) ){
            $this->error = $this->messages['subscriber_exists'];
            return false;
        }

        //Suscribir nuevo usuario
        //$this->ironman->set_option( 'encode_body', true );//La petición requiere datos en formato json
        $request_body = array(
            'emails' => serialize( array( $params ) )
        );
        $success = $this->new_request( "POST", "/addressbooks/$this->list_id/emails", $request_body );
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
            'Name',
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
