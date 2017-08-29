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

    if ($_POST[$option] == '')
        delete_option( 'scws_' . $option, $_POST[$option] );
    else
        update_option( 'scws_' . $option, $_POST[$option] );

    die('1');     
}



/*
 * This function executed the activation saving
 */
add_action( 'wp_ajax_scws_save_activation', 'scws_save_activation' );
function scws_save_activation() {
     
    //Check if activation option was sent
    $options = array( 'firstname', 'lastname', 'company', 'email' );
    $found = false;
    foreach( $options as $option ) {
        if (isset($_POST[$option])) {
            update_option( 'scws_activation_'. $option, $_POST[$option] );
            $found = $option;
        }
    }

    if ($found === false)
        die('0');

    die('1');     

}