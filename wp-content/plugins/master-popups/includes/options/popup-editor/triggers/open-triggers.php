<?php

use MasterPopups\Includes\Functions;

/*
|---------------------------------------------------------------------------------------------------
| On Click element
|---------------------------------------------------------------------------------------------------
*/
$xbox->add_field(array(
    'id' => 'load-counter',
    'name' => __( 'Show if previously visited X pages', 'masterpopups' ),
    'default' => '0',
    'type' => 'select',
    'items' => array(
        '0' => '0',
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
        '5' => '5',
    ),
    'desc' => __( 'Show the popup whenever the user has previously visited X pages. 0 = The first time.', 'masterpopups' ),
));


$on_click = $xbox->add_section( array(
    'name' => __( 'On Click', 'masterpopups' ),
    'desc' => __( 'Display the popup by clicking on certain element', 'masterpopups' ),
    'options' => array(
        'toggle' => true,
        'toggle_default' => 'open',//open,close
    ),
));

$on_click->add_field(array(
	'id' => 'trigger-open-on-click-event',
	'name' => __( 'Event', 'masterpopups' ),
	'type' => 'radio',
	'default' => 'click',
	'items' => array(
		'click' => 'Click',
		'hover' => 'Hover',
	),
	'options' => array(
		'desc_tooltip' => true,
	)
));

$content = __( 'Use this class to execute your popup:', 'masterpopups' );
$content .= '<div style="margin-left: 20px; display: inline-block;">';
	$content .= '<input class="ampp-input-selector" readonly onfocus="this.select()" value="mpp-trigger-popup-'.Functions::post_id().'" style="width: 220px;">';
$content .= '</div>';
$content .= '<div class="ampp-margin-top-10">';
	$content .= __( 'Usage examples:', 'masterpopups' );
	$content .= '<textarea class="ampp-input-selector" readonly style="display: block; margin-top: 4px; width: 100%;">';
		$content .= '<a href="#" class="mpp-trigger-popup-'.Functions::post_id().'">Open popup</a>';
		$content .= "\n".'<a href="mpp-trigger-popup-'.Functions::post_id().'">Open popup</a>';
	$content .= '</textarea>';
$content .= '</div>';

$on_click->add_field(array(
	'id' => 'trigger-open-on-click-info',
	'type' => 'html',
	'content' => $content,
	'grid' => '8-of-8',
	'options' => array(
		'desc_tooltip' => true,
		'show_name' => false,
	)
));
$on_click->add_field(array(
	'id' => 'trigger-open-on-click-custom-class',
	'name' => __( 'Enter your custom class', 'masterpopups' ),
	'type' => 'text',
	'default' => 'your-custom-class',
	'options' => array(
		'desc_tooltip' => true,
	)
));

$on_click->add_field(array(
	'id' => 'trigger-open-on-click-prevent-default',
	'name' => __( 'Prevent Default Event', 'masterpopups' ),
	'type' => 'switcher',
	'default' => 'on',
	'desc' => __( 'Enable to avoid the default event when clicking', 'masterpopups' ),
	'options' => array(
		'desc_tooltip' => false,
	)
));

/*
|---------------------------------------------------------------------------------------------------
| On Load
|---------------------------------------------------------------------------------------------------
*/
$on_page_load = $xbox->add_section( array(
    'name' => __( 'On Page Load', 'masterpopups' ),
    'desc' => __( 'Display the popup automatically after X seconds', 'masterpopups' ),
    'options' => array(
        'toggle' => true,
        'toggle_default' => 'close',//open,close
    ),
));
$on_page_load->open_mixed_field(array('name' => __( 'Status', 'masterpopups' ) ));
$on_page_load->add_field(array(
		'id' => 'trigger-open-on-load',
		'name' => __( 'Enable', 'masterpopups' ),
		'type' => 'switcher',
		'default' => 'off',
		'options' => array(
			'desc_tooltip' => true,
			//'show_name' => false,
		)
	));
$on_page_load->add_field(array(
		'id' => 'trigger-open-on-load-delay',
		'name' => __( 'Time delay', 'masterpopups' ),
		'type' => 'number',
		'default' => '1',
		'options' => array(
			'show_spinner' => true,
			'unit' => 'sec',
			'show_if' => array('trigger-open-on-load', '=', 'on' ),
		),
		'attributes' => array(
			'min' => 0,
		),
	));
$on_page_load->close_mixed_field();

$on_page_load->open_mixed_field(array(
	'name' => __( 'Set cookie', 'masterpopups' ),
	'desc' => __( 'Enable this option to display the popup only once.', 'masterpopups' ),
    'insert_after_name' => "<a href='javascript:void(0)' class='xbox-btn xbox-btn-teal xbox-btn-small ampp-margin-left-20 ampp-btn-clear-cookie cookie-on-load'>Clear Cookie</a>",
));
$on_page_load->add_field(array(
		'id' => 'cookie-on-load',
		'name' => __( 'Enable', 'masterpopups' ),
		'type' => 'switcher',
		'default' => 'off',
		'options' => array(
			'desc_tooltip' => true,
			//'show_name' => false,
		),
	));
$on_page_load->add_field(array(
		'id' => 'cookie-on-load-duration',
		'name' => __( 'Cookie duration', 'masterpopups' ),
		'type' => 'radio',
		'default' => 'days',
		'items' => array(
			'current_session' => __( 'Current session', 'masterpopups' ),
			'days' => __( 'Define days', 'masterpopups' ),
		),
	));
$on_page_load->add_field(array(
		'id' => 'cookie-on-load-days',
		'name' => __( 'Days', 'masterpopups' ),
		'desc' => __( 'The popup will be displayed once every "X" days.', 'masterpopups' ),
		'type' => 'number',
		'default' => '7',
		'options' => array(
			'desc_tooltip' => true,
			'show_spinner' => true,
			'unit' => 'days',
			'show_if' => array('cookie-on-load-duration', '=', 'days' ),
		),
		'attributes' => array(
			'min' => 1,
		),
	));
$on_page_load->close_mixed_field();


/*
|---------------------------------------------------------------------------------------------------
| On Exit Intent
|---------------------------------------------------------------------------------------------------
*/
$on_exit = $xbox->add_section( array(
    'name' => __( 'On Exit Intent', 'masterpopups' ),
    'desc' => __( 'Display the popup when the user tries to leave your website', 'masterpopups' ),
    'options' => array(
        'toggle' => true,
        'toggle_default' => 'close',//open,close
    ),
));
$on_exit->open_mixed_field(array('name' => __( 'Status', 'masterpopups' ) ));
	$on_exit->add_field(array(
		'id' => 'trigger-open-on-exit',
		'name' => __( 'Enable', 'masterpopups' ),
		'type' => 'switcher',
		'default' => 'off',
		'options' => array(
			'desc_tooltip' => true,
			//'show_name' => false,
		)
	));
$on_exit->close_mixed_field();

$on_exit->open_mixed_field(array(
	'name' => __( 'Set cookie', 'masterpopups' ),
	'desc' => __( 'Enable this option to display the popup only once.', 'masterpopups' ),
    'insert_after_name' => "<a href='javascript:void(0)' class='xbox-btn xbox-btn-teal xbox-btn-small ampp-margin-left-20 ampp-btn-clear-cookie cookie-on-exit'>Clear Cookie</a>",
));
	$on_exit->add_field(array(
		'id' => 'cookie-on-exit',
		'name' => __( 'Enable', 'masterpopups' ),
		'type' => 'switcher',
		'default' => 'on',
		'options' => array(
			'desc_tooltip' => true,
			//'show_name' => false,
		)
	));
	$on_exit->add_field(array(
		'id' => 'cookie-on-exit-duration',
		'name' => __( 'Cookie duration', 'masterpopups' ),
		'type' => 'radio',
		'default' => 'current_session',
		'items' => array(
			'current_session' => __( 'Current session', 'masterpopups' ),
			'days' => __( 'Define days', 'masterpopups' ),
		),
	));
	$on_exit->add_field(array(
		'id' => 'cookie-on-exit-days',
		'name' => __( 'Days', 'masterpopups' ),
		'desc' => __( 'The popup will be displayed once every "X" days.', 'masterpopups' ),
		'type' => 'number',
		'default' => '7',
		'options' => array(
			'desc_tooltip' => true,
			'show_spinner' => true,
			'unit' => 'days',
			'show_if' => array('cookie-on-exit-duration', '=', 'days' ),
		),
		'attributes' => array(
			'min' => 1,
		),
	));
$on_exit->close_mixed_field();

/*
|---------------------------------------------------------------------------------------------------
| On Inactivity
|---------------------------------------------------------------------------------------------------
*/
$on_inactivity = $xbox->add_section( array(
    'name' => __( 'On User Inactivity', 'masterpopups' ),
    'desc' => __( 'Display the popup after X seconds of user inactivity', 'masterpopups' ),
    'options' => array(
        'toggle' => true,
        'toggle_default' => 'close',//open,close
    ),
));
$on_inactivity->open_mixed_field(array('name' => __( 'Status', 'masterpopups' ) ));
	$on_inactivity->add_field(array(
		'id' => 'trigger-open-on-inactivity',
		'name' => __( 'Enable', 'masterpopups' ),
		'type' => 'switcher',
		'default' => 'off',
		'options' => array(
			'desc_tooltip' => true,
			//'show_name' => false,
		)
	));
	$on_inactivity->add_field(array(
		'id' => 'trigger-open-on-inactivity-period',
		'name' => __( 'Inactivity time', 'masterpopups' ),
		'type' => 'number',
		'default' => '60',
		'options' => array(
			'show_spinner' => true,
			'unit' => 'sec',
			'show_if' => array('trigger-open-on-inactivity', '=', 'on' ),
		),
		'attributes' => array(
			'min' => 0,
		),
	));
$on_inactivity->close_mixed_field();

$on_inactivity->open_mixed_field(array(
	'name' => __( 'Set cookie', 'masterpopups' ),
	'desc' => __( 'Enable this option to display the popup only once.', 'masterpopups' ),
    'insert_after_name' => "<a href='javascript:void(0)' class='xbox-btn xbox-btn-teal xbox-btn-small ampp-margin-left-20 ampp-btn-clear-cookie cookie-on-inactivity'>Clear Cookie</a>",
));
	$on_inactivity->add_field(array(
		'id' => 'cookie-on-inactivity',
		'name' => __( 'Enable', 'masterpopups' ),
		'type' => 'switcher',
		'default' => 'off',
		'options' => array(
			'desc_tooltip' => true,
			//'show_name' => false,
		)
	));
	$on_inactivity->add_field(array(
		'id' => 'cookie-on-inactivity-duration',
		'name' => __( 'Cookie duration', 'masterpopups' ),
		'type' => 'radio',
		'default' => 'current_session',
		'items' => array(
			'current_session' => __( 'Current session', 'masterpopups' ),
			'days' => __( 'Define days', 'masterpopups' ),
		),
	));
	$on_inactivity->add_field(array(
		'id' => 'cookie-on-inactivity-days',
		'name' => __( 'Days', 'masterpopups' ),
		'desc' => __( 'The popup will be displayed once every "X" days.', 'masterpopups' ),
		'type' => 'number',
		'default' => '7',
		'options' => array(
			'desc_tooltip' => true,
			'show_spinner' => true,
			'unit' => 'days',
			'show_if' => array('cookie-on-inactivity-duration', '=', 'days' ),
		),
		'attributes' => array(
			'min' => 1,
		),
	));
$on_inactivity->close_mixed_field();

/*
|---------------------------------------------------------------------------------------------------
| On Scroll
|---------------------------------------------------------------------------------------------------
*/
$on_scroll = $xbox->add_section( array(
    'name' => __( 'On Scroll', 'masterpopups' ),
    'desc' => __( 'Display the popup after scrolling down X amount, after post content or after certain element', 'masterpopups' ),
    'options' => array(
        'toggle' => true,
        'toggle_default' => 'close',//open,close
    ),
));
$on_scroll->open_mixed_field(array('name' => __( 'Status', 'masterpopups' ) ));
	$on_scroll->add_field(array(
		'id' => 'trigger-open-on-scroll',
		'name' => __( 'Enable', 'masterpopups' ),
		'type' => 'switcher',
		'default' => 'off',
		'options' => array(
			'desc_tooltip' => true,
			//'show_name' => false,
		)
	));
	$on_scroll->add_field(array(
		'id' => 'trigger-open-on-scroll-amount',
		'name' => __( 'Scroll amount', 'masterpopups' ),
		'type' => 'number',
		'default' => '0',
		'options' => array(
			'show_spinner' => true,
			'unit' => '%',
			'unit_picker' => array('px' => 'PX', '%' => '%'),
			'show_if' => array('trigger-open-on-scroll', '=', 'on' ),
		),
		'attributes' => array(
			'min' => 0,
		),
	));
	$on_scroll->add_field(array(
		'id' => 'trigger-open-on-scroll-after-post',
		'name' => __( 'After post content', 'masterpopups' ),
		'type' => 'switcher',
		'default' => 'off',
		'options' => array(
			'desc_tooltip' => true,
			'show_if' => array('trigger-open-on-scroll', '=', 'on' ),
		)
	));
	$on_scroll->add_field(array(
		'id' => 'trigger-open-on-scroll-selector',
		'name' => __( 'Scroll to certain element (ID/Class)', 'masterpopups' ),
		'desc' => __( 'Enter the ID name or Class name like #footer or .widget-title', 'masterpopups' ),
		'type' => 'text',
		'default' => '',
		'options' => array(
			//'desc_tooltip' => true,
			'show_if' => array('trigger-open-on-scroll', '=', 'on' ),
		)
	));
$on_scroll->close_mixed_field();

$on_scroll->open_mixed_field(array(
	'name' => __( 'Set cookie', 'masterpopups' ),
	'desc' => __( 'Enable this option to display the popup only once.', 'masterpopups' ),
    'insert_after_name' => "<a href='javascript:void(0)' class='xbox-btn xbox-btn-teal xbox-btn-small ampp-margin-left-20 ampp-btn-clear-cookie cookie-on-scroll'>Clear Cookie</a>",
));
	$on_scroll->add_field(array(
		'id' => 'cookie-on-scroll',
		'name' => __( 'Enable', 'masterpopups' ),
		'type' => 'switcher',
		'default' => 'off',
		'options' => array(
			'desc_tooltip' => true,
			//'show_name' => false,
		)
	));
	$on_scroll->add_field(array(
		'id' => 'cookie-on-scroll-duration',
		'name' => __( 'Cookie duration', 'masterpopups' ),
		'type' => 'radio',
		'default' => 'current_session',
		'items' => array(
			'current_session' => __( 'Current session', 'masterpopups' ),
			'days' => __( 'Define days', 'masterpopups' ),
		),
	));
	$on_scroll->add_field(array(
		'id' => 'cookie-on-scroll-days',
		'name' => __( 'Days', 'masterpopups' ),
		'desc' => __( 'The popup will be displayed once every "X" days.', 'masterpopups' ),
		'type' => 'number',
		'default' => '7',
		'options' => array(
			'desc_tooltip' => true,
			'show_spinner' => true,
			'unit' => 'days',
			'show_if' => array('cookie-on-scroll-duration', '=', 'days' ),
		),
		'attributes' => array(
			'min' => 1,
		),
	));
$on_scroll->close_mixed_field();


/*
|---------------------------------------------------------------------------------------------------
| Inline
|---------------------------------------------------------------------------------------------------
*/
$display_inline = $xbox->add_section( array(
    'name' => __( 'Display Inline', 'masterpopups' ),
    'desc' => __( 'Embed the popup before or after post/page content', 'masterpopups' ),
    'options' => array(
        'toggle' => true,
        'toggle_default' => 'close',//open,close
    ),
));
$display_inline->add_field(array(
	'id' => 'trigger-open-display-inline-in',
	'name' => __( 'Embed automatically in', 'masterpopups' ),
	'type' => 'checkbox',
	'default' => '',
	'items' => array(
		'before-post' => __( 'Before Post', 'masterpopups' ),
		'after-post' => __( 'After Post', 'masterpopups' ),
	),
	'options' => array(
		'show_name' => true,
	)
));