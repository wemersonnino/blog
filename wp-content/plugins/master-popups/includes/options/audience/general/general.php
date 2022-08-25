<?php

$xbox->add_field( array(
    'id' => 'service',
    'name' => __( 'Subscribers storage', 'masterpopups' ),
    'type' => 'radio',
    'default' => 'master_popups',
    'items' => array_merge( array( 'master_popups' => 'MasterPopups' ), $this->get_integrated_services( true ) ),
    'options' => array(
        'in_line' => false
    ),
    'desc' => sprintf( __( 'Choose where to save your subscribers. If you want to save your subscribers to third party services such as Milchimp, GetResponse, etc. Please first you must add it from %shere%s. Tab "Service Integration".', 'masterpopups' ), '<a href="' . $this->plugin->settings_url . '" target="_blank">', '</a>' ),
) );

$xbox->add_field( array(
    'id' => 'list-status',
    'type' => 'hidden',
    'default' => 'on'
) );

$xbox->add_field( array(
    'id' => 'list-status-message-error',
    'type' => 'html',
    'options' => array(
        'show_if' => array( 'list-status', '!=', 'on' ),
        'show_name' => false,
    ),
    'content' => '
		<div class="ampp-message ampp-message-warning ampp-icon-message">
			<i class="xbox-icon xbox-icon-remove ampp-close-message ampp-close-row"></i>
			<header>' . __( 'Warning', 'masterpopups' ) . '</header>
			<p>' . __( 'It seems that your list no longer exists or the List ID entered is incorrect. Skip this message if you are sure that the list ID is correct or the list is not needed.', 'masterpopups' ) . '</p>
	</div>'
) );

$account_id = $xbox->get_field_value( 'mpp_account-id' );
$account_items = $account_id ? array( $account_id => $account_id ) : array( '' => '- Select account -' );
$xbox->add_field( array(
    'name' => __( 'Account ID', 'masterpopups' ),
    'id' => 'account-id',
    'type' => 'select',
    'default' => $account_id,
    'items' => $account_items,
    'options' => array(
        'show_if' => array( 'service', 'in', array( 'drip' ) ),
    ),
    'grid' => '2-of-8',
    'sanitize_callback' => false,//para que guarde el valor sin verificar en array items
) );

/*
$xbox->add_field( array(
    'name' => __( 'Helper ID', 'masterpopups' ),
    'id' => 'helper-id',
    'type' => 'text',
    'options' => array(
        'show_if' => array( 'service', 'in', array( 'bigmailer' ) ),
        'name_if' => array( 'service' => array( 'bigmailer' => __( 'Brand ID', 'masterpopups' ) ) )
    ),
    'grid' => '2-of-8',
) );
*/

$xbox->add_field( array(
    'name' => __( 'List ID', 'masterpopups' ),
    'id' => 'list-id',
    'type' => 'text',
    'options' => array(
        'show_if' => array( 'service', '!=', 'master_popups' ),
        'name_if' => array( 'inexistente' => array( 'dripaaaa' => __( 'For Drip', 'masterpopups' ), 'bigmaileraaa' => __( 'Brand ID', 'masterpopups' ) ) )
    ),
    'desc' => __( 'Enter the third party List ID. You can use the following button to try to get the lists.', 'masterpopups' ),
    'insert_after_field' => '<a class="xbox-btn xbox-btn-teal xbox-btn-icon xbox-btn-small ampp-float-btn ampp-get-lists">' . __( 'Get Lists', 'masterpopups' ) . '</a>'
) );

$segment_id = $xbox->get_field_value( 'mpp_segment-id' );
$segment_items = $segment_id ? array( $segment_id => $segment_id ) : array( '' => '- Select segment -' );
$xbox->add_field( array(
    'name' => __( 'Segment ID', 'masterpopups' ),
    'id' => 'segment-id',
    'type' => 'select',
    'default' => $segment_id,
    'items' => $segment_items,
    'options' => array(
        'show_if' => array( 'service', '==', 'newsman' )
    ),
    'grid' => '2-of-8',
    'sanitize_callback' => false,//para que guarde el valor sin verificar en array items
    'insert_after_field' => '<a class="xbox-btn xbox-btn-teal xbox-btn-icon xbox-btn-small ampp-margin-left-20 ampp-get-segments">' . __( 'Get segments', 'masterpopups' ) . '</a>'
) );

//$xbox->add_field( array(
//    'name' => __( 'Form ID', 'masterpopups' ),
//    'id' => 'form-id',
//    'type' => 'text',
//    'options' => array(
//        'show_if' => array( 'service', '=', 'sales_autopilot' )
//    ),
//    'desc' => __( 'Form ID of your List.', 'masterpopups' ),
//) );

$xbox->open_mixed_field(array(
    'name' => 'Double opt-in',
    'options' => array(
        'show_if' => array( 'service', 'in', array(
            'mailchimp',
            'mailster',
            'newsman',
            'drip',
            'sendfox',
            'fluent_crm',
            'sendinblue',
        ) ),
    ),
));
$xbox->add_field( array(
    'id' => 'double-opt-in',
    'desc' => __( 'Activate this option if email confirmation is required before subscribing. Only supporting for some CRM Services.', 'masterpopups' ),
    'type' => 'switcher',
    'default' => 'off',
    'options' => array(
        'show_name' => false
    ),
//    'options' => array(
//        'show_if' => array( 'service', 'in', array(
//            'mailchimp',
//            'mailster',
//            'newsman',
//            'drip',
//            'sendfox',
//            'fluent_crm',
//            'sendinblue',
//        ) ),
//    ),
) );
$xbox->add_field( array(
    'name' => __( 'Template ID', 'masterpopups' ),
    'id' => 'template-id',
    'type' => 'text',
    'options' => array(
        'show_if' => array( 'service', '=', 'sendinblue' )
    ),
    'attributes' => array(
        'style' => 'width: 170px'
    ),
    'desc' => __( 'Id of the Double opt-in template', 'masterpopups' ),
) );
$xbox->add_field( array(
    'name' => __( 'Redirection URL', 'masterpopups' ),
    'id' => 'redirection-url',
    'type' => 'text',
    'options' => array(
        'show_if' => array( 'service', '=', 'sendinblue' )
    ),
    'desc' => __( 'URL of the web page that user will be redirected to after clicking on the double opt in URL', 'masterpopups' ),
) );

$xbox->close_mixed_field();

$xbox->add_field( array(
    'id' => 'allow-data-update',
    'name' => __( 'Update user data', 'masterpopups' ),
    'desc' => __( 'Enable to allow updating user data if the email already exists in the list.', 'masterpopups' ) . ' (Only in some services)',
    'type' => 'switcher',
    'default' => 'on',
    'options' => array(
        'show_if' => array( 'service', 'in', array(
            'master_popups',
            'active_campaign',
            'mailster',
            'totalsend',
            'mailpoet',
            'ontraport',
            'sendpulse',
            'sendinblue',
            'sendgrid',
            'sendpress',
            'agilecrm',
            'moosend',
            'clever_reach',
            'mailwizz',
            'email_octopus',
            'automizy',
            'mailbluster',
            'sendfox',
            'constant_contact',
            'pipedrive',
            'fluent_crm'
        ) ),
    ),
) );