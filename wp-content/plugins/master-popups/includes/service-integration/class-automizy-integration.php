<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;

class AutomizyIntegration extends ServiceIntegration {
    private $api_endpoint = 'https://gateway.automizy.com/v2/';

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
        $this->ironman->set_headers( array(
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer $this->token",
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
        $error_message = isset( $body['detail'] ) ? $body['detail'] : '';
        $this->error = $this->get_error_message( $error_message );
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        $success = $this->new_request( "GET", "/smart-lists" );
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
        $lists = isset( $body['smartLists'] ) ? $body['smartLists'] : array();
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
        $success = $this->new_request( "GET", "/contacts/$email");
        if( ! $success ){
            return false;
        }
        $body = $this->get_response_body( true );
        $contact = ! empty( $body['id'] ) ? $body['id'] : false;
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
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'firstname';

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : 'lastname';

        //Datos necesarios para la suscripción
        $params = array();
        $params['email'] = $email;
        $params['status'] = 'ACTIVE';//ACTIVE, INACTIVE, BOUNCED, UNSUBSCRIBED, BANNED
        $params['customFields'] = array();

        if( $first_name['value'] ){
            $params['customFields']['firstname'] = $first_name['value'];
        }
        if( $last_name['value'] ){
            $params['customFields']['lastname'] = $last_name['value'];
        }

        if( ! empty( $data['custom_fields'] ) ){
            $custom_fields = $this->get_custom_fields();
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                if( strtolower( $cf_name ) == 'tags' ){
                    $params['tags'] = explode( ',', $cf_value );
                } else {
                    $key = $this->isset_field( $cf_name, $custom_fields, false );
                    if( $key !== false ){
                        $params['customFields'][$cf_name] = $cf_value;//Acepta mayúsculas o minúsculas
                    }
                }
            }
        }

        //Comprobamos si el usuario ya está registrado
        if( $subscriber_id = $this->subscriber_exists( $email ) ){
            $overwrite = isset( $data['overwrite'] ) && $data['overwrite'] == 'on' ? true : false;
            if( ! $overwrite ){
                $this->error = $this->messages['subscriber_exists'];
                return false;
            }
            //Actualizar Usuario
            $request_body = $params;
            $success = $this->new_request( "PATCH", "/contacts/$subscriber_id", $request_body );
        } else{
            //Suscribir nuevo usuario
            $request_body = $params;
            $success = $this->new_request( "POST", "/smart-lists/{$this->list_id}/contacts", $request_body );
            foreach( $this->get_registered_lists(1) as $list_id ){
                $this->new_request( "POST", "/smart-lists/{$list_id}/contacts", $request_body );
            }
        }

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
        $success = $this->new_request( "GET", "/custom-fields" );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $fields = isset( $body['customFields'] ) ? $body['customFields'] : array();
        foreach( $fields as $field ){
            $items[] = $field['name'];
        }
        return $items;
    }

}
