<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;

class CleverReachIntegration extends ServiceIntegration {
    private $api_endpoint = 'https://rest.cleverreach.com/v2/';

    private $client_number = '';
    private $client_email = '';
    private $client_password = '';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $api_key, $email, $password ){
        $this->client_number = trim( $api_key );
        $this->client_email = trim( $email );
        $this->client_password = trim( $password );

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
        $error_message = isset( $body['error']['message'] ) ? $body['error']['message'] : '';
        $this->error = $this->get_error_message( $error_message );
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        $success = parent::new_request( "POST", "/login", array(
            "client_id"=> $this->client_number,
            "login"=> $this->client_email,
            "password"=> $this->client_password,
        ) );
        $body = $this->get_response_body( true );
        if( $success && ! isset( $body['error'] ) ){
            $token = $body;
            $this->ironman->set_headers( array(
                'Authorization' => "Bearer $token",
            ) );
        } else {
            $error_message = isset( $body['error']['message'] ) ? $body['error']['message'] : '';
            $this->error = $this->get_error_message( $error_message );
            $success = false;
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
        $success = $this->new_request( "GET", "/groups.json" );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $lists = is_array( $body ) ? $body : array();
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
        $success = $this->new_request( "GET", "/groups.json/$this->list_id/receivers/", array(
            'email_list' => $email,
            //'activeonly' => false,
        ));
        if( ! $success ){
            return false;
        }
        $body = $this->get_response_body( true );
        $contact = ! empty( $body[0] ) ? $body[0] : false;
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
        $params['source'] = get_bloginfo( 'name' );
        $params['email'] = $email;
        $params['activated'] = time();
        $params['attributes'] = array();
        $params['global_attributes'] = array();

        if( $first_name['value'] ){
            $data['custom_fields'][$first_name['name']] = $first_name['value'];
        }
        if( $last_name['value'] ){
            $data['custom_fields'][$last_name['name']] = $last_name['value'];
        }

        if( ! empty( $data['custom_fields'] ) ){
            $local_fields = $this->get_custom_fields();
            $global_fields = $this->get_default_fields();
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                $cf_name = strtolower( $cf_name );
                if( in_array( $cf_name, $local_fields ) ){
                    $params['attributes'][$cf_name] = $cf_value;
                } else if( in_array( $cf_name, $global_fields ) ){
                    $params['global_attributes'][$cf_name] = $cf_value;
                }
            }
        }

        if( $subscriber = $this->subscriber_exists( $email ) ){
            $overwrite = isset( $data['overwrite'] ) && $data['overwrite'] == 'on' ? true : false;
            if( ! $overwrite ){
                $this->error = $this->messages['subscriber_exists'];
                return false;
            }
            unset( $params['activated'] );
            $request_body = array(
                'postdata' => $params
            );
            //Actualizar datos
            $success = $this->new_request( "PUT", "/groups.json/$this->list_id/receivers/{$subscriber['id']}", $request_body );
        } else {
            //Suscribir nuevo usuario
            $request_body = array(
                'postdata' => $params
            );
            $success = $this->new_request( "POST", "/groups.json/$this->list_id/receivers", $request_body );
        }

        return $success;
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Retorna todos los campos por defecto. Campos globales, disponibles en cualquier lista.
	|---------------------------------------------------------------------------------------------------
	*/
    public function get_default_fields(){
        $items = array();
        $success = $this->new_request( "GET", "/groups.json/null/attributes" );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $fields = is_array( $body ) ? $body : array();
        foreach( $fields as $field ){
            $items[$field['id']] = $field['name'];
        }
        return $items;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados. Campos locales, sólo de la lista actual.
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        $success = $this->new_request( "GET", "/groups.json/$this->list_id/attributes" );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $fields = is_array( $body ) ? $body : array();
        foreach( $fields as $field ){
            $items[$field['id']] = $field['name'];
        }
        return $items;
    }

}
