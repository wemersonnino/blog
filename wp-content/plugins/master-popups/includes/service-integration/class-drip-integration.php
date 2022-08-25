<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;

class DripIntegration extends ServiceIntegration {
    private $api_endpoint = 'https://api.getdrip.com/v2/';
    private $account_id = null;

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $api_key ){
        $this->api_key = trim( $api_key );

        $this->ironman = new IronMan( $this->api_endpoint );
        $basic_auth = base64_encode( $this->api_key . ':' );
        $this->ironman->set_headers( array(
            'Authorization' => "Basic $basic_auth",
            'User-Agent' => 'MasterPopups plugin',
            'Content-Type' => 'application/json; charset=utf-8',
        ) );
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
        $errors = isset( $body['errors'] ) ? $body['errors'] : array();
        $errors_string = '';
        foreach( $errors as $error ){
            $errors_string .= ! empty( $error['message'] ) ? $error['message'].' ' : '';
        }
        if( $errors_string ){
            $this->error = $this->error . ". $errors_string";
        }
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        $success = $this->new_request( "GET", "/accounts" );
        if( $this->ironman->response_code == 401 ){
            return false;
        }
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Get accounts
    |---------------------------------------------------------------------------------------------------
    */
    public function get_accounts(){
        $items = array();
        if( ! $this->is_connect() ){
            return array();
        }
        //$this->new_request( "GET", "/accounts" );//$this->is_connect() hace la petición de las cuentas
        $body = $this->get_response_body( true );
        $accounts = isset( $body['accounts'] ) ? $body['accounts'] : array();
        foreach( $accounts as $account ){
            $items[$account['id']] = $account['name'];
        }
        return $items;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists( $args = array() ){
        $items = array();
        if( empty( $args['account_id'] ) ){
            return array();
        }
        $this->account_id = $args['account_id'];
        $success = $this->new_request( "GET", "$this->account_id/campaigns" );

        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $campaigns = isset( $body['campaigns'] ) ? $body['campaigns'] : array();
        foreach( $campaigns as $campaign ){
            $items[$campaign['id']] = $campaign['name'];
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
        $params['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $params['custom_fields'] = array();

        $data['custom_fields'][$first_name['name']] = $first_name['value'];
        $data['custom_fields'][$last_name['name']] = $last_name['value'];

        if( ! empty( $data['custom_fields'] ) ){
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                $new_name = Functions::string_to_underscore( $cf_name );//Sólo acepta nombre en underscore
                if( $new_name == 'eu_consent' && $cf_value ){
                    $params['eu_consent'] = 'granted';//GDPR (granted, denied)
                } else {
                    $params['custom_fields'][$new_name] = $cf_value;
                }
            }
        }

        //Suscribir nuevo usuario
        $this->ironman->set_option( 'encode_body', true );//La petición requiere datos en formato json
        //POST /v2/:account_id/subscribers
        $success = $this->new_request( "POST", "$this->account_id/subscribers", array( 'subscribers' => array( $params ) ) );

        if( $success ){
            $body = $this->get_response_body( true );
            if( isset( $body['subscribers'][0]['email'] ) ){
                //POST /v2/:account_id/campaigns/:campaign_id/subscribers
                $double_optin = isset( $data['double-opt-in'] ) && $data['double-opt-in'] == 'on' ? true : false;
                $params = array(
                    'email' => $body['subscribers'][0]['email'],
                    'double_optin' => $double_optin,
                );
                $success = $this->new_request( "POST", "$this->account_id/campaigns/{$this->list_id}/subscribers", array( 'subscribers' => array( $params ) ) );
            }
        }
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        $success = $this->new_request( "GET", "$this->account_id/custom_field_identifiers" );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $fields = isset( $body['custom_field_identifiers'] ) ? $body['custom_field_identifiers'] : array();
        foreach( $fields as $field ){
            $items[$field] = $field;
        }
        return $items;
    }

}
