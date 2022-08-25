<?php namespace MasterPopups\Includes;


class Cookie {

    /*
    |---------------------------------------------------------------------------------------------------
    | Validate cookie name
    |---------------------------------------------------------------------------------------------------
    */
    public static function validate_cookie_name( $name = '' ){
        //No se permiten espacios en blanco
        //Php convierte puntos (.) en _ guiones bajos a los nombres de las cookies
        $name = str_replace( array( ' ', '.', ), array( '', '_' ), $name );
        return $name;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Set cookie
    |---------------------------------------------------------------------------------------------------
    */
    public static function set( $name, $value = '', $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false ){
        setcookie( self::validate_cookie_name( $name ), $value, $expire, $path, $domain, $secure, $httponly );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Set cookie encoded
    |---------------------------------------------------------------------------------------------------
    */
    public static function set_encoded( $name, $value = '', $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false ){
        $value = json_encode( $value, JSON_UNESCAPED_SLASHES );
        setcookie( self::validate_cookie_name( $name ), $value, $expire, $path, $domain, $secure, $httponly );
        return $value;
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Get cookie value
	|---------------------------------------------------------------------------------------------------
	*/
    public static function get( $name, $decode = false ){
        $name = self::validate_cookie_name( $name );
        $cookie_value = isset( $_COOKIE[$name] ) ? $_COOKIE[$name] : '';
        if( $decode && isset( $_COOKIE[$name] ) ){
            $cookie_value = json_decode( stripslashes( $_COOKIE[$name] ), true );
        }
        return $cookie_value;
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Delete cookie
	|---------------------------------------------------------------------------------------------------
	*/
    public static function delete( $name, $path = '/' ){
        $name = self::validate_cookie_name( $name );
        if( isset( $_COOKIE[$name] ) ){
            unset( $_COOKIE[$name] );
            setcookie( $name, '', time() - 3600, $path );
        }
    }


}