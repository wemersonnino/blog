<?php

$xbox->add_field( array(
	'id' => 'position',
	'name' => __( 'Position', 'masterpopups' ),
	'type' => 'image_selector',
	'default' => 'middle-center',
	'items' => array(
		'top-left' => MPP_URL.'assets/admin/images/popup-position-modal.png',
		'top-center' => MPP_URL.'assets/admin/images/popup-position-modal.png',
		'top-right' => MPP_URL.'assets/admin/images/popup-position-modal.png',
		'middle-left' => MPP_URL.'assets/admin/images/popup-position-modal.png',
		'middle-center' => MPP_URL.'assets/admin/images/popup-position-modal.png',
		'middle-right' => MPP_URL.'assets/admin/images/popup-position-modal.png',
		'bottom-left' => MPP_URL.'assets/admin/images/popup-position-modal.png',
		'bottom-center' => MPP_URL.'assets/admin/images/popup-position-modal.png',
		'bottom-right' => MPP_URL.'assets/admin/images/popup-position-modal.png',
		'top-bar' => MPP_URL.'assets/admin/images/popup-position-infobar.png',
		'bottom-bar' => MPP_URL.'assets/admin/images/popup-position-infobar.png',
	),
	'options' => array(
		'width' => '100px',
		'in_line' => false
	),
));

$xbox->open_mixed_field(array('name' => __( 'Popup size', 'masterpopups' )));
	$xbox->add_field(array(
		'id' => 'width',
		'name' => __( 'Width', 'masterpopups' ),
		'type' => 'number',
		'default' => 640,
		'options' => array(
			'show_spinner' => true,
			//'show_if' => array('full-screen', '=', 'off')
		),
		'attributes' => array(
			'min' => 0,
		),
	));
	$xbox->add_field(array(
		'id' => 'height',
		'name' => __( 'Height', 'masterpopups' ),
		'type' => 'number',
		'default' => 360,
		'options' => array(
			'show_spinner' => true,
			//'show_if' => array('full-screen', '=', 'off')
		),
		'attributes' => array(
			'min' => 0,
		),
	));
	$xbox->add_field(array(
		'id' => 'full-screen',
		'name' => __( 'Full screen', 'masterpopups' ),
		'type' => 'switcher',
		'default' => 'off',
		'desc' => __( 'The popup will cover the entire screen area. This option will overwrite the previous size options.', 'masterpopups' ),
		'options' => array(
			'desc_tooltip' => true,
		),
	));
	$xbox->add_field(array(
		'id' => 'browser-height',
		'name' => __( 'Browser Height', 'masterpopups' ),
		'type' => 'hidden',
		'default' => 580,
	));
$xbox->close_mixed_field();

$xbox->open_mixed_field(array('name' => __( 'Popup background', 'masterpopups' )));
	$xbox->add_field(array(
		'id' => 'bg-color',
		'name' => 'Background color',
		'type' => 'colorpicker',
		'default' => 'rgba(255,255,255,0.9)',
		'options' => array(
			'format' => 'rgba',
			'opacity' => '0.7',
		),
	));
	$xbox->add_field(array(
		'id' => 'bg-repeat',
		'name' => 'Background repeat',
		'type' => 'select',
		'default' => 'no-repeat',
		'items' => array(
			'no-repeat' => 'No repeat',
			'repeat' => 'Repeat',
			'repeat-x' => 'Repeat-x',
			'repeat-y' => 'Repeat-y',
		),
	));
	$xbox->add_field(array(
		'id' => 'bg-size',
		'name' => 'Background size',
		'type' => 'select',
		'default' => 'cover',
		'items' => array(
			'auto' => 'Auto',
			'cover' => 'Cover',
			'contain' => 'Contain',
		),
	));
	$xbox->add_field(array(
		'id' => 'bg-position',
		'name' => 'Background position',
		'type' => 'text',
		'default' => 'center center',
		'row_class' => 'not-full-width',
		'attributes' => array(
			'style' => 'width: 110px'
		)
	));
	$xbox->add_field(array(
		'id' => 'bg-image',
		'name' => 'Background image',
		'type' => 'file',
		'options' => array(
			'mime_types' => array( 'jpg', 'jpeg', 'png', 'gif', 'ico' ),
			'preview_size' => array( 'width' => '30px','height' => '30px' ),
		),
		'row_class' => 'mpp-image-file',
		'grid' => '7-of-8 last'
	));
$xbox->close_mixed_field();

$xbox->open_mixed_field(array('name' => __( 'Styles', 'masterpopups' )));
$xbox->add_field(array(
    'id' => 'border-radius',
    'name' => __( 'Border radius', 'masterpopups' ),
    'type' => 'number',
    'default' => '0',
    'attributes' => array(
        'min' => 0,
    ),
    'options' => array(
        'show_spinner' => true,
    ),
));
$xbox->add_field(array(
    'id' => 'box-shadow',
    'name' => 'Box shadow',
    'type' => 'text',
    'default' => '0px 0px 16px 4px rgba(0,0,0,0.5)',
    'grid' => '3-of-8'
));
$xbox->close_mixed_field();

/*
|---------------------------------------------------------------------------------------------------
| Wordpress editor
|---------------------------------------------------------------------------------------------------
*/
include MPP_DIR . 'includes/options/popup-editor/general/wp-editor.php';