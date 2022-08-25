<?php

use MasterPopups\Includes\Functions as Functions;

$xbox->add_field(array(
	'type' => 'title',
	'name' => __( 'Successful form submission', 'masterpopups' ),
	'desc' => __( 'What would you like to do when the form was successfully submitted?', 'masterpopups'),
));

/*
|---------------------------------------------------------------------------------------------------
| Set Cookie
|---------------------------------------------------------------------------------------------------
*/
$xbox->open_mixed_field(array(
	'name' => __( 'Set cookie after conversion', 'masterpopups' ),
	'desc_name' => __( 'Enable this option to not display again the popup after successful form submission.', 'masterpopups' ),
    'insert_after_name' => "<a href='javascript:void(0)' class='xbox-btn xbox-btn-teal xbox-btn-small ampp-margin-top-10 ampp-btn-clear-cookie cookie-on-conversion'>Clear Cookie</a>",
));
	$xbox->add_field(array(
		'id' => 'cookie-on-conversion',
		'name' => __( 'Enable', 'masterpopups' ),
		'type' => 'switcher',
		'default' => 'on',
		'options' => array(
			'desc_tooltip' => true,
			//'show_name' => false,
		)
	));
	$xbox->add_field(array(
		'id' => 'cookie-on-conversion-duration',
		'name' => __( 'Cookie duration', 'masterpopups' ),
		'type' => 'radio',
		'default' => 'days',
		'items' => array(
			'current_session' => __( 'Current session', 'masterpopups' ),
			'days' => __( 'Define days', 'masterpopups' ),
		),
	));
	$xbox->add_field(array(
		'id' => 'cookie-on-conversion-days',
		'name' => __( 'Days', 'masterpopups' ),
		'desc' => __( 'The popup will be displayed once every "X" days.', 'masterpopups' ),
		'type' => 'number',
		'default' => '60',
		'options' => array(
			'desc_tooltip' => true,
			'show_spinner' => true,
			'unit' => 'days',
			'show_if' => array('cookie-on-conversion-duration', '=', 'days' ),
		),
		'attributes' => array(
			'min' => 1,
		),
	));
    $xbox->add_field(array(
        'id' => 'message-on-conversion',
        'name' => __( 'Message after conversion', 'masterpopups' ),
        'desc' => __( 'This message will allow to show the popup even after the conversion but it will replace to the form.', 'masterpopups' ),
        'type' => 'textarea',
        'default' => '',
        'attributes' => array(
            'rows' => '3'
        ),
    ));
$xbox->close_mixed_field();

/*
|---------------------------------------------------------------------------------------------------
| Close Popup
|---------------------------------------------------------------------------------------------------
*/
$xbox->open_mixed_field(array('name' => __( 'Close popup', 'masterpopups' ) ));
	$xbox->add_field(array(
		'id' => 'form-submission-ok-close-popup',
		'name' => __( 'Enable', 'masterpopups' ),
		'type' => 'switcher',
		'default' => 'on',
	));
	$xbox->add_field(array(
		'id' => 'form-submission-ok-close-popup-delay',
		'name' => __( 'Close popup after', 'masterpopups' ),
		'type' => 'number',
		'default' => '3200',
		'options' => array(
			'show_spinner' => true,
			'unit' => 'ms',
			'show_if' => array('form-submission-ok-close-popup', '=', 'on' ),
		),
		'attributes' => array(
			'min' => 0,
			'step' => 100,
		),
	));
	$xbox->add_field(array(
		'id' => 'form-submission-ok-open-popup-id',
		'name' => __( 'Close popup and Open another popup', 'masterpopups' ),
		'type' => 'text',
		'desc' => __( 'Enter the popup id. E.g: 20', 'masterpopups' ),
		'default' => '',
		'options' => array(
			'show_if' => array('form-submission-ok-close-popup', '=', 'on')
		),
		'grid' => '4-of-8 last'
	));
$xbox->close_mixed_field();


/*
|---------------------------------------------------------------------------------------------------
| Download file
|---------------------------------------------------------------------------------------------------
*/
$xbox->open_mixed_field(array('name' => __( 'Download file', 'masterpopups' ) ));
	$xbox->add_field(array(
		'id' => 'form-submission-ok-download-file',
		'type' => 'switcher',
		'default' => 'off',
		'options' => array(
			'show_name' => false,
		)
	));
	$xbox->add_field(array(
		'id' => 'form-submission-ok-file',
		'name' => __( 'File to download', 'masterpopups' ),
		'type' => 'file',
		'default' => '',
		'desc' => __( 'Enter the url of the file that the user will download after sending the form. Add http/https. e.g: http://google.com', 'masterpopups' ),
		'options' => array(
			'show_if' => array('form-submission-ok-download-file', '=', 'on' )
		)
	));
$xbox->close_mixed_field();

/*
|---------------------------------------------------------------------------------------------------
| Redirect user
|---------------------------------------------------------------------------------------------------
*/
$xbox->open_mixed_field(array('name' => __( 'Redirect user', 'masterpopups' ) ));
	$xbox->add_field(array(
		'id' => 'form-submission-ok-redirect',
		'type' => 'switcher',
		'default' => 'off',
		'options' => array(
			'show_name' => false,
		)
	));
	$xbox->add_field(array(
		'id' => 'form-submission-ok-redirect-to',
		'name' => __( 'Redirect to URL', 'masterpopups' ),
		'type' => 'text',
		'default' => 'http://',
		'desc' => __( 'Enter the url where you would like to redirect the user after sending the form. Add http/https. e.g: http://google.com', 'masterpopups' ),
	));
    $xbox->add_field(array(
        'id' => 'form-submission-ok-redirect-target',
        'name' => 'Target',
        'type' => 'radio',
        'default' => '_self',
        'items' => array(
            '_self' => __( 'Same Window', 'masterpopups' ),
            '_blank' => __( 'New Window', 'masterpopups' ),
        ),
    ));
$xbox->close_mixed_field();

