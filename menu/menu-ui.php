<?php

/*
 * Register the Menu Page
 */
function scws_menu() {
    add_menu_page(
        __( 'GFW', 'sensitive-chinese' ),
        __('GFW', 'sensitive-chinese' ),
        'manage_options',
        'scws_options',
        'scws_menu_function',
        'dashicons-editor-strikethrough',
        6
    );
    add_submenu_page( 
        'scws_options', 
        __( 'GFW Scanner Overview', 'sensitive-chinese' ),
        'Overview',
        'manage_options',
        'scws_options', 
        'scws_menu_function' 
    );

    include('menu-db-scan.php');
    add_submenu_page( 
        'scws_options', 
        __( 'GFW Database Scan', 'sensitive-chinese' ),
        'Database Scan',
        'manage_options',
        'scws_db_scan', 
        'scws_menu_db_scan' 
    );

    include('menu-file-scan.php');
    add_submenu_page( 
        'scws_options', 
        __( 'GFW Theme and Plugin Scan', 'sensitive-chinese' ),
        __('File Scan', 'sensitive-chinese' ),
        'manage_options',
        'scws_file_scan', 
        'scws_menu_file_scan' 
    );

    include('menu-active-scan.php');
    add_submenu_page( 
        'scws_options', 
        __( 'GFW Active Scans', 'sensitive-chinese' ),
        __('Active Scan', 'sensitive-chinese' ),
        'manage_options',
        'scws_active_scan', 
        'scws_menu_active_scan' 
    );
}
add_action( 'admin_menu', 'scws_menu' );


/*
 * Output the main menu content
 */
function scws_menu_function() {
    ?>

    <h1><?php _e('GFW Scanner Overview', 'sensitive-chinese'); ?></h1>

    <p><?php _e('The Great Firewords of China plugin works in three ways:', 'sensitive-chinese'); ?></p>

    <ol>
        <li><?php _e('It scans database content.'); ?></li>
        <li><?php _e('It scans theme and plugin content.'); ?></li>
        <li><?php _e('It actively monitors new pages, posts, and comments and alerts you when any sensitive words are added to your site.'); ?></li>
    </ol>
    
    <p><?php _e('You can activate scans using the menu options in the left column.') ?></p>

	<p><?php _e('Please use your best judgement when editing any content the GFW plugin identifies as sensitive. The plugin relies on <a href="https://github.com/jasonqng/chinese-keywords?utm_source=StudioHyperset.com&utm_medium=Case%20Study&utm_campaign=Launch%20a%20Chinese%20Website&utm_term=StudioHyperset&utm_content=StudioHyperset" target="_blank">this list</a>, which contains several generic terms such as "it," "admin," and "gov." Your site won\' t necessarily run afoul of the Chinese authorities just because our plugin identifies a sensitive keyword.') ?></p>
  
  	<p><strong><?php _e('To learn how we used this plugin to help a global business intelligence company launch its marketing site on the Chinese mainland, <a href="http://studiohyperset.com/how-do-i-launch-a-chinese-website/?utm_source=GFW%20Plugin&utm_medium=Plugin&utm_campaign=Launch%20a%20Chinese%20Website" target="_blank">click here</a>.') ?></strong></p>
  	
  	<hr />
  	
<p><em>a <a href="http://studiohyperset.com/?utm_source=GFW%20Plugin&utm_medium=Plugin&utm_campaign=Launch%20a%20Chinese%20Website" target="_blank">Studio Hyperset</a> expression</em></p>

   
    <?php
}



/*
 * Register the CSS
 */
function scws_admin_style() {
        wp_register_style( 'scws_admin_style', SCWS_URL . '/assets/css/styles.css', false, '1.0.0' );
        wp_enqueue_style( 'scws_admin_style' );
}
add_action( 'admin_enqueue_scripts', 'scws_admin_style' );