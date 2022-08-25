<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;

class EmailOctopusIntegration extends ServiceIntegration {
    private $api_endpoint = 'https://emailoctopus.com/api/1.5/';

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
        ) );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Request
    |---------------------------------------------------------------------------------------------------
    */
    public function new_request( $method, $url, $body = array(), $headers = array(), $options = array() ){
        $success = parent::new_request( $method, $url, array_merge( $body, array( 'api_key' => $this->api_key ) ), $headers, $options );
        $body = $this->get_response_body( true );
        $error_message = isset( $body['error']['message'] ) ? $body['error']['message'] : '';
        $this->error = $this->get_error_message( $error_message );
        //d($this->response);
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
        $memberId = md5( $email );
        $success = $this->new_request( "GET", "/lists/$this->list_id/contacts/$memberId");
        if( ! $success ){
            return false;
        }
        $body = $this->get_response_body( true );
        $contact_id = ! empty( $body['id'] ) ? $body['id'] : false;
        return $contact_id;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Agrega un suscriptor a una lista
    |---------------------------------------------------------------------------------------------------
    */
    public function add_subscriber( $email, $data = array() ){
        $first_name = $data['first_name'];
        $first_name['value'] = ! empty( $first_name['value'] ) ? $first_name['value'] : '';
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'FirstName';

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : 'LastName';

        //Datos necesarios para la suscripción
        $params = array();
        $params['email_address'] = $email;
        $params['status'] = 'SUBSCRIBED';//SUBSCRIBED, UNSUBSCRIBED or PENDING
        $params['fields'] = array();
        $params['fields']['FirstName'] = $first_name['value'];
        $params['fields']['LastName'] = $last_name['value'];

        if( ! empty( $data['custom_fields'] ) ){
            $custom_fields = $this->get_custom_fields();
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                $key = $this->isset_field( $cf_name, $custom_fields, false );
                if( $key !== false ){
                    $cf_name = ucfirst( $cf_name );
                    $params['fields'][$cf_name] = $cf_value;
                }
            }
        }

        //Suscribir nuevo usuario
        $request_body = $params;

        //Comprobamos si el usuario ya está registrado
        if( $subscriber_id = $this->subscriber_exists( $email ) ){
            $overwrite = isset( $data['overwrite'] ) && $data['overwrite'] == 'on' ? true : false;
            if( ! $overwrite ){
                $this->error = $this->messages['subscriber_exists'];
                return false;
            }
            //Actualizar Usuario
            $success = $this->new_request( "PUT", "/lists/{$this->list_id}/contacts/$subscriber_id", $request_body );
            foreach( $this->get_registered_lists(1) as $list_id ){
                $this->new_request( "PUT", "/lists/{$list_id}/contacts/$subscriber_id", $request_body );
            }

        } else{
            //Suscribir nuevo usuario
            $success = $this->new_request( "POST", "/lists/{$this->list_id}/contacts", $request_body );
            foreach( $this->get_registered_lists(1) as $list_id ){
                $this->new_request( "POST", "/lists/{$list_id}/contacts", $request_body );
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
            'email_address',
            'FirstName',
            'LastName'
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        $success = $this->new_request( "GET", "/lists/$this->list_id" );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $fields = isset( $body['fields'] ) ? $body['fields'] : array();
        foreach( $fields as $field ){
            $items[] = $field['tag'];
        }
        return $items;
    }

}
