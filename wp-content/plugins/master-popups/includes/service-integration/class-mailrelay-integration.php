<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;

class MailrelayIntegration extends ServiceIntegration {
    private $api_endpoint = 'https://your_address/ccm/admin/api/version/2/&type=json';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $auth_type, $token, $url = '' ){
        $this->auth_type = $auth_type;//basic_auth, oauth2
        $this->token = trim( $token );
        $domain = str_replace( array( 'http://', 'https://' ), '', untrailingslashit( $url ) );
        $this->api_endpoint = "https://{$domain}/ccm/admin/api/version/2/&type=json";

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
        $success = parent::new_request( $method, $url, array_merge( $body, array( 'apiKey' => $this->token ) ), $headers, $options );
        $body = $this->get_response_body( true );
        if( ! $success || isset( $body['status'] ) ){
            $error_message = isset( $body['error'] ) ? $body['error'] : '';
            $this->error = $this->get_error_message( $error_message );
            if( isset( $body['status'] ) && $body['status'] == 0 ) {
                $success = false;
            }
        }
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
        $success = $this->new_request( "GET", "", array( 'function' => 'getGroups' ) );
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
        $success = $this->new_request( "GET", "", array( 'function' => 'getSubscribers', 'email' => $email ) );
        if( ! $success ){
            return false;
        }
        $body = $this->get_response_body( true );
        $users = ! empty( $body['data'] ) ? $body['data'] : array();
        $subscriber_id = ! empty( $users[0] ) ? $users[0]['id'] : false;
        return $subscriber_id;
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
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : 'last_name';

        //Datos necesarios para la suscripción
        $params = array();
        $params['email'] = $email;
        $params['name'] = trim( $first_name['value'] .' '. $last_name['value'] );
        $params['groups'] = $this->lists;//Suscripción a varias listas

        //$params['customFields'] = array();
        //No guarda campos personalizados. Esto no funciona: array( 'f_1' => 'Madrid', 'f_2' => '5555-5555' )
        //https://mailrelay.com/en/api-documentation/function/addSubscriber
        if( ! empty( $data['custom_fields'] ) ){
//            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
//                $params['custom_fields'][$cf_name] = $cf_value;
//            }
        }

        //Importante. Rellena los campos del tipo {origin_url}, {ip}, {popup_title} con su valor real
        $params = $this->populate_render_fields( $params );

        //Comprobamos si el usuario ya está registrado
        if( $subscriber_id = $this->subscriber_exists( $email ) ){
            $overwrite = isset( $data['overwrite'] ) && $data['overwrite'] == 'on' ? true : false;
            if( ! $overwrite ){
                $this->error = $this->messages['subscriber_exists'];
                return false;
            }
            //Actualizar Usuario
            $params['function'] = 'updateSubscriber';
            $params['id'] = $subscriber_id;
            $request_body = $params;
            $success = $this->new_request( "POST", "", $request_body );
        } else{
            //Suscribir nuevo usuario
            $params['function'] = 'addSubscriber';
            $request_body = $params;
            $success = $this->new_request( "POST", "", $request_body );
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
            'name',
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
