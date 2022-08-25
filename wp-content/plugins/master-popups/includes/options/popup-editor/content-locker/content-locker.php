<?php

use MasterPopups\Includes\Functions;

$xbox->add_field(array(
    'id' => 'content-locker',
    'name' => __( 'Content locker', 'masterpopups' ),
    'desc' => __( 'Enable to use the popup as a content blocker.', 'masterpopups' ),
    'type' => 'switcher',
    'default' => 'off',
));

$xbox->add_field(array(
    'id' => 'content-locker-type',
    'name' => __( 'Content locker type', 'masterpopups' ),
    'type' => 'radio',
    'default' => 'shortcode',
    'items' => array(
        'shortcode' => __( 'Lock with shortcode', 'masterpopups' ),
        'page_content' => __( 'Lock page content', 'masterpopups' ),
        'whole_page' => __( 'Lock whole page', 'masterpopups' ),
    ),
));
$xbox->open_mixed_field(array(
    'name' => __( 'Unlock content', 'masterpopups' ),
));
$xbox->add_field(array(
    'id' => 'content-locker-unlock',
    'name' => __( 'Choose how the content should be unlocked', 'masterpopups' ),
    'desc' => sprintf(__( 'If you choose the second option, click %shere%s to choose Form type.', 'masterpopups' ), "<a href='javascript:void(0)' class='ampp-link-go-tab ampp-link-go-tab-form-submission'>", "</a>"),
    'type' => 'radio',
    'default' => 'password',
    'items' => array(
        'password' => __( 'Unlock using password', 'masterpopups' ),
        'form' => __( 'Unlock using Subscription/Contact Form', 'masterpopups' ),
    ),
));
$xbox->add_field(array(
    'id' => 'content-locker-password',
    'name' => __( 'Password to unlock', 'masterpopups' ),
    'type' => 'text',
    'grid' => '2-of-8',
    'sanitize_callback' => false,
    'options' => array(
        'show_if' => array('content-locker-unlock', '=', 'password')
    ),
));
$xbox->close_mixed_field();

$xbox->add_field(array(
    'id' => 'content-locker-duration',
    'name' => __( 'Cookie duration', 'masterpopups' ),
    'desc' => __( 'Re-lock content after "X" days.', 'masterpopups' ),
    'type' => 'number',
    'default' => '365',
    'options' => array(
        'show_spinner' => true,
        'unit' => 'days',
    ),
    'attributes' => array(
        'min' => 1,
    ),
    'insert_after_name' => "<a href='javascript:void(0)' class='xbox-btn xbox-btn-teal xbox-btn-small ampp-margin-left-20 ampp-btn-clear-cookie cookie-content-locker'>Clear Cookie</a>",
));

$popup_id = Functions::post_id();
$block_content = '';
$block_content .= '<textarea class="ampp-input-selector" readonly style="display: block; width: 100%; height: 60px;">';
$block_content .= '[mpp_content_locker popup_id="'.$popup_id.'"]Content to lock here[/mpp_content_locker]';
$block_content .= '</textarea>';

$xbox->add_field( array(
    'id' => 'content-locker-shortcode',
    'name' => __('Content locker shortcode', 'masterpopups'),
    'type' => 'html',
    'content' => $block_content,
    'grid' => '8-of-8',
));