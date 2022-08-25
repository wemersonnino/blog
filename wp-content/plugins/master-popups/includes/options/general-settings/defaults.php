<?php
$crm_integrations = array(
    'integrated-services' => array(),
    'service-auth-type' => 'basic_auth',
    'service-api_version' => 'default',
);
$synchronizations = array(
    'sync-wp-comments-enabled' => 'off',
    'sync-wp-comments-only-approved' => 'off',
    'sync-wp-comments-list-id' => '',
    //'sync-wp-comments-forms' => array(),
    'sync-wp-comments-use-checkbox' => 'off',
    'sync-wp-comments-checkbox-text' => __( 'I want to subscribe', 'masterpopups' ),
);

$css_scripts = array(
    'custom-css' => '',
    'custom-javascript' => '',
);

$email_validations = array(
    'mx-record-email-validation' => 'on',
    'kickbox-email-validation' => 'off',
    'kickbox-email-validation-apikey' => '',
    'neverbounce-email-validation' => 'off',
    'neverbounce-email-validation-apikey' => '',
    'algocheck-email-validation' => 'off',
    'algocheck-email-validation-apikey' => '',
    'proofy-email-validation' => 'off',
    'proofy-email-validation-apikey' => '',
    'proofy-email-validation-userid' => '',
    'thechecker-email-validation' => 'off',
    'thechecker-email-validation-apikey' => '',
);

$general = array(
    'popups-z-index' => '99999999',
    'sticky-z-index' => '100000005',
    'disable-user-roles' => array(),
    'enable-enqueue-popups' => 'on',
    'show-link-edit-popup' => 'off',
    'attach-error-on-form-failed' => 'on',
    'target-enabled-custom-post-types' => 'on',
    'target-display-all-tags' => 'off',
    'verify-wp-nonce' => 'on',
    'send-data-to-developer' => 'on',

    'minify-js' => 'on',
    'load-videojs' => 'off',
    'load-google-fonts' => 'on',
    'load-font-awesome' => 'on',

    'recaptcha-site-key' => '',
    'recaptcha-secret-key' => '',
    'recaptcha-version' => 'v2',//v2,v3,invisible
    'recaptcha-version3-score' => '0.6',//0 to 1
    //'recaptcha-hide-badge' => 'off',


    'validation-msg-general' => __( 'This field is required', 'masterpopups' ),
    'validation-msg-email' => __( 'Invalid email address', 'masterpopups' ),
    'validation-msg-checkbox' => __( 'This field is required, please check', 'masterpopups' ),
    'validation-msg-dropdown' => __( 'This field is required. Please select an option', 'masterpopups' ),
    'validation-msg-minlength' => __( 'Min length:', 'masterpopups' ),
    'form-submission-back-to-form-text' => __( 'Back to form', 'masterpopups' ),
    'form-submission-close-popup-text' => __( 'Close', 'masterpopups' ),

    'debug-mode' => 'off',
    'debug-ip' => '',
    'fake-version' => '15.0.0',

    'link-powered-by-enabled' => 'off',
    'link-powered-by-username' => '',
);


$header_footer_scripts = array(
    'header-scripts' => '
<script>

</script>

',//Salto de línea aquí soluciona un problema Error 500 al guardar ajustes.
    'footer-scripts' => '
<script>

</script>

',//Salto de línea aquí soluciona un problema Error 500 al guardar ajustes.
    'header-scripts-priority' => '10',
    'footer-scripts-priority' => '10',
);

$defaults = array();
$defaults = array_merge( $defaults, $crm_integrations );
$defaults = array_merge( $defaults, $general );
$defaults = array_merge( $defaults, $email_validations );
$defaults = array_merge( $defaults, $synchronizations );
$defaults = array_merge( $defaults, $css_scripts );
$defaults = array_merge( $defaults, $header_footer_scripts );

return $defaults;