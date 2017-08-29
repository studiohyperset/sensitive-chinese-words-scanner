<?php
/********************************
 *
 * This file include functions related
 * with admin & plugin UI
 *
 ********************************/



/*
 * Add a settings link in plugin list page
 */
function plugin_add_settings_link( $links ) {
	$settings_link = '<a href="admin.php?page=scws_options">' . __( 'Settings' ) . '</a>';
	array_push( $links, $settings_link );
	return $links;
}
add_filter( "plugin_action_links_". SCWS_NAME, 'plugin_add_settings_link' );



/*
 * Register the Menu Pages
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

    include( SCWS_PATH . '/menu/menu-db-scan.php');
    add_submenu_page( 
        'scws_options', 
        __( 'GFW Database Scan', 'sensitive-chinese' ),
        'Database Scan',
        'manage_options',
        'scws_db_scan', 
        'scws_menu_db_scan' 
    );

    include( SCWS_PATH . '/menu/menu-file-scan.php');
    add_submenu_page( 
        'scws_options', 
        __( 'GFW Theme and Plugin Scan', 'sensitive-chinese' ),
        __('File Scan', 'sensitive-chinese' ),
        'manage_options',
        'scws_file_scan', 
        'scws_menu_file_scan' 
    );

    include( SCWS_PATH . '/menu/menu-active-scan.php');
    add_submenu_page( 
        'scws_options', 
        __( 'GFW Active Scans', 'sensitive-chinese' ),
        __('Active Scans', 'sensitive-chinese' ),
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
    
    if (scws_menu_check_activation() === false) return;

    ?>

    <h1><?php _e('Hi', 'sensitive-chinese'); ?>, <?php echo get_option('scws_activation_firstname', ''); ?>.</h1>

    <p><?php printf( __('Thank you for using the GFW plugin to scan %s website. The plugin works in three ways:', 'sensitive-chinese'), get_option('scws_activation_company', '') . "'s"); ?></p>

    <ol>
        <li><?php _e('It scans database content.', 'sensitive-chinese'); ?></li>
        <li><?php _e('It scans theme and plugin content.', 'sensitive-chinese'); ?></li>
        <li><?php _e('It actively monitors new pages, posts, and comments and alerts you when any sensitive words are added to your site.', 'sensitive-chinese'); ?></li>
    </ol>
    
    <p><?php _e('You can activate scans using the menu options in the left column.') ?></p>

	<p><?php printf(__('Please use your best judgement when editing any content the GFW plugin identifies as sensitive. The plugin relies on %sthis list%s, which contains several generic terms such as "it," "admin," and "gov." Your site won\' t necessarily run afoul of the Chinese authorities just because our plugin identifies a sensitive keyword.', 'sensitive-chinese'), '<a href="https://github.com/jasonqng/chinese-keywords?utm_source=StudioHyperset.com&utm_medium=Case%20Study&utm_campaign=Launch%20a%20Chinese%20Website&utm_term=StudioHyperset&utm_content=StudioHyperset" target="_blank">', '</a>'); ?></p>
  
  	<p><strong><?php printf(__('To learn how we used this plugin to help a global business intelligence company launch its marketing site on the Chinese mainland, %sclick here%s.', 'sensitive-chinese'), '<a href="http://studiohyperset.com/how-do-i-launch-a-chinese-website/?utm_source=GFW_Plugin&utm_medium=Plugin&utm_campaign=Launch%20a%20Chinese%20Website" target="_blank">', '</a>'); ?></strong></p>
  	
  	<hr />
  	
    <p><em>a <a href="http://studiohyperset.com/?utm_source=GFW_Plugin&utm_medium=Plugin&utm_campaign=Launch%20a%20Chinese%20Website" target="_blank">Studio Hyperset</a> expression</em></p>

   
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



/*
 * Handle the activation function
 */
function scws_menu_check_activation() {

    if ( get_option( 'scws_activation_email', '' ) == '' ) {
        ?>
        <div id="scws_activate">
            <div class="center-this">
                <h2><?php _e('Let\'s get started.', 'sensitive-chinese'); ?></h2>
                <p><?php _e('So we can customize your experience, please tell us a little about yourself.', 'sensitive-chinese'); ?></p>

                <!--[if lte IE 8]>
                <script charset="utf-8" type="text/javascript" src="//js.hsforms.net/forms/v2-legacy.js"></script>
                <![endif]-->
                <script charset="utf-8" type="text/javascript" src="//js.hsforms.net/forms/v2.js"></script>
                <script>
                hbspt.forms.create({ 
                    portalId: '550584',
                    formId: '480a40ec-fd1d-43a8-9849-e0488baf94b7',
                    redirectUrl: "<?php echo admin_url('admin.php?page=scws_options'); ?>",
                    onFormReady: function($form) {
                        $form.find('input').each( function(){
                            jQuery(this).attr('placeholder', jQuery(this).parent().parent().children('label').text());
                        });
                    },
                    onFormSubmit: function($form) {
                        var data = $form.serialize();
                        data += '&action=scws_save_activation';
                        jQuery.post( ajaxurl, data, function( result ) {

                        });
                    }
                });
                </script>
            </div>
        </div>
        <?php

        return false;
    }
    
    return true;

}