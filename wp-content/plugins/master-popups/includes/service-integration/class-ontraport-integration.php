<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;

class OntraportIntegration extends ServiceIntegration {
    private $api_endpoint = 'https://api.ontraport.com/1/';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $api_key, $app_id ){
        $this->api_key = trim( $api_key );
        $this->app_id = trim( $app_id );

        $this->ironman = new IronMan( $this->api_endpoint );
        $this->ironman->set_headers( array(
            'Api-key' => $this->api_key,
            'Api-Appid' => $this->app_id,
            'Content-Type' => 'application/x-www-form-urlencoded',
        ) );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        $success = $this->new_request( "GET", "/Groups" );
        if( $this->ironman->response_code == 401 ){
            $body = $this->ironman->response_body;
            if( is_string( $body ) && json_decode( $body ) == null ){
                $this->error = $this->error ? $this->error . ". {$body}": $body;
            }
            return false;
        }
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists(){
        $items = array();
        $success = $this->new_request( "GET", "/Tags" );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $lists = isset( $body['data'] ) ? $body['data'] : array();
        foreach( $lists as $list ){
            $items[$list['tag_id']] = $list['tag_name'];
        }
        return $items;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verificar si el contacto está en la lista indicada
    |---------------------------------------------------------------------------------------------------
    */
    public function subscriber_exists( $email ){
        $success = $this->new_request( "GET", "/Contacts", array('search' => $email, 'ids' => '0', 'range' => 50 ) );
        if( ! $success ){
            return false;
        }
        $body = $this->get_response_body( true );
        $contact = isset( $body['data'][0] ) ? $body['data'][0] : false;
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
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'firstname';

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : 'lastname';

        //Datos necesarios para la suscripción
        $params = array();
        $params['email'] = $email;

        if( ! empty( $data['custom_fields'] ) ){
            $custom_fields = $this->get_custom_fields();
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                $key = array_search( $cf_name, $custom_fields );
                if( $key !== false ){
                    if( @preg_match( '/^f[0-9]+/', $key ) ){//Custom fields creados por el usuario: f1575, f1577
                        $params[$key] = $cf_value;
                    } else{
                        $params[$cf_name] = $cf_value;
                    }
                }
            }
        }

        $params['contact_cat'] = $this->list_id;
        $params[$first_name['name']] = $first_name['value'];
        $params[$last_name['name']] = $last_name['value'];


        //Comprobamos si el usuario ya está registrado
        if( $contact = $this->subscriber_exists( $email ) ){
            $overwrite = isset( $data['overwrite'] ) && $data['overwrite'] == 'on' ? true : false;
            if( ! $overwrite ){
                $this->error = $this->messages['subscriber_exists'];
                return false;
            }
            $contact_id = isset( $contact['id'] ) ? $contact['id'] : 0;
            if( $contact_id ){
                $params['id'] = $contact_id;
                //Actualizar datos del usuario
                $success = $this->new_request( "PUT", "/Contacts", $params );
                return $success;
            }
        } else {
            //Suscribir nuevo usuario
            $success = $this->new_request( "POST", "/Contacts", $params );
            return $success;
        }
        return false;
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Retorna todos los campos por defecto
	|---------------------------------------------------------------------------------------------------
	*/
    public function get_default_fields(){
        return array();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        $success = $this->new_request( "GET", "/Contacts/meta" );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $fields = isset( $body['data'][0]['fields'] ) ? $body['data'][0]['fields'] : array();
        foreach( $fields as $key => $field ){
            if( @preg_match( '/^f[0-9]+/', $key ) ){
                $items[$key] = $field['alias'];
            } else{
                $items[$field['alias']] = $key;
            }
        }
        return $items;
    }

}
