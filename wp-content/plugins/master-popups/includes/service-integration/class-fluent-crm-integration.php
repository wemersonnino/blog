<?php namespace MasterPopups\Includes\ServiceIntegration;


class FluentCRMIntegration extends ServiceIntegration {
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
        if( function_exists( 'FluentCrmApi' ) ){
            return true;
        }
        $this->error = sprintf( __( 'Please install and activate the %s plugin.', 'masterpopups' ), '<a href="https://wordpress.org/plugins/fluent-crm/" target="_blank">FluentCRM</a>' );
        return false;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists( $args = array() ){
        $items = array();
        $listApi = FluentCrmApi('lists');
        // Get all the lists
        $allLists = $listApi->all(); // array of all the lists and each list is an object
        $lists = $allLists->toArray();
        foreach( $lists as $list ){
            $items[$list['id']] = $list['title'];
        }
        return $items;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verificar si el contacto está en la lista indicada
    |---------------------------------------------------------------------------------------------------
    */
    private function subscriber_exists( $email ){
        $contactApi = FluentCrmApi('contacts');
        $contact = $contactApi->getContact($email);
        if( ! $contact ){
            return false;
        }
        return $contact;
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

        //Datos necesarios para la suscripción
        $params = array();
        $params['email'] = $email;
        $params['first_name'] = $first_name['value'];
        $params['last_name'] = $last_name['value'];
        //subscribed, unsubscribed, pending, bounced
        $params['status'] = isset( $data['double-opt-in'] ) && $data['double-opt-in'] == 'on' ? 'pending' : 'subscribed';
        $params['tags'] = array();
        $params['lists'] = $this->lists;//Suscripción a varias listas

        if( ! empty( $data['custom_fields'] ) ){
            $default_fields = $this->get_default_fields();
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                $cf_name = strtolower( $cf_name );
                $key = $this->isset_field( $cf_name, $default_fields, false );
                if( $key !== false ){
                    $params[$cf_name] = $cf_value;
                }

                //Soporte para tags
                if( strtolower( $cf_name ) == 'tags' ){
                    $params['tags'] = array();
                    $tags = array_map( 'trim', explode( ',', $cf_value ) );

                    foreach( $tags as $tag_slug ){
                        $tag_id = self::get_tag_id( $tag_slug );
                        if( $tag_id ){
                            $params['tags'][] = $tag_id;
                        }
                    }
                }
            }
        }

        //Importante. Rellena los campos del tipo {origin_url}, {ip}, {popup_title} con su valor real
        $params = $this->populate_render_fields( $params );
        //d($params);

        //Comprobamos si el usuario ya está registrado
        $overwrite = isset( $data['overwrite'] ) && $data['overwrite'] == 'on' ? true : false;
        if( ! $overwrite && $contact = $this->subscriber_exists( $email ) ){
            $this->error = $this->messages['subscriber_exists'];
            if( $contact->status == 'pending' ) {
                $this->error .= ' Check your email and confirm your subscription.';
            }
            return false;
        }
        //Suscribir nuevo usuario
        $contactApi = FluentCrmApi('contacts');
        $contact = $contactApi->createOrUpdate( $params );

        if( ! $contact ){
            return false;
        }

        // Send a double opt-in email if the status is pending
        if( $contact->status == 'pending' ) {
            $contact->sendDoubleOptinEmail();
        }

        return true;
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Retorna todos los campos por defecto
	|---------------------------------------------------------------------------------------------------
	*/
    public function get_default_fields(){
        return array(
            'email',
            'first_name',
            'last_name',
            'phone',
            'company_id',
            'prefix',
            'address_line_1',
            'address_line_2',
            'postal_code',
            'city',
            'state',
            'country',
            'ip',
            'contact_type',
            'avatar',
            'date_of_birth'
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna el id de un tag
    |---------------------------------------------------------------------------------------------------
    */
    public static function get_tag_id( $tag_slug ){
        if( ! function_exists( 'FluentCrmApi' ) ){
            return false;
        }
        $tag_slug = trim( $tag_slug );
        $tagApi = FluentCrmApi('tags');
        $tags = $tagApi->all()->toArray();
        foreach( $tags as $tag ){
            if( $tag['slug'] == $tag_slug ){
                return $tag['id'];
            }
        }
        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los tags
    |---------------------------------------------------------------------------------------------------
    */
    public static function get_all_tags(){
        if( ! function_exists( 'FluentCrmApi' ) ){
            return array();
        }
        $tagApi = FluentCrmApi('tags');
        $tags = $tagApi->all()->toArray();
        $items = array();
        foreach( $tags as $tag ){
            $items[$tag['id']] = $tag['title'];
        }
        return $items;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verifica si un usuario tiene un tag específico
    |---------------------------------------------------------------------------------------------------
    */
    public static function user_has_tags( $find_user_tags = array() ){
        if( ! function_exists( 'FluentCrmApi' ) ){
            return false;
        }
        $contactApi = FluentCrmApi('contacts');
        $contact = $contactApi->getCurrentContact();
        if( ! $contact ){
            return false;
        }
        $contact_tags = $contact->tags()->all()->toArray();
        $user_tags_ids = array();
        foreach( $contact_tags as $tag ){
            $user_tags_ids[] = $tag['object_id'];
        }
        return ! empty( array_intersect( $user_tags_ids, $find_user_tags ) );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        return array();
    }

}
