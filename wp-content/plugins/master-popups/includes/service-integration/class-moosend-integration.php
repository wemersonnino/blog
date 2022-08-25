<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;

class MoosendIntegration extends ServiceIntegration {
    private $api_endpoint = 'https://api.moosend.com/v3/';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $api_key ){
        $this->api_key = trim( $api_key );

        $this->ironman = new IronMan( $this->api_endpoint );

        $this->ironman->set_headers( array(
            //'Content-Type' => 'application/json',//Ocaciona 500 Internal Server Error, aunque la documentación lo requiere.
        ) );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Request
    |---------------------------------------------------------------------------------------------------
    */
    public function new_request( $method, $url, $body = array(), $headers = array(), $options = array() ){
        if( $method == 'GET' ){
            $success = parent::new_request( $method, $url, array_merge( $body, array( 'apikey' => $this->api_key ) ), $headers, $options );
        } else{
            $success = parent::new_request( $method, $url . "?apikey={$this->api_key}", $body, $headers, $options );
        }
        if( ! $success ){
            return false;
        }
        $body = $this->get_response_body( true );
        $error_message = isset( $body['Error'] ) && $body['Error'] !== null ? $body['Error'] : '';
        $this->error = $this->get_error_message( $error_message );
        $success = $body['Error'] === null;//Cuando es null la petición es correcta
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        $success = $this->new_request( "GET", "/lists.json" );
        return $success;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists( $args = array() ){
        $items = array();
        $success = $this->new_request( "GET", "/lists.json" );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $lists = isset( $body['Context']['MailingLists'] ) ? $body['Context']['MailingLists'] : array();
        foreach( $lists as $list ){
            $items[$list['ID']] = $list['Name'];
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
        $success = $this->new_request( "GET", "/subscribers/$this->list_id/view.json", array( 'Email' => $email ));
        if( ! $success ){
            return false;
        }
        $body = $this->get_response_body( true );
        $contact = ! empty( $body['Context'] ) ? $body['Context'] : false;
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
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'first_name';

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : 'last_name';

        //Datos necesarios para la suscripción
        $params = array();
        $params['Email'] = $email;
        $params['Name'] = trim( $first_name['value'] . ' ' . $last_name['value'] );
        $params['HasExternalDoubleOptIn'] = true;//Cuando es verdadero, señala que el usuario se suscribe a la lista.
        $params['CustomFields'] = array();

        if( ! empty( $data['custom_fields'] ) ){
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                //Acepta cualquier campo pero solo guarda los definidos en su plataforma
                $params['CustomFields'][] = $cf_name."=".$cf_value;
            }
        }

        //Comprobamos si el usuario ya está registrado
        $overwrite = isset( $data['overwrite'] ) && $data['overwrite'] == 'on' ? true : false;
        if( ! $overwrite && $this->subscriber_exists( $email ) ){
            $this->error = $this->messages['subscriber_exists'];
            return false;
        }

        //Suscribir nuevo usuario
        $request_body = $params;
        $success = $this->new_request( "POST", "/subscribers/$this->list_id/subscribe.json", $request_body );
        return $success;
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Retorna todos los campos por defecto
	|---------------------------------------------------------------------------------------------------
	*/
    public function get_default_fields(){
        return array(
            'Email',
            'Name',
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
