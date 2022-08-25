<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;

class ExampleIntegration extends ServiceIntegration {
    private $api_endpoint = 'https://api.moosend.com/v3/';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $auth_type, $api_key ){
        $this->auth_type = $auth_type;//basic_auth, oauth2
        $this->api_key = trim( $api_key );

        $this->ironman = new IronMan( $this->api_endpoint );

        $this->ironman->set_option( 'encode_body', true );//La petición requiere datos en formato json
        $this->ironman->set_option( 'reset_body_after_request', true );
        //$basic_auth = base64_encode( $this->email . ':' . $this->api_key );
        $this->ironman->set_headers( array(
            'Content-Type' => 'application/json',
            //'Authorization' => "Basic $basic_auth",
            //'Authorization' => "Bearer $this->api_key",
        ) );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Request
    |---------------------------------------------------------------------------------------------------
    */
    public function new_request( $method, $url, $body = array(), $headers = array(), $options = array() ){
        $success = parent::new_request( $method, $url, $body, $headers, $options );

        //Usar esta forma si por cada método la autenticación es diferente
        if( $method == 'GET' ){
            $success = parent::new_request( $method, $url, array_merge( $body, array( 'apikey' => $this->api_key ) ), $headers, $options );
        } else{
            $success = parent::new_request( $method, $url . "?apikey={$this->api_key}", $body, $headers, $options );
        }

        //$this->error ya tiene un error agregado por la clase padre.
        //Pero es importante obtener también un error de acuerdo a cada servicio
        $body = $this->get_response_body( true );
        $error_message = isset( $body['errors'] ) ? $body['errors'] : '';
        $this->error = $this->get_error_message( $error_message );
        //d("====================== Request: ", $this->get_url());
        //d($this->response);
        //d($this->get_request_body());
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        $success = $this->new_request( "GET", "/lists" );
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
        $body = $this->get_response_body( true );
        $lists = isset( $body['result'] ) ? $body['result'] : array();
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
        $success = $this->new_request( "POST", "/contacts/search", $email);
        if( ! $success ){
            return false;
        }
        $body = $this->get_response_body( true );
        $contact = ! empty( $body['result'] ) ? $body['result'] : false;
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
        $params['email'] = $email;
        $params[$first_name['name']] = $first_name['value'];
        $params[$last_name['name']] = $last_name['value'];
        $params['custom_fields'] = array();


        if( ! empty( $data['custom_fields'] ) ){
            $default_fields = $this->get_default_fields();
            $custom_fields = $this->get_custom_fields();
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                //[1] Algunos servicios permiten enviar cualquier campo. Usar esta manera:
                $params[$cf_name] = $cf_value;

                //[2] Si el servicio requiere sólo campos permtidos entonces usar esta manera
                //Pero ten en cuenta que esta forma no toma en cuenta las mayúsculas ni minúsculas
                if( in_array( $cf_name, $default_fields ) ){
                    $params[$cf_name] = $cf_value;
                }

                //[3] También es posible comprobarlo de esta manera
                //Verifica si existe campo $cf_name en array $custom_fields. Si último parámetro es true distingue mayúsculas y minúsculas
                //Devuelve el index o el key del campo. El key puede ser el ID del campo del servicio
                $key = $this->isset_field( $cf_name, $custom_fields, false );
                if( $key !== false ){
                    $params['custom_fields'][$cf_name] = $cf_value;
                }
                //Algunos servicios requieren el id del campo en lugar del nombre.
                //Previamente debiste agregar el id del campo a la hora de obtener todos los campos en get_custom_fields()
                if( $key !== false ){
                    $params['custom_fields'][$key] = $cf_value;
                }

                //Algunos servicios permiten registrar tags a cada suscriptor
                if( strtolower( $cf_name ) == 'tags' ){
                    $params['tags'] = array_map( 'trim', explode( ',', $cf_value ) );//Usar esta forma si requiere un array de tags
                    $params['tags'] = $cf_value;//Y esta forma si sólo acepta tags separados por comas
                }
            }
        }

        if( empty( $params['custom_fields'] ) ){
            unset( $params['custom_fields'] );//Eliminar si está vacío porque da error
        }

        //Importante. Rellena los campos del tipo {origin_url}, {ip}, {popup_title} con su valor real
        $params = $this->populate_render_fields( $params );

        //[1] Usar esta forma si la api permite crear y actualizar usuarios desde una sola ruta.
        //Comprobamos si el usuario ya está registrado
        $overwrite = isset( $data['overwrite'] ) && $data['overwrite'] == 'on' ? true : false;
        if( ! $overwrite && $this->subscriber_exists( $email ) ){
            $this->error = $this->messages['subscriber_exists'];
            return false;
        }
        //Suscribir nuevo usuario
        $request_body = array(
            'list_ids' => array( $this->list_id ),
            //Algunos servicios permiten registrar un suscriptor a varias listas desde una sóla llamada
            'list_ids' => $this->get_registered_lists(),
            'contacts' => array( $params )
        );
        $success = $this->new_request( "PUT", "/add-or-update", $request_body );
        //Suscribir también a varias listas si existe más de una lista. Ver ejemplos más abajo.


        //[2] Usar esta forma si la api tiene dos rutas para crear y actualizar
        //Comprobamos si el usuario ya está registrado
        if( $subscriber_id = $this->subscriber_exists( $email ) ){
            $overwrite = isset( $data['overwrite'] ) && $data['overwrite'] == 'on' ? true : false;
            if( ! $overwrite ){
                $this->error = $this->messages['subscriber_exists'];
                return false;
            }
            //Actualizar Usuario
            $request_body = $params;
            $success = $this->new_request( "PUT", "/lists/{$this->list_id}/subscribers/$subscriber_id", $request_body );

            //Suscripción a varias listas si existe más de una lista. Con $this->get_registered_lists(1) se itera a partir de la segunda lista.
            foreach( $this->get_registered_lists(1) as $list_id ){
                $this->new_request( "PUT", "/lists/{$list_id}/subscribers/$subscriber_id", $request_body );
            }
        } else{
            //Suscribir nuevo usuario
            $request_body = $params;
            $success = $this->new_request( "POST", "/lists/{$this->list_id}/subscribers", $request_body );

            //Suscripción a varias listas si existe más de una lista. Con $this->get_registered_lists(1) se itera a partir de la segunda lista.
            foreach( $this->get_registered_lists(1) as $list_id ){
                $this->new_request( "POST", "/lists/{$list_id}/subscribers", $request_body );
            }
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
            'country',
            'city',
            'postal_code',
            'address_line_1',
            'address_line_2',
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        $success = $this->new_request( "GET", "/field_definitions" );
        if( ! $success ){
            return array();
        }
        $body = $this->get_response_body( true );
        $fields = isset( $body['custom_fields'] ) ? $body['custom_fields'] : array();
        foreach( $fields as $field ){
            $items[$field['id']] = $field['name'];
        }
        return $items;
    }

}
