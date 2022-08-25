<?php


$xbox->add_tab(array(
    'id' => 'tab-general',
    'items' => array(
        'main-general' => __( 'General', 'masterpopups' ),
        'optimization' => __( 'Optimization', 'masterpopups' ),
        'form-validation' => __( 'Form Validation', 'masterpopups' ),
        'debug' => __( 'Debug', 'masterpopups' ),
    ),
));

$xbox->open_tab_item('main-general');
include dirname(__FILE__) .'/general.php';
$xbox->close_tab_item('main-general');

$xbox->open_tab_item('optimization');
include dirname(__FILE__) .'/optimization.php';
$xbox->close_tab_item('optimization');

$xbox->open_tab_item('form-validation');
include dirname(__FILE__) .'/form-validation.php';
$xbox->close_tab_item('form-validation');

$xbox->open_tab_item('debug');
include dirname(__FILE__) .'/debug.php';
$xbox->close_tab_item('debug');

$xbox->close_tab('tab-general');



