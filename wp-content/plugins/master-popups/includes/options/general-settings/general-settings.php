<?php

$defaults = include dirname( __FILE__ ) . '/defaults.php';
$id_main_tab = 'main-tab';
$items_main_tab = array(
    'crm-integrations' => '<i class="xbox-icon xbox-icon-envelope"></i>'.__( 'Email Marketing Integrations', 'masterpopups' ),
    'general' => '<i class="xbox-icon xbox-icon-object-group"></i>General',
    'email-validations' => '<i class="xbox-icon xbox-icon-check"></i>'.__( 'Email Validations', 'masterpopups' ),
    'syncs' => '<i class="xbox-icon xbox-icon-refresh"></i>'.__( 'Synchronizations', 'masterpopups' ),
    'activation' => '<i class="xbox-icon xbox-icon-key"></i>'.__( 'Plugin Activation', 'masterpopups' ),
    'custom-css' => '<i class="xbox-icon xbox-icon-paint-brush"></i>'.__( 'Custom CSS', 'masterpopups' ),
    'custom-js' => '<i class="xbox-icon xbox-icon-code"></i>'.__( 'Custom JS', 'masterpopups' ),
    'head-footer-scripts' => '<i class="xbox-icon xbox-icon-code"></i>'.__( 'Head & Footer Scripts', 'masterpopups' ),
    'rate' => '<i class="xbox-icon xbox-icon-star"></i>Rate our Plugin',
    //'promote' => '<i class="xbox-icon xbox-icon-dollar"></i>Promote & Earn money',
);
$items_main_tab = apply_filters('mpp_settings_tab_items', $items_main_tab, $id_main_tab );

$xbox->add_main_tab(array(
	'name' => 'Main tab',
	'id' => 'main-tab',
	'items' => $items_main_tab,
));

$xbox->open_tab_item('crm-integrations');
include MPP_DIR . 'includes/options/general-settings/crm-integrations/crm-integrations.php';
$xbox->close_tab_item('crm-integrations');

$xbox->open_tab_item('general');
include MPP_DIR . 'includes/options/general-settings/general/index-general.php';
$xbox->close_tab_item('general');

$xbox->open_tab_item('email-validations');
include MPP_DIR . 'includes/options/general-settings/email-validations/email-validations.php';
$xbox->close_tab_item('email-validations');

$xbox->open_tab_item('syncs');
include MPP_DIR . 'includes/options/general-settings/syncs/syncs.php';
$xbox->close_tab_item('syncs');


$xbox->open_tab_item('activation');
include MPP_DIR . 'includes/options/general-settings/activation/activation.php';
$xbox->close_tab_item('activation');

$xbox->open_tab_item('custom-css');
include MPP_DIR . 'includes/options/general-settings/custom-css/custom-css.php';
$xbox->close_tab_item('custom-css');

$xbox->open_tab_item('custom-js');
include MPP_DIR . 'includes/options/general-settings/custom-js/custom-js.php';
$xbox->close_tab_item('custom-js');

$xbox->open_tab_item('head-footer-scripts');
include MPP_DIR . 'includes/options/general-settings/head-footer-scripts.php';
$xbox->close_tab_item('head-footer-scripts');

$xbox->open_tab_item('rate');
include MPP_DIR . 'includes/options/general-settings/rate.php';
$xbox->close_tab_item('rate');

//$xbox->open_tab_item('promote');
//include MPP_DIR . 'includes/options/general-settings/promote.php';
//$xbox->close_tab_item('promote');

$xbox = apply_filters( 'mpp_settings_tab_fields', $xbox );

$xbox->close_tab('main-tab');



