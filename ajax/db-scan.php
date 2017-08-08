<?php

/*
 * This function executed the search in the DB
 */
add_action( 'wp_ajax_scws_db_scan', 'scws_db_scan_ajax' );
function scws_db_scan_ajax() {

     //Verify Step
     if ( empty( $_POST['step'] ) )
          scws_ajax_die( 0, __('Error! Please try again later', 'sensitive-chinese') );

     $step = intval( $_POST['step'] );

     //Verify Nonce
     if (! wp_verify_nonce( $_POST['scws_db_scan_nonce'], 'scws_db_scan_nonce_'. $step ) )
          scws_ajax_die( 0, __('Error! Please try again later', 'sensitive-chinese') );

     //Verify Step
     if ( empty($_POST['step']) )
          scws_ajax_die( 0, __('Error! Please try again later', 'sensitive-chinese') );

     global $wpdb;
     $return = array();

     switch ( $step ) {

          case 1 :

               //Starting. Let's get all the tables.
               $rows = $wpdb->get_results('SHOW TABLES LIKE "'. $wpdb->prefix .'%"', OBJECT_K );

               if (count($rows) == 0)
                    scws_ajax_die( 0, __('Error! We could not read data from your DB.', 'sensitive-chinese') );

               //Transform array in  a serialized var
               $add = array();
               foreach ($rows as $key => $value) {
                    $add[] = $key;
               }

               $step++;
               $return[] = $step;
               $return[] = htmlspecialchars(json_encode($add), ENT_QUOTES, 'UTF-8');
               $return[] = wp_create_nonce( 'scws_db_scan_nonce_'.$step );
               $return[] = count($add) . ' ' . __('tables found.', 'sensitive-chinese') . '<br />';

               scws_ajax_die( 1, $return );

          break;

          default: 

               if ( empty($_POST['additional']) )
                    scws_ajax_die( 0, __('Error! Please try again later', 'sensitive-chinese') );

               $tables = json_decode(htmlspecialchars_decode($_POST['additional'], ENT_QUOTES));

               if (!isset($tables[$step-2])) {
                    scws_ajax_die( 0, __('All columns checked.', 'sensitive-chinese') );
               }
               $table = $tables[$step-2];

               //Already Know Cases. Saving SQL Resources
               switch ($table) {
                    case $wpdb->prefix . 'commentmeta':
                         $columns = array(
                              (object) array (
                                   'Type' => 'text',
                                   'Field' => 'meta_value'
                              )
                         );
                    break;
                    case $wpdb->prefix . 'comments':
                         $columns = array(
                              (object) array (
                                   'Type' => 'text',
                                   'Field' => 'comment_content'
                              ),
                              (object) array (
                                   'Type' => 'text',
                                   'Field' => 'comment_author'
                              ),
                              (object) array (
                                   'Type' => 'text',
                                   'Field' => 'comment_author_url'
                              )
                         );
                    break;
                    case $wpdb->prefix . 'links':
                         $columns = array(
                              (object) array (
                                   'Type' => 'text',
                                   'Field' => 'link_url'
                              ),
                              (object) array (
                                   'Type' => 'text',
                                   'Field' => 'link_name'
                              ),
                              (object) array (
                                   'Type' => 'text',
                                   'Field' => 'link_description'
                              )
                         );
                    break;
                    case $wpdb->prefix . 'options':
                         $columns = array(
                              (object) array (
                                   'Type' => 'text',
                                   'Field' => 'option_value'
                              )
                         );
                    break;
                    case $wpdb->prefix . 'postmeta':
                    case $wpdb->prefix . 'termmeta':
                    case $wpdb->prefix . 'usermeta':
                         $columns = array(
                              (object) array (
                                   'Type' => 'text',
                                   'Field' => 'meta_value'
                              )
                         );
                    break;
                    case $wpdb->prefix . 'posts':
                         $columns = array(
                              (object) array (
                                   'Type' => 'text',
                                   'Field' => 'post_content'
                              ),
                              (object) array (
                                   'Type' => 'text',
                                   'Field' => 'post_title'
                              ),
                              (object) array (
                                   'Type' => 'text',
                                   'Field' => 'post_excerpt'
                              ),
                              (object) array (
                                   'Type' => 'text',
                                   'Field' => 'post_name'
                              )
                         );
                    break;
                    case $wpdb->prefix . 'terms':
                         $columns = array(
                              (object) array (
                                   'Type' => 'text',
                                   'Field' => 'name'
                              ),
                              (object) array (
                                   'Type' => 'text',
                                   'Field' => 'slug'
                              )
                         );
                    break;
                    case $wpdb->prefix . 'term_relationships':
                         $columns = array(
                              (object) array (
                                   'Type' => 'int',
                                   'Field' => 'object_id'
                              )
                         );
                    break;
                    case $wpdb->prefix . 'term_taxonomy':
                         $columns = array(
                              (object) array (
                                   'Type' => 'text',
                                   'Field' => 'taxonomy'
                              ),
                              (object) array (
                                   'Type' => 'text',
                                   'Field' => 'description'
                              )
                         );
                    break;
                    case $wpdb->prefix . 'users':
                         $columns = array(
                              (object) array (
                                   'Type' => 'text',
                                   'Field' => 'user_nicename'
                              ),
                              (object) array (
                                   'Type' => 'text',
                                   'Field' => 'user_url'
                              ),
                              (object) array (
                                   'Type' => 'text',
                                   'Field' => 'display_name'
                              )
                         );
                    break;
                    default:
                         //Get all columns name from table
                         $columns = $wpdb->get_results('DESCRIBE '. $table);
                    break;
               }

               if (count($columns) == 0)
                    scws_ajax_die( 0, __('Error! We could not read data from your DB.', 'sensitive-chinese') );

               //Get all expected words
               $words = scws_get_words( true );

               //This counter whill check if have any text column
               $textColumns = 0;
               $results = 0;
               $columnResult = '';
               $foundTotal = array();

               foreach ($columns as $column) {
                    set_time_limit(60);
                    
                    //skip not text columns
                    if ( strpos($column->Type, 'text') !== false || strpos($column->Type, 'char') !== false || strpos($column->Type, 'blob') !== false || strpos($column->Type, 'binary') !== false ) {
                         $textColumns++;

                         //Prepare the select row
                         $sql = 'SELECT '. $column->Field .' FROM '. $table . ' WHERE ';
                         $sql .= str_replace('%%%', $column->Field, $words);
                         $search = $wpdb->get_results($sql, ARRAY_A);

                         //If found check singularity
                         if (!empty($search)) {

                              //Detect which words present
                              foreach ($search as $s) {
                                   $found = scws_search_words_in_text( null, $s );

                                   //Create a list
                                   if (count($found) > 0) {
                                        //Increase the counter
                                        $results += count($found);
                                        //Save for Later
                                        foreach ($found as $f) {
                                             $foundTotal[$f[0]] += $f[1];
                                        }
                                   }
                              }
                         }
                    }

               }

               if (count($foundTotal) > 0) {
                    foreach ($foundTotal as $key => $value) {
                         $columnResult .= '<li>'. $key .' <strong>('. $value .')</strong></li>';
                    }
               }

               $step++;
               $return[] = $step;
               $return[] = 0;
               $return[] = wp_create_nonce( 'scws_db_scan_nonce_'.$step );
               if ($textColumns == 0)
                    $return[] = __('No text columns on table', 'sensitive-chinese') . ' '. $table . '<br />';
               else {
                    if (count($foundTotal) == 0)
                         $return[] = __('No sensitive word found on table', 'sensitive-chinese') . ' '. $table . '<br />';
                    else
                         $return[] = count($foundTotal) . ' ' . __('sensitive words found on table', 'sensitive-chinese') . ' '. $table . ':<br />' . $columnResult;
               }

               scws_ajax_die($step, $return);

          break;
     }
     
}



/*
 * Standarize the die function
 */
function scws_ajax_die( $number, $msg, $glue = '|||' ) {
     echo $number;
     echo $glue;
     if (is_array($msg))
          echo implode($glue, $msg);
     else
          echo $msg;
     die();
}