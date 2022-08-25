<?php namespace MasterPopups\Includes\ServiceIntegration;

use MasterPopups\DrewM\MailChimp\MailChimp;
use MasterPopups\Includes\Functions as Functions;

class MailchimpIntegration extends ServiceIntegration {

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $api_key = '' ){
        $this->api_key = $api_key;

        try{
            $this->service = new MailChimp( $this->api_key );
            //$this->service->verify_ssl = true;
        } catch( \Exception $e ){
            $this->error = $e->getMessage();
        }
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
        $response = $this->service->get( '' );
        if( is_array( $response ) && ! empty( $response ) ){
            if( isset( $response['account_id'] ) ){
                return true;
            }
        }
        return false;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists(){
        $response = $this->service->get( 'lists', array( 'count' => 100 ) );
        if( ! $this->service->success() ){
            return array();
        }
        $items = array();
        if( $response['total_items'] >= 1 ){
            foreach( $response['lists'] as $list ){
                $items[$list['id']] = $list['name'];
            }
        }
        return $items;
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
        $params['email_address'] = $email;
        $params['status'] = isset( $data['double-opt-in'] ) && $data['double-opt-in'] == 'on' ? 'pending' : 'subscribed';
        $params['merge_fields'] = array();

        if( ! empty( $first_name['value'] ) ){
            $params['merge_fields'][$first_name['name']] = $first_name['value'];
        }

        if( ! empty( $last_name['value'] ) ){
            $params['merge_fields'][$last_name['name']] = $last_name['value'];
        }

        if( ! empty( $data['custom_fields'] ) ){
            $custom_fields = $this->get_custom_fields();
            foreach( $custom_fields as $cf_id => $cf_name ){
                $cf_name_lower = strtolower( $cf_name );
                if( isset( $data['custom_fields'][$cf_name] ) ){
                    $params['merge_fields'][$cf_name] = $data['custom_fields'][$cf_name];
                } elseif( isset( $data['custom_fields'][$cf_name_lower] ) ){
                    $params['merge_fields'][$cf_name] = $data['custom_fields'][$cf_name_lower];
                }
            }

            //Mailchimp Tags
            foreach( $data['custom_fields'] as $name => $value ){
                if( strtolower( $name ) == "tags" ){
                    $params['tags'] = array_map( 'trim', explode( ',', $value ) );
                }
            }

            //Mailchimp Groups
            $interests = array();
            $params['interests'] = array();
            //Sólo obtener intereses si hay algún campo que lo requiere
            foreach( $data['custom_fields'] as $name => $value ){
                if( Functions::starts_with( 'GROUP:', $name ) ){
                    $interests = $this->get_interests();
                    break;
                }
            }

            if( ! empty( $interests ) ){
                foreach( $data['custom_fields'] as $name => $value ){
                    if( ! Functions::starts_with( 'GROUP:', $name ) ){
                        continue;
                    }
                    list( $ghost, $interest_title ) = explode( ':', $name, 2 );
                    $interest_title2 = str_replace( '_', ' ', $interest_title );
                    $key = $interest_title;
                    if( isset( $interests[$interest_title2] ) ){
                        $key = $interest_title2;
                    }
                    if( ! isset( $interests[$key] ) ){
                        continue;
                    }
                    $type = $interests[$key]['type'];
                    if( $type == 'checkboxes' ){
                        $values = array_map('trim', explode(',', $value));
                        foreach($values as $val){
                            if( array_key_exists( $val, $interests[$key]['interests'] ) ){
                                $interest_category_id = $interests[$key]['interests'][$val];
                                $params['interests'][$interest_category_id] = true;
                            }
                        }
                    } else {
                        if( array_key_exists( $value, $interests[$key]['interests'] ) ){
                            $interest_category_id = $interests[$key]['interests'][$value];
                            $params['interests'][$interest_category_id] = true;
                        }
                    }
                }
            }
        }

        //Eliminar parámetro 'merge_fields' si está vacío porque la api da error.
        if( empty( $params['merge_fields'] ) ){
            unset( $params['merge_fields'] );
        }
        if( empty( $params['interests'] ) ){
            unset( $params['interests'] );
        }

        //$this->debug['test'] = $params;

        //Suscribir nuevo usuario
        $this->response = $this->service->post( "lists/{$this->list_id}/members", $params );

        if( $this->service->success() ){
            return true;
        } else{
            $this->error = isset( $this->response['title'] ) ? $this->response['title'] : $this->service->getLastError();
        }
        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        $response = $this->service->get( "lists/{$this->list_id}/merge-fields", array( 'count' => 100 ) );
        if( ! $this->service->success() ){
            return array();
        }
        if( isset( $response['merge_fields'] ) ){
            foreach( $response['merge_fields'] as $field ){
                $items[$field['merge_id']] = $field['tag'];
            }
        }
        return $items;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los grupos
    |---------------------------------------------------------------------------------------------------
    */
    public function get_groups(){
        $response = $this->service->get( "lists/{$this->list_id}/interest-categories", array( 'count' => 100 ) );
        if( ! $this->service->success() ){
            return array();
        }
        $items = array();
        if( $response['total_items'] >= 1 ){
            foreach( $response['categories'] as $group ){
                $items[$group['id']] = array(
                    'id' => $group['id'],
                    'title' => $group['title'],
                    'type' => $group['type'],
                );
            }
        }
        return $items;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los grupos y sus intereses
    |---------------------------------------------------------------------------------------------------
    */
    public function get_interests(){
        $items = array();
        $groups = $this->get_groups();
        if( empty( $groups ) ) return array();

        foreach( $groups as $group ){
            $interest_category_id = $group['id'];
            $response = $this->service->get( "lists/{$this->list_id}/interest-categories/{$interest_category_id}/interests", array( 'count' => 100 ) );
            if( ! $this->service->success() ){
                continue;
            }
            if( $response['total_items'] >= 1 ){
                $items[$group['title']] = array_merge( $group, array( 'interests' => array() ) );
                foreach( $response['interests'] as $interest ){
                    $items[$group['title']]['interests'][$interest['name']] = $interest['id'];
                }
            }
        }
        return $items;
    }


}
