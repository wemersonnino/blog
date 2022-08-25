<?php namespace MasterPopups\Includes;


use Xbox\Includes\CSS;

class Element extends ElementOptions {
    public $id = 0;
    public $type = 'close-icon';
    public $index = 0;
    public $z_index = 1;
    public $device = 'desktop';

    public $plugin = null;
    public $popup = null;

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $options, $popup, $plugin = null ){
        $this->plugin = $plugin;
        $this->popup = $popup;

        parent::__construct( $options );
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
    | Tab index
    |---------------------------------------------------------------------------------------------------
    */
    public function get_tabindex(){
        $total_elements = 30;//más o menos la cantidad de elementos que tiene un popup
        return $this->popup->unique_id * $total_elements + $this->index + 1;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Construye el elemento
    |---------------------------------------------------------------------------------------------------
    */
    public function build(){
        $return = '';
        $attributes = $this->get_attributes_options();
        $element_class = array();
        $element_class[] = esc_attr( $attributes['class'] );
        $element_class[] = "mpp-element";
        $element_class[] = "mpp-element-{$this->type}";
        //$index = $this->index + 1;
        $index = $this->index;
        $element_class[] = "mpp-{$this->device}-element-$index";
        $element_class[] = in_array( $this->type, $this->all_form_elements() ) ? 'mpp-form-element' : '';
        if( $this->type == 'close-icon' ){
            $element_class[] = 'on' == $this->option( 'e-position-top-right-page' ) ? 'mpp-on-top-right-page' : '';
        }
        $element_class = trim( implode( ' ', $element_class ) );

        $element_data = htmlentities( $this->get_element_data( 'html' ) );
        $element_content_data = htmlentities( $this->get_element_content_data( 'html' ) );
        $element_id = ! empty( $attributes['id'] ) ? 'id="' . esc_attr( $attributes['id'] ) . '"' : '';
        $tabindex = in_array( $this->type, $this->tabindex_elements() ) ? 'tabindex="' . ( $this->get_tabindex() ) . '"' : '';

        $return .= "<div $element_id class='$element_class' title='{$attributes['title']}' $element_data>";
        $return .= "<div class='mpp-element-content' $tabindex $element_content_data>";
        $return .= $this->get_content( 'public' );
        $return .= "</div>";//.mpp-el-content
        $return .= "</div>";//.mpp-element
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna attributos data del elemento
    |---------------------------------------------------------------------------------------------------
    */
    public function get_element_data( $return = 'html' ){
        $element_data = array(
            'index' => $this->index,
            'type' => $this->type,
            'device' => $this->device,
            'position' => json_encode( $this->get_position_options() ),
            'size' => json_encode( $this->get_size_options() ),
            'animation' => json_encode( $this->get_animation_options() ),
            'required' => $this->option( 'e-field-required' ),
            'regex-validation' => $this->option( 'e-regex-validation' ),
            'actions' => json_encode( $this->get_actions_options() ),
            'countdown-timer' => json_encode( $this->get_countdown_options() ),
        );
        if( $this->option( 'e-validation-message' ) ){
            $element_data = wp_parse_args( array( 'validation-message' => $this->option( 'e-validation-message' ) ), $element_data );
        }
        $element_data = apply_filters( 'mpp_element_data', $element_data, $this );
        if( $return == 'html' ){
            $html = '';
            foreach( $element_data as $data => $value ){
                $html .= " data-$data='$value'";
            }
            return $html;
        }
        return $element_data;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna attributos data del elemento
    |---------------------------------------------------------------------------------------------------
    */
    public function get_element_content_data( $return = 'html' ){
        $content_data = array(
            'font' => json_encode( $this->get_font_options() ),
            'padding' => json_encode( $this->get_padding_options() ),
            'border' => json_encode( $this->get_border_options() ),
        );
        if( $return == 'html' ){
            $html = '';
            foreach( $content_data as $data => $value ){
                $html .= " data-$data='$value'";
            }
            return $html;
        }
        return $content_data;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene el contenido de un elemento
    |---------------------------------------------------------------------------------------------------
    */
    public function get_content( $scope = 'public' ){
        $content = '';
        switch( $this->type ){
            case 'close-icon':
                $content = $this->get_content_type_object( $scope, 'e-content-close-icon' );
                break;

            case 'object':
                $content = $this->get_content_type_object( $scope, 'e-content-object' );
                break;

            case 'close-icon':
                $value = $this->option( 'e-content-close-icon' );
                if( Functions::ends_with( '.svg', $value ) ){
                    $content = "<img src='$value'>";
                } else{
                    $content = "<i class='$value'></i>";
                }
                break;

            case 'text-html':
                $content = $this->get_content_type_text_html( $scope );
                break;

            case 'image':
                $content = "<img src='{$this->option('e-content-image')}'>";
                break;

            case 'video':
                $content = $this->get_content_type_video( $scope );
                break;

            case 'button':
                $content = $this->get_content_type_button( $scope );
                break;

            case 'shape':
                $content = $this->get_content_type_shape( $scope );
                break;

            case 'shortcode':
                $content = $this->get_content_type_shortcode( $scope );
                break;

            case 'iframe':
                $content = $this->get_content_type_iframe( $scope );
                break;

            case 'countdown':
                $content = $this->get_content_type_countdown( $scope );
                break;

            case 'field_first_name':
            case 'field_last_name':
            case 'field_email':
            case 'field_phone':
            case 'field_message':
            case 'field_submit':

            case 'custom_field_input_text':
            case 'custom_field_input_hidden':
            case 'custom_field_input_checkbox':
            case 'custom_field_input_checkbox_gdpr':
            case 'custom_field_dropdown':
                $content = $this->get_content_form_fields( $scope );
                break;

            case 'field_recaptcha':
                $content = $this->get_content_type_field_recaptcha( $scope );
                break;

            default:
                $content = 'Add content in class-element.php function get_content()';
        }

        return apply_filters( 'mpp_element_content', $content, $this, $scope );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene el contenido de un elemento tipo close_icon and object
    |---------------------------------------------------------------------------------------------------
    */
    public function get_content_type_object( $scope = 'public', $field_name = 'e-content-object' ){
        $value = $this->option( $field_name );
        if( Functions::ends_with( '.svg', $value ) ){
            $content = "<img src='$value'>";
        } else{
            $content = "<i class='$value'></i>";
        }
        return $content;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene el contenido de un elemento tipo text_html
    |---------------------------------------------------------------------------------------------------
    */
    public function get_content_type_text_html( $scope = 'public' ){
        return $this->option( 'e-content-textarea' );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene el contenido de un elemento tipo button
    |---------------------------------------------------------------------------------------------------
    */
    public function get_content_type_button( $scope = 'public' ){
        return $this->option( 'e-content-textarea' );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene el contenido de un elemento tipo shape
    |---------------------------------------------------------------------------------------------------
    */
    public function get_content_type_shape( $scope = 'public' ){
        return $this->option( 'e-content-textarea' );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene el contenido de un elemento tipo shortcode
    |---------------------------------------------------------------------------------------------------
    */
    public function get_content_type_shortcode( $scope = 'public' ){
        if( $scope == 'admin' ){
            return $this->option( 'e-content-shortcode' );
        }
        return do_shortcode( $this->option( 'e-content-shortcode' ) );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene el contenido de un elemento tipo iframe
    |---------------------------------------------------------------------------------------------------
    */
    public function get_content_type_iframe( $scope = 'public' ){
        $content = '';
        $url = esc_url( $this->option( 'e-content-url' ) );
        if( $scope == 'admin' ){
            $iframe = "<iframe src='$url'></iframe>";
        } else{
            $iframe = "<iframe src='about:blank'></iframe>";
        }
        $content .= "<div class='mpp-iframe-wrap' data-src='$url'>";
        if( $scope == 'public' ){
            $content .= "<i class='mpp-icon mpp-icon-spinner mpp-icon-spin mpp-loader'></i>";
        }
        $content .= $iframe;
        $content .= '</div>';
        return $content;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene el contenido de un elemento tipo countdown
    |---------------------------------------------------------------------------------------------------
    */
    public function get_content_type_countdown( $scope = 'public' ){
        $content = '';
        $date = $this->option( 'e-content-date' ) . ' ' . $this->option( 'e-content-time' );
        $options = array(
            'popupId' => $this->popup->id,
            'type' => $this->option( 'e-countdown-type' ),
            'date' => trim( $date ), //date("Y-m-d H:i:s"),
            'expireDays' => (float) $this->option( 'e-countdown-expire-days' ),
            'expireHours' => (float) $this->option( 'e-countdown-expire-hours' ),
            'labels' => $this->option( 'e-countdown-labels', array() ),
            'strings' => $this->option( 'e-countdown-labels-strings' ),
            'resetTimer' => $this->option( 'e-countdown-reset' ) === 'on',
            'resetType' => $this->option( 'e-countdown-reset-type' ),
            'resetDays' => (float) $this->option( 'e-countdown-reset-days' ),
            'resetHours' => (float) $this->option( 'e-countdown-reset-hours' ),
            'resetAfterDays' => (int) $this->option( 'e-countdown-reset-after-days' ),
        );

        $metadata = json_encode( $options );
        $content .= "<div class='mpp-countdown-wrap'>";
        $content .= "<div class='mpp-countdown' data-options='$metadata'>";
        $content .= "</div>";
        $content .= '</div>';
        return $content;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene el contenido de un elemento tipo video
    |---------------------------------------------------------------------------------------------------
    */
    public function get_content_type_video( $scope = 'public' ){
        $content = '';
        $video = __( 'Video not found', 'masterpopups' );
        $video_poster = $this->option( 'e-video-poster' );
        $play_icon = $this->option( 'e-play-icon' );
        $video_type = $this->option( 'e-video-type' );
        $autoplay = $this->option( 'e-video-autoplay' );

        switch( $video_type ){
            case 'html5':
                $video_url = $this->option( 'e-content-video-html5' );
                $player = new Player( $video_url );
                $extension = Functions::get_file_extension( $video_url );
                if( $video_url && $player->provider == 'html5' ){
                    $random_id = Functions::random_string( 5, false );
                    $video = "<video id='mpp-video-$random_id' data-video-id='$random_id' controls class='video-js vjs-sublime-skin' preload='none'>";
                    $video .= "<source src='{$video_url}' type='video/$extension'>";
                    $video .= "<p class='vjs-no-js'>
            To view this video please enable JavaScript, and consider upgrading to a web browser
            that <a href='http://videojs.com/html5-video-support/' target='_blank'>supports HTML5 video</a>
          </p>";
                    $video .= "</video>";
                }
                break;

            case 'youtube':
            case 'vimeo':
                $video_url = $this->option( 'e-content-video' );
                parse_str( $this->option( 'e-video-' . $video_type . '-parameters' ), $parameters );
                $parameters['autoplay'] = '1';//El autoplay se activa desde js. Por eso aquí debe estar en 1
                $player = new Player( $video_url, true, $parameters );
                $video_poster = $video_poster ? $video_poster : $player->image;

                if( $video_url && $player->provider && $player->provider != 'html5' ){
                    $video = $player->player;
                }
                break;
        }

        $provider = $player->provider ? $player->provider : __( 'Unknown', 'masterpopups' );

        if( $scope == 'admin' ){
            $content = "<div class='mpp-video-poster' style='background-image: url($video_poster)'>";
            $content .= "<div class='mpp-video-caption'>$provider video</div>";
            $content .= "<div class='mpp-play-icon'><i class='$play_icon'></i></div>";
            $content .= "</div>";
        } else{
            $content = "<div class='mpp-video-poster' style='background-image: url($video_poster)'>";
            $content .= "<div class='mpp-play-icon'><i class='$play_icon'></i></div>";
            $content .= "</div>";
            $content .= "<div class='mpp-wrap-video' data-video-type='$provider' data-autoplay='$autoplay'>";
            $content .= $video;
            $content .= "</div>";
        }
        return $content;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene el contenido de un elemento tipo field_recaptcha
    |---------------------------------------------------------------------------------------------------
    */
    public function get_content_type_field_recaptcha( $scope = 'public' ){
        $content = '';
        $recaptcha_version = $this->option( 'e-recaptcha-version' );
        $recaptcha_theme = $this->option( 'e-recaptcha-theme' );
        $content .= "<div class='mpp-recaptcha-wrap mpp-recaptcha-version-$recaptcha_version' data-version='$recaptcha_version' data-theme='$recaptcha_theme'>";
        $content .= '<div class="mpp-recaptcha"></div>';
        $content .= '</div>';
        return $content;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene el contenido de los elementos de formulario
    |---------------------------------------------------------------------------------------------------
    */
    public function get_content_form_fields( $scope = 'public' ){
        $content = '';
        $placeholder = $this->option( 'e-field-placeholder' );
        $name = $this->option( 'e-field-name' );
        $value = $this->get_default_value_form_field();
        $options = $this->option( 'e-field-options' );
        $checked = $this->option( 'e-field-checked' ) == 'on' ? 'checked' : '';
        $required = $this->option( 'e-field-required' ) == 'on' ? 'required' : '';
        $tabindex = $this->get_tabindex();


        //Inputs
        if( in_array( $this->type, self::form_elements()['input'] ) ){
            if( $this->type == 'custom_field_input_checkbox' || $this->type == 'custom_field_input_checkbox_gdpr' ){
                $required = $scope == 'admin' ? '' : $required;
                $content = "<label><input type='checkbox' name='$name' class='mpp-checkbox' value='$value' $required $checked><i class='mpp-icon mpp-icon-check'></i></label>";
            } else{
                if( $scope == 'admin' ){
                    $content = "<span>$placeholder</span>";
                } else{
                    $input_type = $this->option( 'e-input-type' );
                    if( $this->type == 'field_email' ){
                        $input_type = 'email';
                    } else if( $this->type == 'field_phone' ){
                        $input_type = 'tel';
                    } else if( $this->type == 'custom_field_input_hidden' ){
                        $input_type = 'hidden';
                    }

                    $hidden_field = '';
                    if( $this->type == 'field_email' ){
                        $hidden_field = "<input type='hidden' name='mpp_field_email' value='$name'>";
                        $name = 'email';
                    } else if( $this->type == 'field_first_name' ){
                        $hidden_field = "<input type='hidden' name='mpp_field_first_name' value='$name'>";
                        $name = 'first_name';
                    } else if( $this->type == 'field_last_name' ){
                        $hidden_field = "<input type='hidden' name='mpp_field_last_name' value='$name'>";
                        $name = 'last_name';
                    }
                    $valid_characters = $this->option( 'e-valid-characters' );
                    $min_characters = $this->option( 'e-min-characters' );
                    $content = "<input type='$input_type' name='$name' class='mpp-input' value='$value' placeholder='$placeholder' tabindex='$tabindex' data-valid-characters='$valid_characters' data-min-characters='$min_characters'   $required >";
                    $content .= $hidden_field;

                }
            }
        } //Dropdown
        else if( in_array( $this->type, self::form_elements()['dropdown'] ) ){
            if( $scope == 'admin' ){
                $content = "<span>$placeholder<i class='mpp-icon mpp-icon-chevron-down'></i></span>";
            } else{
                $options = explode( "\n", $options );
                $options = array_map( 'trim', $options );
                $options = array_filter( $options );//remove empty options
                $content = "<select name='$name' class='mpp-select' tabindex='$tabindex' $required>";
                $content .= "<option value=''>$placeholder</option>";
                if( is_array( $options ) && ! empty( $options ) ){
                    foreach( $options as $option ){
                        $parts = explode( '|', $option );
                        $parts = array_map( 'trim', $parts );
                        if( count( $parts ) > 1 ){
                            $val = $parts[0];
                            $display = $parts[1];
                        } else{
                            $val = $option;
                            $display = $option;
                        }
                        $selected = $val == $value ? 'selected' : '';
                        $content .= "<option value='$val' $selected>$display</option>";
                    }
                }
                $content .= "</select>";
                $content .= "<i class='mpp-icon mpp-icon-chevron-down mpp-icon-dropdown'></i>";
            }
        } //Textarea
        else if( in_array( $this->type, self::form_elements()['textarea'] ) ){
            if( $scope == 'admin' ){
                $content = "<span>$placeholder</span>";
            } else{
                $content = "<textarea name='$name' class='mpp-textarea' placeholder='$placeholder' tabindex='$tabindex' $required>$value</textarea>";
            }
        } //Submit
        else if( $this->type == self::form_elements()['submit'] ){
            if( $scope == 'admin' ){
                $content = $this->option( 'e-content-textarea' );
            } else{
                $content = $this->option( 'e-content-textarea' );
            }
        }
        return $content;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Valor por defecto de un campo de formulario
    |---------------------------------------------------------------------------------------------------
    */
    public function get_default_value_form_field(){
        $value = $this->option( 'e-field-value' );

        //Logged-in values => {logged-in:email}, {logged-in:first_name}
        if( stripos( $value, '{logged-in:' ) !== false ){
            $split = explode( ':', $value );
            $search = ! empty( $split[1] ) ? $split[1] : '';
            $search = rtrim( $search, '}' );
            $current_user = wp_get_current_user();
            if( $search && $current_user->ID != 0 ){
                switch( $search ){
                    case 'email':
                        $value = $current_user->user_email;
                        break;
                    case 'first_name':
                        $value = $current_user->user_firstname;
                        break;
                    case 'last_name':
                        $value = $current_user->user_lastname;
                        break;
                    case 'ID':
                        $value = $current_user->ID;
                        break;
                }
            } else{
                $value = '';
            }
        }

        return $value;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Construye estilo del elemento
    |---------------------------------------------------------------------------------------------------
    */
    public function build_style(){
        $style = '';
        //$index = $this->index + 1;
        $index = $this->index;
        $selector = ".mpp-box .mpp-wrap-{$this->popup->id} .mpp-{$this->device}-element-$index";
        $style .= $this->get_style( $selector );

        switch( $this->type ){
            case 'field_first_name':
            case 'field_last_name':
            case 'field_email':
            case 'field_phone':
            case 'field_message':
            case 'custom_field_input_text':
            case 'custom_field_input_hidden':
            case 'custom_field_dropdown':
                $target = 'input';
                if( in_array( $this->type, self::form_elements()['textarea'] ) ){
                    $target = 'textarea';
                } else if( in_array( $this->type, self::form_elements()['dropdown'] ) ){
                    $target = 'select';
                }
                $style .= $this->get_content_style( "$selector .mpp-element-content $target" );
                $style .= $this->get_content_style( "$selector .mpp-element-content {$target}:hover", 'hover' );
                $style .= $this->get_content_style( "$selector .mpp-element-content {$target}:focus", 'focus' );
                break;

            case 'shortcode':
                $style .= '';
                //Agregar estilo overflow!
                break;
            default:
                $style .= $this->get_content_style( "$selector .mpp-element-content" );
                $style .= $this->get_content_style( "$selector .mpp-element-content:hover", 'hover' );
                $style .= $this->get_content_style( "$selector .mpp-element-content:focus", 'focus' );

                if( $this->type == 'custom_field_input_checkbox' || $this->type == 'custom_field_input_checkbox_gdpr' ){
                    $css = new CSS( "$selector .mpp-element-content input[type=checkbox]:checked + i" );
                    $css->prop( 'color', $this->option( 'e-field-checked-color' ) );
                    $style .= $css->build_css();
                }
                break;
        }
        return $style;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Estilo del elemento
    |---------------------------------------------------------------------------------------------------
    */
    public function get_style( $selector = null, $type = 'css' ){
        $css = new CSS( $selector );

        $css->prop( 'z-index', $this->z_index );
        $css->prop( 'visibility', $this->option( 'visibility' ) );

        //Size & Position
        $position = $this->get_position_options();
        $size = $this->get_size_options();
        $css->prop( 'width', $size['width'] );
        $css->prop( 'height', $size['height'] );
        $css->prop( 'top', $position['top'] );
        $css->prop( 'left', $position['left'] );

        //Advanced
        $advanced = $this->get_advanced_options();
        $css->prop( 'cursor', $advanced['cursor'] );

        if( $type == 'json' ){
            return json_encode( $css->get_props() );
        }
        return $css->build_css();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Estilo del contenenido del elemento
    |---------------------------------------------------------------------------------------------------
    */
    public function get_content_style( $selector = null, $type = 'css' ){
        //Focus properties
        if( $type == 'focus' ){
            $focus_css = new CSS( $selector );
            $focus = $this->get_focus_options();
            if( $this->option( 'e-focus-border-enable' ) == 'on' ){
                $border_style = $this->option( 'e-border-style' );
                if( $border_style == 'none' ){
                    $focus_css->prop( 'border-style', 'solid' . ' !important' );
                }
                $focus_css->prop( 'border-color', $focus['border-color'] . ' !important' );
            }
            return $focus_css->build_css();
        }

        $css = new CSS();
        $normal_css = new CSS( $selector );
        $admin_selector = ".ampp-{$this->device}-content .ampp-element-{$this->index}";
        $countdown_css = new CSS( $selector . " .mpp-countdown, $admin_selector .mpp-countdown" );
        $countdown_digit_css = new CSS( $selector . " .mpp-countdown .mpp-count-digit, $admin_selector .mpp-countdown .mpp-count-digit" );
        $countdown_count_css = new CSS( $selector . " .mpp-countdown .mpp-count, $admin_selector .mpp-countdown .mpp-count" );
        $countdown_count_top_css = new CSS( $selector . " .mpp-countdown .mpp-count.mpp-top, $admin_selector .mpp-countdown .mpp-count.mpp-top" );
        $countdown_count_bottom_css = new CSS( $selector . " .mpp-countdown .mpp-count.mpp-bottom, $admin_selector .mpp-countdown .mpp-count.mpp-bottom" );

        //Hover properties
        if( $type == 'hover' || $type == 'json' ){
            $hover_css = new CSS( $selector );
            $hover = $this->get_hover_options();
            if( $type == 'json' || $this->option( 'e-hover-bg-enable' ) == 'on' ){
                $hover_css->prop( 'background', $hover['background-color'] );
            }
            if( $type == 'json' || $this->option( 'e-hover-font-enable' ) == 'on' ){
                $hover_css->prop( 'color', $hover['font-color'] );
            }
            if( $type == 'json' || $this->option( 'e-hover-border-enable' ) == 'on' ){
                $hover_css->prop( 'border-color', $hover['border-color'] . ' !important' );
            }
            if( $type == 'hover' ){
                return $hover_css->build_css();
            }
        }

        //Size & Position
        $padding = $this->get_padding_options();
        $padding_css = new CSS();

        $padding_css->prop( 'padding-top', $padding['top'] );
        $padding_css->prop( 'padding-right', $padding['right'] );
        $padding_css->prop( 'padding-bottom', $padding['bottom'] );
        $padding_css->prop( 'padding-left', $padding['left'] );

        //Font
        $font = $this->get_font_options();
        $font_css = new CSS();
        $normal_css->prop( 'line-height', $font['line-height'] );
        $font_css->prop( 'font-family', $font['font-family'] );
        $font_css->prop( 'font-size', $font['font-size'] );
        $font_css->prop( 'font-weight', $font['font-weight'] );
        $font_css->prop( 'font-style', $font['font-style'] );
        $font_css->prop( 'color', $font['color'] );
        $font_css->prop( 'text-align', $font['text-align'] );
        $font_css->prop( 'white-space', $font['white-space'] );
        $font_css->prop( 'text-transform', $font['text-transform'] );
        $font_css->prop( 'text-decoration', $font['text-decoration'] );
        $font_css->prop( 'letter-spacing', $font['letter-spacing'] );
        $font_css->prop( 'text-shadow', $font['text-shadow'] );

        //Background
        $bg = $this->get_background_options();
        $bg_css = new CSS();
        if( $bg['enable-gradient'] == 'on' ){
            $bg_css->prop( 'background', "linear-gradient({$bg['angle-gradient']}, {$bg['color']}, {$bg['color-gradient']} )" );
        } else{
            $bg_css->prop( 'background-color', $bg['color'] );
            $bg_css->prop( 'background-repeat', $bg['repeat'] );
            $bg_css->prop( 'background-size', $bg['size'] );
            $bg_css->prop( 'background-position', $bg['position'] );
            $bg_css->prop( 'background-image', 'url(' . $bg['image'] . ')' );
        }

        //Border
        $border = $this->get_border_options();
        $border_css = new CSS();
        $border_css->prop( 'border-color', $border['color'] . ' !important' );
        $border_css->prop( 'border-style', $border['style'] . ' !important' );
        $border_css->prop( 'border-top-width', $border['top-width'] );
        $border_css->prop( 'border-right-width', $border['right-width'] );
        $border_css->prop( 'border-bottom-width', $border['bottom-width'] );
        $border_css->prop( 'border-left-width', $border['left-width'] );
        $border_css->prop( 'border-radius', $border['radius'] );

        //Advanced
        $advanced = $this->get_advanced_options();
        $normal_css->prop( 'opacity', $advanced['opacity'] );
        $normal_css->prop( 'overflow', $advanced['overflow'] );
        $css->prop( 'box-shadow', $advanced['box-shadow'] );


        if( $type == 'css' && $this->type == 'countdown' ){
            $countdown = $this->get_countdown_options();

            $countdown_css->merge_props( $font_css->get_props() );
            $countdown_css->prop( 'color', $countdown['label-font-color'] );
            $countdown_css->prop( 'font-size', $countdown['label-font-size'] );
            $countdown_css->remove_prop( 'text-shadow' );

            $countdown_digit_css->merge_props( $border_css->get_props() );
            $countdown_digit_css->prop( 'border-color', $border['color'] );
            $countdown_digit_css->prop( 'border-style', $border['style'] );
            $countdown_digit_css->prop( 'width', $countdown['width'] );
            $countdown_digit_css->prop( 'height', $countdown['height'] );
            $countdown_digit_css->prop( 'box-shadow', $advanced['box-shadow'] );
            $countdown_digit_css->merge_props( $bg_css->get_props() );

            $countdown_count_css->merge_props( $font_css->get_props() );
            $countdown_count_css->merge_props( $padding_css->get_props() );
            $countdown_count_css->merge_props( $bg_css->get_props() );

            $countdown_count_top_css->prop( 'border-radius', "{$border['radius']} {$border['radius']} 0 0" );
            $countdown_count_top_css->prop( 'line-height', $countdown_digit_css->prop( 'height' ) );
            $countdown_count_bottom_css->prop( 'border-radius', "0 0 {$border['radius']} {$border['radius']}" );

        } else{
            $normal_css->merge_props( $css->get_props() );
            $normal_css->merge_props( $font_css->get_props() );
            $normal_css->merge_props( $padding_css->get_props() );
            $normal_css->merge_props( $bg_css->get_props() );
            $normal_css->merge_props( $border_css->get_props() );
        }

        if( $type == 'css' ){
            if( $selector == '.ampp-el-content' ){//inline style for visual editor
                return $normal_css->get_inline_style();
            } else{
                $return = '';
                $return .= $countdown_css->build_css();
                $return .= $countdown_digit_css->build_css();
                $return .= $countdown_count_top_css->build_css();
                $return .= $countdown_count_bottom_css->build_css();
                $return .= $countdown_count_css->build_css();
                $return .= $normal_css->build_css();
                return $return;
            }

        } else if( $type == 'json' ){
            $style['normal'] = $normal_css->get_props();
            $style['hover'] = $hover_css->get_props();
            return json_encode( $style );
        }
        return '';
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Elementos de formulario
    |---------------------------------------------------------------------------------------------------
    */
    public static function form_elements(){
        return array(
            'input' => array(
                'field_first_name',
                'field_last_name',
                'field_email',
                'field_phone',
                'custom_field_input_text',
                'custom_field_input_hidden',
                'custom_field_input_checkbox',
                'custom_field_input_checkbox_gdpr',
            ),
            'dropdown' => array(
                'custom_field_dropdown',
            ),
            'textarea' => array(
                'field_message',
            ),
            'submit' => 'field_submit',
            'field_recaptcha' => 'field_recaptcha',
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Todos los elementos de formulario
    |---------------------------------------------------------------------------------------------------
    */
    public static function all_form_elements(){
        $all = array();
        foreach( self::form_elements() as $key => $value ){
            if( is_array( $value ) ){
                foreach( $value as $type ){
                    $all[] = $type;
                }
            } else{
                $all[] = $value;
            }
        }
        return $all;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Todos los elementos que tendrán tabindex
    |---------------------------------------------------------------------------------------------------
    */
    public function tabindex_elements(){
        $arr = array( 'close-icon', 'field_submit', 'button', 'custom_field_input_checkbox', 'custom_field_input_checkbox_gdpr' );
        if( $this->type === 'text-html' && $this->option( 'e-onclick-action' ) != 'default' ){
            array_push( $arr, 'text-html' );
        }
        return $arr;
    }


}
