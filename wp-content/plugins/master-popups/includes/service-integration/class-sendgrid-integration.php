<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;

class SendgridIntegration extends ServiceIntegration {
    private $api_endpoint = 'https://api.sendgrid.com/v3/marketing/';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $api_key ){
        $this->api_key = trim( $api_key );

        $this->ironman = new IronMan( $this->api_endpoint );
        $this->ironman->set_option( 'encode_body', true );//La petición requiere datos en formato json
        $this->ironman->set_headers( array(
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer $this->api_key",
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
        $error_message = isset( $body['errors'] ) ? $body['errors'] : '';
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
        $lists = isset( $body['result'] ) ? $body['result'] : array();
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
        $success = $this->new_request( "POST", "/contacts/search", array(
            'query' => "email LIKE '$email' AND CONTAINS(list_ids, '$this->list_id')"
        ) );
        if( ! $success ){
            return false;
        }
        $body = $this->get_response_body( true );
        $contact = ! empty( $body['result'] ) ? $body['result'] : false;
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
        $params['custom_fields'] = array();


        if( ! empty( $data['custom_fields'] ) ){
            $default_fields = $this->get_default_fields();
            $custom_fields = $this->get_custom_fields();
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                if( in_array( $cf_name, $default_fields ) ){
                    $params[$cf_name] = $cf_value;
                } else{
                    $field_id = array_search( $cf_name, $custom_fields );
                    if( $field_id !== false ){
                        $params['custom_fields'][$field_id] = $cf_value;
                    }
                }
            }
        }

        if( empty( $params['custom_fields'] ) ){
            unset( $params['custom_fields'] );//Eliminar si está vacío porque da error
        }

        //Importante. Rellena los campos del tipo {origin_url}, {ip}, {popup_title} con su valor real
        $params = $this->populate_render_fields( $params );

        //Comprobamos si el usuario ya está registrado
        $overwrite = isset( $data['overwrite'] ) && $data['overwrite'] == 'on' ? true : false;
        if( ! $overwrite && $this->subscriber_exists( $email ) ){
            $this->error = $this->messages['subscriber_exists'];
            return false;
        }

        //Suscribir nuevo usuario
        $request_body = array(
            'list_ids' => array( $this->list_id ),
            'contacts' => array( $params )
        );
        $success = $this->new_request( "PUT", "/contacts", $request_body );

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
            'first_name',
            'last_name',
            'country',
            'city',
            'postal_code',
            'address_line_1',
            'address_line_2',
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        $success = $this->new_request( "GET", "/field_definitions" );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $fields = isset( $body['custom_fields'] ) ? $body['custom_fields'] : array();
        foreach( $fields as $field ){
            $items[$field['id']] = $field['name'];
        }
        return $items;
    }

}
