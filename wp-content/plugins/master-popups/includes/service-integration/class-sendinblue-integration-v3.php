<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;

class SendinblueIntegrationV3 extends ServiceIntegration {
    private $api_endpoint = 'https://api.sendinblue.com/v3/';

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
            'api-key' => $api_key,
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
        $error_message = isset( $body['message'] ) ? $body['message'] : '';
        $this->error = $this->get_error_message( $error_message );
//        d("====================== Request: ", $this->get_url());
//        d($this->ironman->options);
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
        $success = $this->new_request( "GET", "/contacts/lists", array( 'limit' => 50 ) );//límite es 50
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
        $lists = isset( $body['lists'] ) ? $body['lists'] : array();
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
    public function subscriber_exists( $email ){
        $email = strtolower( $email );
        $email = urlencode( $email );
        $success = $this->new_request( "GET", "/contacts/$email" );
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
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'NAME';

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : 'SURNAME';

        //Datos necesarios para la suscripción
        $params = array();
        $double_optin = isset( $data['double-opt-in'] ) ? $data['double-opt-in'] : 'off';
        $params['email'] = $email;
        $params['updateEnabled'] = true;
        if( $double_optin == 'on' ){
            $params['includeListIds'] = array_map( 'intval', $this->get_registered_lists() );
            $params['redirectionUrl'] = $data['redirection-url'];
            $params['templateId'] = intval( $data['template-id'] );
            $this->debug['templateId'] = $params['templateId'];
        } else {
            $params['listIds'] = array_map( 'intval', $this->get_registered_lists() );
        }

        $params['attributes'] = array();
        $params['attributes'][$first_name['name']] = $first_name['value'];
        $params['attributes'][$last_name['name']] = $last_name['value'];


        if( ! empty( $data['custom_fields'] ) ){
            $custom_fields = $this->get_custom_fields();
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                $cf_name = strtolower( $cf_name );
                $key = $this->isset_field( $cf_name, $custom_fields, false );
                if( $key !== false ){
                    $value = $cf_value;
                    if( $cf_value === 'false' || $cf_value === '0' ){
                        $value = false;
                    }else if( $cf_value === 'true' || $cf_value === '1' ){
                        $value = true;
                    }
                    $params['attributes'][$cf_name] = $value;
                }
            }
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
        $request_body = $params;
        $this->debug['params'] = $params;
        $url = $double_optin == 'on' ? '/contacts/doubleOptinConfirmation' : '/contacts';
        $success = $this->new_request( "POST", $url, $request_body );

        return $success;
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Retorna todos los campos por defecto
	|---------------------------------------------------------------------------------------------------
	*/
    public function get_default_fields(){
        return array(
            'NAME',
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        $success = $this->new_request( "GET", "/contacts/attributes" );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $fields = isset( $body['attributes'] ) ? $body['attributes'] : array();
        foreach( $fields as $field ){
            $items[] = $field['name'];
        }
        return $items;
    }

}
