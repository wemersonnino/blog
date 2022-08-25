<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;

class MailBlusterIntegration extends ServiceIntegration {
    private $api_endpoint = 'https://api.mailbluster.com/api/';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $auth_type, $api_key ){
        $this->auth_type = $auth_type;//basic_auth, oauth2
        $this->api_key = trim( $api_key );

        $this->ironman = new IronMan( $this->api_endpoint );

        $this->ironman->set_option( 'encode_body', true );//La petición requiere datos en formato json
        $this->ironman->set_option( 'reset_body_after_request', true );

        $this->ironman->set_headers( array(
            'Content-Type' => 'application/json',
            'Authorization' => "$api_key",
        ) );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Request
    |---------------------------------------------------------------------------------------------------
    */
    public function new_request( $method, $url, $body = array(), $headers = array(), $options = array() ){
        $success = parent::new_request( $method, $url, $body, $headers, $options );

        if( ! $success ){
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
        $success = $this->new_request( "GET", "/products" );
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
        $email = strtolower( $email );
        $success = $this->new_request( "GET", "/leads/" . md5( $email ) . '/', $email );
        return $success;
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

        $params = array();
        $params['email'] = $email;
        $params['firstName'] = $first_name['value'];
        $params['lastName'] = $last_name['value'];
        $params['timezone'] = $data['timezone'];
        $params['postal_code'] = $data['postal_code'];
        $params['subscribed'] = true; // Oblogatorio para subscripción
        $params['data'] = array();


        if( ! empty( $data['custom_fields'] ) ){
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                if( strtolower( $cf_name ) == 'tags' ){
                    $params['tags'] = explode( ',', $cf_value );
                    continue;
                }
                $params['data'][$cf_name] = $cf_value;
            }
        }

        if( empty( $params['data'] ) ){
            unset( $params['data'] );
        }

        $overwrite = isset( $data['overwrite'] ) && $data['overwrite'] == 'on' ? true : false;
        $subscriber_exists = $this->subscriber_exists( $email );
        if( ! $overwrite && $subscriber_exists ){
            $this->error = $this->messages['subscriber_exists'];
            return false;
        }
        $params['overrideExisting'] = $overwrite;

        //Importante. Rellena los campos del tipo {origin_url}, {ip}, {popup_title} con su valor real
        $params = $this->populate_render_fields( $params );

        $request_body = $params;
        $success = $this->new_request( "POST", "/leads/", $request_body );
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
            'firstName',
            'lastName',
            'timezone',
            'postal_code',
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array( 'tags' );
        return $items;
    }

}
