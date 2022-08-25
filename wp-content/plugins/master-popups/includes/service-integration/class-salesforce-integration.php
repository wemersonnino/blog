<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;

class SalesforceIntegration extends ServiceIntegration {
    protected $service_name = 'salesforce';

    private $api_endpoint = '';//https://na111.salesforce.com

    protected $url = '';
    protected $clientKey = '';
    protected $clientSecret = '';
    private $connected = false;

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $auth_type, $api_key = '', $api_token = '' ){
        parent::__construct( $this->service_name );

        $this->auth_type = $auth_type;
        $this->url = '';//untrailingslashit( $url );
        $this->clientKey = $api_key;
        $this->clientSecret = $api_token;

        $this->ironman = new IronMan( $this->api_endpoint );

        $this->redirect_url = Functions::get_plugin_instance()->settings_url;
        $settings = array(
            'baseUrl' => $this->url,
            'clientKey' => $this->clientKey,
            'clientSecret' => $this->clientSecret,
            'callback' => $this->redirect_url,
        );

        if( defined( 'DOING_AJAX' ) && DOING_AJAX ){
            if( $this->auth_type === 'oauth2' ){
                $this->debug['oauth2-settings'] = $settings;
                $this->connect_with_oauth2( $settings );
            }
        } else if( self::is_oauth2() ){
            $this->set_oauth2_fields( false );//agregar true o false dependiendo si se quiere comprobar el parámetro state en la URL

            $settings['clientKey'] = $this->clientKey;
            $settings['clientSecret'] = $this->clientSecret;

            if( self::should_go_to_oauth2_authorization() ){
                $base_url = 'https://login.salesforce.com/';
                $params = array(
                    'state' => md5(time().mt_rand()),
                    'response_type' => 'code',
                    'redirect_uri' => $this->redirect_url,
                    'client_id' => $this->clientKey,
                    //'client_secret' => $this->clientSecret,
                );

                $auth_url = $base_url . 'services/oauth2/authorize';
                $this->go_oauth2_authorization( $auth_url, $params );
            } else{
                $settings = array_merge( $settings, array( 'code' => $_GET['code'] ) );
                $this->connect_with_oauth2( $settings );
            }
        } else {
            $this->connect_with_oauth2( $settings );
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Conección mediante OAuth 2
    |---------------------------------------------------------------------------------------------------
    */
    public function connect_with_oauth2( $settings ){
        $oauth2_data = $this->get_oauth2_data();
        if( ! empty( $oauth2_data['access_token'] ) && ! empty( $oauth2_data['clientKey'] ) && $oauth2_data['clientKey'] == $settings['clientKey'] ){
            $success = $this->validate_access_token_oauth2( $oauth2_data );
        } else {
            $success = $this->request_access_token_oauth2( $settings );
        }
        if( ! $success ){
            $success = $this->refresh_access_token_oauth2( $settings );
        }
        if( $success ){
            $this->after_connection_oauth2();
        }

        $oauth2_data = $this->get_oauth2_data();//por si se guardaron nuevos cambios
        if( $success && ! empty( $oauth2_data['instance_url'] ) ){
            $this->api_endpoint = $oauth2_data['instance_url'];
            $token = $oauth2_data['access_token'];
            $this->ironman = new IronMan( $this->api_endpoint );
            $this->ironman->set_option( 'encode_body', true );//La petición requiere datos en formato json
            $this->ironman->set_option( 'reset_body_after_request', true );
            $this->ironman->set_headers( array(
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer $token",
            ) );
            $this->connected = true;
            return true;
        }
        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba y valida Token OAuth 2
    |---------------------------------------------------------------------------------------------------
    */
    public function validate_access_token_oauth2( $oauth2_data ){
        $instance_url = trailingslashit( $oauth2_data['instance_url'] );
        $token = $oauth2_data['access_token'];
        $this->ironman->set_headers( array(
            'Authorization' => "Bearer $token",
        ) );
        $success = parent::new_request( "GET", "{$instance_url}services/data/v20.0/sobjects/Account/", array() );
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Request Access Token  OAuth 2
    |---------------------------------------------------------------------------------------------------
    */
    public function request_access_token_oauth2( $settings ){
        $data = array(
            "code" => isset( $settings['code'] ) ? $settings['code'] : '',
            "grant_type" => 'authorization_code',
            "client_id" => $settings['clientKey'],
            "client_secret" => $settings['clientSecret'],
            "redirect_uri" => $this->redirect_url,
        );
        $success = parent::new_request( "POST", "https://login.salesforce.com/services/oauth2/token", $data );
        $body = $this->get_response_body( true );
        if( $success ){
            $accessTokenData = $body;//access_token, refresh_token, signature, scope, instance_url, id, token_type, issued_at
            //Guardamos datos del token para usar mediante Ajax al suscribir usuarios
            $this->save_oauth2_connection( $settings, $accessTokenData );
        } else {
            $error_message = isset( $body['error_description'] ) ? $body['error_description'] : '';
            $this->error = $this->get_error_message( $error_message );
            $success = false;
        }
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Refresh Access Token  OAuth 2
    |---------------------------------------------------------------------------------------------------
    */
    public function refresh_access_token_oauth2( $settings ){
        $oauth2_data = $this->get_oauth2_data();
        $success = parent::new_request( "POST", "https://login.salesforce.com/services/oauth2/token", array(
            "grant_type" => 'refresh_token',
            "client_id" => $settings['clientKey'],
            "client_secret" => $settings['clientSecret'],
            "refresh_token" => isset( $oauth2_data['refresh_token'] ) ? $oauth2_data['refresh_token'] : '',
        ) );
        $body = $this->get_response_body( true );
        if( $success ){
            $accessTokenData = $body;//access_token, signature, scope, instance_url, id, token_type, issued_at
            //Guardamos datos del token para usar mediante Ajax al suscribir usuarios
            $this->save_oauth2_connection( $oauth2_data, $accessTokenData );
        } else {
            $error_message = isset( $body['error_description'] ) ? $body['error_description'] : '';
            $this->error = $this->get_error_message( $error_message );
            $success = false;
        }
        return $success;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Request
    |---------------------------------------------------------------------------------------------------
    */
    public function new_request( $method, $url, $body = array(), $headers = array(), $options = array() ){
        $success = parent::new_request( $method, $url, $body, $headers, $options );
        $body = $this->get_response_body( true );
        if( is_array( $body ) && isset( $body[0]['errorCode'] ) ){
            $error_code = isset( $body[0]['errorCode'] ) ? $body[0]['errorCode'] : '';
            $error_message = isset( $body[0]['message'] ) ? $body[0]['message'] : '';
            if( $error_code == 'UNKNOWN_EXCEPTION' ){
                $error_code = 'DUPLICATES_DETECTED';
            }
            $error_message = $error_code . '. '. $error_message;
        } else {
            $error_message = isset( $body['error_description'] ) ? $body['error_description'] : '';
        }
        $this->error = $this->get_error_message( $error_message );
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        return $this->connected;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists( $args = array() ){
        $items = array();
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
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'FirstName';

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : 'LastName';

        //Datos necesarios para la suscripción
        $params = array();
        $params['Email'] = $email;
        $params['FirstName'] = $first_name['value'];
        $params['LastName'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '-';//Last name es obligatorio, no debe estar vacío


        if( ! empty( $data['custom_fields'] ) ){
            $custom_fields = $this->get_custom_fields();
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                $key = $this->isset_field( $cf_name, $custom_fields, false );
                if( $key !== false ){
                    $params[$cf_name] = $cf_value;
                }
            }
        }

        //Suscribir nuevo usuario
        $request_body = $params;
        $success = $this->new_request( "POST", "/services/data/v20.0/sobjects/Contact/", $request_body );
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
        $success = $this->new_request( "GET", "/services/data/v20.0/sobjects/Contact/describe/" );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $fields = isset( $body['fields'] ) ? $body['fields'] : array();
        foreach( $fields as $field ){
            $items[] = $field['name'];
        }
        return $items;
    }

}