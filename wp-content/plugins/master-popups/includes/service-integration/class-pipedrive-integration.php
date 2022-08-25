<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;

class PipedriveIntegration extends ServiceIntegration {
    private $api_endpoint = 'https://api.pipedrive.com/v1/';

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
        $url = Functions::make_url( $url, array( 'api_token' => $this->api_key ) );
        $success = parent::new_request( $method, $url, $body, $headers, $options );

        $body = $this->get_response_body( true );
        $error_message = isset( $body['error'] ) ? $body['error'] : '';
        $this->error = $this->get_error_message( $error_message );
//        d("====================== Request: ", $this->get_url());
//        d($this->response);
//        d($this->get_request_body());
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        $success = $this->new_request( "GET", "/organizations" );
        return $success;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists( $args = array() ){
        $items = array();
        $success = $this->new_request( "GET", "/organizations" );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $lists = ! empty( $body['data'] ) ? $body['data'] : array();
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
        $success = $this->new_request( "GET", "/organizations/$this->list_id/persons");
        if( ! $success ){
            return false;
        }
        $body = $this->get_response_body( true );
        $data = ! empty( $body['data'] ) ? $body['data'] : array();
        foreach( $data as $user ){
            foreach( $user['email'] as $user_email ){
                if( strtolower( $user_email['value'] ) == strtolower( $email ) ){
                    return $user['id'];
                }
            }
        }
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
        $params['email'] = $email;
        $name = trim( $first_name['value'] );
        $params['name'] = ! empty( $name ) ? $name : 'NameIsRequired';//name field is required
        $params['org_id'] = $this->list_id; // Aqui se le añade el id de la organización o lista
        $params['first_name'] = $name;
        if( ! empty( $last_name['value'] ) ){
            $params['last_name'] = $last_name['value'];
        }

        if( ! empty( $data['custom_fields'] ) ){
            $custom_fields = $this->get_custom_fields();
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                $index = $this->isset_field( $cf_name, $custom_fields, false );
                if( $index !== false ){
                    $key = $custom_fields[$index];
                    $params[$key] = $cf_value;
                }
            }
        }

        //Importante. Rellena los campos del tipo {origin_url}, {ip}, {popup_title} con su valor real
        $params = $this->populate_render_fields( $params );

        //Comprobamos si el usuario ya está registrado
        $subscriber_id = $this->subscriber_exists( $email );
        if( $subscriber_id ){
            $overwrite = isset( $data['overwrite'] ) && $data['overwrite'] == 'on' ? true : false;
            if( ! $overwrite ){
                $this->error = $this->messages['subscriber_exists'];
                return false;
            }
            //Actualizar Usuario
            $request_body = $params;
            $request_body['id'] = $subscriber_id;
            $success = $this->new_request( "PUT", "/persons/$subscriber_id", $request_body );

            //No permite Suscripción a varias listas
        } else{
            //Suscribir nuevo usuario
            $request_body = $params;
            $success = $this->new_request( "POST", "/persons", $request_body );

            //No permite Suscripción a varias listas
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
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        $success = $this->new_request( "GET", "/personFields" );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $fields = ! empty( $body['data'] ) ? $body['data'] : array();
        foreach( $fields as $field ){
            $items[] = $field['key'];
        }
        return $items;
    }
}
