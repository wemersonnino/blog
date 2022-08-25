<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;

class AgileCRMIntegration extends ServiceIntegration {
    private $api_endpoint = 'https://{domain}.agilecrm.com/dev/api';
    private $email = '';
    private $url = '';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $api_key, $email, $url ){
        $this->api_key = trim( $api_key );
        $this->email = trim( $email );
        $this->url = trim( $url );

        $this->api_endpoint = trailingslashit( $this->url ) . 'dev/api';

        $this->ironman = new IronMan( $this->api_endpoint );
        $this->ironman->set_option( 'encode_body', true );//La petición requiere datos en formato json
        $basic_auth = base64_encode( $this->email . ':' . $this->api_key );
        $this->ironman->set_headers( array(
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',//Para que las respuestas estén en json y no en xml
            'Authorization' => "Basic $basic_auth",
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
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        $success = $this->new_request( "GET", "/contacts" );
        return $success;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists( $args = array() ){
        $items = array();
        $success = $this->new_request( "GET", "/workflows" );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $lists = (array) $body;
        foreach( $lists as $list ){
            $list_id = number_format( $list['id'], 0, '', '' );
            $items[$list_id] = $list['name'];
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
        $success = $this->new_request( "GET", "/contacts/search/email/$email" );
        if( ! $success ){
            return false;
        }
        $body = $this->get_response_body( true );
        $contact = isset( $body['id'] ) ? $body : false;
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
        $params[] = array(
            'name' => 'email',
            'type' => 'SYSTEM',
            'value' => $email,
        );
        $params[] = array(
            'name' => 'first_name',
            'type' => 'SYSTEM',
            'value' => $first_name['value'],
        );
        $params[] = array(
            'name' => 'last_name',
            'type' => 'SYSTEM',
            'value' => $last_name['value'],
        );

        $tags = array();
        if( ! empty( $data['custom_fields'] ) ){
            $default_fields = $this->get_default_fields();//SYSTEM fields
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                if( strtolower( $cf_name ) == "tags" ){
                    $tags = array_map( 'trim', explode( ',', $cf_value ) );
                } else if( in_array( $cf_name, $default_fields ) ){
                    $params[] = array(
                        'name' => $cf_name,
                        'type' => 'SYSTEM',
                        'value' => $cf_value,
                    );
                } else{
                    //Acepta cualquier campo
                    $params[] = array(
                        'name' => $cf_name,
                        'type' => 'CUSTOM',
                        'value' => $cf_value,
                    );
                }
            }
        }

        //Comprobamos si el usuario ya está registrado
        $overwrite = isset( $data['overwrite'] ) && $data['overwrite'] == 'on' ? true : false;
        if( ! $overwrite && $this->subscriber_exists( $email ) ){
            $this->error = $this->messages['subscriber_exists'];
            return false;
        }

        //Comprobamos si el usuario ya está registrado
        if( $subscriber = $this->subscriber_exists( $email ) ){
            $overwrite = isset( $data['overwrite'] ) && $data['overwrite'] == 'on' ? true : false;
            if( ! $overwrite ){
                $this->error = $this->messages['subscriber_exists'];
                return false;
            }
            $subscriber_id = number_format( $subscriber['id'], 0, '', '' );
            $request_body = array(
                'id' => $subscriber_id,
                'properties' => $params,
            );
            $success = $this->new_request( "PUT", "/contacts/edit-properties", $request_body );

            //Edit tags
            if( ! empty( $tags ) ){
                $request_body = array(
                    'id' => $subscriber_id,
                    'tags' => $tags,
                );
                $this->new_request( "PUT", "/contacts/edit/tags", $request_body );
            }
        } else{
            //Suscribir nuevo usuario
            $request_body = array(
                'properties' => $params,
                'tags' => $tags,
            );
            $success = $this->new_request( "POST", "/contacts", $request_body );
        }

        $this->add_subscriber_to_list( $email );

        return $success;
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Agrega un suscriptor a la lista
	|---------------------------------------------------------------------------------------------------
	*/
    public function add_subscriber_to_list( $email ){
        $success = null;
        //Agregar a lista=campaña
        if( $this->list_id ){
            $request_body = array(
                'email' => $email,
                'workflow-id' => $this->list_id,
            );
            $this->ironman->set_option( 'encode_body', false );//No acepta json
            $this->ironman->set_headers( array(
                'Content-Type' => 'application/x-www-form-urlencoded',
            ) );
            $success = $this->new_request( "POST", "/campaigns/enroll/email", $request_body );
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
            'first_name',
            'last_name',
            'phone',
            'company',
            'title',
            'address',
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
