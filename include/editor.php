<?php
/********************************
 *
 * This file include functions that
 * will change the file editor behavior
 *
 ********************************/



/*
 * Return the file types that we will search for
 */
function scws_get_file_types() {
     return array('txt', 'php', 'js', 'doc', 'html', 'xml');
}



/*
 * Allow editor to edit custom file formats
 */
add_filter('wp_theme_editor_filetypes', 'scws_editor_filetypes');
add_filter('editable_extensions', 'scws_editor_filetypes');
function scws_editor_filetypes( $types ) {
     return array_unique( array_merge( scws_get_file_types(), $types ) );
}