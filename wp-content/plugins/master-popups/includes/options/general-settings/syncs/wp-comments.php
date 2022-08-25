<?php

use MasterPopups\Includes\Functions;
use MasterPopups\Includes\Lista;
use MasterPopups\Includes\Settings;

$section_wp_comments->add_field( array(
    'name' => __( 'Enable synchronization', 'masterpopups' ),
    'id' => 'sync-wp-comments-enabled',
    'desc' => __( 'Activate this option to start saving user data.', 'masterpopups' ),
    'type' => 'switcher',
    'default' => $defaults['sync-wp-comments-enabled'],
));

$section_wp_comments->add_field( array(
    'name' => __( 'Only approved comments', 'masterpopups' ),
    'id' => 'sync-wp-comments-only-approved',
    'desc' => __( 'Synchronize only when comments are approved.', 'masterpopups' ),
    'type' => 'switcher',
    'default' => $defaults['sync-wp-comments-only-approved'],
));

$section_wp_comments->add_field(array(
    'id' => 'sync-wp-comments-list-id',
    'name' => 'List',
    'type' => 'select',
    'items' => Lista::get_all_lists(),
    'desc' => __( 'Choose the list where the new users will be stored.', 'masterpopups' ),
    'grid' => '2-of-8',
));

$section_wp_comments->open_mixed_field(array(
    'name' => __( 'Checkbox', 'masterpopups' ),
    'desc_name' => __( 'Shows a checkbox at the end of the form.', 'masterpopups' ),
));
$section_wp_comments->add_field( array(
    'id' => 'sync-wp-comments-use-checkbox',
    'name' => __( 'Display Checkbox', 'masterpopups' ),
    'type' => 'switcher',
    'default' => $defaults['sync-wp-comments-use-checkbox'],
    'grid' => '6-of-6',
));
$section_wp_comments->add_field( array(
    'id' => 'sync-wp-comments-checkbox-text',
    'name' => __('Checkbox text', 'masterpopups'),
    'type' => 'text',
    'default' => $defaults['sync-wp-comments-checkbox-text'],
    'grid' => '3-of-6',
));
$section_wp_comments->close_mixed_field();

