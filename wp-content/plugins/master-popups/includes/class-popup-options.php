<?php namespace MasterPopups\Includes;

class PopupOptions extends BaseOptions {
    public $options_manager = null;
    public $settings = null;

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    protected function __construct( $options = array() ){
        $this->options_manager = $this->plugin->options_manager;
        $xbox_id = $this->plugin->arg( 'xbox_ids', 'popup-editor' );
        $prefix = $this->plugin->arg( 'prefix' );
        parent::__construct( $xbox_id, $prefix, $options );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Establece las opciones guardadas en la bd o sus valores por defecto
    |---------------------------------------------------------------------------------------------------
    */
    //Esta función se llama tres veces en admin
    //1.- class-options-manager.php -> build_popup_editor
    //2.- class-popups.php -> add_popups_to_admin_footer
    //3.- class-popups.php -> load_resources_in_footer

    //Esta función se llama dos veces en public
    //1.- class-popups.php -> add_popups
    //2.- class-popups.php -> load_resources_in_footer
    protected function set_options( $initial_options = array() ){
        $default_options = array(
            $this->prefix . 'id' => $this->id,
            $this->prefix . 'status' => 'on',
            $this->prefix . 'type' => 'modal',
        );

        //Set prefix
        foreach( $initial_options as $key => $val ){
            $initial_options[$this->prefix . $key] = $val;
            unset( $initial_options[$key] );
        }

        $default_options = wp_parse_args( $initial_options, $default_options );
        $default_options = wp_parse_args( $this->general_popup_options(), $default_options );
        $default_options = wp_parse_args( $this->overlay_options(), $default_options );
        $default_options = wp_parse_args( $this->sticky_options(), $default_options );
        $default_options = wp_parse_args( $this->publish_options(), $default_options );
        $default_options = wp_parse_args( $this->trigger_options(), $default_options );
        $default_options = wp_parse_args( $this->target_options(), $default_options );
        $default_options = wp_parse_args( $this->notification_bar_options(), $default_options );
        $default_options = wp_parse_args( $this->content_locker_options(), $default_options );
        $default_options = wp_parse_args( $this->form_submission_options(), $default_options );
        $default_options = wp_parse_args( $this->advanced_options(), $default_options );

        do_action( 'mpp_popup_before_set_options', $default_options, $this );
        $this->options = $default_options;
        $this->status = $this->option( 'status' );
        do_action( 'mpp_popup_after_set_options', $this->options, $this );

        $this->options = apply_filters( 'mpp_popup_options', $this->options, $this );

        return $this->options;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Opciones generales del popup
    |---------------------------------------------------------------------------------------------------
    */
    public function general_popup_options(){
        $options = array();
        $this->set_option_to( $options, 'position', 'middle-center' );
        $this->set_option_to( $options, 'width', 640 );
        $this->set_option_to( $options, 'height', 360 );
        $this->set_option_to( $options, 'width_unit', 'px' );
        $this->set_option_to( $options, 'height_unit', 'px' );
        $this->set_option_to( $options, 'full-screen', 'off' );
        $this->set_option_to( $options, 'browser-width', 1000 );//1000
        $this->set_option_to( $options, 'browser-height', 580 );//480

        //Background
        $this->set_option_to( $options, 'bg-color', 'rgba(255,255,255,0.9)' );
        $this->set_option_to( $options, 'bg-repeat', 'no-repeat' );
        $this->set_option_to( $options, 'bg-size', 'cover' );
        $this->set_option_to( $options, 'bg-position', 'center center' );
        $this->set_option_to( $options, 'bg-image', '' );

        //Animations
        $this->set_option_to( $options, 'open-animation', 'mpp-zoomIn' );
        $this->set_option_to( $options, 'open-delay', '0' );
        $this->set_option_to( $options, 'open-duration', 800 );
        $this->set_option_to( $options, 'close-animation', 'mpp-zoomOut' );
        $this->set_option_to( $options, 'close-delay', '0' );
        $this->set_option_to( $options, 'close-duration', 700 );

        //Mobile Design
        $this->set_option_to( $options, 'enable-mobile-design', 'off' );
        $this->set_option_to( $options, 'mobile-browser-width', 600 );
        $this->set_option_to( $options, 'mobile-width', 560 );
        $this->set_option_to( $options, 'mobile-height', 315 );
        $this->set_option_to( $options, 'mobile-width_unit', 'px' );
        $this->set_option_to( $options, 'mobile-height_unit', 'px' );

        //Wordpress editor
        $this->set_option_to( $options, 'use-wp-editor', 'off' );
        $this->set_option_to( $options, 'html-code', '' );
        $this->set_option_to( $options, 'wp-editor', '' );
        $this->set_option_to( $options, 'wp-editor-auto-height', 'on' );
        $this->set_option_to( $options, 'wp-editor-padding', '20px 36px' );
        $this->set_option_to( $options, 'wp-editor-enable-font-color', 'off' );
        $this->set_option_to( $options, 'wp-editor-font-color', 'rgba(68, 68, 68, 1)' );
        $this->set_option_to( $options, 'wp-editor-enable-font-size', 'off' );
        $this->set_option_to( $options, 'wp-editor-font-size', '15' );

        $this->set_option_to( $options, 'close-icon-enable', 'on' );
        $this->set_option_to( $options, 'close-icon', 'mppfic-close-cancel-circular-2' );
        $this->set_option_to( $options, 'close-icon-size', '21' );
        $this->set_option_to( $options, 'close-icon-color', 'rgba(0,0,0,0.8)' );
        $this->set_option_to( $options, 'close-icon-color-hover', 'rgba(0,0,0,1)' );


        //Additional Settings
        $this->set_option_to( $options, 'border-radius', '0' );
        $this->set_option_to( $options, 'box-shadow', '0px 0px 16px 4px rgba(0,0,0,0.5)' );
        $this->set_option_to( $options, 'margin-top', '0' );
        $this->set_option_to( $options, 'margin-right', 'auto' );
        $this->set_option_to( $options, 'margin-bottom', '0' );
        $this->set_option_to( $options, 'margin-left', 'auto' );
        $this->set_option_to( $options, 'placeholder-color', 'rgba(134,134,134,1)' );
        $this->set_option_to( $options, 'overflow', 'visible' );
        $this->set_option_to( $options, 'disable-page-scroll', 'off' );
        $this->set_option_to( $options, 'disclaimer-enabled', 'off' );
        $this->set_option_to( $options, 'ratio-small-devices', '1' );
        $this->set_option_to( $options, 'use-theme-links-color', 'on' );
        $this->set_option_to( $options, 'resize-when-opening-keyboard', 'on' );
        $this->set_option_to( $options, 'play-sound', 'off' );
        $this->set_option_to( $options, 'play-sound-delay', '-10' );
        $this->set_option_to( $options, 'play-sound-source', '' );

        //Inline Popup
        $this->set_option_to( $options, 'inline-should-close', 'off' );
        $this->set_option_to( $options, 'inline-disable-triggers', 'off' );

        //Custom cookies
        $this->set_option_to( $options, 'custom-cookies', array() );


        return $options;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Opciones de Overlay
    |---------------------------------------------------------------------------------------------------
    */
    public function overlay_options(){
        $options = array();
        $this->set_option_to( $options, 'overlay-show', 'on' );
        $this->set_option_to( $options, 'overlay-bg-color', 'rgba(0, 1, 5, 0.8)' );
        $this->set_option_to( $options, 'overlay-bg-repeat', 'no-repeat' );
        $this->set_option_to( $options, 'overlay-bg-size', 'cover' );
        $this->set_option_to( $options, 'overlay-bg-position', 'center center' );
        $this->set_option_to( $options, 'overlay-bg-image', '' );
        $this->set_option_to( $options, 'overlay-opacity', '1' );

        //Preloader
        $this->set_option_to( $options, 'preloader-show', 'on' );
        $this->set_option_to( $options, 'preloader-duration', 1000 );
        $this->set_option_to( $options, 'preloader-color-1', 'rgba(0,221,210,1)' );
        $this->set_option_to( $options, 'preloader-color-2', 'rgba(62,153,255,1)' );
        return $options;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Opciones Sticky
    |---------------------------------------------------------------------------------------------------
    */
    public function sticky_options(){
        $options = array();
        $this->set_option_to( $options, 'sticky-control', 'off' );
        $this->set_option_to( $options, 'sticky-control-initial', 'off' );
        $this->set_option_to( $options, 'sticky-control-vertical', 'off' );
        $this->set_option_to( $options, 'sticky-width', 'auto' );
        $this->set_option_to( $options, 'sticky-height', 40 );
        $this->set_option_to( $options, 'sticky-padding-x', 15 );
        $this->set_option_to( $options, 'sticky-font-size', '15' );
        $this->set_option_to( $options, 'sticky-font-color', 'rgba(255,255,255,1)' );
        $this->set_option_to( $options, 'sticky-font-family', 'Roboto' );
        $this->set_option_to( $options, 'sticky-text', 'Open popup' );
        $this->set_option_to( $options, 'sticky-show-icon', 'on' );
        $this->set_option_to( $options, 'sticky-bg-icon', 'rgba(32,95,240,0.8)' );
        $this->set_option_to( $options, 'sticky-icon', 'mpp-icon-chevron-up' );
        $this->set_option_to( $options, 'sticky-bg-color', 'rgba(0,0,0,0.8)' );
        $this->set_option_to( $options, 'sticky-bg-size', 'cover' );
        $this->set_option_to( $options, 'sticky-bg-position', 'center center' );
        $this->set_option_to( $options, 'sticky-bg-image', '' );
        return $options;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Opciones publish
    |---------------------------------------------------------------------------------------------------
    */
    public function publish_options(){
        $options = array();
        $this->set_option_to( $options, 'publish-on', 'now' );
        $this->set_option_to( $options, 'publish-on-date', '' );
        $this->set_option_to( $options, 'publish-on-time', '' );

        $this->set_option_to( $options, 'publish-stop', 'never' );
        $this->set_option_to( $options, 'publish-stop-date', '' );
        $this->set_option_to( $options, 'publish-stop-time', '' );

        return $options;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Opciones trigger
    |---------------------------------------------------------------------------------------------------
    */
    public function trigger_options(){
        $options = array();
        $this->set_option_to( $options, 'load-counter', '0' );
        $this->set_option_to( $options, 'trigger-open-on-click-event', 'click' );
        $this->set_option_to( $options, 'trigger-open-on-click-custom-class', 'your-custom-class' );
        $this->set_option_to( $options, 'trigger-open-on-click-prevent-default', 'on' );

        $this->set_option_to( $options, 'trigger-open-on-load', 'off' );
        $this->set_option_to( $options, 'trigger-open-on-load-delay', 1 );
        $this->set_option_to( $options, 'cookie-on-load', 'off' );
        $this->set_option_to( $options, 'cookie-on-load-duration', 'days' );
        $this->set_option_to( $options, 'cookie-on-load-days', 7 );

        $this->set_option_to( $options, 'trigger-open-on-exit', 'off' );
        $this->set_option_to( $options, 'cookie-on-exit', 'on' );
        $this->set_option_to( $options, 'cookie-on-exit-duration', 'current_session' );
        $this->set_option_to( $options, 'cookie-on-exit-days', 7 );

        $this->set_option_to( $options, 'trigger-open-on-inactivity', 'off' );
        $this->set_option_to( $options, 'trigger-open-on-inactivity-period', 60 );
        $this->set_option_to( $options, 'cookie-on-inactivity', 'off' );
        $this->set_option_to( $options, 'cookie-on-inactivity-duration', 'current_session' );
        $this->set_option_to( $options, 'cookie-on-inactivity-days', 7 );

        $this->set_option_to( $options, 'trigger-open-on-scroll', 'off' );
        $this->set_option_to( $options, 'trigger-open-on-scroll-amount', '0' );
        $this->set_option_to( $options, 'trigger-open-on-scroll-amount_unit', '%' );
        $this->set_option_to( $options, 'trigger-open-on-scroll-after-post', 'off' );
        $this->set_option_to( $options, 'trigger-open-on-scroll-selector', '' );

        $this->set_option_to( $options, 'cookie-on-scroll', 'off' );
        $this->set_option_to( $options, 'cookie-on-scroll-duration', 'days' );
        $this->set_option_to( $options, 'cookie-on-scroll-days', 7 );

        $this->set_option_to( $options, 'trigger-open-display-inline-in', array() );


        //Close triggers
        $this->set_option_to( $options, 'trigger-close-on-click-overlay', 'on' );
        $this->set_option_to( $options, 'trigger-close-on-esc-keydown', 'on' );
        $this->set_option_to( $options, 'trigger-close-automatically', 'off' );
        $this->set_option_to( $options, 'trigger-close-automatically-delay', 10 );
        $this->set_option_to( $options, 'trigger-close-on-scroll', 'off' );
        $this->set_option_to( $options, 'trigger-close-on-scroll-amount', '10' );
        $this->set_option_to( $options, 'trigger-close-on-scroll-amount_unit', '%' );
        $this->set_option_to( $options, 'trigger-close-on-scroll-up', 'off' );
        $this->set_option_to( $options, 'trigger-close-on-scroll-up-amount', '10' );
        $this->set_option_to( $options, 'trigger-close-on-scroll-up-amount_unit', '%' );

        return $options;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Opciones target
    |---------------------------------------------------------------------------------------------------
    */
    public function target_options(){
        $options = array();
        $this->set_option_to( $options, 'display-on-all-site', 'on' );
        $this->set_option_to( $options, 'display-on-homepage', 'on' );
        $this->set_option_to( $options, 'display-on-archive', 'on' );
        $this->set_option_to( $options, 'display-on-page', 'on' );
        $this->set_option_to( $options, 'display-on-page-include', '' );
        $this->set_option_to( $options, 'display-on-page-exclude', '' );
        $this->set_option_to( $options, 'display-on-post', 'on' );
        $this->set_option_to( $options, 'display-on-post-include', '' );
        $this->set_option_to( $options, 'display-on-post-exclude', '' );
        $this->set_option_to( $options, 'display-on-taxonomy-category', 'on' );
        $this->set_option_to( $options, 'display-on-posts-with-taxonomy-category', 'off' );
        $this->set_option_to( $options, 'display-on-taxonomy-category-terms', array() );
        $this->set_option_to( $options, 'display-on-taxonomy-post_tag', 'on' );
        $this->set_option_to( $options, 'display-on-posts-with-taxonomy-post_tag', 'off' );
        $this->set_option_to( $options, 'display-on-taxonomy-post_tag-terms', array() );
        $this->set_option_to( $options, 'display-on-specific-urls', '' );
        $this->set_option_to( $options, 'display-on-specific-urls-exclude', '' );
        $this->set_option_to( $options, 'display-for-users', array() );
        $this->set_option_to( $options, 'display-on-devices', array() );
        $this->set_option_to( $options, 'display-by-post-content', '' );
        $this->set_option_to( $options, 'display-by-referrer-url', '' );
        $this->set_option_to( $options, 'hide-by-referrer-url', '' );
        $this->set_option_to( $options, 'display-by-url-parameters', array() );

        $post_types = $this->options_manager->get_not_builtin_post_types();
        $ops = array();

        $enabled = Settings::get_value('target-enabled-custom-post-types');
        foreach( $post_types as $post_type ){
            $this->set_option_to( $ops, 'display-on-' . $post_type->name, $enabled === 'off' ? 'off' : 'on' );
            $this->set_option_to( $ops, 'display-on-' . $post_type->name . '-include', '' );
            $this->set_option_to( $ops, 'display-on-' . $post_type->name . '-exclude', '' );
        }
        $options = wp_parse_args( $options, $ops );


        if( function_exists( 'FluentCrmApi' ) ){
            //FluentCRM Support
            $this->set_option_to( $options, 'display-by-user-tags-enabled-fluent-crm', 'off' );
            $this->set_option_to( $options, 'display-by-user-tags-fluent-crm', array() );
            $this->set_option_to( $options, 'hide-by-user-tags-enabled-fluent-crm', 'off' );
            $this->set_option_to( $options, 'hide-by-user-tags-fluent-crm', array() );
        }


        return $options;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Opciones de Notification bar
    |---------------------------------------------------------------------------------------------------
    */
    public function notification_bar_options(){
        $options = array();
        $this->set_option_to( $options, 'notification-bar-fixed', 'on' );
        $this->set_option_to( $options, 'notification-bar-push-page-dow', 'on' );
        $this->set_option_to( $options, 'notification-bar-fixed-header-selector', '' );
        $this->set_option_to( $options, 'notification-bar-container-page-selector', '' );
        return $options;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Opciones de Content Loker
    |---------------------------------------------------------------------------------------------------
    */
    public function content_locker_options(){
        $options = array();
        $this->set_option_to( $options, 'content-locker', 'off' );
        $this->set_option_to( $options, 'content-locker-type', 'shortcode' );//shortcode, page_content, whole_page
        $this->set_option_to( $options, 'content-locker-unlock', 'password' );//password, form
        $this->set_option_to( $options, 'content-locker-password', '' );
        $this->set_option_to( $options, 'content-locker-duration', '365' );
        return $options;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Opciones de envío de formulario
    |---------------------------------------------------------------------------------------------------
    */
    public function form_submission_options(){
        $options = array();
        $this->set_option_to( $options, 'form-submission-type', 'none' );
        //Formulario de suscripción
        $this->set_option_to( $options, 'audience-list', '' );
        $this->set_option_to( $options, 'subscription-ok-message', '' );
        $this->set_option_to( $options, 'subscription-error-message', '' );

        $this->set_option_to( $options, 'subscription-admin-notif', 'off' );
        $this->set_option_to( $options, 'subscription-admin-notif-from', 'Wordpress <' . get_option( 'admin_email' ) . '>' );
        $this->set_option_to( $options, 'subscription-admin-notif-to', '' );
        $this->set_option_to( $options, 'subscription-admin-notif-cc', '' );
        $this->set_option_to( $options, 'subscription-admin-notif-subject', 'New user subscription' );
        $this->set_option_to( $options, 'subscription-admin-notif-message', '' );

        $this->set_option_to( $options, 'subscription-user-notif', 'off' );
        $this->set_option_to( $options, 'subscription-user-notif-from', 'Wordpress <' . Functions::from_email( 'noreply' ) . '>' );
        $this->set_option_to( $options, 'subscription-user-notif-subject', 'Thank you for subscribing. This is your discount coupon' );
        $this->set_option_to( $options, 'subscription-user-notif-message', '' );

        //Formulario de contacto
        $this->set_option_to( $options, 'contact-form-ok-message', '' );
        $this->set_option_to( $options, 'contact-form-error-message', '' );

        $this->set_option_to( $options, 'contact-form-admin-notif', 'on' );
        $this->set_option_to( $options, 'contact-form-mail-from', 'Wordpress <' . get_option( 'admin_email' ) . '>' );
        $this->set_option_to( $options, 'contact-form-mail-to', '' );
        $this->set_option_to( $options, 'contact-form-mail-cc', '' );
        $this->set_option_to( $options, 'contact-form-mail-subject', 'New contact form submission' );
        $this->set_option_to( $options, 'contact-form-mail-message', '' );

        //Actions after submission
        $this->set_option_to( $options, 'cookie-on-conversion', 'on' );
        $this->set_option_to( $options, 'cookie-on-conversion-duration', 'days' );
        $this->set_option_to( $options, 'cookie-on-conversion-days', 60 );
        $this->set_option_to( $options, 'message-on-conversion', '' );
        $this->set_option_to( $options, 'form-submission-ok-close-popup', 'on' );
        $this->set_option_to( $options, 'form-submission-ok-close-popup-delay', '3200' );
        $this->set_option_to( $options, 'form-submission-ok-open-popup-id', '' );
        $this->set_option_to( $options, 'form-submission-ok-download-file', 'off' );
        $this->set_option_to( $options, 'form-submission-ok-file', '' );
        $this->set_option_to( $options, 'form-submission-ok-redirect', 'off' );
        $this->set_option_to( $options, 'form-submission-ok-redirect-to', '' );
        $this->set_option_to( $options, 'form-submission-ok-redirect-target', '_self' );
        $this->set_option_to( $options, 'form-redirections', array() );

        //Customize
        $this->set_option_to( $options, 'form-submission-font-size', 14 );
        $this->set_option_to( $options, 'form-submission-font-color', 'rgba(68, 68, 68, 1)' );
        $this->set_option_to( $options, 'form-submission-font-color-success', 'rgba(68, 68, 68, 1)' );
        $this->set_option_to( $options, 'form-submission-font-family', 'Roboto' );
        $this->set_option_to( $options, 'form-submission-border-width', 1 );
        $this->set_option_to( $options, 'form-submission-border-color', 'rgba(0, 181, 183, 1)' );
        $this->set_option_to( $options, 'form-submission-border-style', 'solid' );
        $this->set_option_to( $options, 'form-submission-bg-color', 'rgba(245, 245, 245, 1)' );
        $this->set_option_to( $options, 'form-submission-bg-image', '' );
        $this->set_option_to( $options, 'form-submission-footer-enable', 'on' );
        $this->set_option_to( $options, 'form-submission-footer-font-size', 13 );

        return $options;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Opciones Avanzadas
    |---------------------------------------------------------------------------------------------------
    */
    public function advanced_options(){
        $options = array();
        $this->set_option_to( $options, 'status', 'on' );
        $this->set_option_to( $options, 'attr-class', '' );
        $this->set_option_to( $options, 'custom-css', '' );
        $this->set_option_to( $options, 'custom-javascript', '' );
        $this->set_option_to( $options, 'callback-before-open', '' );
        $this->set_option_to( $options, 'callback-after-open', '' );
        $this->set_option_to( $options, 'callback-before-close', '' );
        $this->set_option_to( $options, 'callback-after-close', '' );
        $this->set_option_to( $options, 'callback-after-form-submission', '' );
        return $options;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna las cookies creadas por el usuario
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_cookies(){
        $saved_cookies = $this->option( 'custom-cookies' );
        $cookies = array();
        if( is_array( $saved_cookies ) ){
            foreach( $saved_cookies as $key => $value ){
                $cookies[$value[$this->prefix . 'name']] = array(
                    'name' => $value[$this->prefix . 'name'],
                    'enable' => $value[$this->prefix . 'enable'],
                    'duration' => $value[$this->prefix . 'duration'],
                    'days' => $value[$this->prefix . 'days'],
                    'days_unit' => $value[$this->prefix . 'days_unit'],
                    'behavior' => isset( $value[$this->prefix . 'behavior'] ) ? $value[$this->prefix . 'behavior'] : array(),
                    'event' => isset( $value[$this->prefix . 'event'] ) ? $value[$this->prefix . 'event'] : 'click',
                );
            }
        }
        return $cookies;
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
