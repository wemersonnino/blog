<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;

class EgoiIntegration extends ServiceIntegration {
    private $api_endpoint = 'https://api.egoiapp.com/';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $api_key ){
        $this->api_key = trim( $api_key );

        $this->ironman = new IronMan( $this->api_endpoint );
        $this->ironman->set_headers( array(
            //'Content-Type' => 'application/json; charset=utf-8',
            'Apikey' => $this->api_key,
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
        $success = $this->new_request( "GET", "/lists", array( 'limit' => 100 ) );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $lists = isset( $body['items'] ) ? $body['items'] : array();
        foreach( $lists as $list ){
            $items[$list['list_id']] = $list['public_name'];
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
        $params['status'] = "active";//"active", "inactive", "removed", "unconfirmed"//Servicio no envía email de confirmación.

        $extra_fields = array();
        if( ! empty( $data['custom_fields'] ) ){
            $custom_fields = $this->get_custom_fields();
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                $main_fields = array( 'birth_date', 'language', 'cellphone', 'phone' );
                if( in_array( $cf_name, $main_fields ) ){
                    $params[$cf_name] = $cf_value;
                } else{
                    $field_id = array_search( $cf_name, $custom_fields );
                    if( $field_id !== false ){
                        $extra_fields[] = array(
                            'field_id' => $field_id,
                            'value' => $cf_value,
                        );
                    }
                }
            }
        }

        //Suscribir nuevo usuario
        $request_body = array(
            'base' => $params,
            'extra' => $extra_fields
        );
        $success = $this->new_request( "POST", "/lists/$this->list_id/contacts", $request_body );
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
            'birth_date',
            'language',
            'cellphone',
            'phone'
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        $success = $this->new_request( "GET", "/lists/$this->list_id/fields", array( 'limit' => 100 ) );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $fields = isset( $body['items'] ) ? $body['items'] : $body;
        foreach( $fields as $field ){
            $items[$field['field_id']] = $field['name'];
        }
        return $items;
    }

}
