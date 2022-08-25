<?php

$xbox->add_field(array(
    'id' => 'minify-js',
    'name' => __( 'Minify Js Files', 'masterpopups' ),
    'type' => 'switcher',
    'default' => 'on',
));

$xbox->add_field(array(
    'id' => 'load-videojs',
    'name' => __( 'Add HTML5 Video Support', 'masterpopups' ),
    'type' => 'switcher',
    'default' => 'off',
));

$xbox->add_field(array(
    'id' => 'load-google-fonts',
    'name' => __( 'Load Google Fonts', 'masterpopups' ),
    'type' => 'switcher',
    'default' => 'on',
));

$xbox->add_field(array(
    'id' => 'load-font-awesome',
    'name' => __( 'Load Font Awesome', 'masterpopups' ),
    'type' => 'switcher',
    'default' => 'on',
));