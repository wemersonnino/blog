<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;

class ConstantContactIntegration extends ServiceIntegration {
    private $api_endpoint = 'https://api.constantcontact.com/v2';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $auth_type, $api_key, $access_token ){
        $this->auth_type = $auth_type;//basic_auth, oauth2
        $this->api_key = trim( $api_key );
        $this->access_token = trim( $access_token );

        $this->ironman = new IronMan( $this->api_endpoint );

        $this->ironman->set_option( 'encode_body', true );//La petición requiere datos en formato json
        $this->ironman->set_option( 'reset_body_after_request', true );
        $this->ironman->set_headers( array(
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer $this->access_token",
        ) );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Request
    |---------------------------------------------------------------------------------------------------
    */
    public function new_request( $method, $url, $body = array(), $headers = array(), $options = array() ){
        $url = Functions::make_url( $url, array( 'api_key' => $this->api_key ) );
        $success = parent::new_request( $method, $url, $body, $headers, $options );
        if( ! $success ){
            $body = $this->get_response_body( true );
            $error_message = isset( $body[0]['error_message'] ) ? $body[0]['error_message'] : '';
            $this->error = $this->get_error_message( $error_message );
        }
        //d( "==== Request: ", $this->get_url(), '====', $method );
        # d( $this->ironman->headers );
        //d( $this->response );
        //d( $this->get_request_body() );
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
        $success = $this->new_request( "GET", "/lists" );
        if( ! $success ){
            return array();
        }
        $lists = $this->get_response_body( true );
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
        $email = strtolower( $email );
        $success = $this->new_request( "GET", "/contacts?email=$email&status=ALL&limit=1" );
        if( ! $success ){
            return false;
        }
        $body = $this->get_response_body( true );
        $contact = ! empty( $body['results'][0] ) ? $body['results'][0] : false;
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

        $params = array();
        $params['email_addresses'][] = array( 'email_address' => $email, 'opt_in_source' => 'ACTION_BY_OWNER' );// https://developer.constantcontact.com/docs/contacts-api/contacts-collection.html?method=POST
        $params[$first_name['name']] = $first_name['value'];
        $params[$last_name['name']] = $last_name['value'];
        $params['status'] = 'ACTIVE';
        $params['custom_fields'] = array();


        $base_lists = $this->get_registered_lists();
        $lists = array();
        foreach( $base_lists as $base_list ){
            $lists[] = array( 'id' => $base_list );
        }

        $params['lists'] = $lists;

        if( ! empty( $data['custom_fields'] ) ){
            $custom_fields = $this->get_custom_fields();
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                $key = $this->isset_field( $cf_name, $custom_fields, false );
                if( $key !== false ){
                    $params['custom_fields'][] = array( 'name' => $cf_name, 'value' => $cf_value );
                }
            }
        }

        if( empty( $params['custom_fields'] ) ){
            unset( $params['custom_fields'] );//Eliminar si está vacío porque da error
        }

        $params = $this->populate_render_fields( $params );

        $subscriber = $this->subscriber_exists( $email );
        if( $subscriber ){
            $overwrite = isset( $data['overwrite'] ) && $data['overwrite'] == 'on' ? true : false;
            if( ! $overwrite ){
                $this->error = $this->messages['subscriber_exists'];
                return false;
            }
            $request_body = $params;
            $success = $this->new_request( "PUT", "/contacts/" . $subscriber['id'] . "?action_by=ACTION_BY_OWNER", $request_body );
        } else{
            $request_body = $params;
            $success = $this->new_request( "POST", "/contacts/", $request_body );
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
            'prefix_name',
            'first_name',
            'middle_name',
            'last_name',
            'fax',
            'job_title',
            'home_phone',
            'cell_phone',
            'work_phone',
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        for( $i = 1; $i < 16; $i++ ){
            $items[] = 'custom_field_' . $i;
        }
        return $items;
    }
}
