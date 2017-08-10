<?php

/*
 * Register the Menu Page
 */
function scws_menu() {
    add_menu_page(
        __( 'Sensitive Chinese Words Scanner', 'sensitive-chinese' ),
        __('Sensitive Chinese', 'sensitive-chinese' ),
        'manage_options',
        'scws_options',
        'scws_menu_function',
        'dashicons-editor-strikethrough',
        6
    );

    include('menu-db-scan.php');
    add_submenu_page( 
        'scws_options', 
        __( 'Sensitive Chinese DB Scanner', 'sensitive-chinese' ),
        'DB Scan',
        'manage_options',
        'scws_db_scan', 
        'scws_menu_db_scan' 
    );

    include('menu-file-scan.php');
    add_submenu_page( 
        'scws_options', 
        __( 'Sensitive Chinese File Scanner', 'sensitive-chinese' ),
        __('File Scan', 'sensitive-chinese' ),
        'manage_options',
        'scws_file_scan', 
        'scws_menu_file_scan' 
    );

    include('menu-active-scan.php');
    add_submenu_page( 
        'scws_options', 
        __( 'Sensitive Chinese Active Scanner', 'sensitive-chinese' ),
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

    <h1><?php _e('Sensitive Chinese Words Scanner Options', 'sensitive-chinese'); ?></h1>

    <p><?php _e('Welcome to ...', 'sensitive-chinese'); ?></p>

    <p><?php _e('Our plugin works in 3 fronts:', 'sensitive-chinese'); ?></p>

    <ol>
        <li><?php _e('Scanning content in your current DB. Brief description...'); ?></li>
        <li><?php _e('Scanning content in your theme and plugins. Brief description...'); ?></li>
        <li><?php _e('Actively checking new content saved (like posts and comments). Brief description...'); ?></li>
    </ol>

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