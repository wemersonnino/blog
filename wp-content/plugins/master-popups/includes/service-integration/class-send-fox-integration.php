<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;

class SendFoxIntegration extends ServiceIntegration {
    private $api_endpoint = 'https://api.sendfox.com';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $auth_type, $token ){
        $this->auth_type = $auth_type;//basic_auth, oauth2
        $this->token = trim( $token );

        $this->ironman = new IronMan( $this->api_endpoint );

        $this->ironman->set_option( 'encode_body', true );//La petición requiere datos en formato json
        $this->ironman->set_option( 'reset_body_after_request', true );

        $this->ironman->set_headers( [
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer $this->token",
        ] );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Request
    |---------------------------------------------------------------------------------------------------
    */
    public function new_request( $method, $url, $body = array(), $headers = array(), $options = array() ){
        $success = parent::new_request( $method, $url, $body, $headers, $options );
        $body = $this->get_response_body( true );
        if( is_null( $body ) ){
            $success = false;
            $error_message = 'Unauthorized';
            $this->error = $this->get_error_message( $error_message );
        } else{
            $error_message = isset( $body['errors'] ) ? $body['errors'] : '';
            $this->error = $this->get_error_message( $error_message );
        }
//        d( "====================== Request: ", $this->get_url() );
//        d( $this->response );
//        d( $this->get_request_body() );
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
        $success = $this->is_connect();
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $lists = isset( $body['data'] ) ? $body['data'] : array();
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
        $email = strtolower( $email );
        $success = $this->new_request( "GET", "/contacts?email=$email" );
        if( ! $success ){
            return false;
        }
        $body = $this->get_response_body( true );
        $contact = ! empty( $body['data'] ) ? $body['data'] : false;
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
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'first_name';

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : 'last_name';

        //Datos necesarios para la suscripción
        $params = array();
        $params['email'] = $email;
        $params[$first_name['name']] = $first_name['value'];
        $params[$last_name['name']] = $last_name['value'];
        $params['lists'] = $this->get_registered_lists( 0 );


        if( ! empty( $data['custom_fields'] ) ){
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                $params[$cf_name] = $cf_value;
            }
        }

        //Comprobamos si el usuario ya está registrado
        $overwrite = isset( $data['overwrite'] ) && $data['overwrite'] == 'on' ? true : false;
        if( ! $overwrite && $this->subscriber_exists( $email ) ){
            $this->error = $this->messages['subscriber_exists'];
            return false;
        }

        //Importante. Rellena los campos del tipo {origin_url}, {ip}, {popup_title} con su valor real
        $params = $this->populate_render_fields( $params );

        //Suscribir nuevo usuario
        $request_body = $params;
        $success = $this->new_request( "POST", "/contacts", $request_body );

        return $success;
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Retorna todos los campos por defecto
	|---------------------------------------------------------------------------------------------------
	*/
    public function get_default_fields(){
        return [
            'email',
            'first_name',
            'last_name',
        ];
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
