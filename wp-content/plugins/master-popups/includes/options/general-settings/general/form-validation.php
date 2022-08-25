<?php

$xbox->add_field(array(
    'name' => __( 'Google reCAPTCHA', 'masterpopups' ),
    'desc' => sprintf(__( 'Register your domain name with Google reCaptcha service and add the Api keys to the fields below. Go to %s', 'masterpopups' ), '<a href="https://www.google.com/recaptcha/admin#list" target="_blank">'.__( 'Google reCAPTCHA', 'masterpopups' ).'</a>' ),
    'type' => 'title',
));
$xbox->add_field(array(
    'id' => 'recaptcha-site-key',
    'name' => __( 'Site Key', 'masterpopups' ),
    'type' => 'text',
    'default' => '',
    'grid' => '5-of-8'
));
$xbox->add_field(array(
    'id' => 'recaptcha-secret-key',
    'name' => __( 'Secret Key', 'masterpopups' ),
    'type' => 'text',
    'default' => '',
    'grid' => '5-of-8'
));
$xbox->add_field(array(
    'id' => 'recaptcha-version',
    'name' => __( 'reCaptcha Version', 'masterpopups' ),
    'type' => 'radio',
    'default' => 'v2',
    'items' => array(
        'v3' => 'Version 3 (Invisible)',
        'v2' => 'Version 2 (Checkbox)',
        'invisible' => 'Version 2 (Invisible)',
    ),
));
$xbox->add_field(array(
    'id' => 'recaptcha-version3-score',
    'name' => __( 'reCaptcha Version 3 score', 'masterpopups' ),
    'type' => 'select',
    'default' => '0.6',
    'items' => array(
        '0.1' => '0.1',
        '0.2' => '0.2',
        '0.3' => '0.3',
        '0.4' => '0.4',
        '0.5' => '0.5',
        '0.6' => '0.6',
        '0.7' => '0.7',
        '0.8' => '0.8',
        '0.9' => '0.9',
        '1' => '1',
    ),
    'options' => array(
        'show_if' => array('recaptcha-version', '=', 'v3')
    )
));
//$xbox->add_field(array(
//    'id' => 'recaptcha-hide-badge',
//    'name' => __( 'Hide reCaptcha Badge', 'masterpopups' ),
//    'type' => 'switcher',
//    'default' => 'off',
//));



$xbox->add_field(array(
    'name' => __( 'Form validation messages', 'masterpopups' ),
    'type' => 'title',
));
$xbox->add_field(array(
    'id' => 'validation-msg-general',
    'name' => 'General',
    'type' => 'text',
    'default' => __( 'This field is required', 'masterpopups' ),
    'grid' => '5-of-8'
));
$xbox->add_field(array(
    'id' => 'validation-msg-email',
    'name' => 'Email',
    'type' => 'text',
    'default' => __( 'Invalid email address', 'masterpopups' ),
    'grid' => '5-of-8'
));
$xbox->add_field(array(
    'id' => 'validation-msg-checkbox',
    'name' => 'Checkbox',
    'type' => 'text',
    'default' => __( 'This field is required, please check', 'masterpopups' ),
    'grid' => '5-of-8'
));
$xbox->add_field(array(
    'id' => 'validation-msg-dropdown',
    'name' => 'Dropdown',
    'type' => 'text',
    'default' => __( 'This field is required. Please select an option', 'masterpopups' ),
    'grid' => '5-of-8'
));
$xbox->add_field(array(
    'id' => 'validation-msg-minlength',
    'name' => 'Min length',
    'type' => 'text',
    'default' => __( 'Min length:', 'masterpopups' ),
    'grid' => '5-of-8'
));
$xbox->add_field(array(
    'id' => 'form-submission-back-to-form-text',
    'name' => __( 'Back to form', 'masterpopups' ),
    'type' => 'text',
    'default' => __( 'Back to form', 'masterpopups' ),
    'grid' => '2-of-8'
));
$xbox->add_field(array(
    'id' => 'form-submission-close-popup-text',
    'name' => __( 'Close', 'masterpopups' ),
    'type' => 'text',
    'default' => __( 'Close', 'masterpopups' ),
    'grid' => '2-of-8'
));