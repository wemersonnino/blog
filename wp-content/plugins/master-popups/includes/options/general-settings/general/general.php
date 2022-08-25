<?php
use MasterPopups\Includes\Functions as Functions;


$xbox->add_field(array(
    'id' => 'popups-z-index',
    'name' => __( 'Z-Index for Popups', 'masterpopups' ),
    'type' => 'number',
    'default' => '99999999',
    'options' => array(
        'show_spinner' => true,
        'show_unit' => false,
    ),
    'attributes' => array(
        'min' => 1,
    ),
    'grid' => '2-of-8'
));

$xbox->add_field(array(
    'id' => 'sticky-z-index',
    'name' => __( 'Z-Index for Sticky', 'masterpopups' ),
    'type' => 'number',
    'default' => '100000005',
    'options' => array(
        'show_spinner' => true,
        'show_unit' => false,
    ),
    'attributes' => array(
        'min' => 1,
    ),
    'grid' => '2-of-8'
));


$xbox->add_field(array(
    'id' => 'disable-user-roles',
    'name' => __( 'Disable popup management for', 'masterpopups' ),
    'type' => 'checkbox',
    'default' => array(),
    'items' => Functions::get_user_roles(array('edit_posts'), array('administrator')),
));

$xbox->add_field(array(
    'id' => 'enable-enqueue-popups',
    'name' => __( 'Enable popups queue', 'masterpopups' ),
    'desc' => __( 'By default the popups are displayed one at a time, if you want to show multiple popups at time disable this option.', 'masterpopups' ),
    'type' => 'switcher',
    'default' => 'on',
));

$xbox->add_field(array(
    'id' => 'show-link-edit-popup',
    'name' => __( 'Show link to edit the Popup', 'masterpopups' ).' (for admin user)',
    'type' => 'switcher',
    'default' => 'off',
));

$xbox->add_field(array(
    'id' => 'attach-error-on-form-failed',
    'name' => __( 'Attach internal errors after form failed', 'masterpopups' ),
    'desc' => __( 'Activate this option to show internal errors when the form submission fails. Allows you to configure the form correctly.', 'masterpopups' ),
    'type' => 'switcher',
    'default' => 'on',
));

$xbox->add_field(array(
    'id' => 'target-enabled-custom-post-types',
    'name' => __( 'Show Popup on Custom Post Types', 'masterpopups' ),
    'desc' => sprintf(__( 'Default value for "%s" option in the Popup Editor.', 'masterpopups' ), 'Display Popup on Custom Post Types'),
    'type' => 'switcher',
    'default' => 'on',
));

$xbox->add_field(array(
    'id' => 'target-display-all-tags',
    'name' => __( 'Display all Tags in the Popup Editor', 'masterpopups' ),
    'type' => 'switcher',
    'default' => 'off',
));

$xbox->add_field(array(
    'id' => 'verify-wp-nonce',
    'name' => __( 'Verify WP Nonce', 'masterpopups' ),
    'desc' => __( 'Increase security when processing forms. If you have problems with the cache of your website, please disable this option.', 'masterpopups' ),
    'type' => 'switcher',
    'default' => 'on',
));


$xbox->add_field(array(
    'id' => 'send-data-to-developer',
    'name' => 'Collaborate with the development',
    'desc' => 'This option allows the developer to improve and optimize the options of the plugin.',
    'type' => 'switcher',
    'default' => 'on',
));
