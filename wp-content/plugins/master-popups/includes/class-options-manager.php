<?php namespace MasterPopups\Includes;

class OptionsManager {
    public $plugin;
    public $mb_settings = '';
    public $mb_popup_editor = '';
    public $mb_audience_editor = '';
    protected static $instance = null;

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    private function __construct( $plugin ){
        $this->plugin = $plugin;
        $this->mb_settings = $this->plugin->arg( 'xbox_ids', 'settings' );
        $this->mb_popup_editor = $this->plugin->arg( 'xbox_ids', 'popup-editor' );
        $this->mb_audience_editor = $this->plugin->arg( 'xbox_ids', 'audience-editor' );

        add_action( 'xbox_init', array( $this, 'create_metabox_admin_page' ) );

        if( ! $this->plugin->should_continue() ){
            return;
        }

        add_action( 'xbox_init', array( $this, 'create_metabox_popup_editor' ) );
        add_action( 'xbox_init', array( $this, 'create_metabox_audience_lists' ) );
        add_action( 'add_meta_boxes', array( $this, 'remove_unnecessary_metaboxes' ), 99, 2 );
        add_filter( 'get_user_option_screen_layout_' . $this->plugin->post_types['popups'], array( $this, 'screen_layout' ) );
        add_filter( 'get_user_option_screen_layout_' . $this->plugin->post_types['lists'], array( $this, 'screen_layout' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'localize_scripts' ) );
        add_filter( 'xbox_filter_data_for_save', array( $this, 'parse_elements_data' ), 10, 1 );
        add_action( 'post_edit_form_tag', array( $this, 'allow_upload_files_metaboxes' ) );

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

    public static function get_instance( $plugin = null ){
        if( null === self::$instance ){
            self::$instance = new self( $plugin );
        }
        return self::$instance;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Restaura opciones de los elementos que han sido serializados desde jquery para evitar: max_input_vars
    |---------------------------------------------------------------------------------------------------
    */
    public function parse_elements_data( $data = array() ){
        if( isset( $data['mpp_desktop-elements'] ) && is_string( $data['mpp_desktop-elements'] ) ){
            $parsed = ParseStr::parse( $data['mpp_desktop-elements'] );
            $data['mpp_desktop-elements'] = is_array( $parsed ) ? $parsed['mpp_desktop-elements'] : array();
        }
        if( isset( $data['mpp_mobile-elements'] ) && is_string( $data['mpp_mobile-elements'] ) ){
            $parsed = ParseStr::parse( $data['mpp_mobile-elements'] );
            $data['mpp_mobile-elements'] = is_array( $parsed ) ? $parsed['mpp_mobile-elements'] : array();
        }
        return $data;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Construye el editor de popups
    |---------------------------------------------------------------------------------------------------
    */
    public function build_popup_editor(){
        if( ! Functions::is_editing_post_type( $this->plugin->post_types['popups'] ) ){
            return '';
        }
        $popup = Popups::get( Functions::post_id() );
        if( ! $popup ){
            $popup = new Popup();
        }
        $McEditor = McEditor::get_instance( $this->plugin, $this );
        return $McEditor->build( $popup );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Crea página principal de opciones
    |---------------------------------------------------------------------------------------------------
    */
    public function create_metabox_admin_page( $hook ){
        $save_button_text = __( 'Save Changes', 'masterpopups' );
        $options = array(
            'id' => $this->mb_settings,
            'title' => __( 'General Settings', 'masterpopups' ),
            'menu_title' => __( 'General Settings', 'masterpopups' ),
            'parent' => $this->plugin->main_menu_item,
            'capability' => 'manage_options',
            'header' => array(
                'desc' => '',
                //'icon' => '<img src="' .MPP_URL . 'assets/admin/images/lolo-for-options.png">',
                'icon' => '',
                'submit-buttons-sticky' => true,
            ),
            'class' => 'ampp',
            'form_options' => array(
                'show_save_button' => true,
                'save_button_text' => $save_button_text . '<i class="xbox-icon xbox-icon-save"></i>',
            ),
            'footer' => $this->plugin->arg( 'name' ) . ' v' . MPP_VERSION,
        );
        $xbox = xbox_new_admin_page( $options );
        include MPP_DIR . 'includes/options/general-settings/general-settings.php';
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Crea metabox para popups
    |---------------------------------------------------------------------------------------------------
    */
    public function create_metabox_popup_editor( $hook ){
        $popup_id = Functions::post_id();
        $save_button_name = 'publish';
        $save_button_text = __( 'Publish', 'masterpopups' );
        if( Functions::is_post_page( 'edit' ) ){
            $save_button_name = 'save';
            $save_button_text = __( 'Update', 'masterpopups' );
        }

        $peview_button = "";
        $peview_button_text = __( 'Preview', 'masterpopups' );
        if( Functions::is_post_page( 'edit' ) ){
            $peview_button = "<a href='#'' class='xbox-btn xbox-btn-teal mpp-btn-preview-{$popup_id}'>{$peview_button_text}</a>";
        }

        $popup_shortcode = '[mpp_popup id="' . $popup_id . '"]Open popup[/mpp_popup]';
        $inline_shortcode = '[mpp_inline id="' . $popup_id . '"]';
        $shortcodes = '<div class="ampp ampp-popup-shortcodes">';
        $shortcodes .= '<strong>Shortcodes: </strong>';
        $shortcodes .= "<input class='ampp-input-selector' readonly onfocus='this.select()' value='$popup_shortcode' style='width: 360px; margin-left: 10px; margin-right: 20px'>";
        $shortcodes .= "<input class='ampp-input-selector' readonly onfocus='this.select()' value='$inline_shortcode' style='width: 210px;'>";
        $shortcodes .= '</div>';

        $options = array(
            'id' => $this->mb_popup_editor,
            'title' => 'Popup Editor',
            'post_types' => array( $this->plugin->post_types['popups'] ),
            'fields_prefix' => $this->plugin->arg( 'prefix' ),
            'class' => 'ampp mpp',
            'header' => array(
                'icon' => '',
                //'icon' => '<img src="' .MPP_URL . 'assets/admin/images/lolo-for-options.png">',
                'desc' => '',
                'submit-buttons-sticky' => true,
            ),
            'form_options' => array(
                'show_save_button' => Settings::plugin_status(),
                //'show_reset_button' => true,
                'save_button_id' => 'save-popup',
                'save_button_name' => $save_button_name,
                'save_button_text' => $save_button_text . '<i class="xbox-icon xbox-icon-save"></i>',
                'save_button_class' => '',
                'reset_button_text' => __( 'Reset to Defaults', 'xbox' ),
                'reset_button_class' => '',
                'insert_before_buttons' => $peview_button
            ),
            'import_settings' => array(
                'update_uploads_url' => false,//No es necesario que sea true porque las imágenes se guardan en la ruta del plugin, no en wp-content/uploads
                'update_plugins_url' => true,//True para que actualize las rutas para las imágenes
                'show_authentication_fields' => true,//Porque algunos sitios web requieren autenticación para importar las plantilas
            ),
            'data_' => get_option( base64_decode( 'bXBwLXBsdWdpbi1zdGF0dXM=' ) ),
            'insert_before' => $shortcodes,
            'footer' => $this->plugin->arg( 'name' ) . ' v' . MPP_VERSION,
        );
        $xbox = xbox_new_metabox( $options );
        $xbox = mpp_add_fields_popup_editor( $xbox, $this );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Crea metabox para listas de audiencia
    |---------------------------------------------------------------------------------------------------
    */
    public function create_metabox_audience_lists( $hook ){
        $save_button_name = 'publish';
        $save_button_text = __( 'Publish', 'masterpopups' );
        if( Functions::is_post_page( 'edit' ) ){
            $save_button_name = 'save';
            $save_button_text = __( 'Update', 'masterpopups' );
        }

        $options = array(
            'id' => $this->mb_audience_editor,
            'title' => 'Audience List',
            'post_types' => array( $this->plugin->post_types['lists'] ),
            'fields_prefix' => $this->plugin->arg( 'prefix' ),
            'class' => 'ampp mpp',
            'header' => array(
                //'icon' => '<img src="' .MPP_URL . 'assets/admin/images/lolo-for-options.png">',
                'icon' => '',
                'desc' => __( 'This interface allows you to create a list to store your subscribers.', 'masterpopups' ),
                'submit-buttons-sticky' => true,
            ),
            'form_options' => array(
                'show_save_button' => true,
                //'show_reset_button' => true,
                'save_button_id' => 'save-popup',
                'save_button_name' => $save_button_name,
                'save_button_text' => $save_button_text . '<i class="xbox-icon xbox-icon-save"></i>',
                'save_button_class' => '',
                'reset_button_text' => __( 'Reset to Defaults', 'xbox' ),
                'reset_button_class' => '',
            ),
            'footer' => $this->plugin->arg( 'name' ) . ' v' . MPP_VERSION,
        );
        $xbox = xbox_new_metabox( $options );

        $list = new Lista();//Se utiliza en las opciones
        include MPP_DIR . 'includes/options/audience/audience.php';
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Remove Unnecessary Metaboxes
    |---------------------------------------------------------------------------------------------------
    | http://stackoverflow.com/questions/28279831/how-do-i-remove-all-the-metaboxes-for-a-custom-post-type
    */
    public function remove_unnecessary_metaboxes( $post_type, $post ){
        global $wp_meta_boxes;

        if( ! in_array( $post_type, $this->plugin->post_types ) ){
            return false;
        }

        /** Metaboxes que no se eliminarán */
        $exceptions = array(
            'submitdiv',
            $this->mb_popup_editor,
            $this->mb_audience_editor,
        );

        /** Loop through each page key of the '$wp_meta_boxes' global... */
        if( ! empty( $wp_meta_boxes ) ) : foreach( $wp_meta_boxes as $page => $page_boxes ) :
            /** Loop through each contect... */
            if( ! empty( $page_boxes ) ) : foreach( $page_boxes as $context => $box_context ) :
                /** Loop through each type of meta box... */
                if( ! empty( $box_context ) ) : foreach( $box_context as $box_type ) :
                    /** Loop through each individual box... */
                    if( ! empty( $box_type ) ) : foreach( $box_type as $id => $box ) :
                        /** Check to see if the meta box should be removed... */
                        if( ! in_array( $id, $exceptions ) ) :
                            /** Remove the meta box */
                            remove_meta_box( $id, $page, $context );
                        endif;
                    endforeach;
                    endif;
                endforeach;
                endif;
            endforeach;
            endif;
        endforeach;
        endif;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Screen layout to 1 column
    |---------------------------------------------------------------------------------------------------
    */
    public function screen_layout(){
        return 1;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Permite la carga de archivos desde los metaboxes. Requerido para la función Import
    |---------------------------------------------------------------------------------------------------
    */
    public function allow_upload_files_metaboxes(){
        global $post;
        if( $this->plugin->post_types['popups'] != $post->post_type ){
            return;
        }
        echo ' enctype="multipart/form-data" encoding="multipart/form-data"';
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Tipos de post personalizados para las opciones "Display Target"
    |---------------------------------------------------------------------------------------------------
    */
    public function get_not_builtin_post_types(){
        $excludes = array( $this->plugin->post_types['popups'] );
        $post_types = get_post_types( array( 'public' => true, '_builtin' => false, 'show_in_nav_menus' => true ), 'objects' );
        foreach( $excludes as $post_type ){
            if( isset( $post_types[$post_type] ) ){
                unset( $post_types[$post_type] );
            }
        }
        return $post_types;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Agrega los tipos de elementos a JS con todos sus datos
    |---------------------------------------------------------------------------------------------------
    */
    public function localize_scripts(){
        wp_localize_script( 'mpp-admin', 'MPP_TYPES', Types::get_all() );
        wp_localize_script( 'mpp-admin', 'MPP_SERVICES', Services::get_all() );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Html para agregar los botones de tipos de elementos
    |---------------------------------------------------------------------------------------------------
    */
    public function get_html_type_elements( $group = 'all' ){
        $return = '';
        $types = Types::get_all();
        $filtered = array();
        $label = '';
        if( $group == 'all' ){
            $filtered = $types;
        } else{
            if( $group == 'form' ){
                foreach( $types as $type => $data ){
                    if( Functions::starts_with( 'field_', $type ) || Functions::starts_with( 'custom_field_', $type ) ){
                        $filtered[$type] = $data;
                    }
                }
            } else if( $group == 'basic' ){
                foreach( $types as $type => $data ){
                    if( ! Functions::starts_with( 'field_', $type ) && ! Functions::starts_with( 'custom_field_', $type ) ){
                        $filtered[$type] = $data;
                    }
                }
            }
        }

        $class = 'xbox-btn xbox-btn-normal xbox-btn-teal xbox-custom-add';
        if( $group == 'all' ){
            $class .= ' xbox-add-group-item';
        }
        foreach( $filtered as $type => $data ){
            $return .= "<a class='$class' data-item-type='{$type}'><i class='{$data['icon']}'></i>{$data['text']}</a>";
        }
        return $return;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Botones html para el tipo de elemento "Button"
    |---------------------------------------------------------------------------------------------------
    */
    public function get_html_button_styles(){
        $return = '';
        $return .= '<h4>' . __( 'Button styles', 'masterpopups' ) . '</h4><div class="xbox-field-description ampp-padding-bottom-10">' . __( 'Click on any button to choose styles. Any previously defined style will be replaced.', 'masterpopups' ) . '</div>';
        $return .= "
			<a class='mpp-btn mpp-btn-green' data-bg-color='#05B489'>Download</a>
			<a class='mpp-btn mpp-btn-blue' data-bg-color='#2287E1'>Download</a>
			<a class='mpp-btn mpp-btn-dark' data-bg-color='#464D57'>Download</a>
			<a class='mpp-btn mpp-btn-red' data-bg-color='#E56464'>Download</a>
			<a class='mpp-btn mpp-btn-yellow' data-bg-color='#F5CA2D'>Download</a>
			";

        $return .= "
			<a class='mpp-btn mpp-btn-green mpp-btn-shadow' data-bg-color='#05B489'>Download</a>
			<a class='mpp-btn mpp-btn-blue mpp-btn-shadow' data-bg-color='#2287E1'>Download</a>
			<a class='mpp-btn mpp-btn-dark mpp-btn-shadow' data-bg-color='#464D57'>Download</a>
			<a class='mpp-btn mpp-btn-red mpp-btn-shadow' data-bg-color='#E56464'>Download</a>
			<a class='mpp-btn mpp-btn-yellow mpp-btn-shadow' data-bg-color='#F5CA2D'>Download</a>
			";

        $return .= "
			<a class='mpp-btn mpp-btn-outline' data-bg-color='rgba(255,255,255,0.0)'>Download</a>
			<a class='mpp-btn mpp-btn-outline mpp-btn-radius' data-bg-color='rgba(255,255,255,0.0)'>Download</a>
			<a class='mpp-btn mpp-btn-outline mpp-btn-rounded' data-bg-color='rgba(255,255,255,0.0)'>Download</a>
			";

        $return .= "
			<a class='mpp-btn mpp-btn-green mpp-btn-rounded' data-bg-color='#05B489'>Download</a>
			<a class='mpp-btn mpp-btn-blue mpp-btn-rounded' data-bg-color='#2287E1'>Download</a>
			<a class='mpp-btn mpp-btn-dark mpp-btn-rounded' data-bg-color='#464D57'>Download</a>
			<a class='mpp-btn mpp-btn-red mpp-btn-rounded' data-bg-color='#E56464'>Download</a>
			<a class='mpp-btn mpp-btn-yellow mpp-btn-rounded' data-bg-color='#F5CA2D'>Download</a>
			";

        $return .= "
			<a class='mpp-btn mpp-btn-green mpp-btn-rounded mpp-btn-shadow' data-bg-color='#05B489'>Download</a>
			<a class='mpp-btn mpp-btn-blue mpp-btn-rounded mpp-btn-shadow' data-bg-color='#2287E1'>Download</a>
			<a class='mpp-btn mpp-btn-dark mpp-btn-rounded mpp-btn-shadow' data-bg-color='#464D57'>Download</a>
			<a class='mpp-btn mpp-btn-red mpp-btn-rounded mpp-btn-shadow' data-bg-color='#E56464'>Download</a>
			<a class='mpp-btn mpp-btn-yellow mpp-btn-rounded mpp-btn-shadow' data-bg-color='#F5CA2D'>Download</a>
			";
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Lista de botones para agregar servicios para la integración de servicios
    |---------------------------------------------------------------------------------------------------
    */
    public function get_html_integration_buttons(){
        $return = '';
        foreach( Services::get_all() as $service => $data ){
            $return .= "<a class='xbox-btn xbox-btn-teal xbox-add-group-item xbox-custom-add' data-item-type='{$service}'>{$data['text']}</a>";
        }
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna los servicios integrados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_integrated_services( $only_connected = true, $all_data = false ){
        $xbox = xbox_get( $this->mb_settings );
        $value = $xbox->get_field_value( 'integrated-services', array() );
        $integrated_services = array();
        foreach( $value as $index => $service ){
            if( ! $only_connected || $service['service-status'] == 'on' ){
                if( $all_data ){
                    $integrated_services[$service['integrated-services_type']] = $service;
                } else{
                    $integrated_services[$service['integrated-services_type']] = $service['integrated-services_name'];
                }
            }
        }
        return $integrated_services;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Lista de servicios para la integración de servicios
    |---------------------------------------------------------------------------------------------------
    */
    public function get_html_services_list(){
        $return = '';
        $value = $this->get_integrated_services( false );
        $integrated_services = array();

        foreach( $value as $type => $name ){
            $integrated_services[] = $type;
        }

        foreach( Services::get_all() as $service => $data ){
            $return .= "<div class='ampp-service-item ampp-clearfix' data-item-type='{$service}'>";
            $return .= "<div class='ampp-service-item-image'>";
            $return .= "<img src='{$data['image_url']}'>";
            $return .= "</div>";
            $return .= "<div class='ampp-service-item-info'>";
            $return .= "<h4>{$data['text']}</h4>";
            if( in_array( $service, $integrated_services ) ){
                $return .= "<a class='xbox-btn' data-item-type='{$service}'><i class='xbox-icon xbox-icon-check'></i>" . __( 'Integrated', 'masterpopups' ) . "</a>";
            } else{
                $return .= "<a class='xbox-btn xbox-btn-teal ampp-integrate-service' data-item-type='{$service}'><i class='xbox-icon xbox-icon-arrow-down'></i>" . __( 'Integrate', 'masterpopups' ) . "</a>";
            }

            $return .= "</div>";
            $return .= "</div>";
        }
        return $return;
    }


}