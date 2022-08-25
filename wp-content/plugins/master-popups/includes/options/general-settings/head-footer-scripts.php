<?php

$xbox->open_mixed_field(array(
    'name' => 'Scripts in Head',
    'desc_name' => sprintf( __( 'Add valid html code to insert into the %s tag', 'masterpopups' ), '<code>&lt;head&gt;</code>' ),
));
$xbox->add_field( array(
    'id' => 'header-scripts',
    'name' => 'Scripts in Head',
    'type' => 'code_editor',
    'default' => $defaults['header-scripts'],
    'options' => array(
        'language' => 'html',
        'theme' => 'tomorrow_night',
        'height' => '280px',
    ),
) );
$xbox->add_field( array(
    'id' => 'header-scripts-priority',
    'name' => __('Wp Hook Priority', 'masterpopups'),
    'type' => 'number',
    'default' => $defaults['header-scripts-priority'],
    'attributes' => array(
        'min' => 1
    ),
    'options' => array(
        'show_spinner' => true,
        'show_unit' => false,
    )
));
$xbox->close_mixed_field();

$xbox->open_mixed_field(array(
    'name' => 'Scripts in Footer',
    'desc_name' => sprintf( __( 'Add valid html code to insert before %s tag', 'masterpopups' ), '<code>&lt;/body&gt;</code>' ),
));
$xbox->add_field( array(
    'id' => 'footer-scripts',
    'name' => 'Scripts in Footer',
    'type' => 'code_editor',
    'default' => $defaults['footer-scripts'],
    'options' => array(
        'language' => 'html',
        'theme' => 'tomorrow_night',
        'height' => '280px',
    ),
) );
$xbox->add_field( array(
    'id' => 'footer-scripts-priority',
    'name' => __('Wp Hook Priority', 'masterpopups'),
    'type' => 'number',
    'default' => $defaults['footer-scripts-priority'],
    'attributes' => array(
        'min' => 1
    ),
    'options' => array(
        'show_spinner' => true,
        'show_unit' => false,
    )
));
$xbox->close_mixed_field();