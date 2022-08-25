<?php namespace MasterPopups\Includes\Synchronizations;

use MasterPopups\Includes\Functions;
use MasterPopups\Includes\Lista;

class WpComments extends BaseSync {
    public $plugin;
    protected static $instance = null;
    protected $checkbox_label_class = 'mpp-sync-label-wp-comments';
    protected $meta_key = 'masterpopups_should_subscribe';

    /*
	|---------------------------------------------------------------------------------------------------
	| Constructor
	|---------------------------------------------------------------------------------------------------
	*/
    protected function __construct( $plugin, $masterpopups = null ){
        $this->plugin = $plugin;
        parent::__construct( $masterpopups, 'wp-comments' );

        $this->hooks();
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

    public static function get_instance( $plugin, $masterpopups = null ){
        if( null === self::$instance ){
            self::$instance = new self( $plugin, $masterpopups );
        }
        return self::$instance;
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Plugin hooks
	|---------------------------------------------------------------------------------------------------
	*/
    private function hooks(){
        add_filter( 'comment_form_submit_field', array( $this, 'add_checkbox' ), 10, 2 );
        add_action( 'comment_post', array( $this, 'form_submit' ), 11, 3 );
        add_action( 'wp_set_comment_status', array( $this, 'wp_set_comment_status' ), 11, 2 );
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Verifica si se debe sincronizar
	|---------------------------------------------------------------------------------------------------
	*/
    public function should_sync( $comment_ID, $comment_status = '0' ){
        if( ! $this->is_valid_list() ){
            return false;
        }
        $sync = $this->is_sync_enabled();
        if( $sync ){
            $only_approved = $this->option('only-approved') == 'on';
            $is_approved = $comment_status === 'approve' || $comment_status == '1';
            if( ! $only_approved || ( $only_approved && $is_approved ) ){
                $checked = true;
                if( $sync && $this->should_add_checkbox() ){
                    $checked = get_comment_meta( $comment_ID, $this->meta_key, true );
                }
                $sync = !!$checked;
            } else {
                $sync = false;
            }
        }

        //Evitar sincronizar al autor del post
        if( $sync ){
            $commentdata = get_comment( $comment_ID, ARRAY_A );
            $post_author_id = get_post_field( 'post_author', $commentdata['comment_post_ID'] );
            if( $post_author_id == $commentdata['user_id'] ){
                $sync = false;
            }
        }

        return $sync;
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Al actualizar el estado de un comentario
	|---------------------------------------------------------------------------------------------------
	*/
    public function wp_set_comment_status( $comment_ID, $comment_status ){
        if( ! $this->should_sync( $comment_ID, $comment_status ) ){
            return;
        }

        $commentdata = get_comment( $comment_ID, ARRAY_A );

        //Sincronizar
        $this->sync_data( $commentdata );
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Al crear un nuevo comentario
	|---------------------------------------------------------------------------------------------------
	*/
    public function form_submit( $comment_ID, $comment_approved, $commentdata ){
        //Guardar meta value para verificar si se activó el checkbox en "wp_set_comment_status"
        update_comment_meta( $comment_ID, $this->meta_key, isset( $_POST[$this->checkbox_name] ) );

        if( ! $this->should_sync( $comment_ID, $comment_approved ) ){
            return;
        }

        //Sincronizar
        $this->sync_data( $commentdata );
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Sincronizar
	|---------------------------------------------------------------------------------------------------
	*/
    public function sync_data( $commentdata = array() ){
        //Email
        if( isset( $commentdata['comment_author_email'] ) ){
            $this->set_field( 'email', $commentdata['comment_author_email'] );
            unset( $commentdata['comment_author_email'] );
        } else if( isset( $commentdata['your-email'] ) ){
            $this->set_field( 'email', $commentdata['your-email'] );
            unset( $commentdata['your-email'] );
        }

        //First name, Last name
        if( isset( $commentdata['comment_author'] ) ){
            $your_name = preg_split( '/[\s]+/', $commentdata['comment_author'], 2 );
            $this->set_field( 'first_name', array_shift( $your_name ) );
            $this->set_field( 'last_name', array_shift( $your_name ) );
            unset( $commentdata['comment_author'] );
        }

        //Sincronizar
        $this->sync();
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Agrega checkbox automáticamente al formulario
	|---------------------------------------------------------------------------------------------------
	*/
    public function add_checkbox( $submit_field, $args ){
        if( Functions::is_woocommerce_activated() && is_product() ){
            return $submit_field;
        }
        if( $this->should_add_checkbox() ){
            $checkbox = $this->get_checkbox();
            return $checkbox . $submit_field;
        }
        return $submit_field;
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Checkbox
	|---------------------------------------------------------------------------------------------------
	*/
    public function get_checkbox( $class = '' ){
        $checkbox = '';
        $text = $this->option( 'checkbox-text' );
        $checkbox .= "<p class='mpp-sync-field comment-form-{$this->checkbox_label_class}'>";
        $checkbox .= "<label class='{$this->checkbox_label_class}' style='cursor: pointer;'>";
        $checkbox .= "<input type='checkbox' name='{$this->checkbox_name}' id='{$this->checkbox_name}' class='$class'>";
        $checkbox .= $text;
        $checkbox .= "</label>";
        $checkbox .= "</p>";

        return $checkbox;
    }

}