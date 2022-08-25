<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;

class BigMailerIntegration extends ServiceIntegration {
    private $brand_id = null;
    private $api_endpoint = "https://api.bigmailer.io/v1/brands/";

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $auth_type, $api_key, $brand_id ){
        $this->auth_type = $auth_type;//basic_auth, oauth2
        $this->api_key = trim( $api_key );
        $this->brand_id = trim( $brand_id );

        $this->ironman = new IronMan( $this->api_endpoint );

        $this->ironman->set_option( 'encode_body', true ); //La petición requiere datos en formato json
        $this->ironman->set_option( 'reset_body_after_request', true );

        $this->ironman->set_headers( array(
            'Content-Type' => 'application/json',
            'X-API-Key' => "$api_key",
        ) );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Request
    |---------------------------------------------------------------------------------------------------
    */
    public function new_request( $method, $url, $body = array(), $headers = array(), $options = array() ){
        $success = parent::new_request( $method, $this->brand_id . $url, $body, $headers, $options );

        if(!$success){
            $body = $this->get_response_body( true );
            $error_message = isset( $body['message'] ) ? $body['message'] : '';
            $this->error = $this->get_error_message( $error_message );
        }
        # d( "====================== Request: ", $this->get_url() );
        # d( $this->response );
        # d( $this->get_request_body() );
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        $success = $this->new_request( "GET", "/contacts?limit=10" );
        return $success;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists( $args = array() ){
        $items = array();
        return $items;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verificar si el contacto está en la lista indicada
    |---------------------------------------------------------------------------------------------------
    */
    private function subscriber_exists( $email ){
        return false;
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
        $params['list_ids'] = $this->get_registered_lists();
        $params['email'] = $email;
        $params['unsubscribe_all'] = false;
        $params['field_values'] = array();

        if( trim( $first_name['value'] ) != '' ){
            $params['field_values'][] = array( 'name' => $first_name['name'], 'string' => $first_name['value'] );
        }

        if( trim( $last_name['value'] ) != '' ){
            $params['field_values'][] = array( 'name' => $last_name['name'], 'string' => $last_name['value'] );
        }

        if( ! empty( $data['custom_fields'] ) ){
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                $params['field_values'][] = array( 'name' => $cf_name, 'string' => $cf_value );
            }
        }

        if( empty( $params['field_values'] ) ){
            unset( $params['field_values'] ); //Eliminar si está vacío porque da error
        }

        //Importante. Rellena los campos del tipo {origin_url}, {ip}, {popup_title} con su valor real
        $params = $this->populate_render_fields( $params );

        //Suscribir nuevo usuario
        $success = $this->new_request( "POST", "/contacts", $params );
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
