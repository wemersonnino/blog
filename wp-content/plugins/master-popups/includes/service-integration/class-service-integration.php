<?php namespace MasterPopups\Includes\ServiceIntegration;

use MasterPopups\Includes\Functions;
use MasterPopups\Includes\Services;

abstract class ServiceIntegration {
    protected $service_name = null;
    protected $service_title = null;

    protected $auth_type = 'basic_auth';//basic_auth, oauth2
    public $service = null;
    protected $ironman = null;
    protected $subscription_class = null;
    protected $api_key = '';
    protected $token = '';
    protected $lists = array();
    protected $list_id = '';
    protected $list_fields = array();
    public $error = '';
    public $response = null;
    public $messages = array(
        'subscription_ok' => 'Thank you, you have been added to our mailing list.',
        'subscriber_exists' => 'Sorry, user already registered.',
    );
    public $debug = array();
    protected static $go_oauth2_key = 'mpp_go_oauth2';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $service_name = '' ){
        $this->service_name = $service_name;
        $all_services = Services::get_all();
        $this->service_title = isset( $all_services[$service_name] ) ? $all_services[$service_name]['text'] : null;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Agrega la instancia de la clase de suscripción
    |---------------------------------------------------------------------------------------------------
    */
    public function set_subscription_class( $subscription_class ){
        return $this->subscription_class = $subscription_class;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna el api key
    |---------------------------------------------------------------------------------------------------
    */
    public function get_api_key(){
        return $this->api_key;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Establece el id de una lista
    |---------------------------------------------------------------------------------------------------
    */
    public function set_list_id( $list_id, $allow_get_lists = true, $list_fields = array() ){
        $list_id = $this->get_list_id( $list_id );

        $this->set_list_fields( $list_fields );

        if( ! $allow_get_lists ){
            $this->list_id = $list_id;
            return true;
        }
        if( $this->is_valid_list_id( $list_id, $list_fields ) ){
            $this->list_id = $list_id;
            return true;
        } else{
            $this->error = "List ID '$list_id' is not valid.";
            return false;
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Establece campos adicionales para la suscripción
    |---------------------------------------------------------------------------------------------------
    */
    public function set_list_fields( $list_fields = array() ){
        $this->list_fields = $list_fields;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna el id de la lista establecida
    |---------------------------------------------------------------------------------------------------
    */
    public function get_list_id( $list_id = '' ){
        $lists = trim( $list_id );
        //Soporte para varias listas separadas por comas
        if( strpos( $lists, ',' ) !== false ){
            $lists_array = array_map( 'trim', explode( ',', $lists ) );
            $lists_array = array_filter( $lists_array );
            $list_id = isset( $lists_array[0] ) ? $lists_array[0] : '';
            if( empty( $this->lists ) ){
                $this->lists = $lists_array;
            }
        } else{
            $list_id = $lists;
            $this->lists = array( $list_id );
        }
        return $list_id;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna las listas registradas empezando en una posición
    |---------------------------------------------------------------------------------------------------
    */
    public function get_registered_lists( $start = 0 ){
        return array_slice( $this->lists, $start );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si un id de lista es válida
    |---------------------------------------------------------------------------------------------------
    */
    public function is_valid_list_id( $list_id, $list_fields = array() ){
        $lists = $this->get_lists( $list_fields );
        if( is_array( $lists ) ){
            return in_array( $list_id, array_keys( $lists ) );
        }
        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si un email es válido
    |---------------------------------------------------------------------------------------------------
    */
    public function is_valid_email( $email ){
        return filter_var( $email, FILTER_VALIDATE_EMAIL );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    abstract public function is_connect();

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    abstract public function get_lists();

    /*
    |---------------------------------------------------------------------------------------------------
    | Agrega un suscriptor a una lista
    |---------------------------------------------------------------------------------------------------
    */
    abstract public function add_subscriber( $email, $data = array() );

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
        return array();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Realiza una nueva petición usando IronMan HTTP Client
    |---------------------------------------------------------------------------------------------------
    */
    public function new_request( $method, $url, $body = array(), $headers = array(), $options = array() ){
        $this->error = '';//reset error
        if( ! $this->ironman ){
            return false;
        }
        $this->response = $this->ironman->request( $method, $url, $headers, $body, $options );
        //true si ( $ironman->response_code >= 200 && $ironman->response_code < 300 );
        if( ! $this->ironman->success() ){
            $this->error = $this->ironman->get_error_message();
            return false;
        }
        return true;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Get url
    |---------------------------------------------------------------------------------------------------
    */
    public function get_url(){
        return $this->ironman->url;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Get response code
    |---------------------------------------------------------------------------------------------------
    */
    public function get_response_code(){
        return $this->ironman->response_code;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Get response body
    |---------------------------------------------------------------------------------------------------
    */
    public function get_response_body( $array = true ){
        if( is_null( $array ) ){
            return $this->ironman->get_response_body();
        }
        return json_decode( $this->ironman->get_response_body(), $array );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Get request body
    |---------------------------------------------------------------------------------------------------
    */
    public function get_request_body(){
        return $this->ironman->options['body'];
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Get error message
    |---------------------------------------------------------------------------------------------------
    */
    public function get_error_message( $error_message = '' ){
        if( $error_message ){
            if( is_array( $error_message ) && ! empty( $error_message ) ){
                $this->error = $this->error ? rtrim( $this->error, '.' ) . ". " : $this->error;
                foreach( $error_message as $error ){
                    $this->error .= ! empty( $error['message'] ) ? $error['message'] . '. ' : '';
                }
            } else{
                $this->error = $this->error ? rtrim( $this->error, '.' ) . ". $error_message" : $error_message;
            }
        }
        return $this->error;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verifica si un valor existe en un array
    |---------------------------------------------------------------------------------------------------
    */
    public function isset_field( $field, $array, $case_sensitive = false ){
        if( $case_sensitive ){
            $key = array_search( $field, $array );
            return $key;
        }
        $field = strtolower( $field );
        $array_lower = array_map( 'strtolower', $array );
        $key = array_search( $field, $array_lower );
        return $key;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Rellena los campos del tipo {origin_url}, {ip}, {popup_title} con su valor real
    |---------------------------------------------------------------------------------------------------
    */
    public function populate_render_fields( $params = array() ){
        if( ! $this->subscription_class ){
            return $params;
        }
        $json_params = json_encode( $params );
        $json_params = strtr( $json_params, $this->subscription_class->render_fields );
        return json_decode( $json_params, true );
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Agrega los campos OAuth 2  a la lista de integraciones conectadas
    |---------------------------------------------------------------------------------------------------
    */
    public function set_oauth2_fields( $check_page_plugin_settings = false ){
        $plugin = Functions::get_plugin_instance();
        $xbox = xbox_get( $plugin->arg( 'xbox_ids', 'settings' ) );
        $integrated_services = $xbox->get_field_value( 'integrated-services', array() );

        if( self::should_go_to_oauth2_authorization() ){
            $fields = array();
            $fields['service-auth-type'] = 'oauth2';
            if( isset( $_GET['url'] ) ){
                $this->url = untrailingslashit( urldecode( $_GET['url'] ) );
                $fields['service-url'] = $this->url;
            }
            if( isset( $_GET['clientKey'] ) ){
                $this->clientKey = $_GET['clientKey'];
                $fields['service-api-key'] = $this->clientKey;
            }
            if( isset( $_GET['clientSecret'] ) ){
                $this->clientSecret = $_GET['clientSecret'];
                $fields['service-token'] = $this->clientSecret;
            }

            $exists_service = false;
            foreach( $integrated_services as $index => &$service ){
                if( $service['integrated-services_type'] == $this->service_name ){
                    $exists_service = true;
                    $service = array_merge( $service, $fields );
                }
            }

            if( ! $exists_service ){
                $fields['integrated-services_type'] = $this->service_name;
                $fields['integrated-services_name'] = $this->service_title;
                $fields['service-status'] = 'off';
                $integrated_services[] = array_merge( Services::integration_fields(), $fields );
            }

            $xbox->set_field_value( 'integrated-services', $integrated_services );
        }

        if( self::return_from_oauth2_authorization( $check_page_plugin_settings ) ){
            foreach( $integrated_services as $index => &$service ){
                if( $service['integrated-services_type'] == $this->service_name ){
                    $this->url = $service['service-url'];
                    $this->clientKey = $service['service-api-key'];
                    $this->clientSecret = $service['service-token'];
                }
            }
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verifica si la conexión se realiza mediante OAuth 2
    |---------------------------------------------------------------------------------------------------
    */
    public static function is_oauth2( $check_page_plugin_settings = false ){
        return self::should_go_to_oauth2_authorization() || self::return_from_oauth2_authorization( $check_page_plugin_settings );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna el nombre del servicio OAuth 2
    |---------------------------------------------------------------------------------------------------
    */
    public static function get_service_name_oauth2(){
        $service_name = get_option( "mpp_service_name_oauth2" );
        //$service_name = isset( $_GET['oauth2'] ) ? $_GET['oauth2'] : '';
        return $service_name ? $service_name : '';
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Autoriza la integración con OAuth 2
    |---------------------------------------------------------------------------------------------------
    */
    public function go_oauth2_authorization( $auth_url, $params = array() ){
        $authUrl = Functions::make_url( $auth_url, $params );

//        $oauth2_data = $this->get_oauth2_data();
//        d($authUrl, $params, $oauth2_data);

        update_option( self::$go_oauth2_key, 'true' );
        header( 'Location: ' . $authUrl );
        exit;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verifica si se debe ir a autorizar mediante OAuth 2
    |---------------------------------------------------------------------------------------------------
    */
    public static function should_go_to_oauth2_authorization(){
        $exists_oauth2 = isset( $_GET['oauth2'] ) && ! empty( $_GET['oauth2'] );
        if( $exists_oauth2 ){
            update_option( "mpp_service_name_oauth2", $_GET['oauth2'] );
        }
        return Functions::is_page_plugin_settings() && $exists_oauth2;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verifica si se ha retornado de autorizar mediante OAuth 2
    |---------------------------------------------------------------------------------------------------
    */
    public static function return_from_oauth2_authorization( $check_page_plugin_settings = false ){
        $code = ! empty( $_GET['code'] ) ? $_GET['code'] : false;
        $is_page_plugin_settings = Functions::is_page_plugin_settings();
        if( ! $code ){
            return false;
        }

        if( ! get_option( self::$go_oauth2_key ) ){
            return false;
        }

        if( $check_page_plugin_settings ){
            if( ! $is_page_plugin_settings ){
                return false;
            }
        }
        return true;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Guarda los datos de la conexión OAuth 2
    |---------------------------------------------------------------------------------------------------
    */
    public function save_oauth2_connection( $settings, $response_settings = array() ){
        update_option( "mpp_{$this->service_name}_oauth2", array_merge( $settings, (array) $response_settings ) );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verifica si hay una conexión existente con OAuth 2
    |---------------------------------------------------------------------------------------------------
    */
    public function get_oauth2_data(){
        return get_option( "mpp_{$this->service_name}_oauth2" );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verifica si hay una conexión existente con OAuth 2
    |---------------------------------------------------------------------------------------------------
    */
    public function set_connected_status( $status = true ){
        $plugin = Functions::get_plugin_instance();
        $xbox = xbox_get( $plugin->arg( 'xbox_ids', 'settings' ) );
        $integrated_services = $xbox->get_field_value( 'integrated-services', array() );
        foreach( $integrated_services as $index => &$service ){
            if( $service['integrated-services_type'] == $this->service_name ){
                $service['service-status'] = $status ? 'on' : 'off';
            }
        }
        $xbox->set_field_value( 'integrated-services', $integrated_services );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Acciones a realizar después de la conexión mediante OAuth 2
    |---------------------------------------------------------------------------------------------------
    */
    public function after_connection_oauth2(){
        if( is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ){
            //Actualizamos el estado del servicio a conectado
            $this->set_connected_status( true );

            //Mostramos un mensaje exitoso
            $this->save_oauth2_connected_notice();

            $current_url = Functions::current_url( false, false );
            $admin_page = untrailingslashit( Functions::get_plugin_instance()->settings_url );

            //Esta opción se guarda justo antes de ir a realizar la autorización en go_oauth2_authorization()
            //Y se comprueba al retornar la petición  en return_from_oauth2_authorization()
            delete_option( self::$go_oauth2_key );//comentar esto para debug

            if( $current_url != $admin_page ){
                //Redirijimos al usuario a la página de ajustes del plugin
                wp_redirect( $admin_page );
                exit;
            }
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Muestra mensaje de conexión exitosa mediante OAuth 2
    |---------------------------------------------------------------------------------------------------
    */
    public function save_oauth2_connected_notice(){
        update_option( 'mpp_message_oauth2_connected', true );
    }

    public static function show_message_on_connection(){
        if( get_option( 'mpp_message_oauth2_connected' ) ){
            add_action( 'admin_notices', array( __CLASS__, 'connected_notice' ) );
        }
        delete_option( 'mpp_message_oauth2_connected' );
    }

    public static function connected_notice(){
        ?>
        <div class="updated notice is-dismissible" style="margin-left: 1px; margin-right: 20px; margin-top: 10px;">
            <p><strong>Successfully connected. Save Settings.</strong></p>
        </div>
        <?php
    }


}
