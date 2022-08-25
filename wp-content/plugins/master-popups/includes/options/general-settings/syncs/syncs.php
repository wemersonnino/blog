<?php

$xbox->add_tab(array(
    'id' => 'tab-syncs',
    'items' => array(
        'syncs-general' => __( 'Synchronizations', 'masterpopups' )
    ),
));
$xbox->open_tab_item('syncs-general');


$section_wp_comments = $xbox->add_section(array(
    'id' => 'section-wp-comments',
    'name' => 'WP Comment Form',
    'options' => array(
        'toggle' => true,
        'toggle_default' => 'open',
    )
));
include dirname(__FILE__) .'/wp-comments.php';

do_action( 'masterpopups_general_settings_syncs', $xbox );

$xbox->close_tab_item('syncs-general');

$xbox->close_tab('tab-syncs');