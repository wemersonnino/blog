<?php namespace MasterPopups\Includes;

class ListOptions extends BaseOptions {
    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    protected function __construct( $options = array() ){
        $xbox_id = $this->plugin->arg( 'xbox_ids', 'audience-editor' );
        $prefix = $this->plugin->arg( 'prefix' );
        parent::__construct( $xbox_id, $prefix, $options );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Establece las opciones guardadas en la bd o sus valores por defecto
    |---------------------------------------------------------------------------------------------------
    */
    protected function set_options( $initial_options = array() ){
        $default_options = array(
            $this->prefix . 'id' => $this->id,
            $this->prefix . 'status' => 'on',
        );

        //Set prefix
        foreach( $initial_options as $key => $val ){
            $initial_options[$this->prefix . $key] = $val;
            unset( $initial_options[$key] );
        }

        $options = wp_parse_args( $initial_options, $default_options );
        $this->set_option_to( $options, 'service', 'master_popups' );
        $this->set_option_to( $options, 'list-status', 'on' );
        $this->set_option_to( $options, 'helper-id', '' );
        $this->set_option_to( $options, 'account-id', '' );
        $this->set_option_to( $options, 'list-id', '' );
        $this->set_option_to( $options, 'segment-id', '' );
        $this->set_option_to( $options, 'form-id', '' );
        $this->set_option_to( $options, 'double-opt-in', 'off' );
        $this->set_option_to( $options, 'template-id', '' );
        $this->set_option_to( $options, 'redirection-url', '' );
        $this->set_option_to( $options, 'allow-data-update', 'on' );

        $this->set_option_to( $options, 'subscribers', array() );
        $this->set_option_to( $options, 'total-subscribers', 0 );

        do_action( 'mpp_list_before_set_options', $options, $this );
        $this->options = $options;
        do_action( 'mpp_list_after_set_options', $this->options, $this );

        $this->options = apply_filters( 'mpp_list_options', $this->options, $this );

        return $this->options;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Lista
    |---------------------------------------------------------------------------------------------------
    */
    public function get_list_data(){
        $list = get_post( $this->option( 'audience-list' ) );
        if( ! $list ){
            return array();
        }
        return array(
            'service' => get_post_meta( $list->ID, $this->prefix . 'service', true ),
        );
    }

}
