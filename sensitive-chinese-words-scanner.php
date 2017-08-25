<?php 
/*
**************************************************************************

Plugin Name:  The Great Firewords of China
Plugin URI:   http://studiohyperset.com/how-do-i-launch-a-chinese-website/
Description:  Scan your website for words and phrases that the Chinese government considers sensitive. Edit or remove content the plugin identifies, and decrease the chance your site will be blocked by the Great Firewall of China. If your site's already being blocked, this plugin can help you discover possible reasons why.
Version:      0.2.5
Author:       Studio Hyperset
Author URI:   https://www.studiohyperset.com
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