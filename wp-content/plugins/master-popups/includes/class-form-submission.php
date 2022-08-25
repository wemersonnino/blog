<?php namespace MasterPopups\Includes;


abstract class FormSubmission {
    public $plugin = null;
    public $prefix = 'mpp_';
    public $type = 'ContactForm';
    public $service = null;
    public $post_data = array();
    public $saved_data = array();
    public $popup = null;
    public $cannot = '';
    public $email = '';
    public $elements = array();
    public $fields = array();
    public $custom_fields = array();
    public $render_fields = array();
    public $result = array(
        'message' => '',
        'success' => false,
        'error' => true,
        'actions' => array(),
    );
    public $source = '';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $plugin, $post_data = array(), $type = 'ContactForm', $source = MPP_SOURCE_FORM_SUBMIT_POPUP ){
        $this->plugin = $plugin;
        $this->prefix = $this->plugin->arg( 'prefix' );
        $this->post_data = $post_data;
        $this->type = $type;
        $this->source = $source;
        $this->cannot = __( 'This action cannot be performed.', 'masterpopups' ) . ' ';
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Agrega los campos principales y los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function set_fields( $fields = array(), $custom_fields = array() ){
        $this->fields = wp_parse_args( $fields, $this->fields );
        $this->custom_fields = wp_parse_args( $custom_fields, $this->custom_fields );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si hay campos para procesar
    |---------------------------------------------------------------------------------------------------
    */
    public function has_fields(){
        if( ! isset( $this->post_data['current_device'] ) || ! isset( $this->post_data['popup_elements'] ) ){
            $this->result['message'] = 'Error Code 1. ' . __( 'Data is missing', 'masterpopups' );
            return false;
        }
        if( ! $this->exists_popup() ){
            return false;
        }

        $all_elements = $this->popup->desktop_elements;
        if( $this->post_data['current_device'] == 'mobile' ){
            $all_elements = $this->popup->mobile_elements;
        }

        foreach( $this->post_data['popup_elements'] as $index ){
            $this->elements[] = $all_elements[$index];
        }

        $this->fields = array();
        foreach( $this->elements as $element ){
            $field_name = $element->option( 'e-field-name' );
            $name = $field_name;
            //PHP magic. Dots and spaces in $_POST variable names are converted to underscores
            $name = str_replace( array( '.', ' ' ), '_', $name );
            if( $element->type == 'field_email' ){
                $name = 'email';
            } else if( $element->type == 'field_first_name' ){
                $name = 'first_name';
            } else if( $element->type == 'field_last_name' ){
                $name = 'last_name';
            }
            //Sólo procesar los campos que tienen name
            if( $field_name && isset( $this->post_data[$name] ) ){
                $value = $this->post_data[$name];
                $value = is_array( $value ) ? implode( ',', $value ) : $value;
                $this->fields[$name] = array(
                    'field_name' => $field_name,//Xbox option "Field name"
                    'value' => $value,
                    'index' => $element->index,
                    'type' => $element->type,
                    'required' => $element->option( 'e-field-required' ),
                );
                if( ! in_array( $name, array( 'email', 'first_name', 'last_name' ) ) ){
                    $this->custom_fields[$name] = $value;
                }
            }
        }

        //Hooks
        do_action( 'masterpopups_form_fields', $this );

        //Debug
        $this->result['debug'] = array(
            'post_data' => $this->post_data,//$_POST
            'custom_fields' => $this->custom_fields,
            'fields' => $this->fields,
        );

        return true;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Establece datos adicionales a guardar
    |---------------------------------------------------------------------------------------------------
    */
    public function set_additional_data_to_save( $data = array() ){
        foreach( $data as $key => $value ){
            $this->saved_data[$key] = $value;
        }

        if( $this->popup ){
            $this->saved_data['popup_id'] = $this->popup->id;
            $this->saved_data['popup_title'] = $this->popup->title;
        } else{
            $this->saved_data['popup_id'] = '';
            $this->saved_data['popup_title'] = '';
        }

        $this->saved_data['date'] = current_time( 'mysql', 0 );
        $this->saved_data['user_id'] = Functions::random_string( 32, true );
        $this->saved_data['ip'] = $_SERVER['REMOTE_ADDR'];
        $this->saved_data['origin_url'] = $_SERVER['HTTP_REFERER'];
        $this->saved_data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $this->saved_data['unique_id'] = uniqid();
        $this->saved_data['post_id'] = Functions::post_id();
        $this->saved_data['post_title'] = esc_attr( get_the_title(Functions::post_id()) );
        $this->saved_data['page_title'] = $this->saved_data['post_title'];
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Establece los valores a los campos con su respectivo render
    |---------------------------------------------------------------------------------------------------
    */
    public function set_render_fields(){
        foreach( $this->saved_data as $key => $value ){
            $name = '{render=' . $key . '}';
            $value = is_array( $value ) ? implode( ',', $value ) : $value;
            $this->render_fields[$name] = $value;

            //Soporte para formato simple {field_name}
            $name = '{' . $key . '}';
            $this->render_fields[$name] = $value;
        }
        foreach( $this->fields as $name => $info ){
            if( ! isset( $info['field_name'], $info['value'] ) ) continue;
            $name = '{render=' . $info['field_name'] . '}';
            $this->render_fields[$name] = $info['value'];

            //Soporte para formato simple {field_name}
            $name = '{' . $info['field_name'] . '}';
            $this->render_fields[$name] = $info['value'];
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si el popup que se está procesando existe, basado en el id recibido desde el usuario
    |---------------------------------------------------------------------------------------------------
    */
    public function exists_popup(){
        if( ! isset( $this->post_data['popup_id'] ) ){
            $this->result['message'] = 'Error Code 2. ' . __( 'Data is missing', 'masterpopups' );
            return false;
        }

        $popup_id = $this->post_data['popup_id'];
        if( ! $this->plugin->is_published_popup( $popup_id ) ){
            $this->result['message'] = __( 'The popup no longer exists.', 'masterpopups' );
            return false;
        }
        $this->popup = Popups::get( $popup_id );
        return true;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Valida el email. Se llama desde Ajax con un formulario de contacto o de suscripción
    |---------------------------------------------------------------------------------------------------
    */
    public function validate_email(){
        if( ! isset( $this->post_data['email'] ) ){
            $error_message = __( 'Data is missing.', 'masterpopups' );
            $error_message .= ' ' . __( 'The email field is missing.', 'masterpopups' );
            $this->result['message'] = $error_message;
            return false;
        }

        $email = trim( $this->post_data['email'] );
        if( $this->is_valid_email( $email ) ){
            $this->email = $email;
            return true;
        }

        //Verificamos el email que está en los fields
        $email = ! empty( $this->fields['email']['value'] ) ? trim( $this->fields['email']['value'] ) : '';
        if( $this->is_valid_email( $email ) ){
            $this->email = $email;
            return true;
        }

        $this->result['message'] = __( 'Invalid email address.', 'masterpopups' );
        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna el email
    |---------------------------------------------------------------------------------------------------
    */
    public function get_email(){
        return $this->email;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verifica si el email es válido
    |---------------------------------------------------------------------------------------------------
    */
    public function is_valid_email( $email = '' ){
        $settings = $this->plugin->settings;
        $valid_email = Functions::is_email( $email );
        $is_valid = !!$valid_email;

        $email_valid_by_services = array(
            'mx-record' => array( 'status' => null, 'error' => '' ),
            'kickbox' => array( 'status' => null, 'error' => '' ),
            'neverbounce' => array( 'status' => null, 'error' => '' ),
            'algocheck' => array( 'status' => null, 'error' => '' ),
            'proofy' => array( 'status' => null, 'error' => '' ),
            'thechecker' => array( 'status' => null, 'error' => '' ),
        );

        $emailVerification = new EmailVerification();

        foreach( $email_valid_by_services as $service => $val ){
            $is_on = $settings->option( $service.'-email-validation' ) == 'on';
            $api_key = $settings->option( $service.'-email-validation-apikey' );

            //d('service', $service, $is_on, $api_key);
            if( $service === 'mx-record' ){
                if( $is_on ){
                    if( ! $emailVerification->mx_record( $email ) ){
                        $is_valid = false;
                        $email_valid_by_services[$service]['status'] = false;
                    } else {
                        $email_valid_by_services[$service]['status'] = true;
                    }
                }
            } else if( $service === 'proofy' ){
                $user_id = $settings->option( $service.'-email-validation-userid' );
                if( $is_on && $api_key && $user_id ){
                    if( ! $emailVerification->$service( $email, $api_key, $user_id ) ){
                        $is_valid = false;
                        $email_valid_by_services[$service]['status'] = false;
                    } else {
                        $email_valid_by_services[$service]['status'] = true;
                    }
                    $email_valid_by_services[$service]['error'] = $emailVerification->get_error();
                }
            } else {
                if( $is_on && $api_key ){
                    if( ! $emailVerification->$service( $email, $api_key ) ){
                        $is_valid = false;
                        $email_valid_by_services[$service]['status'] = false;
                    } else {
                        $email_valid_by_services[$service]['status'] = true;
                    }
                    $email_valid_by_services[$service]['error'] = $emailVerification->get_error();
                }
            }
        }

//        d($valid_email);
//        d($email_valid_by_services);
//        d($is_valid);
//        d($emailVerification->response);

        return $is_valid;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Valida varios emails
    |---------------------------------------------------------------------------------------------------
    */
    public function validate_emails( $emails ){
        $valid_emails = array();
        if( ! is_array( $emails ) ){
            $emails = explode( ',', $emails );
        }
        foreach( $emails as $email ){
            $email = trim( $email );
            if( Functions::is_email( $email ) ){
                $valid_emails[] = $email;
            }
        }
        return implode( ',', $valid_emails );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna el parámetro From: de un header. Ej: Admin <admin@gmail.com>. array(name=Admin, email=admin@gmail.com)
    |---------------------------------------------------------------------------------------------------
    */
    public function get_header_from( $content ){
        $content = trim( $content );
        $from_email = '';
        $from_name = '';
        $bracket_pos = strpos( $content, '<' );
        if( $bracket_pos !== false ){
            // Text before the bracketed email is the "From" name.
            if( $bracket_pos > 0 ){
                $from_name = substr( $content, 0, $bracket_pos - 1 );
                $from_name = str_replace( '"', '', $from_name );
                $from_name = trim( $from_name );
            }

            $from_email = substr( $content, $bracket_pos + 1 );
            $from_email = str_replace( '>', '', $from_email );
            $from_email = trim( $from_email );

            // Avoid setting an empty $from_email.
        } elseif( '' !== trim( $content ) ){
            $from_email = trim( $content );
        }
        return array(
            'name' => $from_name,
            'email' => $from_email
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Envía email
    |---------------------------------------------------------------------------------------------------
    */
    public function send_email( $from, $to, $cc = null, $subject, $message = '', $reply = true, $extra_headers = array() ){
        $this->result['debug']['send_email'] = array();
        $sent = false;
        $headers = array();
        $headers[] = 'Content-Type: text/html; charset=UTF-8';

        //Reemplazamos {field_name} por su valor real en from
        if( stripos( $from, '{render' ) !== false || stripos( $from, '{' ) !== false ){
            $from = strtr( $from, $this->render_fields );
        }
        $from_array = $this->get_header_from( $from );
        $this->result['debug']['send_email']['from_array'] = $from_array;
        $header_from = '';
        if( ! empty( $from_array['email'] ) ){
            $from_name = $from_array['name'];
            $from_email = $from_array['email'];
            if( ! filter_var( $from_email, FILTER_VALIDATE_EMAIL ) ){
                $this->result['message'] = __( 'Invalid parameter FROM.', 'masterpopups' );
                $from_name = "WordPress";
                $from_email = get_option( 'admin_email' );
            }
            $from = "$from_name <$from_email>";
            $header_from = "From: $from";
        }
        if( $header_from ){
            $headers[] = $header_from;
        }

        if( $reply && isset( $this->fields['email'] ) ){
            $user_name = '';
            if( isset( $this->fields['first_name'] ) ){
                $user_name = $this->fields['first_name']['value'];
            }
            $headers[] = "Reply-To: $user_name <{$this->fields['email']['value']}>";
        }

        if( $cc ){
            if( stripos( $cc, '{render' ) !== false || stripos( $cc, '{' ) !== false ){
                $cc = strtr( $cc, $this->render_fields );
            }
            $headers[] = "Cc: " . trim( $cc );
            $this->result['debug']['send_email']['cc'] = $cc;
        }

        $headers = array_merge( $headers, $extra_headers );

        //Reemplazamos los campos {field_name} por su valor real en el mensaje
        $body = strtr( $message, $this->render_fields );
        $body = do_shortcode( $body );
        $body = wpautop( $body );

        if( stripos( $to, '{render' ) !== false || stripos( $to, '{' ) !== false ){
            $to = strtr( $to, $this->render_fields );
        }
        $to = $this->validate_emails( $to );

        $this->result['debug']['send_email']['from'] = $header_from;
        $this->result['debug']['send_email']['to'] = $to;


        if( ! empty( $to ) ){
            $sent = wp_mail( $to, $subject, $body, $headers );
        }
        return $sent;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Acciones en caso de éxito
    |---------------------------------------------------------------------------------------------------
    */
    public function get_actions_on_success(){
        $actions = array(
            'close_popup' => $this->popup->option( 'form-submission-ok-close-popup' ) == 'on' ? true : false,
            'close_popup_delay' => $this->popup->option( 'form-submission-ok-close-popup-delay' ),
            'open_popup_id' => (int) $this->popup->option( 'form-submission-ok-open-popup-id' ),
            'download_file' => $this->popup->option( 'form-submission-ok-download-file' ) == 'on' ? true : false,
            'file' => $this->popup->option( 'form-submission-ok-file' ),
            'redirect' => $this->popup->option( 'form-submission-ok-redirect' ) == 'on' ? true : false,
            'redirect_to' => $this->popup->option( 'form-submission-ok-redirect-to' ),
            'redirect_target' => $this->popup->option( 'form-submission-ok-redirect-target' ),
            'advanced_redirection' => $this->get_advanced_redirection(),
            //            'data' => array(
            //                'fields' => $this->fields,
            //                'custom-fields' => $this->custom_fields,
            //                'post-data' => $this->post_data,
            //            )
        );
        if( $this->type == 'ContactForm' ){
            $actions['message'] = $this->popup->option( 'contact-form-ok-message' );
        } elseif( $this->type == 'Subscription' ){
            $actions['message'] = $this->popup->option( 'subscription-ok-message' );
        }
        $actions['message'] = strtr( $actions['message'], $this->render_fields );
        return $actions;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna la URL de las redirecciones avanzadas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_advanced_redirection(){
        $redirections = $this->popup->option( 'form-redirections' );
        $redirect_to = '';
        if( is_array( $redirections ) && ! empty( $redirections ) ){
            foreach( $redirections as $key => $item ){
                $name = $item['mpp_field-name'];
                $value = $item['mpp_field-value'];
                $url = trim( $item['mpp_redirect-to'] );
                $condition = $item['mpp_condition'];
                if( empty( $url ) ){
                    continue;
                }
                $redirect = false;
                foreach( $this->fields as $field ){
                    if( $field['field_name'] == $name ){
                        if( $condition == 'equal' && $field['value'] == $value ){
                            $redirect = true;
                        } else if( $condition == 'not_equal' && $field['value'] != $value ){
                            $redirect = true;
                        } else if( $condition == 'less' && $field['value'] < $value ){
                            $redirect = true;
                        } else if( $condition == 'less_equal' && $field['value'] <= $value ){
                            $redirect = true;
                        } else if( $condition == 'higher' && $field['value'] > $value ){
                            $redirect = true;
                        } else if( $condition == 'higher_equal' && $field['value'] >= $value ){
                            $redirect = true;
                        }
                        if( $redirect ){
                            $redirect_to = $url;
                            break 2;
                        }
                    }
                }
            }
        }
        return $redirect_to;
    }


}
