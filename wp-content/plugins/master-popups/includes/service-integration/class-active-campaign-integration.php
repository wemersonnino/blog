<?php namespace MasterPopups\Includes\ServiceIntegration;

use MasterPopups\ActiveCampaign\ActiveCampaign;

class ActiveCampaignIntegration extends ServiceIntegration {
    protected $api_url = '';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $api_key = '', $api_url = '' ){
        $this->api_key = $api_key;
        $this->api_url = $api_url;
        $this->service = new ActiveCampaign( $this->api_url, $this->api_key );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        if( ! $this->service ){
            return false;
        }
        return $this->service->credentials_test();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists(){
        $items = array();
        $lists = (array) $this->service->api( "list/list_", array(
            'ids' => 'all',
            'full' => '0' //http://www.activecampaign.com/api/example.php?call=list_list
        ) );
        if( $lists ){
            foreach( $lists as $list ){
                if( is_object( $list ) ){
                    $items[$list->id] = $list->name;
                }
            }
        }
        return $items;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verificar si el contacto está en la lista indicada
    |---------------------------------------------------------------------------------------------------
    */
    private function subscriber_exists( $email ){
        $this->response = $this->service->api( "contact/view?email=$email" );
        if( $this->response->success != 1 ){
            return false;
        }
        return $this->response->subscriberid;
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
        $params["p[{$this->list_id}]"] = $this->list_id;
        $params["status[{$this->list_id}]"] = 1;//1: active, 2: unsubscribed

        if( ! empty( $data['custom_fields'] ) ){
            if( isset( $data['custom_fields']['tags'] ) ){
                $params['tags'] = $data['custom_fields']['tags'];
                unset( $data['custom_fields']['tags'] );
            }
            $default_fields = array_map( 'strtolower', $this->get_default_fields() );
            $custom_fields = array_map( 'strtolower', $this->get_custom_fields() );
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                if( in_array( strtolower( $cf_name ), $default_fields ) ){
                    $params[strtolower( $cf_name )] = $cf_value;//name debe estar en minúscula para que guarde
                } else if( in_array( strtolower( $cf_name ), $custom_fields ) ){
                    $params["field[$cf_name,0]"] = $cf_value;//$cf_name = %FIELD_TAG%
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
            $request_body['id'] = $subscriber_id;
            $this->response = $this->service->api( "contact/edit", $request_body );

            //Suscripción a varias listas si existe más de una lista. Con $this->get_registered_lists(1) se itera a partir de la segunda lista.
            foreach( $this->get_registered_lists(1) as $list_id ){
                $request_body["p[{$list_id}]"] = $list_id;
                $this->service->api( "contact/edit", $request_body );
            }
        } else{
            //Suscribir nuevo usuario
            $request_body = $params;
            $this->response = $this->service->api( "contact/add", $request_body );

            //Suscripción a varias listas si existe más de una lista. Con $this->get_registered_lists(1) se itera a partir de la segunda lista.
            foreach( $this->get_registered_lists(1) as $list_id ){
                $request_body["p[{$list_id}]"] = $list_id;
                $this->service->api( "contact/add", $request_body );
            }
        }

        //Si el suscriptor ya existe en la lista general, sólo lo agrega a la lista actual.
        //Success es igual a 1 cuando registra o actualiza bien
        if( $this->response->success == 1 ){
            return true;
        } else{
            $this->error = $this->response->result_message;
            return false;
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos por defecto
    |---------------------------------------------------------------------------------------------------
    */
    //estos campos deben enviarse sin % al inicio ni al final
    public function get_default_fields(){
        return array(
            'first_name',
            'last_name',
            'phone',
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    //La API solo retorna los creados por el usuario, no los por defecto
    public function get_custom_fields(){
        $items = array();
        $response = $this->service->api( "list/field_view?ids=all" );
        if( $response->success == 1 ){
            foreach( get_object_vars( $response ) as $object ){
                if( is_object( $object ) ){
                    $items[] = $object->tag;
                }
            }
            return $items;
        }
        return array();
    }

}

