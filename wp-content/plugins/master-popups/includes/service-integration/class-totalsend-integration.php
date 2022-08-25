<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;
use MasterPopups\Includes\Functions as Functions;

class TotalsendIntegration extends ServiceIntegration {
    private $api_endpoint = 'http://app.totalsend.com/api.php';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $email, $password ){
        $this->email = trim( $email );
        $this->password = trim( $password );
        $this->ironman = new IronMan( $this->api_endpoint );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Request
    |---------------------------------------------------------------------------------------------------
    */
    public function new_request( $method, $url, $body = array(), $headers = array(), $options = array() ){
        return parent::new_request( $method, $url, array_merge( $body, array( 'ResponseFormat' => 'JSON' ) ), $headers, $options );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verifica si la petición se realizó con éxito
    |---------------------------------------------------------------------------------------------------
    */
    public function is_success(){
        $success = $this->ironman->success();
        if( $success ){
            $body = $this->get_response_body( false );
            if( ! $body ){
                return false;
            }
            if( isset( $body->Success ) ){
                $success = $body->Success;
            }
            if( ! $body->Success && ! empty( $body->ErrorText ) ){
                $error = is_array( $body->ErrorText ) ? implode( ' ', $body->ErrorText ) : $body->ErrorText;
                $this->error = $error;
            }
        }
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        $this->new_request( "GET", "/", array(
            'Command' => 'User.Login',
            'Username' => $this->email,
            'Password' => $this->password,
        ) );
        if( $this->is_success() ){
            $body = $this->get_response_body( true );
            if( isset( $body['SessionID'] ) ){
                unset( $this->ironman->body['Username'], $this->ironman->body['Password'] );
                $this->ironman->set_body( array(
                    'SessionID' => $body['SessionID'],
                ) );
            }
        }
        return $this->is_success();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists( $args = array() ){
        $items = array();
        $this->new_request( "GET", "/", array(
            'Command' => 'Lists.Get',
        ) );
        if( ! $this->is_success() ){
            return array();
        }
        $body = $this->get_response_body( true );
        $lists = isset( $body['Lists'] ) ? $body['Lists'] : array();
        if( is_array( $lists ) ){
            foreach( $lists as $list ){
                $items[$list['ListID']] = $list['Name'];
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
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : null;

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : null;

        //Datos necesarios para la suscripción
        $params = array();
        $params['EmailAddress'] = $email;
        //No existen campos first name ni last name.
        //Todos los campos adicionales deben ser así: CustomFieldX
        //donde X es el id del campo personalizado

        //El admin tiene que haber ingresado un nombre a los campos first_name y last_name
        if( ! is_null( $first_name['name'] ) && ! empty( $first_name['value'] ) ){
            $data['custom_fields'][$first_name['name']] = $first_name['value'];
        }
        if( ! is_null( $last_name['name'] ) && ! empty( $last_name['value'] ) ){
            $data['custom_fields'][$last_name['name']] = $last_name['value'];
        }
        if( ! empty( $data['custom_fields'] ) ){
            $custom_fields = $this->get_custom_fields();
            foreach( $custom_fields as $cf_id => $cf_name ){
                if( isset( $data['custom_fields'][$cf_name] ) ){
                    $params['CustomField' . $cf_id] = $data['custom_fields'][$cf_name];
                }
            }
        }
        $params = array_merge( $params, array(
            'Command' => 'Subscriber.Subscribe',
            'IPAddress' => $_SERVER['REMOTE_ADDR'],
            'ListID' => $this->list_id,
        ) );
        unset( $this->ironman->body['SubscriberListID'] );

        //Suscribir nuevo usuario
        $this->new_request( "POST", "/", $params );

        if( $this->is_success() ){
            return true;
        }

        //Comprobar error y si ya existe el suscriptor entonces actualizar sus datos
        $body = $this->get_response_body( true );
        $error_message = $this->get_error_message_on_subscribe( $body );
        $this->error = $error_message != '' ? $error_message : $this->error;

        $overwrite = isset( $data['overwrite'] ) && $data['overwrite'] == 'on' ? true : false;
        //$body['ErrorCode'] == 9 (Subscriber exists)
        if( $overwrite && isset( $body['ErrorCode'] ) && $body['ErrorCode'] == 9 ){
            $params['Command'] = 'Subscriber.Get';
            $this->new_request( "GET", "/", $params );
            if( ! $this->is_success() ){
                return false;
            }
            $subscriber = $this->get_response_body( true );
            $subscriber_id = null;
            if( isset( $subscriber['SubscriberInformation']['SubscriberID'] ) ){
                $subscriber_id = $subscriber['SubscriberInformation']['SubscriberID'];
            }
            if( is_null( $subscriber_id ) ){
                return false;
            }

            //Params for update
            unset( $params['ListID'], $params['IPAddress'] );
            unset( $this->ironman->body['ListID'], $this->ironman->body['IPAddress'] );
            $new_params = array(
                'Command' => 'Subscriber.Update',
                'SubscriberID' => $subscriber_id,
                'SubscriberListID' => $this->list_id,
                'Fields' => array()
            );
            foreach( $params as $key => $value ){
                if( Functions::starts_with( 'CustomField', $key ) ){
                    $new_params['Fields'][$key] = $value;
                    unset( $params[$key] );
                    unset( $this->ironman->body[$key] );
                }
            }

            //Update subscriber
            $params = array_merge( $params, $new_params );
            $this->new_request( "POST", "/", $params );
            if( ! $this->is_success() ){
                $body = $this->get_response_body( true );
                if( isset( $body['ErrorCode'] ) && $body['ErrorCode'] == 8 ){
                    $this->error = 'One of the provided custom fields is empty. Custom field ID and title is provided as an additional output parameter. Maybe a mandatory field is missing.';
                }
                return false;
            }
            return true;
        }

        return false;
    }

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
        $items = array();
        $this->new_request( "GET", "/", array(
            'Command' => 'CustomFields.Get',
            'SubscriberListID' => $this->list_id
        ) );
        if( ! $this->is_success() ){
            return array();
        }
        $body = $this->get_response_body( true );
        $fields = isset( $body['CustomFields'] ) ? $body['CustomFields'] : array();
        if( is_array( $fields ) ){
            foreach( $fields as $field ){
                $items[$field['CustomFieldID']] = $field['FieldName'];
            }
        }
        return $items;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Posibles errores durante la suscripción
    |---------------------------------------------------------------------------------------------------
    */
    public function get_error_message_on_subscribe( $body ){
        $error_message = '';
        if( ! is_array( $body ) || ! isset( $body['ErrorCode'] ) ){
            return '';
        }
        if( $body['ErrorCode'] == 1 ){
            $error_message = "Target subscriber list ID is missing";
        } else if( $body['ErrorCode'] == 2 ){
            $error_message = "Email address is missing";
        } else if( $body['ErrorCode'] == 3 ){
            $error_message = "IP address of subscriber is missing";
        } else if( $body['ErrorCode'] == 4 ){
            $error_message = "Invalid subscriber list ID";
        } else if( $body['ErrorCode'] == 5 ){
            $error_message = "Invalid email address";
        } else if( $body['ErrorCode'] == 6 ){
            $error_message = "One of the provided custom fields is empty. Custom field ID and title is provided as an additional output parameter";
        } else if( $body['ErrorCode'] == 7 ){
            $error_message = "One of the provided custom field value already exists in the database.";
        } else if( $body['ErrorCode'] == 8 ){
            $error_message = "One of the provided custom field value failed in validation checking. Custom field ID and title is provided as an additional output parameter";
        } else if( $body['ErrorCode'] == 9 ){
            $error_message = "Email address already exists in the list";
        } else if( $body['ErrorCode'] == 10 ){
            $error_message = "Unknown error occurred";
        } else if( $body['ErrorCode'] == 11 ){
            $error_message = "Invalid user information";
        } else if( $body['ErrorCode'] == 99998 ){
            $error_message = "Authentication failure or session expired";
        } else if( $body['ErrorCode'] == 99999 ){
            $error_message = "Not enough privileges";
        }
        return $error_message;
    }

}
