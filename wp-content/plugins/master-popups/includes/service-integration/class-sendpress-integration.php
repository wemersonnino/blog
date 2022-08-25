<?php namespace MasterPopups\Includes\ServiceIntegration;

use \SendPress_Data as SendPress_Data;

class SendpressIntegration extends ServiceIntegration {

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct(){

    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        if( class_exists( 'SendPress_Data' ) ){
            return true;
        }
        $this->error = sprintf( __( 'Please install and activate the %s plugin.', 'masterpopups' ), '<a href="https://wordpress.org/plugins/sendpress/" target="_blank">SendPress</a>' );
        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists(){
        $items = array();
        $wp_query = SendPress_Data::get_lists();
        $lists = $wp_query->posts;
        foreach( $lists as $list ){
            $items[$list->ID] = $list->post_title;
        }
        return $items;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verificar si el contacto está en la lista indicada
    |---------------------------------------------------------------------------------------------------
    */
    private function subscriber_exists( $email ){
        $subscriber_id = SendPress_Data::get_subscriber_by_email( $email );
        if( $subscriber_id ){
            return $subscriber_id;
        }
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
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'firstname';

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : 'lastname';

        //Datos necesarios para la suscripción
        $params = array();
        $params['email'] = $email;
        $params['firstname'] = $first_name['value'];
        $params['lastname'] = $last_name['value'];

        //Status 2 es cambiado a 1 en caso la opción "Double-Opt-in" está activada en los ajustes de SendPress.
        $params['status'] = 2;//1=unconfirmed, 2=active, 3=unsubscribed, 4=bounced
        $params['phonenumber'] = '';
        $params['salutation'] = '';

        $custom = array();
        if( ! empty( $data['custom_fields'] ) ){
            $custom_fields = $this->get_custom_fields();
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                if( $cf_name == 'phone' || $cf_name == 'phonenumber' ){
                    $params['phonenumber'] = $cf_value;
                } else if( $cf_name == 'salutation' ){
                    $params['salutation'] = $cf_value;
                } else{
                    if( in_array( $cf_name, $custom_fields ) ){
                        $custom[$cf_name] = $cf_value;
                    }
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
            unset( $params['email'], $params['status'] );
            SendPress_Data::update_subscriber( $subscriber_id, $params );//No restorna nada
            $success = true;

            //Actualizar los campos personalizados
            if( ! empty( $custom ) ){
                foreach( $custom as $key => $value ){
                    SendPress_Data::update_subscriber_meta( $subscriber_id, $key, $value, $this->list_id );
                }
            }
        } else{
            //Suscribir nuevo usuario
            $success = SendPress_Data::subscribe_user(
                $this->list_id,
                $params['email'],
                $params['firstname'],
                $params['lastname'],
                $params['status'],
                $custom,
                $params['phonenumber'],
                $params['salutation']
            );
            //$success = array(...);
            $success = ! ! $success;
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
            'firstname',
            'lastname',
            'phonenumber',
            'salutation',
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        $fields = SendPress_Data::get_custom_fields_new();
        if( is_array( $fields ) ){
            foreach( $fields as $key => $data ){
                $items[$data['custom_field_label']] = $data['custom_field_key'];
            }
        }
        return $items;
    }
}

