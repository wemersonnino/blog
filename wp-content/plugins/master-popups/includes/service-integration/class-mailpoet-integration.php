<?php namespace MasterPopups\Includes\ServiceIntegration;


class MailpoetIntegration extends ServiceIntegration {
    private $is_mailpoet3 = false;
    private $is_mailpoet_activated = false;
    private $mailpoet3Api = null;

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct(){
        $this->check_mailpoet_plugin();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Check Mailpoet plugin
    |---------------------------------------------------------------------------------------------------
    */
    public function check_mailpoet_plugin(){
        if( defined( 'MAILPOET_INITIALIZED' ) && MAILPOET_INITIALIZED ){
            $this->is_mailpoet3 = true;
            $this->is_mailpoet_activated = true;
            $this->mailpoet3Api = \MailPoet\API\API::MP( 'v1' );
        }
        if( defined( 'WYSIJA' ) && class_exists( 'WYSIJA' ) ){
            $this->is_mailpoet_activated = true;
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        if( $this->is_mailpoet_activated ){
            return true;
        }
        $this->error = sprintf( __( 'Please install and activate the %s plugin.', 'masterpopups' ), '<a href="https://wordpress.org/plugins/mailpoet/" target="_blank">Mailpoet</a>' );
        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists(){
        $items = array();
        if( $this->is_mailpoet3 ){
            $lists = $this->mailpoet3Api->getLists();
            foreach( $lists as $list ){
                if( ! is_null( $list['deleted_at'] ) ) continue;
                $items[$list['id']] = $list['name'];
            }
        } else{
            $model_list = \WYSIJA::get( 'list', 'model' );
            $lists = $model_list->get( array( 'name', 'list_id' ), array( 'is_enabled' => 1 ) );
            foreach( $lists as $list ){
                $items[$list['list_id']] = $list['name'];
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
        if( $this->is_mailpoet3 ){
            $subscriber_id = 0;
            try{
                $subscriber = $this->mailpoet3Api->getSubscriber( $email );
                if( is_array( $subscriber ) && isset( $subscriber['id'] ) ){
                    $subscriber_id = $subscriber['id'];
                }
            } catch( \Exception $e ){
                //$e->getMessage(); "This subscriber does not exist."
            }
            return array(
                'id' => $subscriber_id,
                'status' => isset( $subscriber['status'] ) ? $subscriber['status'] : 'subscribed',
            );
        } else{
            $model_user = \WYSIJA::get( 'user', 'model' );
            $subscriber = $model_user->getOne( false, array( 'email' => $email ) );
            if( empty( $subscriber ) ){
                return array();
            }
            return array(
                'id' => isset( $subscriber['user_id'] ) ? $subscriber['user_id'] : 0,
                'status' => isset( $subscriber['status'] ) ? $subscriber['status'] : 1,
            );
        }

    }

    /*
    |---------------------------------------------------------------------------------------------------
    | First name and last name
    |---------------------------------------------------------------------------------------------------
    */
    private function get_first_name(){
        return $this->is_mailpoet3 ? 'first_name' : 'firstname';
    }

    private function get_last_name(){
        return $this->is_mailpoet3 ? 'last_name' : 'lastname';
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Agrega un suscriptor a una lista
    |---------------------------------------------------------------------------------------------------
    */
    public function add_subscriber( $email, $data = array() ){
        $first_name = $data['first_name'];
        $first_name['value'] = ! empty( $first_name['value'] ) ? $first_name['value'] : '';
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : $this->get_first_name();

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : $this->get_last_name();

        //Datos necesarios para la suscripción
        $params = array();
        $params['email'] = $email;
        $params[$first_name['name']] = $first_name['value'];
        $params[$last_name['name']] = $last_name['value'];
        $fields = array();

        if( ! empty( $data['custom_fields'] ) ){
            $custom_fields = $this->get_custom_fields();
            $default_fields = $this->get_default_fields();
            foreach( $custom_fields as $cf_id => $cf_name ){
                if( isset( $data['custom_fields'][$cf_name] ) && ! in_array( $cf_name, $default_fields ) ){
                    $fields[$cf_id] = $data['custom_fields'][$cf_name];
                }
            }
        }

        //Suscribir nuevo usuario
        if( $this->is_mailpoet3 ){
            if( method_exists('\MailPoet\Settings\SettingsController', 'getInstance') ){
                $settings = \MailPoet\Settings\SettingsController::getInstance();
            } else {
                $settings = new \MailPoet\Settings\SettingsController();
            }
            $double_optin = (bool)$settings->get( 'signup_confirmation.enabled' );

            $options = array(
                'send_confirmation_email' => $double_optin, // default: true
                'schedule_welcome_email' => $double_optin,// default: true
            );
            $subscriber = $this->subscriber_exists( $params['email'] );
            $subscriber_id = $subscriber['id'];

            //Suscriptor ya existe
            if( $subscriber_id ){
                //Verificamos si debemos actualizar sus datos
                $overwrite = isset( $data['overwrite'] ) && $data['overwrite'] == 'on' ? true : false;
                if( $overwrite ){
                    //la API de Mailpoet 3 no permite actualizar los datos
                }
                $this->error = $this->messages['subscriber_exists'];
                if( $subscriber['status'] == 'unconfirmed' ){
                    $this->error = $this->error . '. Check your email and confirm your subscription.';
                }
                return false;
            }

            //Si el suscriptor no existe, entonces lo creamos
            try{
                $params = array_merge( $params, $fields );
                $lists = array_map( 'intval', array( $this->list_id ) );
                $subscriber = $this->mailpoet3Api->addSubscriber( $params, $lists, $options );
                $subscriber_id = ! empty( $subscriber['id'] ) ? $subscriber['id'] : 0;
            } catch( \Exception $e ){
                $this->error = $e->getMessage();
                if( $e->getCode() == \MailPoet\API\MP\v1\APIException::CONFIRMATION_FAILED_TO_SEND ){
                    //return true;//Si se suscribió perto no se envió el email de confirmación
                }
            }

            if( $subscriber_id > 0 ){
                return true;
            }
            return false;

        } else{
            //Mailpoet v2 support

            $model_user = \WYSIJA::get( 'user', 'model' );
            $subscriber = $this->subscriber_exists( $params['email'] );

            //Status
            $model_config = \WYSIJA::get( 'config', 'model' );
            $double_optin = $model_config->getValue( 'confirm_dbleoptin' );
            $params['status'] = ! $double_optin;

            $subscriber_id = 0;
            //Si el suscriptor ya existe
            if( isset( $subscriber['id'] ) ){
                //Verificamos si debemos actualizar sus datos
                $overwrite = isset( $data['overwrite'] ) && $data['overwrite'] == 'on' ? true : false;
                if( $overwrite ){
                    $subscriber_id = $subscriber['id'];
                    //Si requiere confirmación
                    if( $double_optin ){
                        //Establecer su estado actual
                        $params['status'] = $subscriber['status'];
                    } else{
                        //Si no requiere confirmación, cambiar el estado a 1=confirmado
                        $params['status'] = 1;
                    }
                    $model_user->reset();
                    $model_user->update( $params, array( 'user_id' => $subscriber_id ) );
                }
            } else{
                //Si no existe lo creamos
                $subscriber_id = \WYSIJA::get( 'user', 'helper' )->addSubscriber( array(
                    'user' => $params,
                    'user_list' => array( 'list_ids' => array( $this->lis_id ) )
                ) );
            }

            if( $subscriber_id > 0 ){
                //Agregar campos personalizados
                \WJ_FieldHandler::handle_all( $fields, $subscriber_id );
                return true;
            }
            return false;
        }

        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos por defecto
    |---------------------------------------------------------------------------------------------------
    */
    public function get_default_fields(){
        return array(
            'email',
            $this->get_first_name(),
            $this->get_last_name()
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        if( $this->is_mailpoet3 ){
            $fields = $this->mailpoet3Api->getSubscriberFields();
        } else{
            $fields = \WJ_Field::get_all();
        }

        if( ! isset( $fields ) ){
            return;
        }
        foreach( $fields as $field ){
            if( $this->is_mailpoet3 ){
                if( isset( $field['id'] ) ){
                    $items[$field['id']] = $field['name'];
                }
            } else{
                if( isset( $field->id ) ){
                    $items['cf_' . $field->id] = $field->name;
                }
            }
        }
        return $items;
    }
}

