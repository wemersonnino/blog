<?php

$xbox->add_field(array(
    'id' => 'debug-mode',
    'name' => __( 'Debug mode', 'masterpopups' ),
    'desc' => __( 'This option is only for developers, allows to see processes and errors in the console.', 'masterpopups' ),
    'type' => 'switcher',
    'default' => 'off',
));
$xbox->add_field(array(
    'id' => 'fake-version',
    'name' => 'Debug version',
    'type' => 'text',
    'default' => $defaults['fake-version'],
    'desc' => 'Change this version for development testing only.',
    'grid' => '1-of-8'
));

//$xbox->add_field(array(
//    'id' => 'debug-ip',
//    'name' => 'IP',
//    'type' => 'text',
//    'default' => '',
//    'desc' => __( 'IP for debug mode', 'masterpopups' ),
//    'grid' => '1-of-8'
//));