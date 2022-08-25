<?php namespace MasterPopups\Includes;

use MaxLopez\HTTPClientWP\IronMan;

class EmailVerification {
    private $ironman = null;
    public $response = null;
    public $error = null;

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct(){
        //$this->ironman = new IronMan( $this->api_endpoint );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Get error
    |---------------------------------------------------------------------------------------------------
    */
    public function get_error(){
        return $this->error;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Success
    |---------------------------------------------------------------------------------------------------
    */
    public function success(){
        return $this->ironman->success();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Get response code
    |---------------------------------------------------------------------------------------------------
    */
    public function get_response_code(){
        return $this->ironman->response_code;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Get response body
    |---------------------------------------------------------------------------------------------------
    */
    public function get_response_body( $array = true ){
        if( is_null( $array ) ){
            return $this->ironman->get_response_body();
        }
        return json_decode( $this->ironman->get_response_body(), $array );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Valida email verificando si existe el dominio
    |---------------------------------------------------------------------------------------------------
    */
    public function mx_record( $email, $record = 'MX' ){
        if( empty( $email ) ){
            return false;
        }
        list( $user, $domain ) = explode( '@', $email );
        return checkdnsrr( $domain, $record );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verifiy with kickbox
    |---------------------------------------------------------------------------------------------------
    */
    public function kickbox( $email, $api_key = '' ){
        $success = true;
        $this->ironman = new IronMan();
        $this->ironman->set_option( 'timeout', 15 );
        $api_key = urlencode( $api_key );
        $url = "https://api.kickbox.com/v2/verify?email=$email&apikey=$api_key";
        $this->response = $this->ironman->get( $url );
        $body = $this->get_response_body( true );
        $this->error = isset( $body['message'] ) ? $body['message'] : false;

        //true si ( $ironman->response_code >= 200 && $ironman->response_code < 300 )
        if( $this->success() ){
            if( isset( $body['result'] ) && $body['result'] === 'undeliverable' ){
                $success = false;
            }
        } else{
            //$this->error = $this->ironman->get_error_message();
        }
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verifiy with neverbounce
    |---------------------------------------------------------------------------------------------------
    */
    public function neverbounce( $email, $api_key = '' ){
        $success = true;
        $this->ironman = new IronMan();
        $this->ironman->set_option( 'timeout', 15 );
        $api_key = urlencode( $api_key );
        $url = "https://api.neverbounce.com/v4/single/check?key=$api_key&email=$email";
        $this->response = $this->ironman->post( $url );
        $body = $this->get_response_body( true );
        $this->error = isset( $body['message'] ) ? $body['message'] : false;

        if( $this->success() ){
            if( isset( $body['result'] ) && $body['result'] == 'invalid' ){
                $success = false;
            }
        }
        return $success;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Verifiy with algocheck
    |---------------------------------------------------------------------------------------------------
    */
    public function algocheck( $email, $api_key = '' ){
        $success = true;
        $this->ironman = new IronMan();
        $this->ironman->set_option( 'timeout', 15 );
        $api_key = urlencode( $api_key );
        $url = "https://www.algocheck.com/api_credits.php?request=$api_key/$email";
        $this->response = $this->ironman->get( $url );
        $body = $this->ironman->response_body;

        if( $this->success() ){
            $_body = $this->get_response_body( true );
            if( is_array( $_body ) ){
                $this->error = isset( $_body['error_description'] ) ? $_body['error_description'] : false;
            } else {
                $this->error = stripos( $body, '"usage_limit_reached"' ) !== false ? 'Your monthly usage limit has been reached. Please upgrade your Subscription Plan' : false;
            }
            if( ! $this->error ){
                $is_valid = stripos( $body, '"mx_found":true' ) !== false && stripos( $body, '"smtp_check":true' ) !== false;
                if( ! $is_valid ){
                    $success = false;
                }
            }
        }
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verifiy with proofy
    |---------------------------------------------------------------------------------------------------
    */
    public function proofy( $email, $api_key = '', $user_id = '' ){
        $success = true;
        $this->ironman = new IronMan();
        $this->ironman->set_option( 'timeout', 15 );
        $api_key = urlencode( $api_key );
        $url = "https://api.proofy.io/verifyaddr?aid=$user_id&key=$api_key&email=$email";
        $this->response = $this->ironman->get( $url );
        $body = $this->get_response_body( true );
        $this->error = isset( $body['error'] ) && $body['error'] && isset( $body['message'] ) ? $body['message'] : false;

        if( $this->success() && ! $this->error ){
            $cid = isset( $body['cid'] ) ? $body['cid'] : '';
            $url = "https://api.proofy.io/getresult?aid=$user_id&key=$api_key&cid=$cid";
            $this->response = $this->ironman->get( $url );
            $body = $this->get_response_body( true );
            $this->error = isset( $body['error'] ) && $body['error'] && isset( $body['message'] ) ? $body['message'] : false;

            if( $this->success() && ! $this->error ){
                if( isset( $body['result'][0] ) && isset( $body['checked'] ) && $body['checked'] ){
                    $result = $body['result'][0];
                    $success = in_array( $result['statusName'], array( 'deliverable', 'risky' ) );
                }
            }
        }
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verifiy with thechecker
    |---------------------------------------------------------------------------------------------------
    */
    public function thechecker( $email, $api_key = '' ){
        $success = true;
        $this->ironman = new IronMan();
        $this->ironman->set_option( 'timeout', 15 );
        $api_key = urlencode( $api_key );
        $url = "https://api.thechecker.co/v2/verify?email=$email&api_key=$api_key";
        $this->response = $this->ironman->get( $url );
        $body = $this->get_response_body( true );
        $this->error = isset( $body['message'] ) ? $body['message'] : false;

        if( $this->success() ){
            if( isset( $body['result'] ) && $body['result'] == 'undeliverable' ){
                $success = false;
            }
        }
        return $success;
    }


}