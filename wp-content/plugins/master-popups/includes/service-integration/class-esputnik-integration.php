<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;

class EsputnikIntegration extends ServiceIntegration {
    private $api_endpoint = 'https://esputnik.com/api/v1/';
    private $email;
    private $password;

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $email, $password ){
        $this->email = trim( $email );
        $this->password = trim( $password );

        $this->ironman = new IronMan( $this->api_endpoint );

        $this->ironman->set_option( 'encode_body', true );//La petición requiere datos en formato json
        $basic_auth = base64_encode( $this->email . ':' . $this->password );
        $this->ironman->set_headers( array(
            'Content-Type' => 'application/json',
            'Authorization' => "Basic $basic_auth",
        ) );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Request
    |---------------------------------------------------------------------------------------------------
    */
    public function new_request( $method, $url, $body = array(), $headers = array(), $options = array() ){
        $success = parent::new_request( $method, $url, $body, $headers, $options );
        $body = $this->get_response_body( null );
        $ob = json_decode( $body );
        $error_message = $ob === null ? $body : '';
        $this->error = $this->get_error_message( $error_message );
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        $success = $this->new_request( "GET", "/account/info" );
        return $success;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists( $args = array() ){
        $items = array();
        $success = $this->new_request( "GET", "/groups" );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $lists = is_array( $body ) ? $body : array();
        foreach( $lists as $list ){
            $items[$list['id']] = $list['name'];
        }
        return $items;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna el nombre de una lista
    |---------------------------------------------------------------------------------------------------
    */
    public function get_list_name( $list_id ){
        $lists = $this->get_lists();
        return isset( $lists[$list_id] ) ? $lists[$list_id] : '';
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
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'firstName';

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : 'lastName';

        //Datos necesarios para la suscripción
        $params = array();
        $params['firstName'] = $first_name['value'];
        $params['lastName'] = $last_name['value'];
        $params['channels'] = array(
            array(
                'type' => 'email',
                'value' => $email,
            ),
        );

        $params['fields'] = array();
        $customFieldsIDs = array();
        if( ! empty( $data['custom_fields'] ) ){
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                if( is_numeric( $cf_name ) ){
                    $cf_id = $cf_name;
                    $customFieldsIDs[] = $cf_id;
                    $params['fields'][] = array(
                        'id' => $cf_id,
                        'value' => $cf_value
                    );
                }
            }
        }

        //Suscribir nuevo usuario
        $request_body = array(
            'contacts' => array( $params ),
            'dedupeOn' => 'email',//Verifica la unicidad del contacto, en este caso por email, si existe lo actualiza.
            'groupNames' => array( $this->get_list_name( $this->list_id ) ),
            'customFieldsIDs' => $customFieldsIDs,//Necesario para que se registren los campos personalizados.
        );

        $success = $this->new_request( "POST", "/contacts", $request_body );//Agrega o actualiza un contacto

        return $success;
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Retorna todos los campos por defecto
	|---------------------------------------------------------------------------------------------------
	*/
    public function get_default_fields(){
        return array(
            'firstName',
            'lastName',
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
