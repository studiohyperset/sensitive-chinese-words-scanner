<?php
/********************************
 *
 * This file include functions related
 * with the active check functionality
 *
 ********************************/



/*
 * This function will save in the log
 * and invoke proper actions
 */
function scws_active_scan_do_actions( $founds ) {

     global $wpdb;
     
     //Save in the log
     $report = get_option( 'scws_active_report', array() );

     foreach ( $founds as $found ) {

          //Third item of array will show us
          //where the word was found
          switch ( $found[2] ) {
               case $wpdb->prefix . 'posts':
               case $wpdb->prefix . 'postmeta':
                    $report[] = 'Word <i>'. $found[0] .'</i> detected at <i>'. $found[3] .'</i> of post <a href="'. admin_url( 'post.php?post='. $found[4] .'&action=edit' ) .'">#'. $found[4] .'</a>. <span>'. $found[1] .'</span>';
               break;
               default :
                    $report[] = 'Word <i>'. $found[0] .'</i> detected at <i>'. $found[2] .'</i> in <i>'. $found[3] .'</i> with ID <i>'. $found[4] .'</i>. <span>'. $found[1] .'</span>';
          }

     }
     
     if (count($report) > 100)
          $report = array_slice( $report, -100);

     //Save the log
     update_option( 'scws_active_report', $report );

}



/*
 * This function will verify words after a post save
 */
add_action( 'save_post', 'scws_active_scan_post_save', 9999 );
function scws_active_scan_post_save( $post_id ) {
     
     //First check if plugin is active
     if ( get_option( 'scws_enable_active_scan', 'no' ) != 'yes' )
          return;

     global $wpdb;

     //Let's select all data in DB related with this post. 
     //This is the only reliable way to check all data sent.

     //Lets start with main posts table
     $sql = 'SELECT post_content, post_title, post_excerpt, post_name FROM '. $wpdb->prefix . 'posts WHERE ID = '. $post_id;
     $results = $wpdb->get_results($sql);

     //The ID is not at DB
     if (empty($results))
          return;

     //This will hold the found word in format
     // array( $word_found, $strippedText, $table, $column, $rowid )
     $found = array();

     $words = scws_get_words();

     //Check each column for words
     foreach ($results[0] as $column => $value ) {

          $search = scws_search_words_in_text( $words, $value );

          //No words found in this column
          if (empty($search))
               continue;

          $found[] = array( $search[0][0], scws_feature_word( $value, $search[0][0] ), $wpdb->prefix . 'posts', $column, $post_id );

     }

     //Let's check the Custom Fields Column
     $sql = 'SELECT meta_id, meta_value FROM '. $wpdb->prefix . 'postmeta WHERE post_id = '. $post_id;
     $results = $wpdb->get_results($sql);

     //The ID is not at DB
     if (!empty($results)) {

          //Check each column for words
          foreach ($results as $value ) {
          
               $search = scws_search_words_in_text( $words, $value->meta_value );
     
               //No words found in this column
               if (empty($search))
                    continue;
     
               $found[] = array( $search[0][0], scws_feature_word( $value->meta_value, $search[0][0] ), $wpdb->prefix . 'postmeta', 'meta_value', $value->meta_id );
     
          }

     }

     //A custom filter so theme & plugins may add or remove special cases
     $found = apply_filters( 'scws_active_scan_save_post', $found );

     //Let's proceed with the changes
     if (!empty($found))
          scws_active_scan_do_actions( $found );

     return;

}