<?php namespace MasterPopups\Includes;

use Xbox\Includes\CSS;

class Popup extends PopupOptions {
    public $plugin = null;

    public $unique_id = 0;//For tabindex attribute on form fields.(Inline popups)
    private $unique_ids = array();
    public $desktop_elements = array();
    public $mobile_elements = array();
    public $fonts = array();
    public $other_popups = array();
    public $should_display_arg = array();
    public $should_display = false;


    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $options = array() ){
        $this->plugin = Functions::get_plugin_instance();
        parent::__construct( $options );

        $this->add_elements( 'desktop' );
        $this->add_elements( 'mobile' );

        $this->fonts[$this->option( 'sticky-font-family' )][] = '400';
        $this->fonts[$this->option( 'form-submission-font-family' )][] = '400';
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | ID único para el atributo tabindex de los elementos de formulario
    |---------------------------------------------------------------------------------------------------
    */
    public function set_unique_id(){
        $max = 20;
        $i = 0;
        do{
            $i++;
            $new_unique_id = rand(0, $max);
        } while( $i <= $max && in_array( $new_unique_id, $this->unique_ids ) );
        $this->unique_id = $new_unique_id;
        $this->unique_ids[] = $this->unique_id;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Agrega los elementos al popup
    |---------------------------------------------------------------------------------------------------
    */
    public function add_elements( $device = 'desktop' ){
        $elements = (array) $this->xbox->get_field_value( $device . '-elements', $this->id, array() );
        //Adding close-icon element for new popup
        if( Functions::is_empty( $elements ) || ! $this->id ){
            $defaults = Element::default_options( $this->prefix );
            $defaults[$this->prefix . 'device'] = $device;
            $defaults[$this->prefix . $device . '-elements_type'] = $defaults[$this->prefix . 'type'];
            $defaults[$this->prefix . $device . '-elements_name'] = $defaults[$this->prefix . 'name'];
            $defaults[$this->prefix . $device . '-elements_visibility'] = $defaults[$this->prefix . 'visibility'];

            $element = new Element( $defaults, $this, $this->plugin );
            if( $device == 'desktop' ){
                $this->desktop_elements[] = $element;
            } else{
                $this->mobile_elements[] = $element;
            }
        } else{
            foreach( $elements as $index => $options ){
                if( is_array( $options ) ){
                    $options[$this->prefix . 'index'] = $index;
                    $options[$this->prefix . 'device'] = $device;
                    $element = new Element( $options, $this, $this->plugin );
                    if( $device == 'desktop' ){
                        $this->desktop_elements[] = $element;
                    } else{
                        $this->mobile_elements[] = $element;
                    }

                    //Google fonts
                    if( $font_family = $element->option( 'e-font-family' ) ){
                        $this->fonts[$font_family][] = $element->option( 'e-font-weight' );
                    }

                    //Open other popups
                    if( $element->option( 'e-onclick-popup-id' ) && in_array( $element->option( 'e-onclick-action' ), array( 'open-popup', 'open-popup-and-not-close' ) ) ){
                        $this->other_popups[] = $element->option( 'e-onclick-popup-id' );
                    }
                }
            }
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Construye el Popup
    |---------------------------------------------------------------------------------------------------
    */
    public function build(){
        if( ! $this->id ){
            return;
        }

        $this->set_unique_id();

        $return = "\n<!-- MPP:MasterPopups:Popup id=$this->id -->";

        $container_class = $this->get_css_classes_container();
        $popup_class = $this->get_css_classes( 'popup' );
        $popup_data = $this->get_popup_data( 'html' );

        $return .= "<div class='$container_class'>";
        $return .= "<div class='$popup_class' $popup_data>";
        $return .= $this->build_wrap( 'popup' );
        $return .= Popups::build_link_powered_by();
        $return .= "</div>";//.mpp-popup

        //if ($this->option('overlay-show') == 'on' && !$this->is_notification_bar()) {
        if( $this->option( 'overlay-show' ) == 'on' ){
            $return .= "<div id='mpp-overlay-$this->id' class='mpp-overlay'>";
            $return .= "<div class='mpp-overlay-bg'>";
            $return .= "</div>";//.mpp-overlay-bg
            $return .= "</div>";//.mpp-overlay
        }

        if( $this->option( 'sticky-control' ) == 'on' ){
            $sticky_class = 'mpp-sticky';
            $sticky_class .= $this->option( 'sticky-control-vertical' ) == 'on' ? ' mpp-sticky-vertical' : '';
            $return .= "<div id='mpp-sticky-$this->id' class='$sticky_class'>";
            $return .= "<div class='mpp-sticky-control'>";
            if( $this->option( 'sticky-show-icon' ) == 'on' ){
                $icon_class = $this->option( 'sticky-icon' );
                $return .= "<span class='mpp-sticky-icon'><i class='$icon_class'></i></span>";
            }
            $return .= "<span class='mpp-sticky-text'>" . $this->option( 'sticky-text' ) . "</span>";
            $return .= "</div>";//.mpp-sticky-control
            $return .= "</div>";//.mpp-sticky
        }

        $return .= "</div>";//.mpp-container

        $return .= $this->build_style();
        $return .= $this->build_custom_script();

        if( is_admin() && Functions::is_post_page( 'edit' ) ){
            $return .= $this->build_admin_script();
        }

        $return .= "\n<!-- MPP:MasterPopups:Popup id=$this->id End -->";

        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Construye el Popup inline
    |---------------------------------------------------------------------------------------------------
    */
    public function build_wrap( $popup_type = 'popup' ){
        $return = '';
        $return .= "<div class='mpp-wrap mpp-wrap-$this->id'>";
        $return .= "<div class='mpp-content'>";
        if( $this->option( 'use-wp-editor' ) == 'on' ){
            $return .= $this->get_close_icon( $popup_type );
            $return .= "<div class='mpp-content-wp-editor'>";

            $content = $this->option( 'html-code' );
            $content .= do_shortcode( $this->option( 'wp-editor' ) );
            $content = apply_filters( 'mpp_popup_content', $content, $this );
            $content = wpautop( $content );

            $return .= $content;
            $return .= "</div>";//.mpp-content-wp-editor
        } else{
            $return .= "<div class='mpp-content-desktop' data-device='desktop' style='overflow: {$this->option( 'overflow' )}'>";
            $return .= $this->build_elements( 'desktop', $popup_type );
            $return .= "</div>";//.mpp-content-desktop
            $return .= "<div class='mpp-content-mobile' data-device='mobile' style='overflow: {$this->option( 'overflow' )}'>";
            $return .= $this->build_elements( 'mobile', $popup_type );
            $return .= "</div>";//.mpp-content-mobile
        }
        $return .= "</div>";//.mpp-content
        $return .= "</div>";//.mpp-wrap
        $return .= $this->get_link_edit_popup();
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Link papa editar popup
    |---------------------------------------------------------------------------------------------------
    */
    public function get_link_edit_popup(){
        if( current_user_can( 'edit_post', $this->id ) && $this->plugin->settings->option('show-link-edit-popup') == 'on' ){
            return '<a href="' . get_edit_post_link( $this->id ) . '" target="_blank" class="mpp-link-edit-popup"><i class="mpp-icon-pencil"></i></a>';
        }
        return '';
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Construye el botón de cerrar popup
    |---------------------------------------------------------------------------------------------------
    */
    public function get_close_icon( $popup_type = 'popup' ){
        $return = '';
        if( $this->option( 'close-icon-enable' ) == 'off' ){
            return '';
        }
        if( $popup_type == 'inline' && $this->option( 'inline-should-close' ) == 'off' ){
            return;
        }
        $value = $this->option( 'close-icon' );
        if( Functions::ends_with( '.svg', $value ) ){
            $icon = "<img src='$value'>";
        } else{
            $icon = "<i class='$value'></i>";
        }

        $style = '';
        $css = new CSS( ".mpp-popup-$this->id .mpp-close-icon, .mpp-inline-$this->id .mpp-close-icon" );
        $css->prop( 'font-size', CSS::number( $this->option( 'close-icon-size' ), 'px' ) );
        $css->prop( 'width', CSS::number( $this->option( 'close-icon-size' ), 'px' ) );
        $css->prop( 'height', CSS::number( $this->option( 'close-icon-size' ), 'px' ) );
        $css->prop( 'line-height', CSS::number( $this->option( 'close-icon-size' ), 'px' ) );
        $css->prop( 'color', $this->option( 'close-icon-color' ) );
        $style .= $css->build_css();
        $css = new CSS( ".mpp-popup-$this->id .mpp-close-icon:hover, .mpp-inline-$this->id .mpp-close-icon:hover" );
        $css->prop( 'color', $this->option( 'close-icon-color-hover' ) );
        $style .= $css->build_css();

        $return .= "<div class='mpp-close-icon mpp-close-popup'>";
        $return .= $icon;
        $return .= "</div>";
        $return .= "<style>$style</style>";
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Construye el Popup inline
    |---------------------------------------------------------------------------------------------------
    */
    public function build_inline(){
        if( ! $this->id || is_admin() ){
            return;
        }

        $this->set_unique_id();

        $return = "\n<!-- MPP:MasterPopups:Inline id=$this->id -->";

        $container_class = $this->get_css_classes_container();
        $popup_class = $this->get_css_classes( 'inline' );
        $popup_data = $this->get_popup_data( 'html' );

        $return .= "<div class='$container_class'>";
        $return .= "<div class='$popup_class' $popup_data>";
        $return .= $this->build_wrap( 'inline' );
        $return .= "</div>";//.mpp-inline
        $return .= "</div>";//.mpp-container

        $return .= $this->build_style();
        $return .= $this->build_custom_script();

        $return .= "\n<!-- MPP:MasterPopups:Inline id=$this->id End -->";

        return $return;

    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna attributos data del popup
    |---------------------------------------------------------------------------------------------------
    */
    public function get_popup_data( $return = 'html' ){
        $popup_data = array(
            'popup-id' => $this->id,
            'form-type' => $this->option( 'form-submission-type' ),
        );
        if( $return == 'html' ){
            $html = '';
            foreach( $popup_data as $data => $value ){
                $html .= " data-$data='$value'";
            }
            return $html;
        }
        return $popup_data;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build Elements
    |---------------------------------------------------------------------------------------------------
    */
    public function build_elements( $device = 'desktop', $popup_type = 'popup' ){
        $return = '';
        $elements = array();
        if( $device == 'desktop' ){
            $elements = $this->desktop_elements;
        } else{
            $elements = $this->mobile_elements;
        }
        foreach( $elements as $index => $element ){
            $build = true;
            if( $element->index < 0 ){
                $build = false;
            }
            if( $element->type == 'close-icon' ){
                if( ( $popup_type == 'inline' && $this->option( 'inline-should-close' ) == 'off' ) || $this->option( 'content-locker' ) == 'on' ){
                    $build = false;
                }
            }
            if( $build ){
                $return .= $element->build();
            }
        }
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna las opciones para el plugin js
    |---------------------------------------------------------------------------------------------------
    */
    public function get_plugin_options(){
        $position = $this->get_position();
        $mobile_design = ( 'on' == $this->option( 'enable-mobile-design' ) ) ? true : false;
        $full_screen = ( 'on' == $this->option( 'full-screen' ) ) ? true : false;
        $list_data = $this->get_list_data();

        $options = array(
            'id' => $this->id,
            'position' => $position,
            'fullScreen' => $full_screen,
            'mobileDesign' => $mobile_design,
            'ratioSmallDevices' => (float) $this->option( 'ratio-small-devices', 1 ),
            'list' => array(
                'service' => ! empty( $list_data ) ? $list_data['service'] : ''
            ),
            'afterConversion' => array(
                'message' => trim( $this->option( 'message-on-conversion') ),
            ),
            'wpEditor' => array(
                'enabled' => ( 'on' == $this->option( 'use-wp-editor' ) ) ? true : false,
                'autoHeight' => ( 'on' == $this->option( 'wp-editor-auto-height' ) ) ? true : false,
                'padding' => $this->option( 'wp-editor-padding' ),
            ),
            'sound' => array(
                'enabled' => ( 'on' == $this->option( 'play-sound' ) ) ? true : false,
                'delay' => (int) $this->option( 'play-sound-delay' ),
                'src' => $this->option( 'play-sound-source' )
            ),
            'preloader' => array(
                'show' => ( 'on' == $this->option( 'preloader-show' ) ) ? true : false,
                'duration' => (int) $this->option( 'preloader-duration' )
            ),
            'open' => array(
                'delay' => (int) $this->option( 'open-delay' ),
                'duration' => (int) $this->option( 'open-duration' ),
                'animation' => $this->option( 'open-animation' ),
                'disablePageScroll' => ( 'on' == $this->option( 'disable-page-scroll' ) ) ? true : false,
                'loadCounter' => (int) $this->option( 'load-counter' ),
            ),
            'close' => array(
                'delay' => (int) $this->option( 'close-delay' ),
                'duration' => (int) $this->option( 'close-duration' ),
                'animation' => $this->option( 'close-animation' ),
            ),
            'overlay' => array(
                'show' => ( 'on' == $this->option( 'overlay-show' ) ) ? true : false,
                'durationIn' => 300,
                'durationOut' => 250,
            ),
            'notificationBar' => array(
                'fixed' => ( 'on' == $this->option( 'notification-bar-fixed' ) ? true : false ),
                'pushPageDown' => ( 'on' == $this->option( 'notification-bar-push-page-dow' ) ? true : false ),
                'fixedHeaderSelector' => $this->option( 'notification-bar-fixed-header-selector' ),
                'containerPageSelector' => $this->option( 'notification-bar-container-page-selector' ),
            ),
            'sticky' => array(
                'enabled' => ( 'on' == $this->option( 'sticky-control' ) ) ? true : false,
                'initial' => ( 'on' == $this->option( 'sticky-control-initial' ) ) ? true : false,
                'vertical' => ( 'on' == $this->option( 'sticky-control-vertical' ) ) ? true : false,
            ),
            'inline' => array(
                'shouldClose' => ( 'on' == $this->option( 'inline-should-close' ) ) ? true : false,
                'disableTriggers' => ( 'on' == $this->option( 'inline-disable-triggers' ) ) ? true : false,
            ),
            'desktop' => array(
                'device' => 'desktop',
                'browserWidth' => (int) $this->option( 'browser-width' ),
                'browserHeight' => (int) $this->option( 'browser-height' ),
                'width' => (int) $this->option( 'width' ),
                'widthUnit' => $this->option( 'width_unit' ),
                'height' => (int) $this->option( 'height' ),
                'heightUnit' => $this->option( 'height_unit' ),
            ),
            'mobile' => array(
                'device' => 'mobile',
                'browserWidth' => (int) $this->option( 'mobile-browser-width' ),
                'browserHeight' => (int) $this->option( 'browser-height' ),
                'width' => (int) $this->option( 'mobile-width' ),
                'widthUnit' => $this->option( 'mobile-width_unit' ),
                'height' => (int) $this->option( 'mobile-height' ),
                'heightUnit' => $this->option( 'mobile-height_unit' ),
                'resizeOpeningKeyborad' => ( 'on' == $this->option( 'resize-when-opening-keyboard' ) ) ? true : false
            ),
            'callbacks' => array(),
            'triggers' => array(
                'open' => array(
                    'onClick' => array(
                        'event' => $this->option( 'trigger-open-on-click-event' ),
                        'customClass' => trim( $this->option( 'trigger-open-on-click-custom-class' ), '.' ),
                        'preventDefault' => ( 'on' == $this->option( 'trigger-open-on-click-prevent-default' ) ) ? true : false,
                    ),
                    'onLoad' => array(
                        'enabled' => ( 'on' == $this->option( 'trigger-open-on-load' ) ) ? true : false,
                        'delay' => 1000 * (int) $this->option( 'trigger-open-on-load-delay' ),
                    ),
                    'onExit' => array(
                        'enabled' => ( 'on' == $this->option( 'trigger-open-on-exit' ) ) ? true : false,
                    ),
                    'onInactivity' => array(
                        'enabled' => ( 'on' == $this->option( 'trigger-open-on-inactivity' ) ) ? true : false,
                        'period' => 1000 * (int) $this->option( 'trigger-open-on-inactivity-period' ),
                    ),
                    'onScroll' => array(
                        'enabled' => ( 'on' == $this->option( 'trigger-open-on-scroll' ) ) ? true : false,
                        'amount' => CSS::number( $this->option( 'trigger-open-on-scroll-amount' ), $this->option( 'trigger-open-on-scroll-amount_unit' ) ),
                        'afterPost' => ( 'on' == $this->option( 'trigger-open-on-scroll-after-post' ) ) ? true : false,
                        'selector' => $this->option( 'trigger-open-on-scroll-selector' ),
                        'displayed' => false,
                    ),
                ),
                'close' => array(
                    'onClickOverlay' => ( 'on' == $this->option( 'trigger-close-on-click-overlay' ) ) ? true : false,
                    'onEscKeydown' => ( 'on' == $this->option( 'trigger-close-on-esc-keydown' ) ) ? true : false,
                    'automatically' => array(
                        'enabled' => ( 'on' == $this->option( 'trigger-close-automatically' ) ) ? true : false,
                        'delay' => 1000 * (int) $this->option( 'trigger-close-automatically-delay' ),
                    ),
                    'onScroll' => array(
                        'enabled' => ( 'on' == $this->option( 'trigger-close-on-scroll' ) ) ? true : false,
                        'amount' => CSS::number( $this->option( 'trigger-close-on-scroll-amount' ), $this->option( 'trigger-close-on-scroll-amount_unit' ) ),
                    ),
                    'onScrollUp' => array(
                        'enabled' => ( 'on' == $this->option( 'trigger-close-on-scroll-up' ) ) ? true : false,
                        'amount' => CSS::number( $this->option( 'trigger-close-on-scroll-up-amount' ), $this->option( 'trigger-close-on-scroll-up-amount_unit' ) ),
                    ),
                ),
            ),
            'cookies' => array(
                'loadCounter' => array(
                    'name' => $this->plugin->get_cookie_name( 'loadCounter', $this->id ),
                ),
                'onLoad' => array(
                    'name' => $this->plugin->get_cookie_name( 'onLoad', $this->id ),
                    'enabled' => ( 'on' == $this->option( 'cookie-on-load' ) ) ? true : false,
                    'duration' => $this->option( 'cookie-on-load-duration' ),
                    'days' => (int) $this->option( 'cookie-on-load-days' ),
                ),
                'onExit' => array(
                    'name' => $this->plugin->get_cookie_name( 'onExit', $this->id ),
                    'enabled' => ( 'on' == $this->option( 'cookie-on-exit' ) ) ? true : false,
                    'duration' => $this->option( 'cookie-on-exit-duration' ),
                    'days' => (int) $this->option( 'cookie-on-exit-days' ),
                ),
                'onInactivity' => array(
                    'name' => $this->plugin->get_cookie_name( 'onInactivity', $this->id ),
                    'enabled' => ( 'on' == $this->option( 'cookie-on-inactivity' ) ) ? true : false,
                    'duration' => $this->option( 'cookie-on-inactivity-duration' ),
                    'days' => (int) $this->option( 'cookie-on-inactivity-days' ),
                ),
                'onScroll' => array(
                    'name' => $this->plugin->get_cookie_name( 'onScroll', $this->id ),
                    'enabled' => ( 'on' == $this->option( 'cookie-on-scroll' ) ) ? true : false,
                    'duration' => $this->option( 'cookie-on-scroll-duration' ),
                    'days' => (int) $this->option( 'cookie-on-scroll-days' ),
                ),
                'onConversion' => array(
                    'name' => $this->plugin->get_cookie_name( 'onConversion', $this->id ),
                    'enabled' => ( 'on' == $this->option( 'cookie-on-conversion' ) ) ? true : false,
                    'duration' => $this->option( 'cookie-on-conversion-duration' ),
                    'days' => (int) $this->option( 'cookie-on-conversion-days' ),
                ),
            ),
            'custom_cookies' => $this->get_custom_cookies(),
            'contentLocker' => array(
                'cookies' => array(
                    'unlockWithPassword' => $this->plugin->get_cookie_name( 'unlockWithPassword' ),
                    'unlockWithForm' => $this->plugin->get_cookie_name( 'unlockWithForm' ),
                    'duration' => (int) $this->option( 'content-locker-duration' )
                ),
                'enabled' => ( 'on' == $this->option( 'content-locker' ) ) ? true : false,
                'type' => $this->option( 'content-locker-type' ),
                'unlock' => $this->option( 'content-locker-unlock' ),
            )
        );
        return apply_filters( 'mpp_public_popup_options', $options, $this );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna las variables javascript con las opciones del popup
    |---------------------------------------------------------------------------------------------------
    */
    public function get_js_options(){
        $return = '';
        $options = $this->get_plugin_options();
        $return .= "\n\n MPP_POPUP_OPTIONS[$this->id] = " . json_encode( $options ) . ";";
        $before_open = trim( $this->option( 'callback-before-open' ) );
        $after_open = trim( $this->option( 'callback-after-open' ) );
        //$before_close = trim( $this->option( 'callback-before-close' ) );
        $after_close = trim( $this->option( 'callback-after-close' ) );
        $on_submit = trim( $this->option( 'callback-after-form-submission' ) );

        if( Functions::starts_with( 'function(', $before_open ) && Functions::ends_with( '}', $before_open ) ){
            $return .= "\n MPP_POPUP_OPTIONS[$this->id].callbacks.beforeOpen = $before_open;";
        }
        if( Functions::starts_with( 'function(', $after_open ) && Functions::ends_with( '}', $after_open ) ){
            $return .= "\n MPP_POPUP_OPTIONS[$this->id].callbacks.afterOpen = $after_open;";
        }
        // if( Functions::starts_with( 'function(', $before_close ) && Functions::ends_with( '}', $before_close ) ){
        // 	$return .= "\n MPP_POPUP_OPTIONS[$this->id].callbacks.beforeClose = $before_close;";
        // }
        if( Functions::starts_with( 'function(', $after_close ) && Functions::ends_with( '}', $after_close ) ){
            $return .= "\n MPP_POPUP_OPTIONS[$this->id].callbacks.afterClose = $after_close;";
        }
        if( Functions::starts_with( 'function(', $on_submit ) && Functions::ends_with( '}', $on_submit ) ){
            $return .= "\n MPP_POPUP_OPTIONS[$this->id].callbacks.onSubmit = $on_submit;";
        }
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna las opciones necesarias para analizar comportamiento de los popups
    |---------------------------------------------------------------------------------------------------
    */
    public function get_display_options(){
        $return = '';
        $should_display = $this->should_display();

        $options = array(
            'id' => $this->id,
            'is_on' => $this->is_on(),
            'should_display' => $should_display,
            'should_display_target' => $this->should_display_arg['by_target'],
            'should_display_by_publish_settings' => $this->should_display_arg['by_publish_settings'],
            'target' => $this->target_options(),
            'triggers' => $this->trigger_options(),
        );
        $return .= "\n\n MPP_POPUP_DISPLAY_OPTIONS[$this->id] = " . json_encode( $options ) . ";";
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build Admin Script
    |---------------------------------------------------------------------------------------------------
    */
    public function build_admin_script(){
        $return = '';
        $return .= '<';
        $return .= 'script>';
        $return .= "
	(function($){
		jQuery(document).ready(function($){
			$('.mpp-btn-preview-{$this->id}').on('click', function(event){
				event.preventDefault();
				$('.mpp-popup-{$this->id}').MasterPopups(MPP_POPUP_OPTIONS[{$this->id}]);
			});
		});
	})(jQuery);
		";
        return $return . '</script>';
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Construye javascript personalizado
    |---------------------------------------------------------------------------------------------------
    */
    public function build_custom_script(){
        $return = "";
        $custom_js = $this->option( 'custom-javascript' );
        $return .= "<";
        $return .= "script>";
        $return .= "\n//Custom javascript\n";
        $return .= $custom_js;
        $return .= '</script>';
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Construye el css para el popup
    |---------------------------------------------------------------------------------------------------
    */
    public function build_style(){
        $style = '<style>';

        $style .= $this->get_popup_style( ".mpp-popup-$this->id, .mpp-inline-$this->id" );
        $style .= $this->get_wrap_style( ".mpp-wrap-$this->id" );
        $style .= $this->get_content_style( ".mpp-wrap-$this->id .mpp-content" );
        $style .= $this->get_overlay_style( "#mpp-overlay-$this->id .mpp-overlay-bg" );
        $style .= $this->get_sticky_style();
        $style .= $this->get_preloader_style();
        $style .= $this->get_form_submission_style();
        $style .= $this->get_custom_embed_content_style();

        //Placeholder style
        $style .= ".mpp-wrap-$this->id ::-webkit-input-placeholder {
			color: {$this->option( 'placeholder-color' )} !important;
		}";
        $style .= ".mpp-wrap-$this->id ::-moz-placeholder {
			color: {$this->option( 'placeholder-color' )} !important;
		}";
        $style .= ".mpp-wrap-$this->id :-ms-input-placeholder {
			color: {$this->option( 'placeholder-color' )} !important;
		}";
        $style .= ".mpp-wrap-$this->id :-moz-placeholder {
			color: {$this->option( 'placeholder-color' )} !important;
		}";

        if( $this->option( 'use-theme-links-color' ) == 'off' ){
            $temp = '.mpp-element.mpp-element-text-html .mpp-element-content';
            $style .= "$temp > a, $temp > a:hover, $temp > a:focus {
                color: inherit;
            }";
        }

        $style_elements = '';
        foreach( $this->desktop_elements as $element ){
            $style_elements .= $element->build_style();
        }
        foreach( $this->mobile_elements as $element ){
            $style_elements .= $element->build_style();
        }
        $style .= $style_elements;
        $style .= "\n/* Custom CSS */\n";
        $style .= str_replace( '[id]', $this->id, $this->option( 'custom-css' ) );
        $style .= '</style>';
        return $style;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Construye el css para .mpp-popup y mpp-inline
    |---------------------------------------------------------------------------------------------------
    */
    public function get_popup_style( $selector = null, $type = 'css' ){
        $css = new CSS( $selector );
        $css->prop( 'margin-top', CSS::number( $this->option( 'margin-top' ), 'px' ) );
        $css->prop( 'margin-right', CSS::number( $this->option( 'margin-right' ), 'px' ) );
        $css->prop( 'margin-bottom', CSS::number( $this->option( 'margin-bottom' ), 'px' ) );
        $css->prop( 'margin-left', CSS::number( $this->option( 'margin-left' ), 'px' ) );

        if( $type == 'json' ){
            return json_encode( $css->get_props() );
        }
        return $css->build_css();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Construye el css para .mpp-wrap
    |---------------------------------------------------------------------------------------------------
    */
    public function get_wrap_style( $selector = null, $type = 'css' ){
        $css = new CSS( $selector );
        $css->prop( 'background-repeat', $this->option( 'bg-repeat' ) );
        $css->prop( 'background-size', $this->option( 'bg-size' ) );
        $css->prop( 'background-position', $this->option( 'bg-position' ) );
        $css->prop( 'background-image', 'url(' . $this->option( 'bg-image' ) . ')' );
        $css->prop( 'box-shadow', $this->option( 'box-shadow' ) );
        $css->prop( 'border-radius', CSS::number( $this->option( 'border-radius' ), 'px' ) );

        if( $type == 'json' ){
            return json_encode( $css->get_props() );
        }
        return $css->build_css();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Construye el css para .mpp-content
    |---------------------------------------------------------------------------------------------------
    */
    public function get_content_style( $selector = null, $type = 'css' ){
        $css = new CSS( $selector );
        $css->prop( 'background-color', $this->option( 'bg-color' ) );
        $css->prop( 'border-radius', CSS::number( $this->option( 'border-radius' ), 'px' ) );

        if( $type == 'json' ){
            return json_encode( $css->get_props() );
        }
        return $css->build_css();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Construye el css para .overlay
    |---------------------------------------------------------------------------------------------------
    */
    public function get_overlay_style( $selector = null, $type = 'css' ){
        $css = new CSS( $selector );
        $css->prop( 'background-color', $this->option( 'overlay-bg-color' ) );
        $css->prop( 'background-repeat', $this->option( 'overlay-bg-repeat' ) );
        $css->prop( 'background-size', $this->option( 'overlay-bg-size' ) );
        $css->prop( 'background-position', $this->option( 'overlay-bg-position' ) );
        $css->prop( 'background-image', 'url(' . $this->option( 'overlay-bg-image' ) . ')' );
        $css->prop( 'opacity', $this->option( 'overlay-opacity' ) );

        if( $type == 'json' ){
            return json_encode( $css->get_props() );
        }
        return $css->build_css();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Construye el css para .overlay
    |---------------------------------------------------------------------------------------------------
    */
    public function get_sticky_style(){
        $style = '';
        $css = new CSS( "#mpp-sticky-$this->id .mpp-sticky-control" );
        $css->prop( 'width', CSS::number( $this->option( 'sticky-width' ), 'px' ) );
        $css->prop( 'height', CSS::number( $this->option( 'sticky-height' ), 'px' ) );
        $css->prop( 'padding-left', CSS::number( $this->option( 'sticky-padding-x' ), 'px' ) );
        $css->prop( 'padding-right', CSS::number( $this->option( 'sticky-padding-x' ), 'px' ) );
        $css->prop( 'font-size', CSS::number( $this->option( 'sticky-font-size' ), 'px' ) );
        $css->prop( 'color', $this->option( 'sticky-font-color' ) );
        $css->prop( 'font-family', $this->option( 'sticky-font-family' ) );
        $css->prop( 'background-color', $this->option( 'sticky-bg-color' ) );
        $css->prop( 'background-size', $this->option( 'sticky-bg-size' ) );
        $css->prop( 'background-position', $this->option( 'sticky-bg-position' ) );
        $css->prop( 'background-image', 'url(' . $this->option( 'sticky-bg-image' ) . ')' );
        $css->prop( 'line-height', CSS::number( $this->option( 'sticky-height' ), 'px' ) );

        $style .= $css->build_css();

        $css = new CSS( "#mpp-sticky-$this->id .mpp-sticky-icon" );
        $css->prop( 'background-color', $this->option( 'sticky-bg-icon' ) );
        $style .= $css->build_css();

        return $style;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Construye el css para .preloader
    |---------------------------------------------------------------------------------------------------
    */
    public function get_preloader_style(){
        $style = '';
        $css = new CSS();
        $css->prop( 'background', $this->option( 'preloader-color-1' ) );
        $css = $css->build_css();

        $style .= ".mpp-wrap-$this->id .mpp-preloader.mpp-preloader-animation .mpp-preloader-spinner1 { $css }";
        $style .= "#mpp-overlay-$this->id .mpp-preloader.mpp-preloader-animation .mpp-preloader-spinner1 { $css }";

        $css = new CSS();
        $css->prop( 'background', $this->option( 'preloader-color-2' ) );
        $css = $css->build_css();
        $style .= ".mpp-wrap-$this->id .mpp-preloader.mpp-preloader-animation .mpp-preloader-spinner2 { $css }";
        $style .= "#mpp-overlay-$this->id .mpp-preloader.mpp-preloader-animation .mpp-preloader-spinner2 { $css }";

        return $style;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Construye el css para el mensaje después de enviar el formulario
    |---------------------------------------------------------------------------------------------------
    */
    public function get_form_submission_style(){
        $style = '';
        $css = new CSS();
        $css->prop( 'font-size', CSS::number( $this->option( 'form-submission-font-size' ), 'px' ) );
        $css->prop( 'color', $this->option( 'form-submission-font-color' ) );
        $css->prop( 'font-family', $this->option( 'form-submission-font-family' ) );
        $css->prop( 'border-width', CSS::number( $this->option( 'form-submission-border-width' ), 'px' ) );
        $css->prop( 'border-color', $this->option( 'form-submission-border-color' ) );
        $css->prop( 'border-style', $this->option( 'form-submission-border-style' ) );
        $css->prop( 'background-color', $this->option( 'form-submission-bg-color' ) );
        $css->prop( 'background-image', 'url(' . $this->option( 'form-submission-bg-image' ) . ')' );
        $css->prop( 'border-radius', CSS::number( $this->option( 'border-radius' ), 'px' ) );

        $css = $css->build_css();
        $style .= ".mpp-wrap-$this->id .mpp-processing-form, .mpp-wrap-$this->id .mpp-conversion { $css }";

        //Footer font size
        $font_size = "font-size: {$this->option( 'form-submission-footer-font-size' )}px";
        $style .= ".mpp-wrap-$this->id .mpp-processing-form .mpp-processing-form-footer .mpp-back-to-form { $font_size }";
        $style .= ".mpp-wrap-$this->id .mpp-processing-form .mpp-processing-form-footer .mpp-close-popup { $font_size }";
        $style .= ".mpp-wrap-$this->id .mpp-conversion .mpp-conversion-footer .mpp-back-to-form { $font_size }";
        $style .= ".mpp-wrap-$this->id .mpp-conversion .mpp-conversion-footer .mpp-close-popup { $font_size }";

        if( $this->option( 'form-submission-footer-enable' ) == 'off' ){
            $style .= ".mpp-container-$this->id .mpp-processing-form .mpp-processing-form-footer { display:none !important; }";
            $style .= ".mpp-container-$this->id .mpp-conversion .mpp-conversion-footer { display:none !important; }";
        }

        //Color on Success
        $color = "color: {$this->option( 'form-submission-font-color-success' ) }";
        $style .= ".mpp-container-$this->id .mpp-form-sent-ok .mpp-processing-form .mpp-processing-form-content { $color }";

        return $style;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Construye el css para el contenido de "Wp editor" y "HTML Code"
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_embed_content_style(){
        $style = '';
        $css = new CSS( ".mpp-wrap-$this->id .mpp-content-wp-editor" );
        if( $this->option( 'wp-editor-enable-font-color' ) == 'on' ){
            $css->prop( 'color', $this->option( 'wp-editor-font-color' ) );
        }
        if( $this->option( 'wp-editor-enable-font-size' ) == 'on' ){
            $css->prop( 'font-size', CSS::number( $this->option( 'wp-editor-font-size' ), 'px' ) );
        }
        $style .= $css->build_css();
        return $style;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Get other popups
    |---------------------------------------------------------------------------------------------------
    */
    public function get_other_popups(){
        $this->other_popups[] = $this->option( 'form-submission-ok-open-popup-id' );
        return $this->other_popups;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Si el popup está activado
    |---------------------------------------------------------------------------------------------------
    */
    public function is_on(){
        return 'on' == $this->status;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Si el popup está activado
    |---------------------------------------------------------------------------------------------------
    */
    public function get_position(){
        if( 'on' == $this->option( 'full-screen' ) && ! $this->is_notification_bar() ){
            return 'full-screen';
        }
        return $this->option( 'position' );
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Si el popup es una barra de notificación
    |---------------------------------------------------------------------------------------------------
    */
    public function is_notification_bar(){
        return $this->option( 'position' ) == 'top-bar' || $this->option( 'position' ) == 'bottom-bar';
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Si el popups se debe mostrar
    |---------------------------------------------------------------------------------------------------
    */
    public function should_display(){
        if( empty( $this->should_display_arg ) ){
            $target = new Target( $this->plugin, $this );
            $display_by_publish_settings = $this->should_display_by_publish_settings();
            //Siempre mostrar el popup para "content locker" que usa los shortcodes
            if( $this->option( 'content-locker' ) == 'on' && $this->option( 'content-locker-type' ) == 'shortcode' ){
                $display_by_target = true;
            } else {
                $display_by_target = $target->should_display_popup();
            }
            $this->should_display_arg = array(
                'by_target' => $display_by_target,
                'by_publish_settings' => $display_by_publish_settings,
            );
            $display = $this->should_display_arg['by_target'] && $this->should_display_arg['by_publish_settings'];
            $this->should_display = apply_filters( 'mpp_should_display_popup', $display, $this );
        }
        return $this->should_display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si el popup se debe mostrar usando los ajustes de publicación
    |---------------------------------------------------------------------------------------------------
    */
    public function should_display_by_publish_settings(){
        $timezone = Functions::get_timezone();
        $publish = true;
        if( $this->option( 'publish-on' ) == 'date' ){
            $full_date_publish = $this->option( 'publish-on-date' ) . ' ' . $this->option( 'publish-on-time' );
            $date_publish = new \DateTime( $full_date_publish, $timezone );
            $date_now = new \DateTime( 'now', $timezone );
            if( $date_now < $date_publish ){
                $publish = false;
            }
        }

        $stop = false;
        if( $this->option( 'publish-stop' ) == 'date' ){
            $full_date_stop = $this->option( 'publish-stop-date' ) . ' ' . $this->option( 'publish-stop-time' );
            $date_stop = new \DateTime( $full_date_stop, $timezone );
            $date_now = new \DateTime( 'now', $timezone );
            if( $date_now >= $date_stop ){
                $stop = true;
            }
        }

        return $publish && ! $stop;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Get popup trigger
    |---------------------------------------------------------------------------------------------------
    */
    public function get_trigger_content( $content = '', $atts = array() ){
        $return = '';
        $tag = 'span';
        if( in_array( $atts['tag'], array( 'a', 'span', 'div', 'button' ) ) ){
            $tag = $atts['tag'];
        }
        $return .= "<$tag class='mpp-trigger-popup mpp-trigger-popup-$this->id {$atts['class']}'>";
        $return .= do_shortcode( $content );
        $return .= "</$tag>";
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Clases css del popup
    |---------------------------------------------------------------------------------------------------
    */
    public function get_css_classes( $popup_type = 'popup' ){
        $popup_class = array();
        $popup_class[] = 'mpp-box';
        $popup_class[] = 'mpp-position-' . $this->get_position();
        $popup_class[] = ltrim( $this->option( 'attr-class' ), '.' );

        if( $popup_type == 'inline' ){
            $popup_class[] = 'mpp-inline';
            $popup_class[] = 'mpp-inline-' . $this->id;
        } else {
            $popup_class[] = 'mpp-popup';
            $popup_class[] = 'mpp-popup-' . $this->id;
        }
        if( $this->option( 'content-locker' ) == 'on' ){
            $popup_class[] = 'mpp-content-locker-popup';
        }
        $popup_class = implode( ' ', $popup_class );
        return $popup_class;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Clases css del container
    |---------------------------------------------------------------------------------------------------
    */
    public function get_css_classes_container(){
        $container_class = array();
        $container_class[] = 'mpp-container';
        $container_class[] = 'mpp-container-' . $this->id;
        $container_class[] = 'mpp-container-position-' . $this->get_position();
        $container_class = implode( ' ', $container_class );
        return $container_class;
    }

}
