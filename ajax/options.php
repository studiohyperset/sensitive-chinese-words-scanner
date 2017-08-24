<?php

/*
 * This function executed the options saving
 */
add_action( 'wp_ajax_scws_save_options', 'scws_save_options' );
function scws_save_options() {
     
     //Check if any of expected options was sent
     $options = array( 'enable_active_scan', 'active_scan_warn', 'active_scan_warn_email', 'active_scan_autoreplace', 'active_scan_autoreplace_word' );
     $found = false;
     foreach( $options as $option ) {
          if (isset($_POST[$option])) {
               $found = $option;
               break;
          }
     }

     if ($found === false)
          die('0');

     update_option( 'scws_' . $option, $_POST[$option] );

     die('1');     
}