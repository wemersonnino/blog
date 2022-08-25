<?php

$xbox->add_field(array(
	'type' => 'title',
	'name' => __( 'Plugin Activation', 'masterpopups' ),
	//'desc' => __( 'From here you can activate your license to get live updates and more benefits.', 'masterpopups'),
));
$xbox->add_field(array(
	'id' => 'activation-status',
	'type' => 'hidden',
	'default' => 'off',
));
$xbox->add_field(array(
	'id' => 'activation-auth',
	'type' => 'hidden',
	'default' => 'codexhelp:activation',
));
$xbox->add_field(array(
	'id' => 'activation-status-info',
	'name' => __( 'Status', 'masterpopups' ),
	'type' => 'html',
	'content' => '<span class="ampp-activation-status ampp-bold xbox-color-red">'.__( 'Not Activated', 'masterpopups').'</span>',
));
$xbox->add_field(array(
	'id' => 'activation-username',
	'name' => __( 'Envato Username', 'masterpopups' ).'<span class="xbox-required-field">*</span>',
	'type' => 'text',
	'default' => '',
	'desc' => '',
	'grid' => '4-of-8',
	'options' => array(
		'show_name' => true,
		'helper' => '<i class="xbox-icon xbox-icon-user"></i>'
	),
));
//$xbox->add_field(array(
//	'id' => 'activation-api-key',
//	'name' => __( 'Envato Api Key', 'masterpopups' ).'<span class="xbox-required-field">*</span>',
//	'type' => 'text',
//	'default' => '',
//	'desc' => sprintf(__( 'Click %shere%s for more information', 'masterpopups' ), '<a target="_blank" href="http://masterpopups.com/docs/how-to-activate-your-license/">', '</a>'),
//	'grid' => '4-of-8',
//	'options' => array(
//		'show_name' => true,
//		'helper' => '<i class="xbox-icon xbox-icon-key"></i>'
//	),
//));
$xbox->add_field(array(
	'id' => 'activation-purchase-code',
	'name' => __( 'Purchase Code', 'masterpopups' ).'<span class="xbox-required-field">*</span>',
	'type' => 'text',
	'default' => '',
	'desc' => sprintf(__( 'Click %shere%s for more information', 'masterpopups' ), '<a target="_blank" href="http://masterpopups.com/docs/how-to-activate-your-license/">', '</a>'),
	'grid' => '4-of-8',
	'options' => array(
		'show_name' => true,
		'helper' => '<i class="xbox-icon xbox-icon-shopping-cart"></i>'
	),
));

$xbox->add_field(array(
	'id' => 'activation-email',
	'name' => __( 'Your email', 'masterpopups' ),
	'type' => 'text',
	'default' => '',
	'desc_title' => 'Your email is important to:',
	'desc' => __( 'To get in touch when you need help.<br>Receive discount offers for MasterPopups on your next purchases.', 'masterpopups' ),
	'grid' => '4-of-8',
	'options' => array(
		'show_name' => true,
		'helper' => '<i class="xbox-icon xbox-icon-envelope-o"></i>'
	),
	'attributes' => array(
		'type' => 'email'
	)
));

$xbox->add_field(array(
	'id' => 'activation-type',
	'name' => __( 'Activation type', 'masterpopups' ),
	'type' => 'radio',
	'default' => 'activation',
	'items' => array(
		'activation' => __( 'Activation', 'masterpopups' ),
		'deactivation' => __( 'Deactivation', 'masterpopups' ),
	),
	'options' => array(
		'show_name' => true,
		'helper' => '<i class="xbox-icon xbox-icon-envelope-o"></i>'
	),
));

$xbox->add_field(array(
	'id' => 'activation-domain',
	'name' => __( 'Domain', 'masterpopups' ),
	'type' => 'text',
	'default' => '',
	'desc' => __( 'Enter the domain in which you want to deactivate the plugin', 'masterpopups' ),
	'grid' => '4-of-8',
	'options' => array(
		'show_name' => true,
		'helper' => '<i class="xbox-icon xbox-icon-globe"></i>',
		'show_if' => array( 'activation-type', 'deactivation' )
	),
	'attributes' => array(
		'placeholder' => 'site.com'
	)
));

$xbox->add_field(array(
	'id' => 'activation-validate-purchase',
	'name' => '',
	'type' => 'button',
	'content' => __( 'Validate Purchase', 'masterpopups' ),
	'desc' => '',
	'options' => array(
		'show_name' => true,
		'color' => 'teal',
	),
	'attributes' => array(
		//'placeholder' => 'site.com'
	)
));


/*
|---------------------------------------------------------------------------------------------------
| Activation Offer
|---------------------------------------------------------------------------------------------------
*/
$offer_notif = $xbox->add_section( array(
    'id' => 'activation-offer',
    'name' => __( 'SALE 50% OFF only for 2 Hours', 'masterpopups' ),
    'desc' => __( 'Buy one or more licenses with a 50% discount for a limited time. Only chance.', 'masterpopups' ),
    'options' => array(
        'toggle' => false,
    ),
));
$offer_notif->add_field(array(
    'id' => '',
    'name' => __( 'Your email', 'masterpopups' ).'<span class="xbox-required-field">*</span>',
    'type' => 'html',
    'content' => '<span class="offer-not-reload">We know that you will need more MasterPopups licenses again. <br>So we have a gift for you. Don\'t reload the page or you will lose the offer.</span><img src="'.MPP_URL.'/assets/admin/images/offer-activation.png">',
    'options' => array(
        'show_name' => false,
    ),
));
$offer_notif->add_field(array(
    'type' => 'title',
    'name' => __( 'Contact us', 'masterpopups' ),
    'desc' => __( 'Contact us using the following form to send you the link with the discount.', 'masterpopups'),
));
$offer_notif->add_field(array(
    'id' => 'sale-offer-email',
    'name' => __( 'Your email', 'masterpopups' ).'<span class="xbox-required-field">*</span>',
    'type' => 'text',
    'default' => '',
    'sanitize_callback' => false,
    'desc' => 'Important. Enter your valid email to send the purchase link with the discount.',
));
$offer_notif->add_field(array(
    'id' => 'sale-offer-message',
    'name' => __( 'Message body', 'masterpopups' ).'<span class="xbox-required-field">*</span>',
    'type' => 'textarea',
    'attributes' => array(
        'rows' => '7'
    ),
    'default' => '
Hi.

I want this amazing offer, please send me the purchase link with the discount.

Thanks!',
));
$offer_notif->add_field(array(
    'id' => 'sale-offer-send',
    'name' => '',
    'type' => 'button',
    'content' => __( 'I want the offer', 'masterpopups' ),
    'desc' => '',
    'options' => array(
        'show_name' => true,
        'color' => 'teal',
    ),
));