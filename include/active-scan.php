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

    //Store in the email variable
    // array( word, location )
    $email = array();

    foreach ( $founds as $found ) {
        $url = '';

        //Third item of array will show us
        //where the word was found
        switch ( $found[2] ) {
            case $wpdb->prefix . 'posts':
                $report[] = 'Word <i>'. $found[0] .'</i> detected at <i>'. $found[3] .'</i> of post <a href="'. admin_url( 'post.php?post='. $found[5] .'&action=edit' ) .'">#'. $found[5] .'</a>. <span>'. $found[1] .'</span>';
                $url = '<i>'. ucwords($found[3]) .'</i> of post <a href="'. admin_url( 'post.php?post='. $found[5] .'&action=edit' ) .'">#'. $found[5] .'</a>';
            break;
            case $wpdb->prefix . 'postmeta':
                $meta = get_metadata_by_mid( 'post', $found[4] );
                $meta = $meta->meta_key;
                $report[] = 'Word <i>'. $found[0] .'</i> detected at custom field <i>'. $meta .'</i> of post <a href="'. admin_url( 'post.php?post='. $found[5] .'&action=edit' ) .'">#'. $found[5] .'</a>. <span>'. $found[1] .'</span>';
                $url = 'Field <i>'. ucwords($meta) .'</i> of post <a href="'. admin_url( 'post.php?post='. $found[5] .'&action=edit' ) .'">#'. $found[5] .'</a>';
            break;
            case $wpdb->prefix . 'comments':
                $report[] = 'Word <i>'. $found[0] .'</i> detected at <i>'. $found[3] .'</i> of comment <a href="'. admin_url( 'comment.php?action=editcomment&c='. $found[5] ) .'">#'. $found[5] .'</a>. <span>'. $found[1] .'</span>';
                $url = '<i>'. ucwords($found[3]) .'</i> of comment <a href="'. admin_url( 'comment.php?action=editcomment&c='. $found[5] ) .'">#'. $found[5] .'</a>';
            break;
            case $wpdb->prefix . 'commentmeta':
                $meta = get_metadata_by_mid( 'comment', $found[4] );
                $meta = $meta->meta_key;
                $report[] = 'Word <i>'. $found[0] .'</i> detected at custom field <i>'. $meta .'</i> of comment <a href="'. admin_url( 'comment.php?action=editcomment&c='. $found[5] ) .'">#'. $found[5] .'</a>. <span>'. $found[1] .'</span>';
                $url = 'Field <i>'. ucwords($meta) .'</i> of comment <a href="'. admin_url( 'comment.php?action=editcomment&c='. $found[5] ) .'">#'. $found[5] .'</a>';
            break;
            case $wpdb->prefix . 'terms':
            case $wpdb->prefix . 'term_taxonomy':
                $term = get_term( $found[5] );
                $report[] = 'Word <i>'. $found[0] .'</i> detected at <i>'. $found[3] .'</i> of term <a href="'. admin_url( 'term.php?taxonomy='. $term->taxonomy .'&tag_ID='. $found[5] ) .'">#'. $found[5] .'</a>. <span>'. $found[1] .'</span>';
                $url = '<i>'. ucwords($found[3]) .'</i> of term <a href="'. admin_url( 'term.php?taxonomy='. $term->taxonomy .'&tag_ID='. $found[5] ) .'">#'. $found[5] .'</a>';
            break;
            case $wpdb->prefix . 'termmeta':
                $term = get_term( $found[5] );
                $meta = get_metadata_by_mid( 'term', $found[4] );
                $meta = $meta->meta_key;
                $report[] = 'Word <i>'. $found[0] .'</i> detected at custom field <i>'. $meta .'</i> of term <a href="'. admin_url( 'term.php?taxonomy='. $term->taxonomy .'&tag_ID='. $found[5] ) .'">#'. $found[5] .'</a>. <span>'. $found[1] .'</span>';
                $url = 'Field <i>'. ucwords($meta) .'</i> of term <a href="'. admin_url( 'term.php?taxonomy='. $term->taxonomy .'&tag_ID='. $found[5] ) .'">#'. $found[5] .'</a>';
            break;
            case $wpdb->base_prefix . 'users':
                $report[] = 'Word <i>'. $found[0] .'</i> detected at <i>'. $found[3] .'</i> of user <a href="'. admin_url( 'user-edit.php?user_id='. $found[5] ) .'">#'. $found[5] .'</a>. <span>'. $found[1] .'</span>';
                $url = '<i>'. ucwords($found[3]) .'</i> of user <a href="'. admin_url( 'user-edit.php?user_id='. $found[5] ) .'">#'. $found[5] .'</a>';
            break;
            case $wpdb->base_prefix . 'usermeta':
                $meta = get_metadata_by_mid( 'user', $found[4] );
                $meta = $meta->meta_key;
                $report[] = 'Word <i>'. $found[0] .'</i> detected at custom field <i>'. $meta .'</i> of user <a href="'. admin_url( 'user-edit.php?user_id='. $found[5] ) .'">#'. $found[5] .'</a>. <span>'. $found[1] .'</span>';
                $url = 'Field <i>'. ucwords($meta) .'</i> of user <a href="'. admin_url( 'user-edit.php?user_id='. $found[5] ) .'">#'. $found[5] .'</a>';
            break;
            default :
                $report[] = 'Word <i>'. $found[0] .'</i> detected at <i>'. $found[2] .'</i> in <i>'. $found[3] .'</i> with ID <i>'. $found[4] .'</i>  and parent ID <i>'. $found[5] .'</i>. <span>'. $found[1] .'</span>';
                $url = '<i>'. ucwords($found[3]) .'</i> in <i>'. $found[3] .'</i> with ID <i>'. $found[4] .'</i>  and parent ID <i>'. $found[5] .'</i>';
        }

        $email[] = array( $found[0], $url );

    }
    
    if (count($report) > 100)
        $report = array_slice( $report, -100);

    //Save the log
    update_option( 'scws_active_report', $report );

    scws_active_scan_do_email_submission( $email );

}



/*
 * This function will send the email alert
 */
function scws_active_scan_do_email_submission( $report ) {

    //Check if any report to send
    if (!empty($report)) {

        //Check if option is on
        if ( get_option( 'scws_active_scan_warn', 'no' ) == 'yes' ) {

            //Check if email is set
            $to = get_option( 'scws_active_scan_warn_email', get_option('scws_activation_email', '') );
            if ($to !== '') {

                //If have more than 5 results, show only 5
                $total = count($report);
                if ($total > 5)
                    $report = array_slice( $report, -5 );

                //Load email templates in var
                $inner = file_get_contents( SCWS_PATH . '/assets/email/inner.html' );
                $body = file_get_contents( SCWS_PATH . '/assets/email/main.html' );

                //Create the inner table
                $table = '';
                foreach( $report as $email ) {
                    $table .= str_replace( array('%word%', '%location%'), $email, $inner );
                }

                //Show the additional words
                if ($total > 5) {
                    $table .= '<tr align="left" style="background: #FFFFFF; border: 0; border-collapse: collapse; border-spacing: 0" bgcolor="#FFFFFF"><td><table cellspacing="0" cellpadding="0" border="0" style="border: 0; border-collapse: collapse; border-spacing: 0; min-width: 650px"><tbody><tr style="border: 0; border-collapse: collapse; border-spacing: 0"><td width="20" height="30" bgcolor="#FFFFFF" align="left" valign="top" style="background: #FFFFFF; font-size: 1px; height: 30px; line-height: 1px; width: 20px"> </td><td width="600" align="left" valign="top" height="30" style="background: #FFFFFF; height: 30px; width: 600px" bgcolor="#FFFFFF"><!-- content start --><p style="color: #111111 !important; font-family: Open Sans; font-size: 20px; font-style: normal; font-weight: 400; line-height: 24px;  margin-top:0; padding-top:0; margin-right:0; padding-right:0; margin-bottom:0; padding-bottom:0; margin-left:0; padding-left:0;  text-shadow: none">'. ($total - 5) .' more words found. <a href="'. admin_url( 'admin.php?page=scws_active_scan' ) .'">View the full report on your site.</a></p></div><!-- content end --></td><td width="19" height="30" bgcolor="#FFFFFF" align="left" valign="top" style="background: #FFFFFF; font-size: 1px; height: 30px; line-height: 1px; width: 19px"> </td></tr></tbody></table></td></tr>';
                }
                
                //Update main template
                $body = str_replace( 
                    array( '%firstname%', '%results%', '%shlogo%' ), 
                    array( get_option('scws_activation_firstname', ''), $table, SCWS_PATH . '/assets/email/sh_logo.jpg'  ), 
                    $body 
                );

                //Subject
                $subject = 'Chinese Sensitive Content Report';
                $headers = array('Content-Type: text/html; charset=UTF-8');
                wp_mail( $to, $subject, $body, $headers );

            }
        }

    }

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
     // array( $word_found, $strippedText, $table, $column, $rowid, $original_id_related )
     $found = array();

     $words = scws_get_words();

     //Check each column for words
     foreach ($results[0] as $column => $value ) {

          $search = scws_search_words_in_text( $words, $value );

          //No words found in this column
          if (empty($search))
               continue;

          foreach( $search as $s ) {
               $found[] = array( $s[0], scws_feature_word( $value, $s[0] ), $wpdb->prefix . 'posts', $column, $post_id, $post_id );
          }

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
               
               foreach( $search as $s ) {
                    $found[] = array( $s[0], scws_feature_word( $value->meta_value, $s[0] ), $wpdb->prefix . 'postmeta', 'meta_value', $value->meta_id, $post_id );
               }
     
          }

     }

     //A custom filter so theme & plugins may add or remove special cases
     $found = apply_filters( 'scws_active_scan_save_post', $found );

     //Let's proceed with the changes
     if (!empty($found))
          scws_active_scan_do_actions( $found );

     return;

}



/*
 * This function will verify words after a comment is made
 */
add_action( 'comment_post', 'scws_active_scan_comment_save', 10 );
add_action( 'edit_comment', 'scws_active_scan_comment_save', 10 );
function scws_active_scan_comment_save( $post_id ) {
     
     //First check if plugin is active
     if ( get_option( 'scws_enable_active_scan', 'no' ) != 'yes' )
          return;

     global $wpdb;

     //Let's select all data in DB related with this comment. 
     //This is the only reliable way to check all data sent.

     //Lets start with main comments table
     $sql = 'SELECT comment_author, comment_author_url, comment_content FROM '. $wpdb->prefix . 'comments WHERE comment_ID = '. $post_id;
     $results = $wpdb->get_results($sql);

     //The ID is not at DB
     if (empty($results))
          return;

     //This will hold the found word in format
     // array( $word_found, $strippedText, $table, $column, $rowid, $original_id_related )
     $found = array();

     $words = scws_get_words();

     //Check each column for words
     foreach ($results[0] as $column => $value ) {

          $search = scws_search_words_in_text( $words, $value );

          //No words found in this column
          if (empty($search))
               continue;

          foreach( $search as $s ) {
               $found[] = array( $s[0], scws_feature_word( $value, $s[0] ), $wpdb->prefix . 'comments', $column, $post_id, $post_id );
          }

     }

     //Let's check the Comments Custom Fields Column
     $sql = 'SELECT meta_id, meta_value FROM '. $wpdb->prefix . 'commentmeta WHERE comment_id = '. $post_id;
     $results = $wpdb->get_results($sql);

     //The ID is not at DB
     if (!empty($results)) {

          //Check each column for words
          foreach ($results as $value ) {
          
               $search = scws_search_words_in_text( $words, $value->meta_value );

               //No words found in this column
               if (empty($search))
                    continue;

               foreach( $search as $s ) {
                    $found[] = array( $s[0], scws_feature_word( $value->meta_value, $s[0] ), $wpdb->prefix . 'commentmeta', 'meta_value', $value->meta_id, $post_id );
               }

          }

     }

     //A custom filter so theme & plugins may add or remove special cases
     $found = apply_filters( 'scws_active_scan_save_comment', $found );

     //Let's proceed with the changes
     if (!empty($found))
          scws_active_scan_do_actions( $found );

     return;

}



/*
 * This function will verify words after a new term is added or edited
 */
add_action( 'edited_terms', 'scws_active_scan_term_save', 10 );
add_action( 'create_term', 'scws_active_scan_term_save', 10 );
function scws_active_scan_term_save( $post_id ) {
     
     //First check if plugin is active
     if ( get_option( 'scws_enable_active_scan', 'no' ) != 'yes' )
          return;

     global $wpdb;

     //Let's select all data in DB related with this term. 
     //This is the only reliable way to check all data sent.

     //Lets start with main terms table
     $sql = 'SELECT name, slug FROM '. $wpdb->prefix . 'terms WHERE term_id = '. $post_id;
     $results = $wpdb->get_results($sql);

     //The ID is not at DB
     if (empty($results))
          return;

     //This will hold the found word in format
     // array( $word_found, $strippedText, $table, $column, $rowid, $original_id_related )
     $found = array();

     $words = scws_get_words();

     //Check each column for words
     foreach ($results[0] as $column => $value ) {

          $search = scws_search_words_in_text( $words, $value );

          //No words found in this column
          if (empty($search))
               continue;

          foreach( $search as $s ) {
               $found[] = array( $s[0], scws_feature_word( $value, $s[0] ), $wpdb->prefix . 'terms', $column, $post_id, $post_id );
          }

     }
     
     //Let's check the Terms Custom Fields Column
     $sql = 'SELECT meta_id, meta_value FROM '. $wpdb->prefix . 'termmeta WHERE term_id = '. $post_id;
     $results = $wpdb->get_results($sql);

     //The ID is not at DB
     if (!empty($results)) {

          //Check each column for words
          foreach ($results as $value ) {
          
               $search = scws_search_words_in_text( $words, $value->meta_value );

               //No words found in this column
               if (empty($search))
                    continue;

               foreach( $search as $s ) {
                    $found[] = array( $s[0], scws_feature_word( $value->meta_value, $s[0] ), $wpdb->prefix . 'termmeta', 'meta_value', $value->meta_id, $post_id );
               }

          }

     }
     
     //Let's check the Terms Description Field
     $sql = 'SELECT term_taxonomy_id, description FROM '. $wpdb->prefix . 'term_taxonomy WHERE term_id = '. $post_id;
     $results = $wpdb->get_results($sql);

     //The ID is not at DB
     if (!empty($results)) {

          //Check each column for words
          foreach ($results as $value ) {
          
               $search = scws_search_words_in_text( $words, $value->description );

               //No words found in this column
               if (empty($search))
                    continue;

               foreach( $search as $s ) {
                    $found[] = array( $s[0], scws_feature_word( $value->description, $s[0] ), $wpdb->prefix . 'term_taxonomy', 'description', $value->term_taxonomy_id, $post_id );
               }

          }

     }

     //A custom filter so theme & plugins may add or remove special cases
     $found = apply_filters( 'scws_active_scan_save_term', $found );

     //Let's proceed with the changes
     if (!empty($found))
          scws_active_scan_do_actions( $found );

     return;

}



/*
 * This function will verify words after a new user is created or update his profile
 */
add_action( 'user_register', 'scws_active_scan_user_save', 10 );
add_action( 'profile_update', 'scws_active_scan_user_save', 10 );
function scws_active_scan_user_save( $post_id ) {
     
     //First check if plugin is active
     if ( get_option( 'scws_enable_active_scan', 'no' ) != 'yes' )
          return;

     global $wpdb;

     //Let's select all data in DB related with this user
     //This is the only reliable way to check all data sent.

     //Lets start with main users table
     $sql = 'SELECT user_nicename, display_name FROM '. $wpdb->base_prefix . 'users WHERE ID = '. $post_id;
     $results = $wpdb->get_results($sql);
     
     //The ID is not at DB
     if (empty($results))
          return;

     //This will hold the found word in format
     // array( $word_found, $strippedText, $table, $column, $rowid, $original_id_related )
     $found = array();

     $words = scws_get_words();

     //Check each column for words
     foreach ($results[0] as $column => $value ) {

          $search = scws_search_words_in_text( $words, $value );

          //No words found in this column
          if (empty($search))
               continue;

          foreach( $search as $s ) {
               $found[] = array( $s[0], scws_feature_word( $value, $s[0] ), $wpdb->base_prefix . 'users', $column, $post_id, $post_id );
          }

     }

     //Let's check the User Custom Fields Column
     $sql = 'SELECT umeta_id, meta_value FROM '. $wpdb->base_prefix . 'usermeta WHERE user_id = '. $post_id;
     $results = $wpdb->get_results($sql);

     //The ID is not at DB
     if (!empty($results)) {

          //Check each column for words
          foreach ($results as $value ) {
          
               $search = scws_search_words_in_text( $words, $value->meta_value );

               //No words found in this column
               if (empty($search))
                    continue;

               foreach( $search as $s ) {
                    $found[] = array( $s[0], scws_feature_word( $value->meta_value, $s[0] ), $wpdb->base_prefix . 'usermeta', 'meta_value', $value->umeta_id, $post_id );
               }

          }

     }

     //A custom filter so theme & plugins may add or remove special cases
     $found = apply_filters( 'scws_active_scan_save_user', $found );

     //Let's proceed with the changes
     if (!empty($found))
          scws_active_scan_do_actions( $found );

     return;

}