<?php
/**
 * Plugin Name: Master Popups
 * Plugin URI: http://masterpopups.com
 * Description: Multi-Purpose Popup Plugin for WordPress with Powerful and Easy Email Marketing Integration
 * Version: 3.8.5
 * Author: CodexHelp
 * Author URI: https://codecanyon.net/user/codexhelp
 * Text Domain: masterpopups
 * Domain Path: /languages/
 */



if( ! class_exists( 'MasterPopups\Loader', false ) ){
    include dirname( __FILE__ ) . '/loader.php';
    $loader = MasterPopups\Loader::get_instance(array(
        'master-popups*/master-popups.php',
        'master-popups-lite*/master-popups-lite.php',
    ));
    $plugin_data = $loader->get_plugin_data();
    if( empty( $plugin_data ) ){
        //Exit during activation to set info to $plugin_data
        return;
    }

    if ( ! class_exists( 'MasterPopups', false ) ) {
        include dirname( __FILE__ ) . '/includes/class-master-popups.php';
    }

    if( ! function_exists('MasterPopups') ){
        function MasterPopups( $plugin_data ){
            return MasterPopups::get_instance(array(
                'plugin_data'        => $plugin_data,
                'version'            => $plugin_data['Version'],
                'name'               => $plugin_data['Name'],
                'menu_name'          => 'Master Popups',
                'short_name'         => 'MasterPopups',
                'slug'               => 'master-popups',
                'text_domain'        => 'master-popups',
                'prefix'             => 'mpp_',
                'post_types'         => array(
                    'popups'        => 'master-popups',
                    'lists'          => 'mpp_audience',
                ),
                'xbox_ids'           => array(
                    'settings'           => 'settings-master-popups',
                    'popup-editor'       => 'popup-editor-master-popups',
                    'audience-editor'    => 'audience-editor-master-popups',
                ),
                'item_id'            => '20142807',
            ));
        }
        $MasterPopups = MasterPopups( $plugin_data );
        do_action('masterpopups_loaded', $MasterPopups );
    }
}
register_activation_hook( __FILE__, array( '\MasterPopups\Loader', 'on_activate'   ) );
