<?php namespace MasterPopups\Includes;

use Xbox\Includes\CSS;

class McEditor {
    public $plugin = null;
    public $popup = null;
    protected static $instance = null;
    protected $google_fonts = array();

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    private function __construct( $plugin = null, $options_manager = null ){
        $this->plugin = $plugin;
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

    public static function get_instance( $plugin = null, $options_manager = null ){
        if( null === self::$instance ){
            self::$instance = new self( $plugin, $options_manager );
        }
        return self::$instance;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build popup editor
    |---------------------------------------------------------------------------------------------------
    */
    public function build( $popup ){
        if( ! $popup ){
            return '';
        }

        $this->popup = $popup;

        $return = '';
        $return .= "<div id='mc-wrap'>";

        $return .= $this->build_header();
        $return .= "<div id='mc'>";
        $return .= $this->build_rule();
        $return .= $this->build_panels();
        $return .= "<div id='mc-viewport' tabindex='500'>";
        $return .= $this->build_device();
        $return .= "</div>";//#mc-viewport
        $return .= "<div id='mc-resizable-handler' class='ui-resizable-handle ui-resizable-s'><i class='xbox-icon xbox-icon-ellipsis-h'></i></div>";
        $return .= "</div>";//#mc
        //$return .= $this->build_footer();
        $return .= $this->build_context_menu();
        $return .= "</div>";//#mc-wrap
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Rules
    |---------------------------------------------------------------------------------------------------
    */
    public function build_rule(){
        $return = '';
        $return .= "<div id='mc-x-rule' class='mc-rule xbox-noselect'>";
        for( $i = -15; $i < 30; $i++ ){
            $unit = $i * 100;
            $return .= "<li><span>{$unit}</span></li>";
        }
        $return .= "</div>";
        $return .= "<div id='mc-y-rule' class='mc-rule xbox-noselect'>";
        for( $i = -5; $i < 10; $i++ ){
            $unit = $i * 100;
            $return .= "<li><span>{$unit}</span></li>";
        }
        $return .= "</div>";
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build panels
    |---------------------------------------------------------------------------------------------------
    */
    public function build_panels(){
        $return = '';
        $return .= "<span id='mc-open-settings' class='mc-open-panel'><i class='xbox-icon xbox-icon-wrench'></i></span>";
        $return .= "<span class='mc-open-types mc-open-types-top mc-open-panel'><i class='xbox-icon xbox-icon-backward'></i></span>";
        $return .= "<span class='mc-open-types mc-open-types-bottom mc-open-panel'><i class='xbox-icon xbox-icon-backward'></i></span>";

        $return .= "<div id='mc-types' class='mc-is-open mc-panel mc-panel-blue---'>";
        $return .= "<i class='mpp-icon mpp-icon-spinner mpp-icon-spin ampp-loader'></i>";
        $return .= "<div class='mc-section'>";
        $return .= "<h4><i class='xbox-icon xbox-icon-caret-right'></i>Basic elements</h4>";
        $return .= $this->plugin->options_manager->get_html_type_elements( 'basic' );
        $return .= "</div>";//.mc-section
        $return .= "<div class='mc-section'>";
        $return .= "<h4><i class='xbox-icon xbox-icon-caret-right'></i>Form elements</h4>";
        $return .= $this->plugin->options_manager->get_html_type_elements( 'form' );
        $return .= "</div>";//.mc-section
        $return .= "</div>";//#mc-types

        $return .= "<div id='mc-settings' class='mc-panel mc-panel-blue---'>";
//        $return .= "<div class='mc-section mc-section-guides'>";
//        $return .= "<h4><i class='xbox-icon xbox-icon-caret-right'></i>Guides</h4>";
//        $return .= "<div class='mc-fieldset mc-has-icheck'>";
//        $return .= "<div class='mc-control'>";
//        $return .= "<label><input type='checkbox' class='mc-checkbox' name='mc-show-guides' checked>Show guides</label>";
//        $return .= "</div>";
//        $return .= "</div>";//.mc-fieldset
//        $return .= "</div>";//.mc-section

        $return .= "<div class='mc-section mc-section-shortcuts'>";
        $return .= "<h4><i class='xbox-icon xbox-icon-caret-right'></i>Keyboard Shortcuts</h4>";
        $return .= "<div class='mc-fieldset'>";
        $return .= "<label class='mc-label'>" . __( 'Move Element', 'masterpopups' ) . ":</label>";
        $return .= "<div class='mc-control'>";
        $return .= "Shift + Arrow keys";
        $return .= "</div>";
        $return .= "</div>";//.mc-fieldset
        $return .= "<div class='mc-fieldset'>";
        $return .= "<label class='mc-label'>" . __( 'Duplicate Element', 'masterpopups' ) . ":</label>";
        $return .= "<div class='mc-control'>";
        $return .= "(Ctrl | Command) + (D | J)";
        $return .= "</div>";
        $return .= "</div>";//.mc-fieldset
        $return .= "<div class='mc-fieldset'>";
        $return .= "<label class='mc-label'>" . __( 'Remove Element', 'masterpopups' ) . ":</label>";
        $return .= "<div class='mc-control'>";
        $return .= "Backspace or Delete";
        $return .= "</div>";
        $return .= "</div>";//.mc-fieldset

        $return .= "</div>";//.mc-section

        $return .= "</div>";//#mc-settings
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Context menu
    |---------------------------------------------------------------------------------------------------
    */
    public function build_context_menu(){
        $items = array(
            array(
                'name' => __( 'Copy Style', 'masterpopups' ),
                'icon' => 'xbox-icon xbox-icon-eyedropper',
                'class' => 'mc-copy-style',
                'action' => 'copy-style',
                'auto-close' => true,
            ),
            array(
                'name' => __( 'Paste Style', 'masterpopups' ),
                'icon' => 'xbox-icon xbox-icon-pencil-square-o',
                'class' => 'mc-paste-style',
                'action' => 'paste-style',
                'auto-close' => true,
            ),
            array(
                'name' => __( 'Duplicate', 'masterpopups' ),
                'icon' => 'xbox-icon xbox-icon-clone',
                'class' => 'mc-duplicate-element',
                'action' => 'duplicate-element',
                'auto-close' => true,
            ),
//            array(
//                'name' => __( 'Font', 'masterpopups' ),
//                'icon' => 'xbox-icon xbox-icon-trash',
//                'class' => '',
//                'action' => '',
//                'auto-close' => true,
//                'submenu' => array(
//                    array(
//                        'name' => __( 'Align Left', 'masterpopups' ),
//                        'icon' => 'xbox-icon xbox-icon-eyedropper',
//                        'class' => '',
//                        'action' => 'align-left',
//                        'auto-close' => false,
//                    ),
//                    array(
//                        'name' => __( 'Align Center', 'masterpopups' ),
//                        'icon' => 'xbox-icon xbox-icon-pencil-square-o',
//                        'class' => '',
//                        'action' => 'align-center',
//                        'auto-close' => false,
//                    ),
//                    array(
//                        'name' => __( 'Align Right', 'masterpopups' ),
//                        'icon' => 'xbox-icon xbox-icon-pencil-square-o',
//                        'class' => '',
//                        'action' => 'align-right',
//                        'auto-close' => false,
//                    ),
//                )
//            ),
//            array(
//                'name' => __( 'Otro Element', 'masterpopups' ),
//                'icon' => 'xbox-icon xbox-icon-trash',
//                'class' => '',
//                'action' => '',
//                'auto-close' => false,
//            ),
            array(
                'name' => __( 'Remove', 'masterpopups' ),
                'icon' => 'xbox-icon xbox-icon-trash',
                'class' => 'mc-remove-element',
                'action' => 'remove-element',
                'auto-close' => true
            ),
        );
        $return = '';
        $return .= "<nav id='mc-context-menu'>";
        $return .= "<ul class='mc-ctx-menu'>";
        foreach( $items as $key => $item ){
            $return .= "<li class='mc-ctx-item mc-{$item['action']}'>";
            $return .= "<div class='mc-ctx-item-inner'>";
            $return .= "<div class='mc-ctx-item-link {$item['class']}' data-action='{$item['action']}' data-auto-close='{$item['auto-close']}'>";
            $return .= "<i class='mc-ctx-item-icon {$item['icon']}'></i>";
            $return .= "<span class='mc-ctx-name'>{$item['name']}</span>";
            $return .= "</div>";
            if( isset( $item['submenu'] ) ){
                $return .= "<i class='mc-ctx-item-icon mc-ctx-arrow-right xbox-icon xbox-icon-arrow-right'></i>";
            }
            $return .= "</div>";
            if( isset( $item['submenu'] ) ){
                $return .= "<ul class='mc-ctx-submenu'>";

                foreach( $item['submenu'] as $k => $subitem ){
                    $return .= "<li class='mc-ctx-item'>";
                    $return .= "<div class='mc-ctx-item-inner'>";
                    $return .= "<div class='mc-ctx-item-link {$subitem['class']}' data-action='{$subitem['action']}' data-auto-close='{$subitem['auto-close']}'>";
                    $return .= "<i class='mc-ctx-item-icon {$subitem['icon']}'></i>";
                    $return .= "<span class='mc-ctx-name'>{$subitem['name']}</span>";
                    $return .= "</div>";
                    $return .= "</div>";
                    $return .= "</li>";
                }
                $return .= "</ul>";
            }
            $return .= "</li>";
        }
        $return .= "</ul>";
        $return .= "</nav>";

        return $return;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Header
    |---------------------------------------------------------------------------------------------------
    */
    public function build_header(){
        $return = '';
        $return .= "<div id='mc-header'>";
        $return .= "<div id='mc-top-tools'>";

        $return .= "<span class='mc-icon-tool' data-action='distribute-heights' title='" . esc_html__( 'Distribute vertically', 'masterpopups' ) . "'></span>";
        $return .= "<span class='mc-icon-tool' data-action='distribute-widths' title='" . esc_html__( 'Distribute horizontally', 'masterpopups' ) . "'></span>";

        $return .= "<span class='mc-divider-tool'></span>";

        $return .= "<span class='mc-icon-tool mc-icon-can-disable mc-disabled' data-action='alignment-left' title='" . esc_html__( 'Align left', 'masterpopups' ) . "'></span>";
        $return .= "<span class='mc-icon-tool mc-icon-can-disable mc-disabled' data-action='alignment-center-x' title='" . esc_html__( 'Align center X', 'masterpopups' ) . "'></span>";
        $return .= "<span class='mc-icon-tool mc-icon-can-disable mc-disabled' data-action='alignment-right' title='" . esc_html__( 'Align right', 'masterpopups' ) . "'></span>";


        $return .= "<span class='mc-divider-tool'></span>";

        $return .= "<span class='mc-icon-tool mc-icon-can-disable mc-disabled' data-action='alignment-top' title='" . esc_html__( 'Align top', 'masterpopups' ) . "'></span>";
        $return .= "<span class='mc-icon-tool mc-icon-can-disable mc-disabled' data-action='alignment-center-y' title='" . esc_html__( 'Align center Y', 'masterpopups' ) . "'></span>";
        $return .= "<span class='mc-icon-tool mc-icon-can-disable mc-disabled' data-action='alignment-bottom' title='" . esc_html__( 'Align bottom', 'masterpopups' ) . "'></span>";

        $return .= "<span class='mc-divider-tool'></span>";

        $return .= "<span class='mc-icon-tool' data-action='max-width' title='" . esc_html__( 'Use max width', 'masterpopups' ) . "'></span>";
        $return .= "<span class='mc-icon-tool' data-action='max-height' title='" . esc_html__( 'Use max height', 'masterpopups' ) . "'></span>";

        $return .= "<span class='mc-divider-tool'></span>";

        $return .= "<span class='mc-icon-tool' data-action='position-left' title='" . esc_html__( 'Position left', 'masterpopups' ) . "'></span>";
        $return .= "<span class='mc-icon-tool' data-action='position-right' title='" . esc_html__( 'Position right', 'masterpopups' ) . "'></span>";
        $return .= "<span class='mc-icon-tool' data-action='position-top' title='" . esc_html__( 'Position top', 'masterpopups' ) . "'></span>";
        $return .= "<span class='mc-icon-tool' data-action='position-bottom' title='" . esc_html__( 'Position bottom', 'masterpopups' ) . "'></span>";
        $return .= "<span class='mc-icon-tool' data-action='position-center-x' title='" . esc_html__( 'Position center X', 'masterpopups' ) . "'></span>";
        $return .= "<span class='mc-icon-tool' data-action='position-center-y' title='" . esc_html__( 'Position center Y', 'masterpopups' ) . "'></span>";

        $return .= "<span class='mc-divider-tool'></span>";

        $return .= "<span class='mc-icon-tool' data-action='full-width' title='" . esc_html__( 'Full width', 'masterpopups' ) . "'></span>";
        $return .= "<span class='mc-icon-tool' data-action='full-height' title='" . esc_html__( 'Full height', 'masterpopups' ) . "'></span>";

        $return .= "<span class='mc-divider-tool'></span>";
        $return .= "<span class='mc-icon-tool mc-disabled' data-action='undo' title='" . esc_html__( 'Undo', 'masterpopups' ) . "'></span>";

        $return .= "</div>";//#mc-top-tools
        $return .= "</div>";//#mc-header
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Footer
    |---------------------------------------------------------------------------------------------------
    */
    public function build_footer(){
        $return = '';
        $return .= "<div id='mc-footer'>";
        $return .= "</div>";
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build Device
    |---------------------------------------------------------------------------------------------------
    */
    public function build_device(){
        $return = '';
        $css = new CSS();
        $css->prop( 'width', CSS::number( $this->popup->option( 'browser-width' ), 'px' ) );
        $css->prop( 'height', CSS::number( $this->popup->option( 'browser-height' ), 'px' ) );
        $device_style = $css->build_css();

        $return .= "<div id='mc-device' data-device='desktop' style='{$device_style}'>";
        $return .= "<div id='mc-device-caption' class='noselect'>" . __( 'Browser', 'masterpopups' ) . "</div>";
        $return .= "<div id='mc-device-resizable-handler' class='ui-resizable-handle ui-resizable-s'></div>";
        $return .= $this->build_popup();
        $return .= "</div>";
        return $return;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Build Popup
    |---------------------------------------------------------------------------------------------------
    */
    public function build_popup(){
        $return = '';
        $popup_class[] = 'ampp-popup';
        if( 'on' == $this->popup->option( 'full-screen' ) ){
            $popup_class[] = 'ampp-full-screen';
        }
        $popup_class[] = 'ampp-position-' . $this->popup->option( 'position' );
        $popup_class = implode( ' ', $popup_class );

        $css = new CSS();
        $style = $css->build_css( array(
            'width' => CSS::number( $this->popup->option( 'width' ), $this->popup->option( 'width_unit' ) ),
            'height' => CSS::number( $this->popup->option( 'height' ), $this->popup->option( 'height_unit' ) ),
            'margin-top' => CSS::number( $this->popup->option( 'margin-top' ), 'px' ),
            'margin-right' => CSS::number( $this->popup->option( 'margin-right' ), 'px' ),
            'margin-bottom' => CSS::number( $this->popup->option( 'margin-bottom' ), 'px' ),
            'margin-left' => CSS::number( $this->popup->option( 'margin-left' ), 'px' ),
        ) );
        $wrap_style = $this->popup->get_wrap_style();
        $content_style = $this->popup->get_content_style();
        $overflow = $this->popup->option( 'overflow' );

        $return .= "<div class='$popup_class' style='$style'>";
        $return .= "<div class='ampp-wrap' style='$wrap_style'>";
        $return .= "<div id='mc-desktop-content' class='ampp-content ampp-desktop-content' style='$content_style; overflow: $overflow;'>";
        //$return .= "<div class='ui-selectable-helper' style='width:200px; height: 100px;'></div>";
        $return .= $this->build_elements( 'desktop' );
        $return .= "</div>";//.ampp-elements
        $return .= "<div id='mc-mobile-content' class='ampp-content ampp-mobile-content' style='$content_style; overflow: $overflow;'>";
        $return .= $this->build_elements( 'mobile' );
        $return .= "</div>";//.ampp-elements
        $return .= "</div>";//ampp-wrap
        $return .= "</div>";//.ampp-popup
        $return .= $this->build_overlay();

        //Google fonts
        //$href = Functions::make_url_google_fonts( $this->google_fonts, array_values( Assets::local_fonts() ) );
        //$return .= '<link class="mpp-google-fonts" href="' . $href . '" rel="stylesheet" type="text/css">';

        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build Overlay
    |---------------------------------------------------------------------------------------------------
    */
    public function build_overlay(){
        $return = '';
        $overlay_style = $this->popup->get_overlay_style();
        $return .= "<div class='ampp-overlay' style='{$overlay_style}'>";
        $return .= "</div>";//.ampp-popup
        return $return;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Build Elements
    |---------------------------------------------------------------------------------------------------
    */
    public function build_elements( $device = 'desktop' ){
        $return = '';
        $elements = array();
        if( $device == 'desktop' ){
            $elements = $this->popup->desktop_elements;
        } else{
            $elements = $this->popup->mobile_elements;
        }

        foreach( $elements as $index => $element ){
            $content_style = str_replace( '!important', '', $element->get_content_style( '.ampp-el-content' ) );
            $data_style = str_replace( '!important', '', $element->get_content_style( '.ampp-el-content', 'json' ) );
            $return .= "<div class='ampp-element ampp-element-{$element->index} mpp-element-{$element->type} mc-element ' style='{$element->get_style()}' data-index='{$element->index}' data-type='{$element->type}' data-device='$device' tabindex='1'>";
            $return .= "<div class='ampp-el-content' style='$content_style' data-style='$data_style'>";
            $return .= $element->get_content( 'admin' );
            $return .= "</div>";//.ampp-el-content
            $return .= $this->get_controls();
            $return .= "</div>";//.ampp-element

            //Google fonts
            $this->google_fonts[] = $element->option( 'e-font-family' );
        }

        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Controles para los elementos
    |---------------------------------------------------------------------------------------------------
    */
    public function get_controls(){
        $return = '';
        $return .= "<div class='mc-controls'>";
        $return .= "<span class='mc-drag-element' title=''><i class='xbox-icon xbox-icon-arrows'></i></span>";

        $return .= "<div class='mc-position-element'>";
        $return .= "X: <span class='mc-position-element-left'></span>";
        $return .= "Y: <span class='mc-position-element-top'></span>";
        $return .= "</div>";

        $return .= "<span class='mc-loading'><i class='mpp-icon mpp-icon-spinner mpp-icon-spin'></i></span>";

        $return .= "</div>";
        return $return;
    }


}