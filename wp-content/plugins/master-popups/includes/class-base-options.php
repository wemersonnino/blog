<?php namespace MasterPopups\Includes;

abstract class BaseOptions {
    public $id = 0;
    public $ID = 0;
    public $title = '';
    protected $status = 'on';
    protected $options = array();
    protected $defaults = array();
    protected $prefix = '';
    public $xbox = null;

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    protected function __construct( $xbox_id = '', $prefix = '', $options = array(), $defaults = array() ){
        $this->prefix = $prefix;
        $this->xbox = xbox_get( $xbox_id );

        $id = ! empty( $options['id'] ) ? $options['id'] : 0;
        if( $this->set_object_id( $id ) ){
            $this->title = get_the_title( $this->id );
        }

        $this->set_defaults( $defaults );
        $this->set_options( $options );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Get options
    |---------------------------------------------------------------------------------------------------
    */
    public function get_options(){
        return $this->options;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Agrega opciones
    |---------------------------------------------------------------------------------------------------
    */
    public function set_defaults( $defaults = array() ){
        $this->defaults = $defaults;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Agrega opciones
    |---------------------------------------------------------------------------------------------------
    */
    abstract protected function set_options( $options = array() );

    /*
    |---------------------------------------------------------------------------------------------------
    | Agrega id al objeto actual
    |---------------------------------------------------------------------------------------------------
    */
    public function set_object_id( $id = 0 ){
        if( $id ){
            $this->id = $id;
            $this->ID = $this->id;
            return true;
        } else if( Functions::is_post_page( 'new' ) ){
            $this->id = 0;
            $this->ID = 0;
            return false;
        } else {
            $this->id = Functions::post_id();
            $this->ID = $this->id;
            return ! empty( $this->id );
        }
        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Acceso a cualquier método, evita errores al llamar a métodos inexistentes
    |---------------------------------------------------------------------------------------------------
    */
    public function __call( $name, $arguments ){
        if( Functions::starts_with( 'set_', $name ) && strlen( $name ) > 4 ){
            $property = substr( $name, 4 );
            if( property_exists( $this, $property ) && isset( $arguments[0] ) ){
                $this->$property = $arguments[0];
                return $this->$property;
            }
            return null;
        } else if( Functions::starts_with( 'get_', $name ) && strlen( $name ) > 4 ){
            $property = substr( $name, 4 );
            if( property_exists( $this, $property ) ){
                return $this->$property;
            }
            return null;
        } else if( property_exists( $this, $name ) ){
            return $this->$name;
        } else{
            return $this->option( $name );
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Cambia el valor a una opción
    |---------------------------------------------------------------------------------------------------
    */
    public function set_option( $option_name, $value = null ){
        $option_name = $this->get_option_name( $option_name );
        if( isset( $this->options[$option_name] ) && ! is_null( $value ) ){
            $this->options[$option_name] = $value;
        }
        return $this->options;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna el valor de una opción
    |---------------------------------------------------------------------------------------------------
    */
    public function get_option( $option_name = '', $default_value = null ){
        return $this->option( $option_name, $default_value );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna el valor de una opción
    |---------------------------------------------------------------------------------------------------
    */
    public function option( $option_name = '', $default_value = null ){
        $option_name = $this->get_option_name( $option_name );
        $value = null;
        if( isset( $this->options[$option_name] ) ){
            $value = $this->options[$option_name];
        } else {
            $this->set_option_to( $this->options, $option_name, $default_value );
            $value = $this->options[$option_name];
        }
        return $value;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene el nombre real de la opción
    |---------------------------------------------------------------------------------------------------
    */
    public function get_option_name( $name ){
        if( ! Functions::starts_with( $this->prefix, $name ) ){
            return $this->prefix . $name;
        }
        return $name;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Agrega nuevas opciones
    |---------------------------------------------------------------------------------------------------
    */
    public function set_new_default_options( $options = array() ){
        foreach( $options as $name => $value ){
            $this->set_option_to( $this->options, $name, $value );
        }
        return $this->options;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Agrega opción al array de opciones
    |---------------------------------------------------------------------------------------------------
    */
    public function set_option_to( &$array = array(), $option_name = '', $default = '' ){
        //Prefijo es importante para que la importación funcione.
        $option_name = $this->get_option_name( $option_name );
        if( ! $this->xbox ){
            $array[$option_name] = $default;
            return;
        }
        if( strpos( get_class( $this->xbox ), 'Metabox') !== false  ){
            $array[$option_name] = $this->xbox->get_field_value( $option_name, $this->id, $default );
        } else {
            $array[$option_name] = $this->xbox->get_field_value( $option_name, $default );
        }
    }

}
