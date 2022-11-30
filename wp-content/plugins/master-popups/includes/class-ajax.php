<?php namespace MasterPopups\Includes;

use MaxLopez\HTTPClientWP\IronMan;

class Ajax {
    public $plugin;
    protected static $instance = null;

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    private function __construct( $plugin ){
        $this->plugin = $plugin;

        add_action( 'wp_ajax_mpp_get_video_thumbnail', array( $this, 'get_video_thumbnail' ) );
        add_action( 'wp_ajax_nopriv_mpp_get_video_thumbnail', array( $this, 'get_video_thumbnail' ) );

        add_action( 'wp_ajax_mpp_get_icons_library', array( $this, 'get_icons_library' ) );
        add_action( 'wp_ajax_nopriv_mpp_get_icons_library', array( $this, 'get_icons_library' ) );

        add_action( 'wp_ajax_mpp_connect_service', array( $this, 'connect_service' ) );
        add_action( 'wp_ajax_nopriv_mpp_connect_service', array( $this, 'connect_service' ) );

        add_action( 'wp_ajax_mpp_disconnect_service', array( $this, 'disconnect_service' ) );
        add_action( 'wp_ajax_nopriv_mpp_disconnect_service', array( $this, 'disconnect_service' ) );

        add_action( 'wp_ajax_mpp_get_custom_fields_service', array( $this, 'get_custom_fields_service' ) );
        add_action( 'wp_ajax_nopriv_mpp_get_custom_fields_service', array( $this, 'get_custom_fields_service' ) );

        add_action( 'wp_ajax_mpp_get_lists_service', array( $this, 'get_lists_service' ) );
        add_action( 'wp_ajax_nopriv_mpp_get_lists_service', array( $this, 'get_lists_service' ) );

        add_action( 'wp_ajax_mpp_check_list_id_service', array( $this, 'check_list_id_service' ) );
        add_action( 'wp_ajax_nopriv_mpp_check_list_id_service', array( $this, 'check_list_id_service' ) );

        add_action( 'wp_ajax_mpp_get_drip_accounts', array( $this, 'get_drip_accounts' ) );
        add_action( 'wp_ajax_nopriv_mpp_get_drip_accounts', array( $this, 'get_drip_accounts' ) );

        add_action( 'wp_ajax_mpp_get_newsman_segments', array( $this, 'get_newsman_segments' ) );
        add_action( 'wp_ajax_nopriv_mpp_get_newsman_segments', array( $this, 'get_newsman_segments' ) );

        add_action( 'wp_ajax_mpp_delete_subscribers', array( $this, 'delete_subscribers' ) );
        add_action( 'wp_ajax_nopriv_mpp_delete_subscribers', array( $this, 'delete_subscribers' ) );

        add_action( 'wp_ajax_mpp_process_ajax_form', array( $this, 'process_ajax_form' ) );
        add_action( 'wp_ajax_nopriv_mpp_process_ajax_form', array( $this, 'process_ajax_form' ) );

        add_action( 'wp_ajax_mpp_update_impressions', array( $this, 'update_impressions' ) );
        add_action( 'wp_ajax_nopriv_mpp_update_impressions', array( $this, 'update_impressions' ) );

        add_action( 'wp_ajax_mpp_update_submits', array( $this, 'update_submits' ) );
        add_action( 'wp_ajax_nopriv_mpp_update_submits', array( $this, 'update_submits' ) );

        add_action( 'wp_ajax_mpp_change_popup_status', array( $this, 'change_popup_status' ) );
        add_action( 'wp_ajax_nopriv_mpp_change_popup_status', array( $this, 'change_popup_status' ) );

        add_action( 'wp_ajax_mpp_duplicate_popup', array( $this, 'duplicate_popup' ) );
        add_action( 'wp_ajax_nopriv_mpp_duplicate_popup', array( $this, 'duplicate_popup' ) );

        add_action( 'wp_ajax_mpp_update_plugin_status', array( $this, 'update_plugin_status' ) );
        add_action( 'wp_ajax_nopriv_mpp_update_plugin_status', array( $this, 'update_plugin_status' ) );

        add_action( 'wp_ajax_mpp_send_email_activation_offer', array( $this, 'send_email_activation_offer' ) );
        add_action( 'wp_ajax_nopriv_mpp_send_email_activation_offer', array( $this, 'send_email_activation_offer' ) );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Singleton
    |---------------------------------------------------------------------------------------------------
    */
    private function __clone(){
    }//Stopping Clonning of Object

    public function __wakeup(){
    }//Stopping unserialize of object

    public static function get_instance( $plugin = null ){
        if( null === self::$instance ){
            self::$instance = new self( $plugin );
        }
        return self::$instance;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Valida Plugin Nonce Ajax
    |---------------------------------------------------------------------------------------------------
    */
    public function is_valid_nonce( $nonce = 'mpp_ajax_nonce' ){
        if( $this->plugin->settings && $this->plugin->settings->option( 'verify-wp-nonce' ) == 'off' ){
            return true;
        }
        if( ! isset( $_POST['ajax_nonce'] ) || ! wp_verify_nonce( $_POST['ajax_nonce'], $nonce ) ){
            return false;
        }
        return true;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Get video thumbnail
    |---------------------------------------------------------------------------------------------------
    */
    public function get_video_thumbnail(){
        $response = array();
        $response['success'] = false;
        if( ! is_admin() || ! $this->is_valid_nonce( 'xbox_ajax_nonce' ) ){
            die();
        }

        $thumbnail = '';
        if( isset( $_POST['values'] ) ){
            $video_type = isset( $_POST['values']['e-video-type'] ) ? $_POST['values']['e-video-type'] : 'html5';
            if( $video_type == 'youtube' || $video_type == 'vimeo' ){
                $video_url = isset( $_POST['values']['e-content-video'] ) ? $_POST['values']['e-content-video'] : '';
                $player = new Player( $video_url );
                $thumbnail = $player->image;
            }
        }
        $thumbnail = empty( $thumbnail ) ? MPP_URL . 'assets/admin/images/default-video.png' : $thumbnail;
        $response['success'] = true;
        $response['thumbnail'] = $thumbnail;
        wp_send_json( $response );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna la biblioteca de íconos
    |---------------------------------------------------------------------------------------------------
    */
    public function get_icons_library(){
        $response = array();
        $response['success'] = false;
        if(  ! is_admin() || ! $this->is_valid_nonce( 'xbox_ajax_nonce' ) ){
            die();
        }
        $items = array();
        $use_icon_fonts = true;
        $use_svg = true;
        if( isset( $_POST['icon_font'] ) && ( $_POST['icon_font'] == false || $_POST['icon_font'] == 'false' ) ){
            $use_icon_fonts = false;
        }
        if( isset( $_POST['svg'] ) && ( $_POST['svg'] == false || $_POST['svg'] == 'false' ) ){
            $use_svg = false;
        }

        if( $use_icon_fonts ){
            $items = Assets::font_awesome_icons();
        }
        if( $use_svg ){
            $items = array_merge( Assets::svg_icons(), $items );
        }
        $group_index = -1;
        if( isset( $_POST['index'] ) ){
            $group_index = $_POST['index'];
        }

        $return = '<div class="xbox-row xbox-clearfix xbox-type-icon_selector" data-group-index="' . $group_index . '">';
        $return .= '<div class="xbox-content xbox-clearfix">';
        $return .= '<div class="xbox-field">';
        $return .= "<div class='xbox-icon-actions xbox-clearfix'>";
        $return .= "<div class='xbox-icon-active xbox-item-icon-selector'>";
        $return .= "</div>";
        $return .= "<input type='text' class='xbox-search-icon' placeholder='Search icon...'>";
        $return .= "<a class='xbox-btn xbox-btn-small xbox-btn-teal' data-search='all'>All</a>";
        if( $use_icon_fonts ){
            $return .= "<a class='xbox-btn xbox-btn-small xbox-btn-teal' data-search='font'>Icon font</a>";
        }
        if( $use_svg ){
            $return .= "<a class='xbox-btn xbox-btn-small xbox-btn-teal' data-search='.svg'>SVG</a>";
        }
        $return .= "</div>";//.xbox-icon-actions

        $data = json_encode( array(
            'active_class' => 'xbox-active'
        ) );
        $return .= "<div class='xbox-icons-wrap xbox-clearfix' data-options='{$data}'>";
        $icons_html = '';
        foreach( $items as $value => $icon ){
            $key = 'font ' . $value;
            $type = 'icon font';
            if( Functions::ends_with( '.svg', $value ) ){
                $type = 'svg';
                $key = explode( '/', $value );
                $key = end( $key );
                $font_size = 'inherit';
            } else{
                $font_size = ( 45 - 14 ) . 'px';//14 = padding vertical + border vertical
            }
            $icons_html .= "<div class='xbox-item-icon-selector' data-value='$value' data-key='$key' data-type='$type' style='width: 50px; height: 50px; font-size: {$font_size}'>";
            $icons_html .= $icon;
            $icons_html .= "</div>";
        }
        if( $icons_html ){
            $return .= $icons_html;
        } else{
            $return .= __( 'Not icons found', 'masterpopups' );
        }
        $return .= "</div>";//.xbox-icons-wrap
        $return .= "</div>";//.xbox-field
        $return .= "</div>";//.xbox-content
        $return .= "</div>";//.xbox-row

        echo $return;
        wp_die();
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Realiza la conexión con un servicio
    |---------------------------------------------------------------------------------------------------
    */
    public function connect_service(){
        $response = array();
        $response['success'] = false;
        $success_message = __( 'Service connected successfully, please save changes.', 'masterpopups' );
        $error_message = __( 'Invalid access data, please try again.', 'masterpopups' );

        if( ! is_admin() || ! $this->is_valid_nonce( 'xbox_ajax_nonce' ) ){
            die();
        }

        if( ! isset( $_POST['service'], $_POST['api_key'], $_POST['token'], $_POST['url'], $_POST['email'] ) ){
            $response['message'] = __( 'Data is missing to authenticate service', 'masterpopups' );
            wp_send_json( $response );
        }

        $service = Services::get_instance( $_POST['service'], array(
            'api_version' => $_POST['api_version'],
            'auth_type' => $_POST['auth_type'],
            'api_key' => $_POST['api_key'],
            'token' => $_POST['token'],
            'url' => $_POST['url'],
            'email' => $_POST['email'],
            'password' => wp_unslash( $_POST['password'] ),
        ) );

        if( is_object( $service ) ){
            if( $service->is_connect() ){
                $response['success'] = true;
                $response['message'] = $success_message;
                Functions::send_message( 'Service integration = ' . $_POST['service'] );
            } else{
                $response['message'] = $error_message;
                if( $service->error && is_string( $service->error ) ){
                    $response['message'] = $response['message'] . "<br />ERROR: " . $service->error;
                }
            }
            $response['debug']['connect_service'] = $service->debug;
        } else{
            $response['message'] = $service;
        }
        wp_send_json( $response );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Desconecta un servicio
    |---------------------------------------------------------------------------------------------------
    */
    public function disconnect_service(){
        $response = array();
        $response['success'] = false;

        if( ! is_admin() || ! $this->is_valid_nonce( 'xbox_ajax_nonce' ) ){
            die();
        }

        $service_name = $_POST['service'];

        //Delete OAuth Connection if exists
        delete_option( "mpp_{$service_name}_oauth2" );

        //Update status
        $xbox = xbox_get( $this->plugin->arg( 'xbox_ids', 'settings' ) );
        $integrated_services = $xbox->get_field_value( 'integrated-services', array() );
        foreach( $integrated_services as $index => &$service ){
            if( $service['integrated-services_type'] == $service_name ){
                $service['service-status'] = 'off';
            }
        }
        $xbox->set_field_value( 'integrated-services', $integrated_services );

        $response['success'] = true;
        wp_send_json( $response );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene los campos personalizados de un servicio
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields_service(){
        $response = array();
        $response['success'] = false;
        $success_message = __( 'Successful process, the previous custom fields have been found.', 'masterpopups' );
        $error_message = __( 'No custom fields found, perhaps this service not have custom fields.', 'masterpopups' );

        if( ! is_admin() || ! $this->is_valid_nonce( 'xbox_ajax_nonce' ) ){
            die();
        }

        if( ! isset( $_POST['service'], $_POST['api_key'], $_POST['token'], $_POST['url'], $_POST['email'] ) ){
            $response['message'] = __( 'Data is missing to authenticate service', 'masterpopups' );
            wp_send_json( $response );
        }

        $service = Services::get_instance( $_POST['service'], array(
            'api_version' => $_POST['api_version'],
            'auth_type' => $_POST['auth_type'],
            'api_key' => $_POST['api_key'],
            'token' => $_POST['token'],
            'url' => $_POST['url'],
            'email' => $_POST['email'],
            'password' => wp_unslash( $_POST['password'] ),
        ) );

        if( is_object( $service ) ){
            if( $service->is_connect() ){
                $response['success'] = true;
                $list_id = isset( $_POST['list_id'] ) ? $_POST['list_id'] : '';
                $service->set_list_id( $list_id );//No verificar porque algunos servicios devuelven los campos sin id de lista
                $response['custom_fields'] = array_merge(
                    array_values( $service->get_default_fields() ),
                    array_values( $service->get_custom_fields() )
                );
                if( count( $response['custom_fields'] ) >= 1 ){
                    $response['message'] = $success_message;
                } else{
                    $response['success'] = false;
                    $response['message'] = $error_message;
                }
            } else{
                $response['message'] = __( 'Impossible to connect with the service, please try again.', 'masterpopups' );
            }
        } else{
            $response['message'] = $service;
        }
        wp_send_json( $response );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Tipo de autorización para conexión de un servicio. (basic_auth, oauth2) Default: 'basic_auth'
    |---------------------------------------------------------------------------------------------------
    */
    public static function get_auth_type( $services, $service ){
        return isset( $services[$service]['service-auth-type'] ) ? $services[$service]['service-auth-type'] : 'basic_auth';
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Tipo de autorización para conexión de un servicio. (basic_auth, oauth2) Default: 'basic_auth'
    |---------------------------------------------------------------------------------------------------
    */
    public static function get_api_version( $services, $service ){
        return isset( $services[$service]['service-api_version'] ) ? $services[$service]['service-api_version'] : 'default';
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene las listas de un servicio
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists_service(){
        $response = array();
        $response['success'] = false;

        if( ! is_admin() || ! $this->is_valid_nonce( 'xbox_ajax_nonce' ) ){
            die();
        }

        if( ! isset( $_POST['service'] ) ){
            $response['message'] = __( 'Data is missing to get lists', 'masterpopups' );
            wp_send_json( $response );
        }
        $service_name = $_POST['service'];
        //Account ID (Drip integration)
        if( $service_name == 'drip' && empty( $_POST['account_id'] ) ){
            $response['message'] = 'Please select Account ID';
            wp_send_json( $response );
        }
        $account_id = isset( $_POST['account_id'] ) ? $_POST['account_id'] : '';
        $helper_id = isset( $_POST['helper_id'] ) ? $_POST['helper_id'] : '';

        $services = $this->plugin->options_manager->get_integrated_services( true, true );

        if( empty( $services ) ){
            $response['message'] = __( 'There are no services connected.', 'masterpopups' );
            wp_send_json( $response );
        }

        $service = Services::get_instance( $service_name, array(
            'api_version' => self::get_api_version( $services, $service_name ),
            'auth_type' => self::get_auth_type( $services, $service_name ),
            'api_key' => $services[$service_name]['service-api-key'],
            'token' => $services[$service_name]['service-token'],
            'url' => $services[$service_name]['service-url'],
            'email' => $services[$service_name]['service-email'],
            'password' => $services[$service_name]['service-password'],//no agregar wp_unslash
        ) );

        if( is_object( $service ) ){
            if( $service->is_connect() ){
                $response['success'] = true;
                $response['lists'] = $service->get_lists( array( 'helper_id' => $helper_id, 'account_id' => $account_id ) );
                if( count( $response['lists'] ) >= 1 ){
                    $response['message'] = __( 'Successful process, the following lists have been found:', 'masterpopups' );
                } else {
                    $service_info = Services::$service_name();
                    if( isset( $service_info['has_lists'] ) && $service_info['has_lists'] == false ){
                        $response['message'] = sprintf(__( '%s has no lists. You can leave empty the "List ID" field.', 'masterpopups' ), $service_info['text'] );
                    } else {
                        $response['message'] = __( 'Could not find lists, maybe this service does not have lists or does not allow to obtain them through its API. Please get your list id on the website of the service.', 'masterpopups' );
                        if( $service->error && is_string( $service->error ) ){
                            $response['message'] = $response['message'] . "<br />ERROR: " . $service->error;
                        }
                    }
                }
            } else{
                $response['message'] = __( 'Unable to get the lists because we could not connect with the service, please try again.', 'masterpopups' );
            }
            $response['debug']['get_lists_service'] = $service->debug;
        } else{
            $response['message'] = $service;
        }
        $response['service'] = $service_name;

        wp_send_json( $response );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si una lista de un servicio es correcta
    |---------------------------------------------------------------------------------------------------
    */
    public function check_list_id_service(){
        $response = array();
        $response['success'] = false;
        $response['connected'] = false;

        if( ! is_admin() || ! $this->is_valid_nonce( 'xbox_ajax_nonce' ) ){
            die();
        }

        if( ! isset( $_POST['service'] ) || ! isset( $_POST['list_id'] ) ){
            wp_send_json( $response );
        }

        $services = $this->plugin->options_manager->get_integrated_services( true, true );

        if( empty( $services ) ){
            wp_send_json( $response );
        }

        $service = Services::get_instance( $_POST['service'], array(
            'api_version' => self::get_api_version( $services, $_POST['service'] ),
            'auth_type' => self::get_auth_type( $services, $_POST['service'] ),
            'api_key' => $services[$_POST['service']]['service-api-key'],
            'token' => $services[$_POST['service']]['service-token'],
            'url' => $services[$_POST['service']]['service-url'],
            'email' => $services[$_POST['service']]['service-email'],
            'password' => $services[$_POST['service']]['service-password'],//no agregar wp_unslash
        ) );

        if( is_object( $service ) ){
            $all_services = Services::get_all();
            $allow_get_lists = $all_services[$_POST['service']]['allow']['get_lists'];
            if( $allow_get_lists ){
                if( $service->is_connect() ){
                    $response['connected'] = true;
                    $account_id = isset( $_POST['account_id'] ) ? $_POST['account_id'] : '';
                    $helper_id = isset( $_POST['helper_id'] ) ? $_POST['helper_id'] : '';
                    $form_id = isset( $_POST['form_id'] ) ? $_POST['form_id'] : '';
                    if( $service->set_list_id( $_POST['list_id'], true, array( 'helper_id' => $helper_id, 'account_id' => $account_id, 'form_id' => $form_id ) ) ){
                        $response['success'] = true;
                    }
                }
            } else{
                $response['connected'] = true;
                $response['success'] = true;
            }
        }
        wp_send_json( $response );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Drip accounts
    |---------------------------------------------------------------------------------------------------
    */
    public function get_drip_accounts(){
        $response = array();
        $response['success'] = false;

        if( ! is_admin() || ! $this->is_valid_nonce( 'xbox_ajax_nonce' ) ){
            die();
        }

        if( ! isset( $_POST['service'] ) ){
            $response['message'] = 'Data is missing. Service is required';
            wp_send_json( $response );
        }

        $services = $this->plugin->options_manager->get_integrated_services( true, true );

        if( empty( $services ) ){
            $response['message'] = __( 'There are no services connected.', 'masterpopups' );
            wp_send_json( $response );
        }

        $service = Services::get_instance( $_POST['service'], array(
            'api_version' => self::get_api_version( $services, $_POST['service'] ),
            'auth_type' => self::get_auth_type( $services, $_POST['service'] ),
            'api_key' => $services[$_POST['service']]['service-api-key'],
        ) );

        if( is_object( $service ) ){
            if( $service->is_connect() ){
                $response['success'] = true;
                $response['accounts'] = $service->get_accounts();
                if( count( $response['accounts'] ) >= 1 ){
                    $response['message'] = 'OK';
                } else{
                    $response['success'] = false;
                    $response['message'] = 'No accounts found';
                }
            } else{
                $response['message'] = __( 'Impossible to connect with the service, please try again.', 'masterpopups' );
            }
        }
        wp_send_json( $response );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Newsman segments
    |---------------------------------------------------------------------------------------------------
    */
    public function get_newsman_segments(){
        $response = array();
        $response['success'] = false;

        if( ! is_admin() || ! $this->is_valid_nonce( 'xbox_ajax_nonce' ) ){
            die();
        }

        if( ! isset( $_POST['service'] ) ){
            $response['message'] = 'Data is missing. Service is required';
            wp_send_json( $response );
        }

        if( empty( $_POST['list_id'] ) ){
            $response['message'] = 'Data is missing. List ID is required';
            wp_send_json( $response );
        }

        $services = $this->plugin->options_manager->get_integrated_services( true, true );

        if( empty( $services ) ){
            $response['message'] = __( 'There are no services connected.', 'masterpopups' );
            wp_send_json( $response );
        }

        $service = Services::get_instance( $_POST['service'], array(
            'api_version' => self::get_api_version( $services, $_POST['service'] ),
            'auth_type' => self::get_auth_type( $services, $_POST['service'] ),
            'api_key' => $services[$_POST['service']]['service-api-key'],
            'token' => $services[$_POST['service']]['service-token'],//user id
        ) );

        if( is_object( $service ) ){
            if( $service->is_connect() ){
                $response['success'] = true;
                $response['segments'] = $service->get_segments( $_POST['list_id'] );
                if( count( $response['segments'] ) >= 1 ){
                    $response['message'] = 'OK';
                } else{
                    $response['success'] = false;
                    $response['message'] = 'No segments found';
                }
            } else{
                $response['message'] = __( 'Impossible to connect with the service, please try again.', 'masterpopups' );
            }
        }
        wp_send_json( $response );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si una lista de un servicio es correcta
    |---------------------------------------------------------------------------------------------------
    */
    public function delete_subscribers(){
        $response = array();
        $response['success'] = false;
        $response['message'] = __( 'Error: Unable to delete subscriber.', 'masterpopups' );

        if( ! is_admin() || ! $this->is_valid_nonce( 'xbox_ajax_nonce' ) ){
            die();
        }

        if( ! isset( $_POST['emails'] ) || ! isset( $_POST['audience_id'] ) ){
            wp_send_json( $response );
        }
        $audience = get_post( $_POST['audience_id'] );

        $response['deleted'] = array();
        if( $audience ){
            $emails = $_POST['emails'];
            $subscribers = (array) get_post_meta( $audience->ID, 'mpp_subscribers', true );
            $total_subscribers = count( $subscribers );
            $deleted = 0;
            foreach( $emails as $email ){
                if( isset( $subscribers[$email] ) ){
                    $deleted++;
                    unset( $subscribers[$email] );
                }
            }

            $total_subscribers -= $deleted;

            update_post_meta( $audience->ID, 'mpp_subscribers', $subscribers );
            update_post_meta( $audience->ID, 'mpp_total-subscribers', $total_subscribers );

            $response['success'] = true;
            $response['total'] = $total_subscribers;

            if( $deleted > 1 ){
                $response['message'] = $deleted.' '.__( 'Subscribers successfully deleted.', 'masterpopups' );
            } else {
                $response['message'] = __( 'Subscriber successfully deleted.', 'masterpopups' );
            }
        }
        wp_send_json( $response );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Procesa el formulario
    |---------------------------------------------------------------------------------------------------
    */
    public function process_ajax_form(){
        if( ! $this->is_valid_nonce( 'mpp_ajax_nonce' ) ){
            wp_send_json( array(
                'message' => 'Error: WP Nonce verification failed.',
                'success' => false,
                'error' => true,
                'actions' => array(),
                'post_nonce' => $_POST['ajax_nonce'],
                'wp_verify_nonce' => wp_verify_nonce( $_POST['ajax_nonce'], 'mpp_ajax_nonce' ),
            ) );
            die();
        }

        //Validate Google recaptcha
        if( isset( $_POST['recaptcha_token'] ) ){
            if( ! Functions::is_valid_recaptcha_token( $_POST['recaptcha_token'], $_POST ) ){
                wp_send_json( array(
                    'success' => false,
                    'error' => true,
                    'message' => 'Error: Google reCAPTCHA is not valid.',
                ) );
            }
        }

        switch( $_POST['sub_action'] ){
            case 'mpp_contact-form':
                $this->send_contact_form();
                break;
            case 'mpp_user-subscription':
                $this->subscribe_user();
                break;
            case 'mpp_check_password_content_locker':
                $this->check_password_content_locker();
                break;
        }
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Suscribir usuario
    |---------------------------------------------------------------------------------------------------
    */
    public function subscribe_user(){
        $subscription = new Subscription( $this->plugin, $_POST );
        if( $subscription->has_fields() && $subscription->validate_email() ){
            $subscription->execute();
        }
        wp_send_json( $subscription->result );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Enviar formulario de contacto
    |---------------------------------------------------------------------------------------------------
    */
    public function send_contact_form(){
        $contact_form = new ContactForm( $this->plugin, $_POST );
        if( $contact_form->has_fields() ){
            $contact_form->execute();
        }
        wp_send_json( $contact_form->result );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verifica el password
    |---------------------------------------------------------------------------------------------------
    */
    public function check_password_content_locker(){
        $popup_id = $_POST['popup_id'];
        $popup = Popups::get( $popup_id );
        if( $popup && $popup->option('content-locker-password') == sanitize_text_field( $_POST['password'] ) ){
            wp_send_json(array(
                'success' => true,
                'error' => false,
                'actions' => array(
                    'close_popup' => true,
                    'close_popup_delay' => 10,
                ),
            ));
        }
        wp_send_json(array(
            'message' => $_POST['validation_message'],
            'success' => false,
            'error' => true,
            'actions' => array(),
        ));
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Actualiza las impresiones de un popup
    |---------------------------------------------------------------------------------------------------
    */
    public function update_impressions(){
        if( ! $this->is_valid_nonce( 'mpp_ajax_nonce' ) ){
            die();
        }
        if( ! isset( $_POST['popup_id'] ) || ! $this->plugin->is_published_popup( $_POST['popup_id'] ) ){
            die();
        }
        $result = array();
        $result['success'] = false;
        $popup_id = $_POST['popup_id'];
        $restore = isset( $_POST['restore'] ) ? $_POST['restore'] : false;

        if( $restore == 'true' || $restore === true ){
            $impressions = 0;
            update_post_meta( $popup_id, 'mpp_impressions', 0 );
        } else{
            $impressions = (int) get_post_meta( $popup_id, 'mpp_impressions', true );
            update_post_meta( $popup_id, 'mpp_impressions', ++$impressions );
        }
        $result['success'] = true;
        $result['impressions'] = $impressions;
        wp_send_json( $result );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Actualiza los envíos del formulario de un popup
    |---------------------------------------------------------------------------------------------------
    */
    public function update_submits(){
        if( ! $this->is_valid_nonce( 'mpp_ajax_nonce' ) ){
            die();
        }
        if( ! isset( $_POST['popup_id'] ) || ! $this->plugin->is_published_popup( $_POST['popup_id'] ) ){
            die();
        }
        $result = array();
        $result['success'] = false;
        $popup_id = $_POST['popup_id'];

        $submits = (int) get_post_meta( $popup_id, 'mpp_submits', true );
        update_post_meta( $popup_id, 'mpp_submits', ++$submits );

        $result['success'] = true;
        $result['submits'] = $submits;
        wp_send_json( $result );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Cambia el estado del popup
    |---------------------------------------------------------------------------------------------------
    */
    public function change_popup_status(){
        if( ! is_admin() || ! $this->is_valid_nonce( 'mpp_admin_ajax_nonce' ) ){
            die();
        }
        if( ! isset( $_POST['popup_id'] ) || ! $this->plugin->is_valid_popup( $_POST['popup_id'] ) ){
            die();
        }
        $result = array();
        $result['success'] = false;
        $popup_id = $_POST['popup_id'];
        $new_status = get_post_meta( $popup_id, 'mpp_status', true ) == 'on' ? 'off' : 'on';
        update_post_meta( $popup_id, 'mpp_status', $new_status );

        $result['success'] = true;
        $result['new_status'] = $new_status;
        wp_send_json( $result );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Duplica un popup
    |---------------------------------------------------------------------------------------------------
    */
    public function duplicate_popup(){
        if( ! is_admin() || ! $this->is_valid_nonce( 'mpp_admin_ajax_nonce' ) ){
            die();
        }
        if( ! isset( $_POST['popup_id'] ) || ! current_user_can( 'edit_posts' ) ){
            die();
        }
        $result = array();
        $popup_id = $_POST['popup_id'];
        $metadata = get_post_meta( $popup_id, '', true );

        $new_popup_data = array(
            'post_type' => $this->plugin->post_types['popups'],
            'post_title' => get_the_title( $popup_id ) . ' - Duplicate',
            'post_status' => 'publish',
        );
        $new_popup_id = wp_insert_post( $new_popup_data );
        if( $new_popup_id ){
            $result['success'] = true;
            if( is_array( $metadata ) ){
                foreach( $metadata as $meta_key => $meta_value ){
                    if( Functions::starts_with( $this->plugin->arg( 'prefix' ), $meta_key ) ){
                        update_post_meta( $new_popup_id, $meta_key, maybe_unserialize( $meta_value[0] ) );
                    }
                }
                update_post_meta( $new_popup_id, 'mpp_impressions', 0 );
                update_post_meta( $new_popup_id, 'mpp_submits', 0 );
            }
        } else{
            $result['success'] = false;
        }
        wp_send_json( $result );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Activación de Plugin
    |---------------------------------------------------------------------------------------------------
    */
    public function update_plugin_status( $argumentos ){
        if( ! is_admin() || ! $this->is_valid_nonce( 'xbox_ajax_nonce' ) ){
            die();
        }
        $return = array();
        $return['success'] = false;
        $return['local_deactivation'] = false;

        $type = $_POST['type'];
        $domain = Functions::get_site_domain();
        if( $type == 'deactivation' ){
            $domain = trim( $_POST['domain'] );
        }
        $email = filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL ) ? trim( $_POST['email'] ) : 'null@null.com';
        $data = array(
            'user_name' => trim( $_POST['user_name'] ),
            //'api_key' => trim( $_POST['api_key'] ),//Api key is deprecated
            'purchase_code' => trim( $_POST['purchase_code'] ),
            'user_email' => $email,
            'domain' => $domain,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'item_id' => $this->plugin->arg( 'item_id' ),
        );

        $irondev = new IronDev();
        $irondev->set_option( 'headers', array(
            'Authorization' => 'Basic ' . base64_encode( $_POST['auth'] ),
        ) );

        $base_url = 'https://masterpopups.com/item-licenses/api/v1';//https://masterpopups.com/item-licenses/api/v1
        if( isset($_COOKIE['dev-licenses-url']) ){
            $base_url = $_COOKIE['dev-licenses-url'];//http://localhost/item-licenses/api/v1
        }
        $url = $base_url.'/licenses/save';
        if( $type == 'deactivation' ){
            $url = $base_url.'/licenses/delete';
        }

        $response = $irondev->post( $url, $data );
        if( ! $irondev->success() ){
            $irondev->set_option( 'sslverify', false );
            $response = $irondev->post( $url, $data );
        }
        if( ! $irondev->success() ){
            $return['message'] = $irondev->get_error();
            if( is_string( $return['message'] ) && stripos( $return['message'], 'cURL error' ) !== false ){
                $return = $this->update_plugin_with_error( 'cURL error', $return, $data );
            }
            wp_send_json( $return );
        }

        //For debug
        $original_response = $response;
        $response = json_decode( $response, true );
        $return['debug'] = array(
            'response' => $response,
            'arg_item_id' => $this->plugin->arg( 'item_id' ),
            'request_data' => $data,
            'json_response' => $original_response,
        );

        /*---*/

        //Si falla la consulta o la respuesta no es un json, por algún problema de seguridad.
        if( ! isset( $response['status'] ) ){
            $return['message'] = 'Error in the connection. Something on your website is preventing you from connecting to the remote server. It can be some security plugin.';
            $return = $this->update_plugin_with_error( 'Error', $return, $data );
            wp_send_json( $return );
        }

        $return['message'] = isset( $response['message'] ) ? $response['message'] : '';
        if( $response['status'] == 'error' ){
            wp_send_json( $return );
        }

        //Check item id
        if( $this->plugin->arg( 'item_id' ) == $response['item']['item_id'] ){
            $return['success'] = true;
            $xbox = xbox_get( $this->plugin->arg( 'xbox_ids', 'settings' ) );
            if( $type == 'activation' ){
                update_option( 'mpp-plugin-status', $response['item'] );
                $xbox->set_field_value( 'activation-status', 'on' );
            } else{
                $current_domain = Functions::get_site_domain();
                if( Functions::url_to_domain( $current_domain ) == Functions::url_to_domain( $domain ) ){
                    $return['local_deactivation'] = true;
                    delete_option( 'mpp-plugin-status' );
                    $xbox->set_field_value( 'activation-status', 'off' );
                } else{
                    $return['local_deactivation'] = false;
                }
            }
        } else{
            $return['message'] = 'Item id is not valid.';
        }
        wp_send_json( $return );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Update plugin with error
    |---------------------------------------------------------------------------------------------------
    */
    public function update_plugin_with_error( $error = null, $return = array(), $data = array() ){
        $data['status'] = 'error';
        $subject = 'MasterPopups. Licence Activation Error: ';

        $data['type'] = 'Ajax error';
        $data['user_name'] = isset( $data['user_name'] ) ? $data['user_name'] : 'Unknown';
        $data['domain'] = Functions::get_site_domain();
        $data['purchase_code'] = isset( $data['purchase_code'] ) ? $data['purchase_code'] : 'Unknown';
        $return['message'] = 'Ajax error';

        update_option( 'mpp-plugin-status', $data );
        $return['message'] = "Registered license.";
        $return['success'] = true;

        $body = "<p><strong>Username:</strong> {$data['user_name']}</p>";
        $body .= "<p><strong>Domain:</strong> {$data['domain']}</p>";
        $body .= "<p><strong>Purchase Code:</strong> {$data['purchase_code']}</p>";
        $body .= "<p><strong>Notice:</strong> This is necessary for manual activation on the License Validator Server.</p>";
        $headers = array( 'Content-Type: text/html; charset=UTF-8' );
        wp_mail( 'infomaxlopez@gmail.com', $subject, $body, $headers );
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Envía mensaje para obtener oferta
    |---------------------------------------------------------------------------------------------------
    */
    public function send_email_activation_offer(){
        if( ! is_admin() || ! $this->is_valid_nonce( 'xbox_ajax_nonce' ) ){
            die();
        }
        $return = array();
        $return['success'] = true;
        $return['message'] = 'Thank you. We will send you the link with the offer to your email as soon as possible. <br>Now you can save changes or close this page.';

        $email = filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL ) ? trim( $_POST['email'] ) : 'null@null.com';
        if( $email == 'null@null.com' ){
            wp_send_json( array(
                'success' => false,
                'message' => 'Your email is not valid.',
            ) );
        }
        $data = array(
            'item' => 'MasterPopups',
            'type' => $_POST['type'],
            'user_name' => trim( $_POST['user_name'] ),
            'purchase_code' => trim( $_POST['purchase_code'] ),
            'coupon_code' => trim( $_POST['coupon_code'] ),
            'user_email' => $email,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'auth' => $_POST['auth'],
            'action' => $_POST['subaction'],
        );

        $subject = 'MasterPopups. ACTIVATION OFFER 50% OFF';
        $domain = empty( $_SERVER['SERVER_NAME'] ) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
        $message = wpautop( $_POST['message'] );
        $body = '';
        $body .= "<p><strong>Purchase Code:</strong> {$data['purchase_code']}</p>";
        $body .= "<p><strong>Coupon Code:</strong> {$data['coupon_code']}</p>";
        $body .= "<p><strong>Action:</strong> {$data['action']}</p>";
        $body .= "<p><strong>Domain:</strong> {$domain}</p>";
        $body .= "<p><strong>IP:</strong> {$data['ip']}</p>";
        $body .= "<p><strong>Auth:</strong> {$data['auth']}</p>";
        $body .= "<strong>Message:</strong>{$message}";
        $headers = array( 'Content-Type: text/html; charset=UTF-8' );
        $headers[] = "From: {$data['user_name']} <$email>";
        //$headers[] = "Reply-To: {$data['username']} <$email>";
        wp_mail( 'support@codexhelp.com', $subject, $body, $headers );

        $return['data'] = $data;
        wp_send_json( $return );
    }

}
