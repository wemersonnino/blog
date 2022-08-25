<?php namespace MasterPopups\Includes\ServiceIntegration;

use MasterPopups\Includes\Functions;
use MasterPopups\Includes\Services;
use MasterPopups\Mautic\Auth\ApiAuth;
use MasterPopups\Mautic\MauticApi;


class MauticIntegration extends ServiceIntegration {
    protected $service_name = 'mautic';

    private $auth = null;
    private $context = null;
    protected $url = '';
    protected $clientKey = '';
    protected $clientSecret = '';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $auth_type, $email_or_username = '', $password = '', $url = '', $api_key = '', $api_token = '' ){
        parent::__construct( $this->service_name );

        $this->auth_type = $auth_type;
        $this->url = untrailingslashit( $url );
        $this->clientKey = $api_key;
        $this->clientSecret = $api_token;

        $settings = array(
            'baseUrl' => $this->url,// Base URL of the Mautic instance
            'version' => 'OAuth2',
            'clientKey' => $this->clientKey,
            'clientSecret' => $this->clientSecret,
            'callback' => Functions::get_plugin_instance()->settings_url,
        );

        if( defined( 'DOING_AJAX' ) && DOING_AJAX ){
            if( $this->auth_type === 'oauth2' ){
                if( $oauth2_data = $this->get_oauth2_data() ){
                    $this->debug['oauth2-data'] = $oauth2_data;
                    if( is_array( $oauth2_data ) ){
                        $settings['accessToken'] = $oauth2_data['access_token'];
                        $settings['accessTokenExpires'] = $oauth2_data['expires'];
                        $settings['refreshToken'] = $oauth2_data['refresh_token'];
                    }
                }
                $this->debug['oauth2-settings'] = $settings;
                $this->connect_with_oauth2( $settings );
            } else if( $email_or_username !== '' && $password !== '' ){
                //Basic Auth
                $settings = array(
                    'userName' => $email_or_username,
                    'password' => $password,
                );
                // Initiate the auth object specifying to use BasicAuth
                $initAuth = new ApiAuth();
                $this->auth = $initAuth->newAuth( $settings, 'BasicAuth' );
                $this->service = new MauticApi();
                $this->debug['mautic-settings-basic-auth'] = $settings;
            }
        } else if( self::is_oauth2() ){
            $this->set_oauth2_fields( false );

            $settings['baseUrl'] = untrailingslashit( $this->url );
            $settings['clientKey'] = $this->clientKey;
            $settings['clientSecret'] = $this->clientSecret;

            if( self::should_go_to_oauth2_authorization() ){
//                $base_url = $settings['baseUrl'];
//                $params = array(
//                    'state'         => $this->service_name,
//                    'grant_type'    => 'authorization_code',
//                    'response_type' => 'code',
//                    'redirect_uri'  => Functions::get_plugin_instance()->settings_url,
//                    'client_id'     => $this->clientKey,
//                    //'client_secret' => $this->clientSecret,
//                );
//
//                $auth_url = trailingslashit( $base_url ).'oauth/v2/authorize';
//                $this->go_oauth2_authorization( $auth_url, $params );
                //Importante para solo para Mautic, reemplaza la línea anterior que se usa en las otras integraciones
                update_option( self::$go_oauth2_key, 'true' );
                $this->connect_with_oauth2( $settings );
            } else {
                $this->connect_with_oauth2( $settings );
            }
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Conección mediante OAuth 2
    |---------------------------------------------------------------------------------------------------
    */
    public function connect_with_oauth2( $settings ){
        // Initiate the auth object
        session_start();
        $initAuth = new ApiAuth();
        $this->auth = $initAuth->newAuth( $settings );
        $this->service = new MauticApi();
//        if( isset( $_SESSION['oauth']['state'] ) ){
//            $_GET['state'] = $_SESSION['oauth']['state'];//Undefined index: state in MauticAPI/lib/Auth/OAuth.php:443
//        }
        try{
            if( $this->auth->validateAccessToken() ){
                if( $this->auth->accessTokenUpdated() ){
                    // $accessTokenData will have the following keys:
                    // For OAuth1.0a: access_token, access_token_secret, expires
                    // For OAuth2: access_token, expires, token_type, refresh_token
                    $accessTokenData = $this->auth->getAccessTokenData();

                    //Guardamos datos del token para usar mediante Ajax al suscribir usuarios
                    $this->save_oauth2_connection( $settings, $accessTokenData );
                    //update_option( 'mpp_mautic_oauth2', array_merge( $settings, (array) $accessTokenData ) );

                    $this->after_connection_oauth2();
                }
            }
        } catch( \Exception $e ){
            // Do Error handling
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna el servicio basado en un contexto
    |---------------------------------------------------------------------------------------------------
    */
    public function get_service( $context ){
        return $this->service->newApi( $context, $this->auth, $this->url );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna una lista de registros basado e un contexto
    |---------------------------------------------------------------------------------------------------
    */
    private function get_all( $context, $limit = 50000 ){
        return $context->getList( '', 0, $limit );//$search = '', $start = 0, $limit = 0, $orderBy = '', $orderByDir = 'ASC', $publishedOnly = false, $minimal = false
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        if( ! $this->service ){
            $this->error = 'Some fields are empty.';
            return false;
        }
        $segmentApi = $this->get_service( 'segments' );
        $segments = $this->get_all( $segmentApi );

        //$response_info = $this->auth->getResponseInfo();
        //$response_code = $response_info['http_code'];
        //$this->auth->getResponseHeaders();


        if( isset( $segments['lists'] ) ){
            return true;
        } else if( isset( $segments['error'] ) || isset( $segments['errors'] ) ){
            $error = isset( $segments['errors'] ) ? $segments['errors'][0]['message'] : '';
        } elseif( isset( $segments['message'] ) ){
            $error = $segments['message'];
        }
        $this->error = 'Did you enter a Mautic URL? ' . $error;
        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists(){
        return $this->get_segments();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verifica si un suscriptor está en la lista actual
    |---------------------------------------------------------------------------------------------------
    */
    public function get_contacts(){
        $contactApi = $this->get_service( 'contacts' );
        $response = $this->get_all( $contactApi );

        if( ! isset( $response['contacts'] ) ){
            return array();
        }
        $contacts = array();
        foreach( $response['contacts'] as $id => $data ){
            $contacts[$data['id']] = $data['fields']['all'];
        }
        return $contacts;
    }

    /*
      |---------------------------------------------------------------------------------------------------
      | Verifica si un suscriptor está en la lista actual
      |---------------------------------------------------------------------------------------------------
      */
    private function subscriber_exists( $email ){
        $contacts = $this->get_contacts();
        foreach( $contacts as $id => $contact ){
            if( $email == $contact['email'] ){
                return true;
            }
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

        //Comprobamos si el usuario ya está registrado
        if( $this->subscriber_exists( $email ) ){
            $this->error = $this->messages['subscriber_exists'];
            return false;
        }

        //Datos necesarios para la suscripción
        $params = array();
        $params['email'] = $email;
        $params[$first_name['name']] = $first_name['value'];
        $params[$last_name['name']] = $last_name['value'];
        $params['ipAddress'] = $_SERVER['REMOTE_ADDR'];

        if( ! empty( $data['custom_fields'] ) ){
            $custom_fields = $this->get_custom_fields();
            foreach( $custom_fields as $cf_id => $cf_name ){
                if( isset( $data['custom_fields'][$cf_name] ) ){
                    $params[$cf_name] = $data['custom_fields'][$cf_name];
                }
            }
        }

        //Suscribir nuevo usuario
        $contactApi = $this->get_service( 'contacts' );
        $this->response = $contactApi->create( $params );

        //'error' is deprecated as of 2.6.0 and will be removed in 3.0. Use the 'errors' array instead.
        if( isset( $this->response['error'] ) ){
            $this->error = $this->response['error']['message'];
            return false;
        }

        $contact_id = $this->response['contact']['id'];
        $segmentApi = $this->get_service( 'segments' );
        $this->response = $segmentApi->addContact( $this->list_id, $contact_id );
        if( ! isset( $this->response['success'] ) ){
            return false;
        }

        return true;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        $fieldApi = $this->get_service( 'contactFields' );
        $response = $this->get_all( $fieldApi );
        if( count( $response['fields'] ) < 1 ){
            return array();
        }
        foreach( $response['fields'] as $data ){
            if( $data['isPublished'] ){
                $items[$data['id']] = $data['alias'];
            }
        }
        return $items;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los segmentos
    |---------------------------------------------------------------------------------------------------
    */
    public function get_segments(){
        $items = array();
        $segmentApi = $this->get_service( 'segments' );
        $response = $this->get_all( $segmentApi );
        if( count( $response['lists'] ) < 1 ){
            return array();
        }
        foreach( $response['lists'] as $data ){
            if( $data['isPublished'] ){
                $items[$data['id']] = $data['name'];
            }
        }
        return $items;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna una lista de "contact owners"
    |---------------------------------------------------------------------------------------------------
    */
    public function get_contact_owners(){
        $items = array();
        $contactApi = $this->get_service( 'contacts' );
        $response = $contactApi->getOwners();

        if( $contactApi->getResponseInfo()['http_code'] == 200 && ! empty( $response ) ){
            foreach( $response as $data ){
                $items[$data['id']] = $data['firstName'] . ' ' . $data['lastName'];
            }
        }
        return $items;
    }

}

