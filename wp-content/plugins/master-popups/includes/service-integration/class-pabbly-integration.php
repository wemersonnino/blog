<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;

class pabblyIntegration extends ServiceIntegration {
    private $api_endpoint = 'https://emails.pabbly.com/api/';

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
        if( isset( $body['status'] ) &&  $body['status'] != 'success' ){
            $success = false;
        }
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
        $success = $this->new_request( "GET", "/subscribers-list" );
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
        $lists = isset( $body['subscribers_list'] ) ? $body['subscribers_list'] : array();
        foreach( $lists as $list ){
            $items[$list['list_id']] = $list['list_name'];
        }
        return $items;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verificar si el contacto está en la lista indicada
    |---------------------------------------------------------------------------------------------------
    */
    private function subscriber_exists( $email ){
        //No permite comprobar si existe usuario
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
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'name';

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : 'lastname';

        //Datos necesarios para la suscripción
        $params = array();
        $params['email'] = $email;
        $params['import'] = 'single';
        $params['date'] = current_time( 'mysql', true );
        $params['list_id'] = $this->list_id;
        $params['name'] = trim( $first_name['value'] . " ". $last_name['value'] );

        if( ! empty( $data['custom_fields'] ) ){
            $custom_fields = $this->get_custom_fields();
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                $cf_name = preg_replace( '/({)|(})/', "", $cf_name );//tag_value = {field_name}
                $key = $this->isset_field( $cf_name, $custom_fields, false );
                if( $key !== false ){
                    $params[$cf_name] = $cf_value;
                }
            }
        }

        //Suscribir nuevo usuario
        $request_body = $params;
        $success = $this->new_request( "POST", "subscribers", $request_body );

        foreach( $this->get_registered_lists(1) as $list_id ){
            $request_body['list_id'] = $list_id;
            $this->new_request( "POST", "subscribers", $request_body );
        }

        return $success;
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Retorna todos los campos por defecto
	|---------------------------------------------------------------------------------------------------
	*/
    public function get_default_fields(){
        return array();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        $success = $this->new_request( "GET", "/personalization-tags" );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $fields = isset( $body['personalization_tags'] ) ? $body['personalization_tags'] : array();
        foreach( $fields as $field ){
            $items[] = preg_replace( '/({)|(})/', "", $field['tag_value'] );//tag_value = {field_name}
        }
        return $items;
    }

}
