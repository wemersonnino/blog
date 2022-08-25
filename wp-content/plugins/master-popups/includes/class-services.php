<?php namespace MasterPopups\Includes;

use MasterPopups\Includes\ServiceIntegration\MailsterIntegration;
use MasterPopups\Includes\ServiceIntegration\MailchimpIntegration;
use MasterPopups\Includes\ServiceIntegration\GetresponseIntegration;
use MasterPopups\Includes\ServiceIntegration\SendinblueIntegration;
use MasterPopups\Includes\ServiceIntegration\SendinblueIntegrationV3;
use MasterPopups\Includes\ServiceIntegration\MailerLiteIntegration;
use MasterPopups\Includes\ServiceIntegration\AutopilotIntegration;
use MasterPopups\Includes\ServiceIntegration\ConstantContactIntegration;
use MasterPopups\Includes\ServiceIntegration\HubspotIntegration;
use MasterPopups\Includes\ServiceIntegration\ActiveCampaignIntegration;
use MasterPopups\Includes\ServiceIntegration\MadMimiIntegration;
use MasterPopups\Includes\ServiceIntegration\MauticIntegration;
use MasterPopups\Includes\ServiceIntegration\MailgunIntegration;
use MasterPopups\Includes\ServiceIntegration\BenchmarkIntegration;
use MasterPopups\Includes\ServiceIntegration\PipedriveIntegration;
use MasterPopups\Includes\ServiceIntegration\FreshmailIntegration;
use MasterPopups\Includes\ServiceIntegration\ServiceIntegration;
use MasterPopups\Includes\ServiceIntegration\TuNewsletterIntegration;
use MasterPopups\Includes\ServiceIntegration\SimplyCastIntegration;
use MasterPopups\Includes\ServiceIntegration\InfusionsoftIntegration;
use MasterPopups\Includes\ServiceIntegration\CustomerIoIntegration;
use MasterPopups\Includes\ServiceIntegration\AweberIntegration;
use MasterPopups\Includes\ServiceIntegration\CampaignMonitorIntegration;
use MasterPopups\Includes\ServiceIntegration\ZohoCampaignsIntegration;
use MasterPopups\Includes\ServiceIntegration\ZohoCampaignsIntegrationV11;
use MasterPopups\Includes\ServiceIntegration\ZohoCRMIntegration;
use MasterPopups\Includes\ServiceIntegration\DripIntegration;
use MasterPopups\Includes\ServiceIntegration\NewsmanIntegration;
use MasterPopups\Includes\ServiceIntegration\iContactIntegration;
use MasterPopups\Includes\ServiceIntegration\ConvertkitIntegration;
use MasterPopups\Includes\ServiceIntegration\TotalsendIntegration;
use MasterPopups\Includes\ServiceIntegration\MailpoetIntegration;
use MasterPopups\Includes\ServiceIntegration\OntraportIntegration;
use MasterPopups\Includes\ServiceIntegration\KlaviyoIntegration;
use MasterPopups\Includes\ServiceIntegration\EgoiIntegration;
use MasterPopups\Includes\ServiceIntegration\SendpulseIntegration;
use MasterPopups\Includes\ServiceIntegration\SendgridIntegration;
use MasterPopups\Includes\ServiceIntegration\SendpressIntegration;
use MasterPopups\Includes\ServiceIntegration\AgileCRMIntegration;
use MasterPopups\Includes\ServiceIntegration\MoosendIntegration;
use MasterPopups\Includes\ServiceIntegration\CleverReachIntegration;
use MasterPopups\Includes\ServiceIntegration\SalesAutopilotIntegration;

//use MasterPopups\Includes\ServiceIntegration\ElasticEmailIntegration;
use MasterPopups\Includes\ServiceIntegration\EsputnikIntegration;
use MasterPopups\Includes\ServiceIntegration\MailwizzIntegration;
use MasterPopups\Includes\ServiceIntegration\SalesforceIntegration;
use MasterPopups\Includes\ServiceIntegration\EmailOctopusIntegration;
use MasterPopups\Includes\ServiceIntegration\PabblyIntegration;
use MasterPopups\Includes\ServiceIntegration\AutomizyIntegration;
use MasterPopups\Includes\ServiceIntegration\SendFoxIntegration;
use MasterPopups\Includes\ServiceIntegration\MailBlusterIntegration;
use MasterPopups\Includes\ServiceIntegration\BigMailerIntegration;
use MasterPopups\Includes\ServiceIntegration\ConstantContactIntegrationV3;
use MasterPopups\Includes\ServiceIntegration\MailrelayIntegration;
use MasterPopups\Includes\ServiceIntegration\FluentCRMIntegration;
use MasterPopups\Includes\ServiceIntegration\EnchargeIntegration;




Services::init();
class Services {


    private static $pro = array(
        //Pro
        'mailster',//Suscripción a varias listas implementado
        'mailchimp',//Soporta tags
        'hubspot',
        'getresponse',
        'sendinblue',//Suscripción a varias listas implementado
        'mailer_lite',
        'active_campaign',
        'mautic',//No tienen listas, los contactos son asociados a un "Contact Owner"
        'infusionsoft',
        'aweber',
        'campaign_monitor',
        'drip',
        'convertkit',
        'totalsend',
        'mailpoet',
        'ontraport',
        'klaviyo',
        'egoi',
        'sendpulse',
        'sendgrid',
        'sendpress',
        'agilecrm',//Soporta tags. Sus listas son campañas y es opcional.
        'moosend',
        'clever_reach',
        'sales_autopilot',//No permite obtener las listas
        //'elastic_email',//Error en su api al agregar un suscriptor
        'esputnik',//No permite obtener los campos personalizados, field_name debe ser el id(numerico) del campo.
        'mailwizz',
        'salesforce',
        'email_octopus', //Suscripción a varias listas implementado
        'pabbly',//No tienen listas?. Suscripción a varias listas implementado
        'automizy',//Suscripción a varias listas implementado
        'zoho_crm',//No tiene listas
        'sendfox',//No tiene custom fields
        'mailbluster',//No tiene listas
        'bigmailer',//No tiene listas, no permite obtener custom fields pero si enviarle custom fields previamente registrados
        'mailrelay',//Listas es igual a Groups. Suscripción a varias listas implementado. No se puede obtener campos personalizados
        'fluent_crm',
        'encharge',//No permite obtener las listas (Segmentos)
    );


    private static $free = array(
        //Free
        'constant_contact',//Los custom fields deben ser del tipo 'customfieldX', X desde 1 hasta 15
        'zoho_campaigns',
        'benchmark',
        'icontact',
        'autopilot',//No permite obtener sus custom fields
        'pipedrive',//Listas son Organizaciones. No permite Suscripción a varias listas.
        'mad_mimi',//No permite obtener sus custom fields, pero tiene campos por defecto visibles en su web
        'freshmail',//La api falla para custom fields inexistentes. first_name y last_name son custom fields
        'simply_cast',
        'customer_io',//No tiene listas, //IronMan
        'mailgun',
        'newsman',
        'tunewsletter',//IronMan
    );

    private static $all = array(
        // 'mailjet',
        // 'campayn',
    );

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna servicios pro
    |---------------------------------------------------------------------------------------------------
    */
    public static function get_pro(){
        return self::$pro;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna servicios free
    |---------------------------------------------------------------------------------------------------
    */
    public static function get_free(){
        return self::$free;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las integraciones con sus datos
    |---------------------------------------------------------------------------------------------------
    */
    public static function get_all(){
        $services = array();
        $all = array_merge( self::$pro, self::$free );
        sort( $all );
        foreach( $all as $service ){
            if( method_exists( __CLASS__, $service ) ){
                $services[$service] = self::$service();
            }
        }
        return $services;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Inicializar servicios
    |---------------------------------------------------------------------------------------------------
    */
    public static function init(){

        //d(get_option( "mpp_zoho_crm_oauth2" ));

        ServiceIntegration::show_message_on_connection();
        if( ServiceIntegration::is_oauth2( false ) ){
            //Todos los campos son necesarios aunque estén vacíos.
            $data = array(
                'auth_type' => 'oauth2',
                'api_version' => 'default',
                'api_key' => '',
                'token' => '',
                'url' => '',
                'email' => '',
                'password' => '',
            );
            Services::get_instance( ServiceIntegration::get_service_name_oauth2(), $data );
        }

    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Campos de una intregación
    |---------------------------------------------------------------------------------------------------
    */
    public static function integration_fields(){
        return array(
            'integrated-services_type' => '',
            'integrated-services_visibility' => 'visible',
            'integrated-services_name' => '',
            'service-status' => '',
            'service-api_version' => 'default',
            'service-auth-type' => 'basic_auth',
            'service-api-key' => '',
            'service-token' => '',
            'service-email' => '',
            'service-password' => '',
            'service-url' => '',
            'services-custom-fields' => '',
            'services-list-id' => '',
        );

        return $services;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retirna una instancia de un servicio
    |---------------------------------------------------------------------------------------------------
    */
    public static function get_instance( $service, $data = array(), $subscription_class = null ){
        $all = array_merge( self::$pro, self::$free );
        if( ! in_array( $service, $all ) ){
            return __( 'Service not supported', 'masterpopups' );
        }
        $instance = null;
        switch( $service ){
            case 'mailster':
                $instance = new MailsterIntegration();
                break;
            case 'mailchimp':
                include MPP_DIR . 'libs/integrations/MailChimpAPI3/MailChimp.php';
                $instance = new MailchimpIntegration( $data['api_key'] );
                break;
            case 'getresponse':
                include MPP_DIR . 'libs/integrations/GetResponseAPI3/GetResponseAPI3.class.php';
                $instance = new GetresponseIntegration( $data['api_key'] );
                break;
            case 'sendinblue':
                if( $data['api_version'] == '3' ){
                    $instance = new SendinblueIntegrationV3( $data['auth_type'], $data['api_key'] );
                } else{
                    include MPP_DIR . 'libs/integrations/SendinblueAPI2/Mailin.php';
                    $instance = new SendinblueIntegration( $data['auth_type'], $data['api_key'] );
                }
                break;
            case 'mailer_lite':
                include MPP_DIR . 'libs/integrations/MailerLiteAPI2/vendor/autoload.php';
                $instance = new MailerLiteIntegration( $data['api_key'] );
                break;
            case 'autopilot':
                include MPP_DIR . 'libs/integrations/AutopilotAPI1/autoload.php';
                $instance = new AutopilotIntegration( $data['api_key'] );
                break;
            case 'constant_contact':
                if( $data['auth_type'] == 'oauth2' ){
                    $instance = new ConstantContactIntegrationV3( $data['auth_type'], $data['api_key'], $data['token'] );
                } else{
                    $instance = new ConstantContactIntegration( $data['auth_type'], $data['api_key'], $data['token'] );
                }
                break;
            case 'hubspot':
                $instance = new HubspotIntegration( $data['api_key'] );
                break;
            case 'active_campaign':
                include MPP_DIR . 'libs/integrations/ActiveCampaignAPI3/ActiveCampaign.class.php';
                $instance = new ActiveCampaignIntegration( $data['api_key'], $data['url'] );
                break;
            case 'mad_mimi':
                include MPP_DIR . 'libs/integrations/MadMimiAPI1/Spyc.class.php';
                include MPP_DIR . 'libs/integrations/MadMimiAPI1/MadMimi.class.php';
                $instance = new MadMimiIntegration( $data['api_key'], $data['email'] );
                break;
            case 'mailgun':
                include MPP_DIR . 'libs/integrations/MailgunAPI1/vendor/autoload.php';
                $instance = new MailgunIntegration( $data['api_key'] );
                break;
            case 'benchmark':
                include MPP_DIR . 'libs/integrations/BenchmarkAPI1/BMEAPI.class.php';
                $instance = new BenchmarkIntegration( $data['email'], $data['password'] );
                break;
            case 'mautic':
                include MPP_DIR . 'libs/integrations/MauticAPI/vendor/autoload.php';
                $instance = new MauticIntegration( $data['auth_type'], $data['email'], $data['password'], $data['url'], $data['api_key'], $data['token'] );
                break;
            case 'pipedrive':
                $instance = new PipedriveIntegration( $data['auth_type'], $data['token'] );
                break;
            case 'freshmail':
                include MPP_DIR . 'libs/integrations/FreshMailAPI1/class.rest.php';
                $instance = new FreshMailIntegration( $data['api_key'], $data['token'] );
                break;
            case 'tunewsletter':
                $instance = new TuNewsletterIntegration( $data['api_key'], $data['url'] );
                break;
            case 'simply_cast':
                include MPP_DIR . 'libs/integrations/SimplyCastAPI1/SimplyCastAPI.php';
                $instance = new SimplyCastIntegration( $data['api_key'], $data['token'] );
                break;
            case 'infusionsoft':
                include MPP_DIR . 'libs/integrations/Infusionsoft/infusionsoft.php';
                $instance = new InfusionsoftIntegration( $data['api_key'], $data['token'] );
                break;
            case 'customer_io':
                $instance = new CustomerIoIntegration( $data['api_key'], $data['token'] );
                break;
            case 'aweber':
                if( ! class_exists( '\AWeberAPI' ) ){
                    include MPP_DIR . 'libs/integrations/aweber_api/aweber_api.php';
                }
                $instance = new AweberIntegration( $data['api_key'] );
                break;
            case 'campaign_monitor':
                $instance = new CampaignMonitorIntegration( $data['api_key'], $data['token'] );
                break;
            case 'zoho_campaigns':
                if( $data['auth_type'] == 'oauth2' ){
                    $instance = new ZohoCampaignsIntegrationV11( $data['auth_type'], $data['api_key'], $data['token'], $data['url'] );
                } else{
                    $instance = new ZohoCampaignsIntegration( $data['api_key'] );
                }
                break;
            case 'zoho_crm':
                $instance = new ZohoCRMIntegration( $data['auth_type'], $data['api_key'], $data['token'], $data['url'] );
                break;
            case 'drip':
                $instance = new DripIntegration( $data['api_key'] );
                break;
            case 'newsman':
                $instance = new NewsmanIntegration( $data['api_key'], $data['token'] );
                break;
            case 'icontact':
                $instance = new iContactIntegration( $data['api_key'], $data['email'], $data['password'] );
                break;
            case 'convertkit':
                $instance = new ConvertkitIntegration( $data['api_key'] );
                break;
            case 'totalsend':
                $instance = new TotalsendIntegration( $data['email'], $data['password'] );
                break;
            case 'mailpoet':
                $instance = new MailpoetIntegration();
                break;
            case 'ontraport':
                $instance = new OntraportIntegration( $data['api_key'], $data['token'] );
                break;
            case 'klaviyo':
                $instance = new KlaviyoIntegration( $data['api_key'] );
                break;
            case 'egoi':
                $instance = new EgoiIntegration( $data['api_key'] );
                break;
            case 'sendpulse':
                $instance = new SendpulseIntegration( $data['api_key'], $data['token'] );
                break;
            case 'sendgrid':
                $instance = new SendgridIntegration( $data['api_key'] );
                break;
            case 'sendpress':
                $instance = new SendpressIntegration();
                break;
            case 'agilecrm':
                $instance = new AgileCRMIntegration( $data['api_key'], $data['email'], $data['url'] );
                break;
            case 'moosend':
                $instance = new MoosendIntegration( $data['api_key'] );
                break;
            case 'clever_reach':
                $instance = new CleverReachIntegration( $data['api_key'], $data['email'], $data['password'] );
                break;
            case 'sales_autopilot':
                $instance = new SalesAutopilotIntegration( $data['token'], $data['password'] );
                break;
            //            case 'elastic_email':
            //                $instance = new ElasticEmailIntegration( $data['api_key'], $data['email'], $data['password'] );
            //                break;
            case 'esputnik':
                $instance = new EsputnikIntegration( $data['email'], $data['password'] );
                break;
            case 'mailwizz':
                $instance = new MailwizzIntegration( $data['api_key'], $data['token'], $data['url'] );
                break;
            case 'salesforce':
                $instance = new SalesforceIntegration( $data['auth_type'], $data['api_key'], $data['token'] );
                break;
            case 'email_octopus':
                $instance = new EmailOctopusIntegration( $data['auth_type'], $data['api_key'] );
                break;
            case 'pabbly':
                $instance = new PabblyIntegration( $data['auth_type'], $data['token'] );
                break;
            case 'automizy':
                $instance = new AutomizyIntegration( $data['auth_type'], $data['token'] );
                break;
            case 'sendfox':
                $instance = new SendFoxIntegration( $data['auth_type'], $data['token'] );
                break;
            case 'mailbluster':
                $instance = new MailBlusterIntegration( $data['auth_type'], $data['api_key'] );
                break;
            case 'bigmailer':
                $instance = new BigMailerIntegration( $data['auth_type'], $data['api_key'], $data['token'] );
                break;
            case 'mailrelay':
                $instance = new MailrelayIntegration( $data['auth_type'], $data['token'], $data['url'] );
                break;
            case 'fluent_crm':
                $instance = new FluentCRMIntegration();
                break;
            case 'encharge':
                $instance = new EnchargeIntegration( $data['auth_type'], $data['api_key'] );
                break;
        }
        $instance->set_subscription_class( $subscription_class );

        return $instance;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "Mailster"
    |---------------------------------------------------------------------------------------------------
    */
    public static function mailster(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/mailster.png',
            'text' => 'Mailster',
            'access_data' => array(//'api_key' => true,
            ),
            'allow' => array(
                'get_lists' => true,
            )
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "MailChimp"
    |---------------------------------------------------------------------------------------------------
    */
    public static function mailchimp(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/mailchimp.png',
            'text' => 'MailChimp',
            'access_data' => array(
                'api_key' => true,
            ),
            'help_url' => array(
                'api_key' => 'http://kb.mailchimp.com/integrations/api-integrations/about-api-keys',
            ),
            'allow' => array(
                'get_lists' => true,
            )
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "GetResponse"
    |---------------------------------------------------------------------------------------------------
    */
    public static function getresponse(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/getresponse.png',
            'text' => 'GetResponse',
            'access_data' => array(
                'api_key' => true,
            ),
            'help_url' => array(
                'api_key' => 'https://support.getresponse.com/videos/where-do-i-find-the-api-key',
            ),
            'allow' => array(
                'get_lists' => true,
            )
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "sendinblue"
    |---------------------------------------------------------------------------------------------------
    */
    public static function sendinblue(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/sendinblue.png',
            'text' => 'Sendinblue',
            'access_data' => array(
                'api_version' => true,
                'api_key' => true,
            ),
            'help_url' => array(
                'api_key' => 'https://my.sendinblue.com/advanced/apikey/',
            ),
            'allow' => array(
                'get_lists' => true,
            )
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "MailerLite"
    |---------------------------------------------------------------------------------------------------
    */
    public static function mailer_lite(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/mailer_lite.png',
            'text' => 'MailerLite',
            'access_data' => array(
                'api_key' => true,
            ),
            'help_url' => array(
                'api_key' => 'https://app.mailerlite.com/subscribe/api',
            ),
            'allow' => array(
                'get_lists' => true,
            )
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "Autopilot"
    |---------------------------------------------------------------------------------------------------
    */
    public static function autopilot(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/autopilot.png',
            'text' => 'Autopilot',
            'access_data' => array(
                'api_key' => true,
            ),
            'help_url' => array(
                'api_key' => 'http://developers.autopilothq.com/getting-started/',
            ),
            'allow' => array(
                'get_lists' => true,
            )
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "Constant contact"
    |---------------------------------------------------------------------------------------------------
    */
    public static function constant_contact(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/constant_contact.png',
            'text' => 'Constant contact',
            'access_data' => array(
                'api_key' => true,
                'token' => true,
            ),
            'auth_fields' => array(
                'basic_auth' => array( 'api_key', 'token' ),
                'oauth2' => array( 'api_key', 'token' ),
            ),
            'help_url' => array(
                'api_key' => 'https://developer.constantcontact.com/v2-api-keys.html',
                'token' => 'https://developer.constantcontact.com/v2-api-keys.html',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
            'names_access_data' => array(
                'api_key' => 'API Key',
                'token' => 'API Secret',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "hubspot"
    |---------------------------------------------------------------------------------------------------
    */
    public static function hubspot(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/hubspot.png',
            'text' => 'Hubspot',
            'access_data' => array(
                'api_key' => true,
            ),
            'help_url' => array(
                'api_key' => 'https://knowledge.hubspot.com/articles/kcs_article/integrations/how-do-i-get-my-hubspot-api-key',
            ),
            'allow' => array(
                'get_lists' => true,
            )
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "Active Campaign"
    |---------------------------------------------------------------------------------------------------
    */
    public static function active_campaign(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/active_campaign.png',
            'text' => 'Active Campaign',
            'access_data' => array(
                'api_key' => true,
                'url' => true,
            ),
            'help_url' => array(
                'api_key' => 'https://help.activecampaign.com/hc/en-us/articles/207317590-Getting-started-with-the-API',
                'url' => 'https://help.activecampaign.com/hc/en-us/articles/207317590-Getting-started-with-the-API',
            ),
            'allow' => array(
                'get_lists' => true,
            )
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "Mad Mimi"
    |---------------------------------------------------------------------------------------------------
    */
    public static function mad_mimi(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/mad_mimi.png',
            'text' => 'Mad Mimi',
            'access_data' => array(
                'api_key' => true,
                'email' => true,
            ),
            'help_url' => array(
                'api_key' => 'https://help.madmimi.com/where-can-i-find-my-api-key/',
                'email' => '',
            ),
            'allow' => array(
                'get_lists' => true,
            )
        );
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "Mailgun"
    |---------------------------------------------------------------------------------------------------
    */
    public static function mailgun(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/mailgun.png',
            'text' => 'Mailgun',
            'access_data' => array(
                'api_key' => true,
            ),
            'help_url' => array(
                'api_key' => 'https://help.mailgun.com/hc/en-us/articles/203380100-Where-can-I-find-my-API-key-and-SMTP-credentials-',
            ),
            'allow' => array(
                'get_lists' => true,
            )
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "Benchmark"
    |---------------------------------------------------------------------------------------------------
    */
    public static function benchmark(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/benchmark.png',
            'text' => 'Benchmark',
            'access_data' => array(
                'email' => true,
                'password' => true,
            ),
            'help_url' => array(
                'email' => '',
                'password' => '',
            ),
            'allow' => array(
                'get_lists' => true,
            )
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "Mautic"
    |---------------------------------------------------------------------------------------------------
    */
    public static function mautic(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/mautic.png',
            'text' => 'Mautic',
            'access_data' => array(
                'email' => true,
                'password' => true,
                'url' => true,
            ),
            'auth_fields' => array(
                'basic_auth' => array( 'email', 'password', 'url' ),
                'oauth2' => array( 'api_key', 'token', 'url' ),
            ),
            'help_url' => array(
                'url' => 'E.g: https://your-mautic-site.com. Connection error? Try with http or https. Check this <a href="' . MPP_URL . 'assets/admin/images/mautic-help.png" target="_blank">guide</a>. <a href="https://masterpopups.com/docs/how-to-integrate-master-popups-with-mautic/" target="_blank">Documentation</a>',
                'api_key' => 'Where do I find this? <a href="https://masterpopups.com/docs/how-to-integrate-master-popups-with-mautic/" target="_blank">Go here</a>.',
                'token' => 'Where do I find this? <a href="https://masterpopups.com/docs/how-to-integrate-master-popups-with-mautic/" target="_blank">Go here</a>.',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
            'names_access_data' => array(
                'email' => 'Mautic Username or email',
                'password' => 'Mautic Password',
                'url' => 'Mautic URL',
                'api_key' => 'Client ID (Public Key)',
                'token' => 'Client Secret (Secret Key)',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "Pipedrive"
    |---------------------------------------------------------------------------------------------------
    */
    public static function pipedrive(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/pipedrive.png',
            'text' => 'Pipedrive',
            'access_data' => array(
                'token' => true,
            ),
            'help_url' => array(
                'token' => 'https://support.pipedrive.com/hc/en-us/articles/207344545',
            ),
            'allow' => array(
                'get_lists' => true,
            )
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "FreshMail"
    |---------------------------------------------------------------------------------------------------
    */
    public static function freshmail(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/freshmail.png',
            'text' => 'Freshmail',
            'access_data' => array(
                'api_key' => true,
                'token' => true
            ),
            'help_url' => array(
                'api_key' => 'https://freshmail.com/help-and-knowledge/help/account-settings/what-is-an-api-key-and-where-can-you-find-it/',
                'token' => 'https://freshmail.com/help-and-knowledge/help/account-settings/what-is-an-api-key-and-where-can-you-find-it/',
            ),
            'allow' => array(
                'get_lists' => true,
            )
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "Tu Newsletter"
    |---------------------------------------------------------------------------------------------------
    */
    public static function tunewsletter(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/tunewsletter.png',
            'text' => 'Tu Newsletter',
            'access_data' => array(
                'api_key' => true,
                'url' => true
            ),
            'help_url' => array(
                'api_key' => '',
                'url' => 'E.g: http://app.tuservidor.net/api/2.0',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
            'names_access_data' => array(
                'api_key' => 'User Key',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "SimplyCast"
    |---------------------------------------------------------------------------------------------------
    */
    public static function simply_cast(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/simply_cast.png',
            'text' => 'SimplyCast',
            'access_data' => array(
                'api_key' => true,
                'token' => true
            ),
            'help_url' => array(
                'api_key' => '',
                'token' => '',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
            'names_access_data' => array(
                'api_key' => 'Public Key',
                'token' => 'Secret Key',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "Infusionsoft"
    |---------------------------------------------------------------------------------------------------
    */
    public static function infusionsoft(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/infusionsoft.png',
            'text' => 'Infusionsoft',
            'access_data' => array(
                'api_key' => true,
                'token' => true
            ),
            'help_url' => array(
                'api_key' => 'http://help.infusionsoft.com/userguides/get-started/tips-and-tricks/api-key',
                'token' => 'http://help.infusionsoft.com/taxonomy/term/4/0',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
            'names_access_data' => array(
                'token' => 'App Name',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "Customer.io"
    |---------------------------------------------------------------------------------------------------
    */
    public static function customer_io(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/customer_io.png',
            'text' => 'Customer.io',
            'access_data' => array(
                'api_key' => true,
                'token' => true
            ),
            'help_url' => array(
                'api_key' => 'https://learn.customer.io/documentation/finding-your-api-key.html',
                'token' => 'https://learn.customer.io/documentation/finding-your-api-key.html',
            ),
            'allow' => array(
                'get_lists' => false,
            ),
            'names_access_data' => array(
                'token' => 'Site ID',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "Aweber"
    |---------------------------------------------------------------------------------------------------
    */
    public static function aweber(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/aweber.png',
            'text' => 'Aweber',
            'access_data' => array(
                'api_key' => true,
            ),
            'help_url' => array(
                'api_key' => 'https://auth.aweber.com/1.0/oauth/authorize_app/8e026577',
            ),
            'allow' => array(
                'get_lists' => false,
            ),
            'names_access_data' => array(
                'api_key' => 'Authorization code',
            ),
        );
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "Campaign Monitor"
    |---------------------------------------------------------------------------------------------------
    */
    public static function campaign_monitor(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/campaign_monitor.png',
            'text' => 'Campaign Monitor',
            'access_data' => array(
                'api_key' => true,
                'token' => true,
            ),
            'help_url' => array(
                'api_key' => 'https://auth.aweber.com/1.0/oauth/authorize_app/8e026577',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
            'names_access_data' => array(
                'token' => 'Client ID',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "Drip"
    |---------------------------------------------------------------------------------------------------
    */
    public static function drip(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/drip.png',
            'text' => 'Drip',
            'access_data' => array(
                'api_key' => true,
            ),
            'help_url' => array(
                'api_key' => 'https://help.drip.com/hc/en-us/articles/115003738532-Your-API-Token',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
            'names_access_data' => array(
                'api_key' => 'API Token',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "Newsman"
    |---------------------------------------------------------------------------------------------------
    */
    public static function newsman(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/newsman.png',
            'text' => 'NewsMan',
            'access_data' => array(
                'api_key' => true,
                'token' => true,
            ),
            'help_url' => array(
                'api_key' => 'https://kb.newsman.app/api/',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
            'names_access_data' => array(
                'token' => 'User ID',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "iContact"
    |---------------------------------------------------------------------------------------------------
    */
    public static function icontact(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/icontact.png',
            'text' => 'iContact',
            'access_data' => array(
                'api_key' => true,
                'email' => true,
                'password' => true,
            ),
            'help_url' => array(),
            'allow' => array(
                'get_lists' => true,
            ),
            'names_access_data' => array(
                'api_key' => 'iContact App Id',
                'email' => 'iContact App Username (email)',
                'password' => 'iContact App Password',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "ConvertKit"
    |---------------------------------------------------------------------------------------------------
    */
    public static function convertkit(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/convertkit.png',
            'text' => 'ConvertKit',
            'access_data' => array(
                'api_key' => true,
            ),
            'help_url' => array(
                'api_key' => 'https://help.convertkit.com/user-guides/understanding-your-convertkit-settings',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "TotalSend"
    |---------------------------------------------------------------------------------------------------
    */
    public static function totalsend(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/totalsend.png',
            'text' => 'TotalSend',
            'access_data' => array(
                'email' => true,
                'password' => true,
            ),
            'help_url' => array(
                'email' => 'http://kb.totalsend.com/docs/wordpress-integration',
                'password' => 'http://kb.totalsend.com/docs/wordpress-integration',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
            'names_access_data' => array(
                'email' => 'API Username (email)',
                'password' => 'API Password',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "Mailpoet"
    |---------------------------------------------------------------------------------------------------
    */
    public static function mailpoet(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/mailpoet.png',
            'text' => 'Mailpoet',
            'access_data' => array(),
            'allow' => array(
                'get_lists' => true,
            )
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "Ontraport"
    |---------------------------------------------------------------------------------------------------
    */
    public static function ontraport(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/ontraport.png',
            'text' => 'Ontraport',
            'access_data' => array(
                'api_key' => true,
                'token' => true,
            ),
            'help_url' => array(
                'api_key' => 'https://ontraport.com/support/integrations/obtain-ontraport-api-key-and-app-id/',
                'token' => 'https://ontraport.com/support/integrations/obtain-ontraport-api-key-and-app-id/',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
            'names_access_data' => array(
                'token' => 'App ID',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "Klaviyo"
    |---------------------------------------------------------------------------------------------------
    */
    public static function klaviyo(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/klaviyo.png',
            'text' => 'Klaviyo',
            'access_data' => array(
                'api_key' => true,
            ),
            'help_url' => array(
                'api_key' => 'https://help.klaviyo.com/hc/en-us/articles/115005062267-Manage-Your-Account-s-API-Keys',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "Egoi"
    |---------------------------------------------------------------------------------------------------
    */
    public static function egoi(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/egoi.png',
            'text' => 'Egoi',
            'access_data' => array(
                'api_key' => true,
            ),
            'help_url' => array(
                'api_key' => 'https://helpdesk.e-goi.com/511369-Whats-E-gois-API-and-where-do-I-find-my-API-key',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "sendpulse"
    |---------------------------------------------------------------------------------------------------
    */
    public static function sendpulse(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/sendpulse.png',
            'text' => 'SendPulse',
            'access_data' => array(
                'api_key' => true,
                'token' => true,
            ),
            'help_url' => array(
                'api_key' => 'https://login.sendpulse.com/settings/#api',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
            'names_access_data' => array(
                'api_key' => 'Client ID',
                'token' => 'Client Secret',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "sendgrid"
    |---------------------------------------------------------------------------------------------------
    */
    public static function sendgrid(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/sendgrid.png',
            'text' => 'SendGrid',
            'access_data' => array(
                'api_key' => true,
            ),
            'help_url' => array(
                'api_key' => 'https://sendgrid.com/docs/ui/account-and-settings/api-keys/',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "sendpress"
    |---------------------------------------------------------------------------------------------------
    */
    public static function sendpress(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/sendpress.png',
            'text' => 'SendPress',
            'access_data' => array(//'api_key' => true,
            ),
            'allow' => array(
                'get_lists' => true,
            )
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "agilecrm"
    |---------------------------------------------------------------------------------------------------
    */
    public static function agilecrm(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/agilecrm.png',
            'text' => 'Agile CRM',
            'access_data' => array(
                'api_key' => true,
                'email' => true,
                'url' => true,
            ),
            'help_url' => array(
                'api_key' => 'Got to your Agile CRM > Admin Settings -> Developers & API -> REST API',
                'url' => 'E.g: https://your-site.agilecrm.com',
            ),
            'allow' => array(
                'get_lists' => false,
            ),
            'names_access_data' => array(
                'email' => 'Agile CRM Email',
                'url' => 'Agile CRM URL',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "moosend"
    |---------------------------------------------------------------------------------------------------
    */
    public static function moosend(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/moosend.png',
            'text' => 'Moosend',
            'access_data' => array(
                'api_key' => true,
            ),
            'help_url' => array(
                'api_key' => 'https://help.moosend.com/hc/en-us/articles/208061865-How-do-I-connect-to-the-Moosend-Web-API-',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "clever_reach"
    |---------------------------------------------------------------------------------------------------
    */
    public static function clever_reach(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/clever_reach.png',
            'text' => 'Clever Reach',
            'access_data' => array(
                'api_key' => true,
                'email' => true,
                'password' => true,
            ),
            'help_url' => array(
                'api_key' => 'CleverReach Client number or Client ID',
                //'url' => 'E.g: https://your-site.agilecrm.com',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
            'names_access_data' => array(
                'api_key' => 'CleverReach Client number',
                'email' => 'CleverReach email',
                'password' => 'CleverReach password',
            ),
        );
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "sales_autopilot"
    |---------------------------------------------------------------------------------------------------
    */
    public static function sales_autopilot(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/sales_autopilot.png',
            'text' => 'Sales Autopilot',
            'access_data' => array(
                'token' => true,
                'password' => true,
            ),
            'help_url' => array(
                'token' => 'E.g: 555555555555555. Go to SalesAutopilot -> Settings -> Integration -> API Keys.',
                'password' => 'E.g: ce9a67b9203a3a6beab64e. Go to SalesAutopilot -> Settings -> Integration -> API Keys.',
            ),
            'allow' => array(
                'get_lists' => false,
            ),
            'names_access_data' => array(
                'token' => 'Username',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "esputnik"
    |---------------------------------------------------------------------------------------------------
    */
    public static function esputnik(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/esputnik.png',
            'text' => 'eSputnik',
            'access_data' => array(
                'email' => true,
                'password' => true,
            ),
            'help_url' => array(
                'email' => 'In Email and Password, enter the email and password you use to log into the eSputnik system.',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
            'names_access_data' => array(
                'email' => 'Email',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "mailwizz"
    |---------------------------------------------------------------------------------------------------
    */
    public static function mailwizz(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/mailwizz.png',
            'text' => 'MailWizz',
            'access_data' => array(
                'api_key' => true,
                'token' => true,
                'url' => true,
            ),
            'help_url' => array(
                'api_key' => 'https://kb.mailwizz.com/articles/find-api-info/',
                'url' => 'E.g: https://mailing.site.com/api/index.php. Connection error? Try with http or https. <a href="https://kb.mailwizz.com/articles/find-api-info/" target="_blank">Look at the end of the article.</a>',
                'token' => 'https://kb.mailwizz.com/articles/find-api-info/',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
            'names_access_data' => array(
                'api_key' => 'Public Key',
                'token' => 'Private Key',
                'url' => 'API Url',
            ),
        );
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "Salesforce"
    |---------------------------------------------------------------------------------------------------
    */
    public static function salesforce(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/salesforce.png',
            'text' => 'Salesforce',
            'access_data' => array(
                'api_key' => true,
                'token' => true,
            ),
            'auth_type' => 'oauth2',
            //Sólo agregar a los servicios que tienen un sólo tipo de autenticación y es diferente de basic_auth
            'auth_fields' => array(
                'oauth2' => array( 'api_key', 'token' ),
            ),
            'help_url' => array(
                'api_key' => 'https://masterpopups.com/docs/how-to-integrate-master-popups-with-salesforce',
                'token' => 'https://masterpopups.com/docs/how-to-integrate-master-popups-with-salesforce',
            ),
            'allow' => array(
                'get_lists' => false,
            ),
            'names_access_data' => array(
                'api_key' => 'Consumer Key',
                'token' => 'Consumer Secret',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "Zoho Campaigns"
    |---------------------------------------------------------------------------------------------------
    */
    public static function zoho_campaigns(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/zoho_campaigns.png',
            'text' => 'Zoho Campaigns',
            'access_data' => array(
                'api_key' => true,
            ),
            'auth_type' => 'oauth2',
            //Sólo agregar a los servicios que tienen un sólo tipo de autenticación y es diferente de basic_auth
            'auth_fields' => array(
                'basic_auth' => array( 'api_key' ),
                'oauth2' => array( 'api_key', 'token', 'url' ),
            ),
            'help_url' => array(
                'url' => 'Enter only one of these values: zoho.com, zoho.eu, zoho.com.au, zoho.in, zoho.com.cn',
                'api_key' => 'Where do I find this? <a href="https://www.zoho.com/accounts/protocol/oauth-setup.html" target="_blank">Go here</a>.',
                'token' => 'Where do I find this? <a href="https://masterpopups.com/docs/how-to-integrate-master-popups-with-zoho-campaigns/" target="_blank">Go here</a>.',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
            'names_access_data' => array(
                'url' => 'Zoho Domain',
                'api_key' => 'Client ID (Public Key) or Authentication Token (Old v1)',
                'token' => 'Client Secret (Secret Key)',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "Zoho CRM"
    |---------------------------------------------------------------------------------------------------
    */
    public static function zoho_crm(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/zoho_crm.png',
            'text' => 'Zoho CRM',
            'access_data' => array(
                'api_key' => true,
                'token' => true,
                'url' => true,
            ),
            'auth_type' => 'oauth2',
            //Sólo agregar a los servicios que tienen un sólo tipo de autenticación y es diferente de basic_auth
            'auth_fields' => array(
                'oauth2' => array( 'api_key', 'token', 'url' ),
            ),
            'help_url' => array(
                'api_key' => 'https://masterpopups.com/docs/how-to-integrate-master-popups-with-zoho-crm',
                'token' => 'https://masterpopups.com/docs/how-to-integrate-master-popups-with-zoho-crm',
                'url' => 'Enter only one of these values: zoho.com, zoho.eu, zoho.com.au, zoho.in, zoho.com.cn',
            ),
            'has_lists' => false,
            'allow' => array(
                'get_lists' => false,
            ),
            'names_access_data' => array(
                'api_key' => 'Client ID',
                'token' => 'Client Secret',
                'url' => 'Zoho Domain',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "email_octopus"
    |---------------------------------------------------------------------------------------------------
    */
    public static function email_octopus(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/email_octopus.png',
            'text' => 'Email Octopus',
            'access_data' => array(
                'api_key' => true,
            ),
            'help_url' => array(
                'api_key' => 'https://help.emailoctopus.com/article/89-where-to-find-my-api-key',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
            'names_access_data' => array(),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "pabbly"
    |---------------------------------------------------------------------------------------------------
    */
    public static function pabbly(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/pabbly.png',
            'text' => 'Pabbly',
            'access_data' => array(
                'token' => true,
            ),
            'help_url' => array(
                'token' => 'Go to Pabbly Account > Email Marketing (App) > Integrations > Developer API > Copy Bearer Token',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
            'names_access_data' => array(
                'token' => 'Token',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "automizy"
    |---------------------------------------------------------------------------------------------------
    */
    public static function automizy(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/automizy.png',
            'text' => 'Automizy',
            'access_data' => array(
                'token' => true,
            ),
            'help_url' => array(
                'token' => 'Go to Automizy Account > Settings > API Token > New Token. <a href="https://developers.automizy.com/automizyrestapi/images/automizy-new-token.png" target="_blank">See image</a>',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
            'names_access_data' => array(
                'token' => 'Token',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "sendfox"
    |---------------------------------------------------------------------------------------------------
    */
    public static function sendfox(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/sendfox.png',
            'text' => 'SendFox',
            'access_data' => array(
                'token' => true,
            ),
            'help_url' => array(
                'token' => 'https://sendfox.helpscoutdocs.com/article/133-access-tokens',
            ),
            'allow' => array(
                'get_lists' => true,
            ),
            'names_access_data' => array(
                'token' => 'Access Token',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "mailbluster"
    |---------------------------------------------------------------------------------------------------
    */
    public static function mailbluster(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/mailbluster.png',
            'text' => 'MailBluster',
            'access_data' => array(
                'api_key' => true,
            ),
            'help_url' => array(
                'api_key' => 'https://app.mailbluster.com/api-doc/getting-started',
            ),
            'allow' => array(
                'get_lists' => false,
            ),
            'names_access_data' => array(
                'api_key' => 'API key',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "bigmailer"
    |---------------------------------------------------------------------------------------------------
    */
    public static function bigmailer(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/bigmailer.png',
            'text' => 'Bigmailer',
            'access_data' => array(
                'api_key' => true,
                'token' => true,
            ),
            'help_url' => array(
                'api_key' => 'Go to Bigmailer Account > Settings > API > API Keys > Create Key',
                'token' => 'Go to Bigmailer Account > Manage Brands > Create Brand and copy the Brand ID'
            ),
            'allow' => array(
                'get_lists' => false,
            ),
            'names_access_data' => array(
                'api_key' => 'Api Key',
                'token' => 'Brand ID',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "mailrelay"
    |---------------------------------------------------------------------------------------------------
    */
    public static function mailrelay(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/mailrelay.png',
            'text' => 'Mailrelay',
            'access_data' => array(
                'token' => true,
                'url' => true,
            ),
            'help_url' => array(
                'token' => 'Go to Mailrelay Account > Settings > API Keys > Add new and copy the Token',
                'url' => 'Example: https://yoursite.ipzmarketing.com'
            ),
            'allow' => array(
                'get_lists' => true,
            ),
            'names_access_data' => array(
                'token' => 'Token',
                'url' => 'Your Mailrelay URL',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "fluent_crm"
    |---------------------------------------------------------------------------------------------------
    */
    public static function fluent_crm(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/fluent_crm.png',
            'text' => 'FluentCRM',
            'access_data' => array(),
            'allow' => array(
                'get_lists' => true,
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "encharge"
    |---------------------------------------------------------------------------------------------------
    */
    public static function encharge(){
        return array(
            'image_url' => MPP_URL . 'assets/admin/images/integrations/encharge.png',
            'text' => 'Encharge',
            'access_data' => array(
                'api_key' => true,
            ),
            'help_url' => array(
                'api_key' => 'Go to <a href="https://app.encharge.io/account/info" target="_blank">Account Info</a> and copy your "Write Key"',
            ),
            'allow' => array(
                'get_lists' => false,
            ),
            'names_access_data' => array(
                'api_key' => 'Write Key',
            ),
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Servicio "elastic_email"
    |---------------------------------------------------------------------------------------------------
    */
    //    public static function elastic_email(){
    //        return array(
    //            'image_url' => MPP_URL . 'assets/admin/images/integrations/elastic_email.png',
    //            'text' => 'Elastic Email',
    //            'access_data' => array(
    //                'api_key' => true,
    //                'email' => true,
    //                'password' => true,
    //            ),
    //            'help_url' => array(
    //                'api_key' => 'CleverReach Client number or Client ID',
    //                //'url' => 'E.g: https://your-site.agilecrm.com',
    //            ),
    //            'allow' => array(
    //                'get_lists' => true,
    //            ),
    //            'names_access_data' => array(
    //                'api_key' => 'CleverReach Client number',
    //                'email' => 'CleverReach email',
    //                'password' => 'CleverReach password',
    //            ),
    //        );
    //    }

}
