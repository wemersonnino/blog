<?php
$xbox->add_field(array(
    'id' => 'placeholder-color',
    'name' => 'Placeholder color',
    'type' => 'colorpicker',
    'default' => 'rgba(134,134,134,1)',
    'options' => array(
        'format' => 'rgba',
        'opacity' => 1,
    ),
));
$xbox->open_mixed_field(array('name' => 'Margin'));
	$xbox->add_field(array(
		'id' => 'margin-top',
		'name' => __( 'Margin top', 'masterpopups' ),
		'type' => 'text',
		'default' => '0',
        'grid' => '2-of-8',
	));
    $xbox->add_field(array(
        'id' => 'margin-right',
        'name' => __( 'Margin right', 'masterpopups' ),
        'type' => 'text',
        'default' => 'auto',
        'grid' => '2-of-8',
    ));
	$xbox->add_field(array(
		'id' => 'margin-bottom',
		'name' => __( 'Margin bottom', 'masterpopups' ),
        'type' => 'text',
        'default' => '0',
        'grid' => '2-of-8',
	));
    $xbox->add_field(array(
        'id' => 'margin-left',
        'name' => __( 'Margin left', 'masterpopups' ),
        'type' => 'text',
        'default' => 'auto',
        'grid' => '2-of-8',
    ));
$xbox->close_mixed_field();


$xbox->add_field(array(
	'id' => 'overflow',
	'name' => 'Overflow',
	'type' => 'select',
	'default' => 'visible',
	'items' => array(
		'auto' => 'Auto',
		'visible' => 'Visible',
		'hidden' => 'Hidden',
		'scroll' => 'Scroll',
	),
));

$xbox->add_field(array(
	'id' => 'disable-page-scroll',
	'name' => __( 'Disable page scroll', 'masterpopups' ),
	'type' => 'switcher',
	'default' => 'off',
	'desc' => __( 'Disable scrolling while the popup is open', 'masterpopups' ),
	'options' => array(
		'desc_tooltip' => false,
	),
));

$xbox->add_field(array(
    'id' => 'disclaimer-enabled',
    'name' => __( 'Enable Disclaimer Features', 'masterpopups' ),
    'desc' => __( 'For disclaimer messages, age verification popups, etc.', 'masterpopups' ),
    'type' => 'switcher',
    'default' => 'off',
    'options' => array(
        'desc_tooltip' => false,
    ),
));
$xbox->add_field(array(
    'id' => 'ratio-small-devices',
    'name' => __( 'Ratio for Small Devices', 'masterpopups' ),
    'desc' => __( 'Enter the value 0.9 if you want the slightly smaller popup.', 'masterpopups' ),
    'type' => 'number',
    'default' => '1',
    'options' => array(
        'show_spinner' => true,
        'show_unit' => false,
    ),
    'attributes' => array(
        'min' => 0,
        'step' => 0.1,
        'precision' => 1
    ),
));

$xbox->add_field(array(
    'id' => 'use-theme-links-color',
    'name' => __( 'Use theme links color', 'masterpopups' ),
    'desc' => __( 'If enabled, all links within the popup will have the color set in the current WordPress theme.', 'masterpopups' ),
    'type' => 'switcher',
    'default' => 'on',
    'options' => array(
        'desc_tooltip' => false,
    ),
));

$xbox->add_field(array(
    'id' => 'resize-when-opening-keyboard',
    'name' => __( 'Resize when opening keyboard', 'masterpopups' ),
    'desc' => __( 'Enable to resize the popup height when opening the keyboard on mobile devices.', 'masterpopups' ),
    'type' => 'switcher',
    'default' => 'on',
    'options' => array(
        'desc_tooltip' => false,
    ),
));

//Notification sounds