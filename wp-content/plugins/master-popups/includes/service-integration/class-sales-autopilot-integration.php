<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;

class SalesAutopilotIntegration extends ServiceIntegration {
    private $api_endpoint = 'http://restapi.emesz.com';

    //Account: Settings -> Integration -> API Keys.
    private $username;//Ejemplo: 5555555555555555555
    private $password;//Ejemplo: ce9a67b9203a3a6beab64e

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $username, $password ){
        $this->username = trim( $username );
        $this->password = trim( $password );

        $this->ironman = new IronMan( $this->api_endpoint );

        $this->ironman->set_option( 'encode_body', true );//La petición requiere datos en formato json
        $this->ironman->set_option( 'reset_body_after_request', true );
        $basic_auth = base64_encode( $this->username . ':' . $this->password );
        $this->ironman->set_headers( array(
            'Content-Type' => 'application/json',
            'Authorization' => "Basic $basic_auth",
        ) );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        parent::new_request( "GET", "/getproduct/0" );
        if( $this->get_response_code() === 401 ){
            return false;
        }
        return true;
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
    | Verificar si el contacto está en la lista indicada
    |---------------------------------------------------------------------------------------------------
    */
    private function subscriber_exists( $email ){
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
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'mssys_firstname';

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : 'mssys_lastname';

        //Datos necesarios para la suscripción
        $params = array();
        $params['email'] = $email;
        $params['mssys_firstname'] = $first_name['value'];
        $params['mssys_lastname'] = $last_name['value'];

        if( ! empty( $data['custom_fields'] ) ){
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                $params[$cf_name] = $cf_value;
            }
        }

        //Suscribir nuevo usuario
        $request_body = $params;

        if( ! $this->list_id ){
            $this->error = 'Enter your List ID';
            return false;
        }

        //$form_id = isset( $this->list_fields['form_id'] ) ? $this->list_fields['form_id']: '';

        //http://restapi.emesz.com/subscribe/<List_ID>/form/<Form_ID>
        //Form_ID no es obligatorio
        parent::new_request( "POST", "/subscribe/$this->list_id/form/0", $request_body );
        // -1: if the email address has to be unique in the list and the email is already existing in the list.
        // -2: email address syntax error.
        // 0: unknown error.
        $body = $this->get_response_body( true);
        $success = $this->get_response_code() == 200 && $body > 0;

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
            'mssys_firstname',
            'mssys_lastname',
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
