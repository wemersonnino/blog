<?php


$xbox->add_field(array(
    'type' => 'title',
    'name' => __( 'Email Validations', 'masterpopups' ),
    'desc' => __( 'Improve your Email Campaign. Activate the services you want to validate all the emails of your subscribers.', 'masterpopups'),
));


$xbox->open_mixed_field( array( 'name' => __( 'MX Record', 'masterpopups' ) ) );
$xbox->add_field(array(
    'id' => 'mx-record-email-validation',
    'name' => __( 'Enable', 'masterpopups' ),
    'type' => 'switcher',
    'default' => $defaults['mx-record-email-validation'],
));
$xbox->close_mixed_field();


$xbox->open_mixed_field( array( 'name' => __( 'KickBox', 'masterpopups' ), 'desc_name' => '<a href="https://kickbox.com/" target="_blank">kickbox.com</a>' ) );
$xbox->add_field(array(
    'id' => 'kickbox-email-validation',
    'name' => __( 'Enable', 'masterpopups' ),
    'type' => 'switcher',
    'default' => $defaults['kickbox-email-validation'],
));
$xbox->add_field( array(
    'id' => 'kickbox-email-validation-apikey',
    'name' => __( 'API Key', 'masterpopups' ). ' (Production Mode)',
    'desc' => 'KickBox > API Keys > Create Production Mode API Key. Where do I find my API Key? <a href="https://docs.kickbox.com/docs/using-the-api" target="_blank">Go here</a>',
    'type' => 'text',
    'default' => $defaults['kickbox-email-validation-apikey'],
    'grid' => '3-of-8',
    'options' => array(
        'show_if' => array( 'kickbox-email-validation', '=', 'on' )
    ),
) );
$xbox->close_mixed_field();



$xbox->open_mixed_field( array( 'name' => __( 'NeverBounce', 'masterpopups' ), 'desc_name' => '<a href="https://neverbounce.com/" target="_blank">neverbounce.com</a>' ) );
$xbox->add_field(array(
    'id' => 'neverbounce-email-validation',
    'name' => __( 'Enable', 'masterpopups' ),
    'type' => 'switcher',
    'default' => $defaults['neverbounce-email-validation'],
));
$xbox->add_field( array(
    'id' => 'neverbounce-email-validation-apikey',
    'name' => __( 'API Key', 'masterpopups' ),
    'desc' => 'NeverBounce > Apps > Create Custom Integration App. Where do I find my API Key? <a href="https://neverbounce.com/help/apps-and-api/where-can-i-find-my-api-key" target="_blank">Go here</a>',
    'type' => 'text',
    'default' => $defaults['neverbounce-email-validation-apikey'],
    'grid' => '3-of-8',
    'options' => array(
        'show_if' => array( 'neverbounce-email-validation', '=', 'on' )
    ),
) );
$xbox->close_mixed_field();



$xbox->open_mixed_field( array( 'name' => __( 'AlgoCheck', 'masterpopups' ), 'desc_name' => '<a href="https://www.algocheck.com/" target="_blank">algocheck.com</a>' ) );
$xbox->add_field(array(
    'id' => 'algocheck-email-validation',
    'name' => __( 'Enable', 'masterpopups' ),
    'type' => 'switcher',
    'default' => $defaults['algocheck-email-validation'],
));
$xbox->add_field( array(
    'id' => 'algocheck-email-validation-apikey',
    'name' => __( 'Full API Key', 'masterpopups' ),
    'desc' => 'AlgoCheck > FullAPI. Where do I find my API Key? <a href="https://www.algocheck.com/api.php" target="_blank">Go here</a>',
    'type' => 'text',
    'default' => $defaults['algocheck-email-validation-apikey'],
    'grid' => '3-of-8',
    'options' => array(
        'show_if' => array( 'algocheck-email-validation', '=', 'on' )
    ),
) );
$xbox->close_mixed_field();



$xbox->open_mixed_field( array( 'name' => __( 'Proofy', 'masterpopups' ), 'desc_name' => '<a href="https://proofy.io/" target="_blank">https://proofy.io</a>' ) );
$xbox->add_field(array(
    'id' => 'proofy-email-validation',
    'name' => __( 'Enable', 'masterpopups' ),
    'type' => 'switcher',
    'default' => $defaults['proofy-email-validation'],
));
$xbox->add_field( array(
    'id' => 'proofy-email-validation-apikey',
    'name' => __( 'API Key', 'masterpopups' ),
    'desc' => 'Proofy > Verify API > API key. Where do I find my API Key? <a href="https://app.proofy.io/apiinfo" target="_blank">Go here</a>',
    'type' => 'text',
    'default' => $defaults['proofy-email-validation-apikey'],
    'grid' => '3-of-8',
    'options' => array(
        'show_if' => array( 'proofy-email-validation', '=', 'on' )
    ),
) );
$xbox->add_field( array(
    'id' => 'proofy-email-validation-userid',
    'name' => __( 'User ID', 'masterpopups' ),
    'desc' => 'Proofy > Verify API > User ID. Where do I find my User ID? <a href="https://app.proofy.io/apiinfo" target="_blank">Go here</a>',
    'type' => 'text',
    'default' => $defaults['proofy-email-validation-userid'],
    'grid' => '2-of-8',
    'options' => array(
        'show_if' => array( 'proofy-email-validation', '=', 'on' )
    ),
) );
$xbox->close_mixed_field();



$xbox->open_mixed_field( array( 'name' => __( 'TheChecker', 'masterpopups' ), 'desc_name' => '<a href="https://thechecker.co/" target="_blank">https://thechecker.co</a>' ) );
$xbox->add_field(array(
    'id' => 'thechecker-email-validation',
    'name' => __( 'Enable', 'masterpopups' ),
    'type' => 'switcher',
    'default' => $defaults['thechecker-email-validation'],
));
$xbox->add_field( array(
    'id' => 'thechecker-email-validation-apikey',
    'name' => __( 'API Key', 'masterpopups' ),
    'desc' => 'TheChecker > API > Api Key. Where do I find my API Key? <a href="https://app.thechecker.co/api" target="_blank">Go here</a>',
    'type' => 'text',
    'default' => $defaults['thechecker-email-validation-apikey'],
    'grid' => '3-of-8',
    'options' => array(
        'show_if' => array( 'thechecker-email-validation', '=', 'on' )
    ),
) );
$xbox->close_mixed_field();

