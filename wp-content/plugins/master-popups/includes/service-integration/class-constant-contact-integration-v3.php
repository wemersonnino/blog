<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;

/**
 * Class ConstantContactIntegrationV3
 *
 * Doc: https://v3.developer.constantcontact.com/api_guide/server_flow.html#step-3-retrieve-the-authorization-code
 * Scopes Doc: https://v3.developer.constantcontact.com/api_guide/scopes.html#scopes-required-by-v3-api-routes
 *
 * @package MasterPopups\Includes\ServiceIntegration
 */
class ConstantContactIntegrationV3 extends ServiceIntegration {
    private $api_endpoint = 'https://api.cc.email/v3';
    protected $service_name = 'constant_contact';
    protected $url = '';
    protected $clientKey = '';
    protected $clientSecret = '';
    private $connected = false;
    private $redirect_url = '';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $auth_type, $api_key = '', $api_token = '' ){
        parent::__construct( $this->service_name );

        $this->auth_type = $auth_type;
        $this->url = 'https://api.cc.email/v3';
        $this->clientKey = $api_key;
        $this->clientSecret = $api_token;
        $this->redirect_url = admin_url( 'edit.php' );
        $this->ironman = new IronMan( $this->api_endpoint );

        $settings = array(
            'clientKey' => $this->clientKey,
            'clientSecret' => $this->clientSecret,
        );

        if( defined( 'DOING_AJAX' ) && DOING_AJAX ){
            if( $this->auth_type === 'oauth2' ){
                $this->debug['oauth2-settings'] = $settings;
                $this->connect_with_oauth2( $settings );
            }
        } else if( self::is_oauth2() ){
            $this->set_oauth2_fields( false ); //agregar true o false dependiendo si se quiere comprobar el parámetro state en la URL

            $settings['clientKey'] = $this->clientKey;
            $settings['clientSecret'] = $this->clientSecret;

            if( self::should_go_to_oauth2_authorization() ){
                $base_url = 'https://api.cc.email/v3/idfed';
                $params = array(
                    'response_type' => 'code',
                    'scope' => 'contact_data',
                    'redirect_uri' => urlencode( $this->redirect_url ),
                    'client_id' => $this->clientKey,
                );
                $this->go_oauth2_authorization( $base_url, $params );
            } else{
                $settings = array_merge( $settings, array( 'code' => $_GET['code'] ) );
                $this->connect_with_oauth2( $settings );
            }
        } else{
            $this->connect_with_oauth2( $settings );
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Conección mediante OAuth 2
    |---------------------------------------------------------------------------------------------------
    */
    public function connect_with_oauth2( $settings ){
        $oauth2_data = $this->get_oauth2_data();
        if( ! empty( $oauth2_data['access_token'] ) && ! empty( $oauth2_data['clientKey'] ) && $oauth2_data['clientKey'] == $settings['clientKey'] ){
            $success = $this->validate_access_token_oauth2( $oauth2_data );
        } else{
            $success = $this->request_access_token_oauth2( $settings );
        }
        if( ! $success ){
            $success = $this->refresh_access_token_oauth2( $settings );
        }
        if( $success ){
            $this->after_connection_oauth2();
        }

        $oauth2_data = $this->get_oauth2_data();//por si se guardaron nuevos cambios
        if( $success ){
            $token = $oauth2_data['access_token'];
            $this->ironman = new IronMan( $this->api_endpoint );
            $this->ironman->set_option( 'encode_body', true );//La petición requiere datos en formato json
            $this->ironman->set_option( 'reset_body_after_request', true );
            $this->ironman->set_headers( array(
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer $token",
            ) );
            $this->connected = true;

            return true;
        }

        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba y valida Token OAuth 2
    |---------------------------------------------------------------------------------------------------
    */
    public function validate_access_token_oauth2( $oauth2_data ){
        $token = $oauth2_data['access_token'];
        $this->ironman = new IronMan( $this->api_endpoint );
        $this->ironman->set_headers( array(
            'Authorization' => "Bearer $token",
        ) );
        $success = parent::new_request( "GET", "/contact_lists?include_count=false", array() );

        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Request Access Token  OAuth 2
    |---------------------------------------------------------------------------------------------------
    */
    public function request_access_token_oauth2( $settings ){
        $data = array(
            "code" => isset( $settings['code'] ) ? $settings['code'] : '',
            "redirect_uri" => $this->redirect_url,
            "grant_type" => 'authorization_code',
        );
        $this->ironman->set_headers( array(
            'Authorization' => "Basic " . base64_encode( $settings['clientKey'] . ':' . $settings['clientSecret'] ),
        ) );
        $success = parent::new_request( "POST", "https://idfed.constantcontact.com/as/token.oauth2", $data );
        $body = $this->get_response_body( true );
        if( $success ){
            $accessTokenData = $body; //access_token, refresh_token, token_type
            //Guardamos datos del token para usar mediante Ajax al suscribir usuarios
            $this->save_oauth2_connection( $settings, $accessTokenData );
        } else{
            $error_message = isset( $body['error_message'] ) ? $body['error_message'] : '';
            $this->error = $this->get_error_message( $error_message );
            $success = false;
        }

        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Refresh Access Token  OAuth 2
    |---------------------------------------------------------------------------------------------------
    */
    public function refresh_access_token_oauth2( $settings ){
        $oauth2_data = $this->get_oauth2_data();
        $this->ironman->set_headers( array(
            'Authorization' => "Basic " . base64_encode( $settings['clientKey'] . ':' . $settings['clientSecret'] ),
        ) );
        $success = parent::new_request( "POST", "https://idfed.constantcontact.com/as/token.oauth2", array(
            "grant_type" => 'refresh_token',
            "refresh_token" => isset( $oauth2_data['refresh_token'] ) ? $oauth2_data['refresh_token'] : '',
        ) );
        $body = $this->get_response_body( true );
        if( $success ){
            $accessTokenData = $body;//access_token, refresh_token, token_type
            //Guardamos datos del token para usar mediante Ajax al suscribir usuarios
            $this->save_oauth2_connection( $oauth2_data, $accessTokenData );
        } else{
            $error_message = isset( $body['error_message'] ) ? $body['error_message'] : '';
            $this->error = $this->get_error_message( $error_message );
            $success = false;
        }

        return $success;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Request
    |---------------------------------------------------------------------------------------------------
    */
    public function new_request( $method, $url, $body = array(), $headers = array(), $options = array() ){
        $success = parent::new_request( $method, $url, $body, $headers, $options );
        if( ! $success ){
            $body = $this->get_response_body( true );
            $error_message = isset( $body['error_message'] ) ? $body['error_message'] : '';
            $this->error = $this->get_error_message( $error_message );
        }

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
        return $this->connected;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists( $args = array() ){
        $success = $this->new_request( "GET", "/contact_lists?include_count=false" );
        if( ! $success ){
            return array();
        }
        $items = array();
        $body = $this->get_response_body( true );
        if( isset( $body['lists'] ) ){
            $lists = $body['lists'];
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
        $email = strtolower( $email );
        $success = $this->new_request( "GET", "/contacts?email=$email&include_count=false" );
        if( ! $success ){
            return false;
        }
        $body = $this->get_response_body( true );
        $contact = ! empty( $body['contacts'] ) ? $body['contacts'][0] : false;

        return $contact;
    }

    private function create_custom_fields( $data_custom_fields ){
        $custom_fields_result = [];
        if( ! empty( $data_custom_fields ) ){
            $custom_fields = $this->get_custom_fields();
            foreach( $data_custom_fields as $cf_name => $cf_value ){
                $key = $this->isset_field( $cf_name, $custom_fields, false );
                if( $key !== false ){
                    $custom_fields_result[] = array(
                        'custom_field_id' => $key,
                        'value' => $cf_value
                    );
                } else{
                    $success = $this->new_request( "POST", "/contact_custom_fields/", array(
                        'label' => $cf_name,
                        'type' => 'string'
                    ) );
                    if( $success ){
                        $body = $this->get_response_body( true );
                        if( isset( $body['custom_field_id'] ) ){
                            $custom_fields_result[] = array(
                                'custom_field_id' => $body['custom_field_id'],
                                'value' => $cf_value
                            );
                        }
                    }
                }
            }
        }

        return $custom_fields_result;
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

        $params = array();
        $params['email_address'] = array( 'address' => $email, 'permission_to_send' => "implicit" );
        $params[$first_name['name']] = $first_name['value'];
        $params[$last_name['name']] = $last_name['value'];
        $params['custom_fields'] = array();
        $params['list_memberships'] = $this->get_registered_lists();;

        $params = $this->populate_render_fields( $params );
        $subscriber = $this->subscriber_exists( $email );

        if( $subscriber ){
            $overwrite = isset( $data['overwrite'] ) && $data['overwrite'] == 'on' ? true : false;
            if( ! $overwrite ){
                $this->error = $this->messages['subscriber_exists'];

                return false;
            }
            $params['custom_fields'] = $this->create_custom_fields( $data['custom_fields'] );
            if( empty( $params['custom_fields'] ) ){
                unset( $params['custom_fields'] );//Eliminar si está vacío porque da error
            }
            $params['update_source'] = 'Account';
            $request_body = $params;
            $success = $this->new_request( "PUT", "/contacts/" . $subscriber['contact_id'], $request_body );
        } else{
            $params['custom_fields'] = $this->create_custom_fields( $data['custom_fields'] );
            if( empty( $params['custom_fields'] ) ){
                unset( $params['custom_fields'] );//Eliminar si está vacío porque da error
            }
            $params['create_source'] = 'Account';
            $request_body = $params;
            $success = $this->new_request( "POST", "/contacts/", $request_body );
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
            'prefix_name',
            'first_name',
            'middle_name',
            'last_name',
            'fax',
            'job_title',
            'home_phone',
            'cell_phone',
            'birthday_day',
            'work_phone',
            'anniversary', // Date
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        $success = $this->new_request( "GET", "/contact_custom_fields" );
        if( ! $success ){
            return $items;
        }
        $body = $this->get_response_body( true );
        $custom_fields = ! empty( $body['custom_fields'] ) ? $body['custom_fields'] : array();

        foreach( $custom_fields as $custom_field ){
            $items[$custom_field['custom_field_id']] = $custom_field['label'];
        }
        return $items;
    }
}