<?php
use MasterPopups\Includes\Assets as Assets;

$elements->add_field(array(
	'id' => 'e-content-textarea',
	'name' => __( 'Text/HTML', 'masterpopups' ),
	'type' => 'textarea',
	'default' => $element_defaults['e-content-textarea'],
	'desc' => 'Enter a valid text/html',
	'options' => array(
		'show_name' => false,
	),
	'insert_after_field' => '<a class="xbox-btn xbox-btn-teal xbox-btn-icon xbox-btn-small ampp-float-btn ampp-open-icon-library ">'.__( 'Insert icon', 'masterpopups' ).'</a>'
));

$elements->add_field(array(
	'id' => 'e-content-shortcode',
	'name' => 'Shortcode',
	'type' => 'textarea',
	'default' => $element_defaults['e-content-shortcode'],
	'desc' => sprintf(__( 'Please enter only %s. Not html. We recommend going to Advanced tab and in the "Overflow" option choose "Auto"', 'masterpopups' ), '[Shortcode]'),
	'options' => array(
		'show_name' => false,
	),
));

$elements->add_field(array(
	'id' => 'e-content-close-icon',
	'name' => 'Close icon',
	'type' => 'icon_selector',
	'default' => $element_defaults['e-content-close-icon'],
	'items' => Assets::close_icons(),
	'options' => array(
		'show_name' => false,
		//'load_with_ajax' => true,
		'ajax_data' => array('class_name' => 'MasterPopups\Includes\Assets', 'function_name' => 'close_icons'),
		'size' => '40px',
		'wrap_height' => '150px'
	)
));

$elements->add_field(array(
	'id' => 'e-content-object',
	'name' => __( 'Choose object', 'masterpopups' ),
	'type' => 'icon_selector',
	'default' => $element_defaults['e-content-object'],
	'items' => array(),
	'options' => array(
		'show_name' => false,
		'hide_buttons' => true,
		'hide_search' => true,
		'size' => '40px',
	),
	'append_in_field' => '<a class="xbox-btn xbox-btn-teal xbox-btn-icon xbox-btn-small ampp-open-object-library">Select object</a>',
));

$elements->add_field(array(
	'id' => 'e-content-image',
	'name' => __( 'Image URL', 'masterpopups' ),
	'type' => 'file',
	'default' => $element_defaults['e-content-image'],
	'options' => array(
		'mime_types' => array( 'jpg', 'jpeg', 'png', 'gif', 'ico' ),
		'preview_size' => array( 'width' => '30px','height' => '30px' ),
		'show_if' => array('', 'aa'),
	),
	'row_class' => 'mpp-image-file',
	'grid' => '7-of-8 last'
));

$elements->add_field(array(
	'id' => 'e-content-url',
	'name' => __( 'Iframe URL', 'masterpopups' ),
	'type' => 'text',
	'default' => $element_defaults['e-content-url'],
	'options' => array(
		'helper' => '<i class="xbox-icon xbox-icon-link"></i>',
	),
));

$elements->add_field(array(
	'id' => 'e-video-type',
	'name' => __( 'Choose video type', 'masterpopups' ),
	'type' => 'radio',
	'default' => $element_defaults['e-video-type'],
	'items' => array(
		'youtube' => 'Youtube',
		'vimeo' => 'Vimeo',
		'html5' => 'Html5',
	),
));
$elements->add_field(array(
	'id' => 'e-content-video',
	'name' => __( 'Video URL', 'masterpopups' ),
	'type' => 'oembed',
	'default' => $element_defaults['e-content-video'],
	'desc' => __( 'Example:', 'masterpopups' ) . ' https://www.youtube.com/watch?v=34Na4j8AVgA',
	'options' => array(
		'preview_onload' => false,
		'show_if' => array('e-video-type', 'in', array('youtube', 'vimeo') ),
		'preview_size' => array( 'width' => '100%', 'height' => '200px' ),
	),
));
$elements->add_field(array(
	'id' => 'e-content-video-html5',
	'name' => __( 'HTML5 Video URL', 'masterpopups' ),
	'type' => 'file',
	'default' => $element_defaults['e-content-video-html5'],
	'options' => array(
        'preview_size' => array( 'width' => '100%', 'height' => '150px' ),
        'show_if' => array('e-video-type', '=', 'html5'),
    ),
));
$elements->open_mixed_field(array('name' => 'Poster image', 'id' =>'e-mixed-video-poster'));
	$elements->add_field(array(
		'id' => 'e-video-load-thumbnail',
		'name' => __( 'Video thumbnail', 'masterpopups' ),
		'type' => 'button',
		'content' => __( 'Load', 'masterpopups' ),
		'options' => array(
			'size' => 'small',
			'color' => 'teal'
		)
	));
	$elements->add_field(array(
		'id' => 'e-video-poster',
		'name' => __( 'From custom url', 'masterpopups' ),
		'type' => 'file',
		'default' => $element_defaults['e-video-poster'],
		'options' => array(
			'mime_types' => array( 'jpg', 'jpeg', 'png', 'gif', 'ico' ),
			'preview_size' => array( 'width' => '30px','height' => '30px' ),
		),
		'row_class' => 'mpp-image-file',
		'grid' => '6-of-8 last'
	));

$elements->close_mixed_field();

$elements->add_field(array(
	'id' => 'e-play-icon',
	'name' => __( 'Play icon', 'masterpopups' ),
	'type' => 'icon_selector',
	'default' => $element_defaults['e-play-icon'],
	'items' => Assets::play_icons(),//Load by ajax
	'options' => array(
		'load_with_ajax' => false,
		'wrap_height' => 'auto',
		'size' => '40px',
		'hide_search' => true,
		'hide_buttons' => true,
	)
));

$elements->open_mixed_field(array('name' => 'Video options', 'id' =>'e-mixed-video-options'));
	$elements->add_field(array(
		'id' => 'e-video-autoplay',
		'name' => __( 'Autoplay', 'masterpopups' ),
		'type' => 'switcher',
		'default' => $element_defaults['e-video-autoplay'],
	));
	$elements->add_field(array(
		'id' => 'e-video-youtube-parameters',
		'name' => __( 'Parameters', 'masterpopups' ),
		'type' => 'text',
		'default' => $element_defaults['e-video-youtube-parameters'],
		'grid' => '6-of-8',
		'options' => array(
			'show_if' => array('e-video-type', '=', 'youtube')
		)
	));
	$elements->add_field(array(
		'id' => 'e-video-vimeo-parameters',
		'name' => __( 'Parameters', 'masterpopups' ),
		'type' => 'text',
		'default' => $element_defaults['e-video-vimeo-parameters'],
		'grid' => '6-of-8',
		'options' => array(
			'show_if' => array('e-video-type', '=', 'vimeo')
		)
	));
$elements->close_mixed_field();

$elements->add_field(array(
	'id' => 'e-button-styles',
	'name' => __( 'Button styles', 'masterpopups' ),
	'type' => 'html',
	'options' => array(
		'show_name' => false,
	),
	'content' => $class->get_html_button_styles()
));


//Form
$elements->add_field(array(
	'id' => 'e-field-placeholder',
	'name' => 'Field placeholder',
	'type' => 'text',
	'default' => $element_defaults['e-field-placeholder'],
));
$elements->add_field(array(
	'id' => 'e-field-name',
	'name' => __( 'Field name', 'masterpopups' ),
	'type' => 'text',
	'default' => $element_defaults['e-field-name'],
	'desc' => __( 'Attribute "name" of the field.', 'masterpopups' ). '
<ul style="padding: 8px 0px; margin-left: 15px; list-style: disc;">
<li>For "Email" enter: <strong>field_email</strong></li>
<li>For "Name" enter: <strong>field_first_name</strong></li>
<li>For "Last Name" enter: <strong>field_last_name</strong></li>
<li>For "Phone", "Input text" or another element you can enter your own name.</li>
</ul>
'.sprintf(__( 'If you wish you can also enter the name of the custom field of your service. Go to %s and Get your custom fields.', 'masterpopups' ), '<a href="'.$class->plugin->settings_url.'" target="_blank">'.__( 'Service Integration', 'masterpopups' ).'</a>' ),
));
$elements->add_field(array(
	'id' => 'e-field-value',
	'name' => __( 'Default value', 'masterpopups' ),
	'type' => 'text',
	'default' => $element_defaults['e-field-name'],
));

$elements->open_mixed_field(array('name' => 'Field validation', 'id' => 'e-field-validation'));
$elements->add_field(array(
	'id' => 'e-field-required',
	'name' => __( 'Is required', 'masterpopups' ),
	'type' => 'switcher',
	'desc' => __( 'Enable to make this field mandatory when submitting the form.', 'masterpopups' ),
	'default' => $element_defaults['e-field-required'],
));
$elements->add_field(array(
    'id' => 'e-validation-message',
    'name' => __( 'Validation message', 'masterpopups' ),
    'type' => 'text',
    'desc' => __( 'Enter a custom validation message. By default is used the message from General Settings > Validation messages.', 'masterpopups' ),
    'default' => $element_defaults['e-validation-message'],
    'options' => array(
        'desc_tooltip' => false,
        'show_if' => array( 'e-field-required', '=', 'on' )
    ),
));
$elements->add_field(array(
    'id' => 'e-regex-validation',
    'name' => __( 'Regex validation', 'masterpopups' ),
    'type' => 'text',
    'desc' => sprintf ( __( 'Enter a regular expression to validate the field value. Example: %s. Leave empty if you are using Content Locker with Password.', 'masterpopups' ), '/[a-zA-Z]+/gi' ),
    'default' => $element_defaults['e-regex-validation'],
    'options' => array(
        'desc_tooltip' => false,
        'show_if' => array( 'e-field-required', '=', 'on' )
    ),
    'sanitize_callback' => false,
));
$elements->close_mixed_field();

$elements->open_mixed_field(array('name' => 'Checked', 'id' => 'e-mixed-checked-options'));
	$elements->add_field(array(
		'id' => 'e-field-checked',
		'name' => __( 'Checked by default', 'masterpopups' ),
		'type' => 'switcher',
		'default' => $element_defaults['e-field-checked'],
	));
	$elements->add_field(array(
		'id' => 'e-field-checked-color',
		'name' => __( 'Color', 'masterpopups' ),
		'type' => 'colorpicker',
		'default' => $element_defaults['e-field-checked-color'],
		'desc' => __( 'The color when it is activated.', 'masterpopups' ),
		'options' => array(
			'format' => 'rgba',
			'opacity' => 1,
		),
	));
$elements->close_mixed_field();
$elements->add_field(array(
	'id' => 'e-field-options',
	'name' => __( 'Options', 'masterpopups' ),
	'type' => 'textarea',
	'default' => $element_defaults['e-field-options'],
	'desc' => __( 'Please enter the list of options, one option for each line. It is also possible to add: value|Display', 'masterpopups' ),
	'attributes' => array(
		'rows' => '4',
	),
));
$elements->add_field(array(
    'id' => 'e-input-type',
    'name' => __( 'Input type', 'masterpopups' ),
    'type' => 'select',
    'default' => $element_defaults['e-input-type'],
    'items' => array(
        'text' => 'text',
        'date' => 'date',
        'time' => 'time',
        'number' => 'number',
        'password' => 'password',
    ),
));


$elements->open_mixed_field(array('id' => 'e-countdown-datetime', 'name' => __( 'Expiration date', 'masterpopups' )));
$elements->add_field(array(
    'id' => 'e-countdown-type',
    'name' => __( 'Countdown type', 'masterpopups' ),
    'type' => 'select',
    'default' => $element_defaults['e-countdown-type'],
    'items' => array(
        'evergreen' => __( 'Evergreen timer', 'masterpopups' ),
        'date_time' => __( 'Expiration date', 'masterpopups' ),
    ),
    'attributes' => array(
        'style' => 'width: 220px'
    ),
));
$elements->add_field(array(
    'id' => 'e-content-date',
    'name' => __( 'Date', 'masterpopups' ),
    'type' => 'date',
    'default' => $element_defaults['e-content-date'],
    'options' => array(
        'show_if' => array('e-countdown-type', '=', 'date_time'),
    ),
));
$elements->add_field(array(
    'id' => 'e-content-time',
    'name' => __( 'Time', 'masterpopups' ),
    'type' => 'time',
    'default' => $element_defaults['e-content-time'],
    'options' => array(
        'show_if' => array('e-countdown-type', '=', 'date_time'),
    ),
));
$elements->add_field(array(
    'id' => 'e-countdown-expire-days',
    'name' => __( 'Expire in Days', 'masterpopups' ),
    'desc' => __( 'Days to end the countdown timer.', 'masterpopups' ),
    'type' => 'number',
    'default' => $element_defaults['e-countdown-expire-days'],
    'options' => array(
        'desc_tooltip' => true,
        'show_spinner' => true,
        'unit' => 'days',
        'show_if' => array('e-countdown-type', '=', 'evergreen'),
    ),
    'attributes' => array(
        'min' => 0,
    ),
));
$elements->add_field(array(
    'id' => 'e-countdown-expire-hours',
    'name' => __( 'Expire in Hours', 'masterpopups' ),
    'desc' => __( 'Hours to end the countdown timer.', 'masterpopups' ),
    'type' => 'number',
    'default' => $element_defaults['e-countdown-expire-hours'],
    'options' => array(
        'desc_tooltip' => true,
        'show_spinner' => true,
        'unit' => 'hours',
        'show_if' => array('e-countdown-type', '=', 'evergreen'),
    ),
    'attributes' => array(
        'min' => 0,
        'step' => 0.1,
        'precision' => 1,
    ),
));
$elements->close_mixed_field();



$elements->open_mixed_field(array('id' => 'e-countdown-labels-options', 'name' => 'Labels'));
$elements->add_field(array(
    'id' => 'e-countdown-labels',
    'name' => __( 'Time labels', 'masterpopups' ),
    'type' => 'checkbox',
    'default' => $element_defaults['e-countdown-labels'],
    'items' => array(
        'seconds' => 'Seconds',
        'minutes' => 'minutes',
        'hours' => 'hours',
        'days' => 'days',
        'weeks' => 'weeks',
        'months' => 'months',
    )
));
$elements->add_field(array(
    'id' => 'e-countdown-label-font-size',
    'name' => 'Font size',
    'type' => 'number',
    'default' => $element_defaults['e-countdown-label-font-size'],
    'options' => array(
        'show_spinner' => true,
        'unit' => 'px',
    ),
    'attributes' => array(
        'min' => 0,
    ),
));
$elements->add_field(array(
    'id' => 'e-countdown-label-font-color',
    'name' => 'Font color',
    'type' => 'colorpicker',
    'default' => $element_defaults['e-countdown-label-font-color'],
    'options' => array(
        'format' => 'rgba',
        'opacity' => 1,
    ),
));
$elements->add_field(array(
    'id' => 'e-countdown-labels-strings',
    'name' => 'Strings',
    'type' => 'text',
    'default' => $element_defaults['e-countdown-labels-strings'],
));
$elements->close_mixed_field();

$elements->open_mixed_field(array('id' => 'e-countdown-digits-options', 'name' => __( 'Digits', 'masterpopups' )));
$elements->add_field(array(
    'id' => 'e-countdown-width',
    'name' => __( 'Width', 'masterpopups' ),
    'type' => 'number',
    'default' => $element_defaults['e-countdown-width'],
    'options' => array(
        'unit' => 'px',
    ),
    'attributes' => array(
        'min' => 20,
    ),
));
$elements->add_field(array(
    'id' => 'e-countdown-height',
    'name' => __( 'Height', 'masterpopups' ),
    'type' => 'number',
    'default' => $element_defaults['e-countdown-height'],
    'options' => array(
        'unit' => 'px',
    ),
    'attributes' => array(
        'min' => 20,
    ),
));
$elements->close_mixed_field();


$elements->add_field(array(
    'id' => 'e-countdown-show-message',
    'name' => __( 'Show a message on timer end', 'masterpopups' ),
    'type' => 'switcher',
    'default' => $element_defaults['e-countdown-show-message'],
));

$elements->add_field(array(
    'id' => 'e-countdown-style',
    'type' => 'hidden',
    'default' => 'flip'
));


$elements->open_mixed_field(array(
    'id' => 'e-countdown-reset-options',
    'name' => __( 'Reset Countdown Timer', 'masterpopups' ),
    'desc_name' => __( 'Activate to reset the counter at the end of the countdown.', 'masterpopups' ),
    ));
$elements->add_field(array(
    'id' => 'e-countdown-reset',
    'name' => __( 'Enable', 'masterpopups' ),
    'type' => 'switcher',
    'default' => $element_defaults['e-countdown-reset'],
    'options' => array(
        'desc_tooltip' => false,
    ),
));
$elements->add_field(array(
    'id' => 'e-countdown-reset-days',
    'name' => __( 'Additional Days', 'masterpopups' ),
    'desc' => __( 'Additional days to the countdown timer.', 'masterpopups' ),
    'type' => 'number',
    'default' => $element_defaults['e-countdown-reset-days'],
    'options' => array(
        'desc_tooltip' => true,
        'show_spinner' => true,
        'unit' => 'days',
    ),
    'attributes' => array(
        'min' => 0,
    ),
));
$elements->add_field(array(
    'id' => 'e-countdown-reset-hours',
    'name' => __( 'Additional Hours', 'masterpopups' ),
    'desc' => __( 'Additional hours to the countdown timer.', 'masterpopups' ),
    'type' => 'number',
    'default' => $element_defaults['e-countdown-reset-hours'],
    'options' => array(
        'desc_tooltip' => true,
        'show_spinner' => true,
        'unit' => 'hours',
    ),
    'attributes' => array(
        'min' => 0,
        'step' => 0.1,
        'precision' => 1,
    ),
));
$elements->add_field(array(
    'id' => 'e-countdown-reset-type',
    'name' => __( 'Reset type', 'masterpopups' ),
    'type' => 'select',
    'default' => $element_defaults['e-countdown-reset-type'],
    'items' => array(
        'auto' => __( 'Reset Automatically', 'masterpopups' ),
        'session' => __( 'Reset in the next Session', 'masterpopups' ),
        'days' => __( 'Reset after X days', 'masterpopups' ),
    ),
));
$elements->add_field(array(
    'id' => 'e-countdown-reset-after-days',
    'name' => __( 'Reset After X Days', 'masterpopups' ),
    'desc' => __( 'The countdown will be reset after the indicated days.', 'masterpopups' ),
    'type' => 'number',
    'default' => $element_defaults['e-countdown-reset-after-days'],
    'options' => array(
        'desc_tooltip' => true,
        'show_spinner' => true,
        'unit' => 'days',
        'show_if' => array('e-countdown-reset-type', '=', 'days'),
    ),
    'attributes' => array(
        'min' => 1,
    ),
));
$elements->close_mixed_field();


$elements->add_field(array(
    'id' => 'e-recaptcha-title',
    'name' => __( 'Google reCAPTCHA', 'masterpopups' ),
    'desc' => sprintf(__( 'Make sure to add your Google reCaptcha Api keys (Site Key and Secret Key) in the %s', 'masterpopups' ), '<a href="'.$class->plugin->settings_url.'" target="_blank">'.__( 'General Settings > Form validation > Google reCAPTCHA', 'masterpopups' ).'</a>' ),
    'type' => 'title',
));
$elements->add_field(array(
    'id' => 'e-recaptcha-version',
    'name' => __( 'reCaptcha version', 'masterpopups' ),
    'desc' => __( 'Make sure the version is the same as the General Settings.', 'masterpopups' ),
    'type' => 'radio',
    'default' => $element_defaults['e-recaptcha-version'],
    'items' => array(
        'v3' => 'Version 3 (Invisible)',
        'v2' => 'Version 2 (Checkbox)',
        'invisible' => 'Version 2 (Invisible)',
    ),
));
$elements->add_field(array(
    'id' => 'e-recaptcha-theme',
    'name' => __( 'reCaptcha theme', 'masterpopups' ),
    'type' => 'radio',
    'default' => $element_defaults['e-recaptcha-theme'],
    'items' => array(
        'light' => 'Light',
        'dark' => 'Dark',
    ),
));


