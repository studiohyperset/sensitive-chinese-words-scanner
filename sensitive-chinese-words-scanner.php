<?php 
/*
**************************************************************************

Plugin Name:  The Great Firewords of China
Plugin URI:   https://studiohyperset.com/how-do-i-launch-a-chinese-website/
Description:  Scan your website for words and phrases that the Chinese government considers sensitive. Edit or remove content the plugin identifies, and decrease the chance your site will be blocked by the Great Firewall of China. If your site's already being blocked, this plugin can help you discover possible reasons why.
Version:      1.2
Author:       Studio Hyperset
Author URI:   https://studiohyperset.com
Text Domain:  sensitive-chinese

**************************************************************************/

define('SCWS_URL', plugins_url('', __FILE__));
define('SCWS_PATH', plugin_dir_path(__FILE__));
define('SCWS_NAME', plugin_basename( __FILE__ ));



//Core Functions
require('include/ui.php');
require('include/words.php');
require('include/editor.php');
require('include/active-scan.php');



//Ajax Functions
require('ajax/db-scan.php');
require('ajax/file-scan.php');
require('ajax/options.php');


function scws_redirect_to_options( $plugin ) {
    
    if( $plugin == plugin_basename( __FILE__ ) ) { 
        wp_redirect('admin.php?page=scws_options');
        exit;
    }

}
add_action( 'activated_plugin', 'cyb_activation_redirect' );


register_activation_hook(__FILE__, 'scws_trigger_activation');
function scws_trigger_activation() {
    add_option('scws_just_activated', true);
}


add_action('admin_init', 'scws_show_activation');
function scws_show_activation() {
    if (get_option('scws_just_activated', false)) {
        delete_option('scws_just_activated');
        wp_redirect('admin.php?page=scws_options');
        exit;
    }
}