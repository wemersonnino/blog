<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;


class MailWizzIntegration extends ServiceIntegration {
    private $api_endpoint = '';//https://mailing.website.com/api/index.php

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $api_key, $token, $url ){
        $this->api_key = trim( $api_key );
        $this->token = trim( $token );
        $this->api_endpoint = trim( $url );

        $this->ironman = new IronMan( $this->api_endpoint );

        //$this->ironman->set_option( 'encode_body', true );//La petición requiere datos en formato json
        $this->ironman->set_option( 'reset_body_after_request', true );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Request
    |---------------------------------------------------------------------------------------------------
    */
    public function new_request( $method, $url, $body = array(), $headers = array(), $options = array() ){
        $timestamp = time();
        $specialHeaderParams = array(
            'X-MW-PUBLIC-KEY' => $this->api_key,
            'X-MW-REMOTE-ADDR' => isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '',
            'X-MW-TIMESTAMP' => $timestamp,
        );
        $this->ironman->set_headers( $specialHeaderParams );

        if( $method === 'GET' ){
            $signatureParams = array_merge( $body, $specialHeaderParams );
        } else{
            $signatureParams = array_merge( $body, $specialHeaderParams );
            ksort( $signatureParams, SORT_STRING );
        }

        $api_url = trailingslashit( $this->api_endpoint ) . ltrim( $url, '/' );
        $api_url = $this->ironman->url_format_get( $api_url, $signatureParams );
        $signatureString = strtoupper( $method ) . ' ' . $api_url;
        $signature = hash_hmac( 'sha1', $signatureString, $this->token, false );
        $this->ironman->set_headers( array( 'X-MW-SIGNATURE' => $signature ) );

        $success = parent::new_request( $method, $url, $body, $headers, $options );
        $body = $this->get_response_body( true );
        $error_message = isset( $body['error'] ) ? $body['error'] : '';
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
        $success = $this->new_request( "GET", "/lists", array( 'page' => 1, 'per_page' => 100 ) );
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
        $lists = isset( $body['data']['records'] ) ? $body['data']['records'] : array();
        foreach( $lists as $list ){
            $items[$list['general']['list_uid']] = $list['general']['name'];
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
        $success = $this->new_request( "GET", "/lists/{$this->list_id}/subscribers/search-by-email", array( 'EMAIL' => $email ) );
        if( ! $success ){
            return false;
        }
        $body = $this->get_response_body( true );
        $contact = ! empty( $body['data']['subscriber_uid'] ) ? $body['data']['subscriber_uid'] : false;
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
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'FNAME';

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : 'LNAME';

        //Datos necesarios para la suscripción
        $params = array();
        $params['EMAIL'] = $email;
        $params['FNAME'] = $first_name['value'];
        $params['LNAME'] = $last_name['value'];

        if( ! empty( $data['custom_fields'] ) ){
            $custom_fields = $this->get_custom_fields();
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                $cf_name = strtoupper( $cf_name );
                if( in_array( $cf_name, $custom_fields ) ){
                    $params[$cf_name] = $cf_value;
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
            $success = $this->new_request( "PUT", "/lists/{$this->list_id}/subscribers/{$subscriber_id}", $request_body );
        } else{
            //Suscribir nuevo usuario
            $request_body = $params;
            $success = $this->new_request( "POST", "/lists/{$this->list_id}/subscribers", $request_body );
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
            'EMAIL',
            'FNAME',
            'LNAME',
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        $success = $this->new_request( "GET", "/lists/{$this->list_id}/fields" );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $fields = isset( $body['data']['records'] ) ? $body['data']['records'] : array();
        foreach( $fields as $field ){
            $items[] = $field['tag'];
        }
        return $items;
    }

}
