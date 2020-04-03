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

    if ($option == 'active_scan_warn_email') {
        //Send it to HubSpot
        $hubspotutk = $_COOKIE['hubspotutk'];
        $ip_addr = $_SERVER['REMOTE_ADDR']; //IP address too.
        $hs_context = array(
            'hutk' => $hubspotutk,
            'ipAddress' => $ip_addr
        );
        $hs_context_json = json_encode($hs_context);

        //replace the values in this URL with your portal ID and your form GUID
        $url = 'https://forms.hubspot.com/uploads/form/v2/4542224/5a531af0-7b8e-4351-9e3f-b8a996cc8ac7';

        $postdata = http_build_query(
            array(
                'firstname' => urlencode( get_option('scws_activation_firstname', '') ),
                'lastname' => urlencode( get_option('scws_activation_lastname', '') ),
                'email' => $_POST[$option],
                'hs_context' => $hs_context_json
            )
        );

        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );

        $context  = stream_context_create($opts);

        $result = @file_get_contents($url, false, $context);
    }

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