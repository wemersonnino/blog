<?php namespace MasterPopups\Includes;

class Subscription extends FormSubmission {
    public $storage = 'master_popups';
    public $list = null;

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $plugin, $post_data = array(), $source = MPP_SOURCE_FORM_SUBMIT_POPUP ){
        parent::__construct( $plugin, $post_data, 'Subscription', $source );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Agrega la Lista
    |---------------------------------------------------------------------------------------------------
    */
    public function set_list( $list = null ){
        if( ! $list ){
            return false;
        }
        if( is_object( $list ) && $list instanceof Lista ){
            $this->list = $list;
        } else {
            $this->list = new Lista( array( 'id' => $list ) );
        }
        return true;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Realiza la suscripción
    |---------------------------------------------------------------------------------------------------
    */
    public function execute(){
        if( ! $this->list && $this->popup && $this->source == MPP_SOURCE_FORM_SUBMIT_POPUP ){
            $list_id = $this->popup->option( 'audience-list' );
            $this->set_list( $list_id );
        }

        if( ! $this->list || get_post_status( $this->list->ID ) != 'publish' ){
            $this->result['message'] = $this->cannot . __( '"Audience List" is empty or has been deleted.', 'masterpopups' );
            return $this->result;
        }

        //Datos adicionales
        $this->set_additional_data_to_save(array(
            'list_title' => $this->list->ID,
            'list_id' => $this->list->title
        ));

        //Suscribir usuarios
        $this->storage = $this->list->option( 'service' );
        if( $this->storage == 'master_popups' ){
            $success = $this->save_in_masterpopups();
        } else{
            //Renderizar los campos antes de suscribir y enviar los emails
            $this->set_render_fields();
            $success = $this->save_in_third_party_service();
        }

        $this->result['debug']['render_fields'] = $this->render_fields;

        if( $this->service != null ){
            $this->result['debug']['on_add_subscriber'] = $this->service->debug;
        }

        if( $success ){
            $this->actions_on_success();
        } else {
            $this->actions_on_error( $this->service );
        }
        return $this->result;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Suscribe un usuario en wordpress
    |---------------------------------------------------------------------------------------------------
    */
    public function save_in_masterpopups(){
        $this->service = null;
        $this->saved_data['email'] = $this->get_email();
        $this->saved_data['first_name'] = isset( $this->fields['first_name'] ) ? $this->fields['first_name']['value'] : '';
        $this->saved_data['last_name'] = isset( $this->fields['last_name'] ) ? $this->fields['last_name']['value'] : '';
        $this->saved_data['custom_fields'] = $this->custom_fields;

        $subscribers = $this->list->option( 'subscribers' );
        if( empty( $subscribers ) ){
            $subscribers = array();
        }
        $this->result['error'] = false;

        $action = 'created';
        $allow_data_update = $this->list->option( 'allow-data-update' );
        if( isset( $subscribers[$this->get_email()] ) && $allow_data_update == 'on' ){
            $action = 'updated';
        }

        //Renderizar los campos antes de suscribir y enviar los emails
        $this->set_render_fields();

        if( $allow_data_update == 'on' || ! isset( $subscribers[$this->get_email()] ) ){
            //Datos adicionales

            $this->saved_data['status'] = 'subscribed';
            $this->saved_data['action'] = $action;

            if( $action == 'updated' ){
                $old_data = (array) $subscribers[$this->get_email()];
                $this->saved_data = array_replace_recursive( $old_data, $this->saved_data );
            }

            $this->saved_data = apply_filters( 'masterpopups_subscription_data', $this->saved_data );
            $subscribers[$this->get_email()] = $this->saved_data;
            update_post_meta( $this->list->ID, $this->prefix . 'subscribers', $subscribers );

            if( $action == 'created' ){
                //Actualizar total de suscriptores
                $total_subscribers = (int) $this->list->option( 'total-subscribers', 0 );
                update_post_meta( $this->list->ID, $this->prefix . 'total-subscribers', ++$total_subscribers );
            }
            return true;
        }

        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Suscribe un usuario en otro servicio
    |---------------------------------------------------------------------------------------------------
    */
    public function save_in_third_party_service(){
        $services = $this->plugin->options_manager->get_integrated_services( true, true );
        if( empty( $services ) || ! isset( $services[$this->storage] ) ){
            $this->result['message'] = $this->cannot . __( 'The service is not yet connected.', 'masterpopups' );
            return false;
        }
        $service = Services::get_instance( $this->storage, array(
            'api_version' => Ajax::get_api_version( $services, $this->storage ),
            'auth_type' => Ajax::get_auth_type( $services, $this->storage ),
            'api_key' => $services[$this->storage]['service-api-key'],
            'token' => $services[$this->storage]['service-token'],
            'url' => $services[$this->storage]['service-url'],
            'email' => $services[$this->storage]['service-email'],
            'password' => $services[$this->storage]['service-password'],
        ), $this );
        if( ! is_object( $service ) ){
            $this->result['message'] = $service;
            return false;
        }
        if( ! $service->is_connect() ){
            $this->result['message'] = $this->cannot . __( 'We could not connect with the service. Try again.', 'masterpopups' );
            return false;
        }

        $this->service = $service;
        $list_id = $this->list->option( 'list-id');
        $helper_id = $this->list->option( 'helper-id' );
        $account_id = $this->list->option( 'account-id');
        $form_id = $this->list->option( 'form-id');
        $all_services = Services::get_all();
        $allow_get_lists = $all_services[$this->storage]['allow']['get_lists'];
        if( ! $this->service->set_list_id( $list_id, $allow_get_lists, array( 'helper_id' => $helper_id, 'account_id' => $account_id, 'form_id' => $form_id ) ) ){
            $this->result['message'] = $this->cannot . __( 'The list no longer exists in the chosen service.', 'masterpopups' );
            return false;
        }

        $data = array();
        $data['email'] = $this->get_email();
        $data['double-opt-in'] = $this->list->option( 'double-opt-in');
        $data['template-id'] = $this->list->option( 'template-id');
        $data['redirection-url'] = $this->list->option( 'redirection-url');
        $data['segment-id'] = $this->list->option( 'segment-id');
        $data['overwrite'] = $this->list->option( 'allow-data-update');
        $data['custom_fields'] = $this->custom_fields;
        $this->saved_data['custom_fields'] = $this->custom_fields;//Necesario para renderizar los campos
        $data['first_name'] = array(
            'name' => '',
            'value' => isset( $this->fields['first_name'] ) ? $this->fields['first_name']['value'] : '',
        );
        if( isset( $this->post_data['mpp_field_first_name'] ) && $this->post_data['mpp_field_first_name'] != 'field_first_name' ){
            $data['first_name']['name'] = $this->post_data['mpp_field_first_name'];
        }
        $data['last_name'] = array(
            'name' => '',
            'value' => isset( $this->fields['last_name'] ) ? $this->fields['last_name']['value'] : '',
        );
        if( isset( $this->post_data['mpp_field_last_name'] ) && $this->post_data['mpp_field_last_name'] != 'field_last_name' ){
            $data['last_name']['name'] = $this->post_data['mpp_field_last_name'];
        }

        $data = apply_filters( 'masterpopups_subscription_data_service', $data );

        $this->result['error'] = false;

        $success = false;
        if( $this->service && $this->service->add_subscriber( $this->get_email(), $data ) ){
            //Actualizar total de suscriptores
            $total_subscribers = (int) $this->list->option( 'total-subscribers', 0 );
            update_post_meta( $this->list->ID, $this->prefix . 'total-subscribers', ++$total_subscribers );

            $success = true;
        }
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Acciones cuando la suscripción fue exitosa
    |---------------------------------------------------------------------------------------------------
    */
    private function actions_on_success(){
        $this->result['success'] = true;
        if( $this->popup && $this->source == MPP_SOURCE_FORM_SUBMIT_POPUP ){
            $this->result['actions'] = $this->get_actions_on_success();
            $this->send_email_notification_to_admin();
            $this->send_email_notification_to_subscriber();
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Acciones cuando la suscripción tuvo algún error
    |---------------------------------------------------------------------------------------------------
    */
    private function actions_on_error( $service = null ){
        $this->result['success'] = false;
        if( $this->popup && $this->source == MPP_SOURCE_FORM_SUBMIT_POPUP ){
            $this->result['actions']['message'] = $this->popup->option( 'subscription-error-message' );
            if( $this->plugin->settings->option( 'attach-error-on-form-failed' ) === 'on' ){
                $error_message = $this->result['message'];
                $error_message .= ' ' . ( isset( $service->error ) ? $service->error : '' );
                $this->result['actions']['error'] = trim( $error_message );
            }
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Envía una notificación por email a un administrador después de la suscripción
    |---------------------------------------------------------------------------------------------------
    */
    public function send_email_notification_to_admin(){
        if( ! $this->popup || $this->popup->option( 'subscription-admin-notif' ) == 'off' ){
            return;
        }
        $from = $this->popup->option( 'subscription-admin-notif-from' );
        $to = $this->popup->option( 'subscription-admin-notif-to' );
        $cc = $this->popup->option( 'subscription-admin-notif-cc' );
        $subject = $this->popup->option( 'subscription-admin-notif-subject' );
        $message = $this->popup->option( 'subscription-admin-notif-message' );

        $this->send_email( $from, $to, $cc, $subject, $message, true, array() );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Envía una notificación por email al nuevo suscriptor después de la suscripción
    |---------------------------------------------------------------------------------------------------
    */
    public function send_email_notification_to_subscriber(){
        if( ! $this->popup || $this->popup->option( 'subscription-user-notif' ) == 'off' ){
            return;
        }
        $to = $this->get_email();
        $from = $this->popup->option( 'subscription-user-notif-from' );
        $subject = $this->popup->option( 'subscription-user-notif-subject' );
        $message = $this->popup->option( 'subscription-user-notif-message' );

        $this->send_email( $from, $to, null, $subject, $message, false, array() );
    }


}
