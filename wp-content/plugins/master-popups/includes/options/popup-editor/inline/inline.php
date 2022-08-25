<?php
$xbox->add_field(array(
	'id' => 'inline-should-close',
	'name' => __( 'Should close popup on Inline Mode', 'masterpopups' ),
	'desc' => __( 'If is disabled, any element or action to close the popup will be deleted or disabled.', 'masterpopups' ),
	'type' => 'switcher',
	'default' => 'off',
));

$xbox->add_field(array(
    'id' => 'inline-disable-triggers',
    'name' => __( 'Disable other triggers', 'masterpopups' ),
    'desc' => __( 'Activate this option to deactivate other triggers (On Page Load, On Exit Intent, etc) in case the popup is already displayed on Inline Mode.', 'masterpopups' ),
    'type' => 'switcher',
    'default' => 'off',
));