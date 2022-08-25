<?php
use MasterPopups\Includes\Functions;

$xbox->add_export_field(array(
	'name' => __( 'Export your popup', 'masterpopups' ),
	'desc' => __( 'Save your popup template and use it on another website. Save the data in .json format', 'masterpopups' ),
	'options' => array(
		'show_name' => false,
        'export_button_text'  => __( 'Download', 'masterpopups' ),
        'export_file_name' => 'backup-master-popup-' . Functions::post_id() . '_date',
	)
));