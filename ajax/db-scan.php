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

               //Get all columns name from table
               $rows = $wpdb->get_results('DESCRIBE '. $table);
               if (count($rows) == 0)
                    scws_ajax_die( 0, __('Error! We could not read data from your DB.', 'sensitive-chinese') );

               //Get all expected words
               $words = scws_get_words( true );

               //This counter whill check if have any text column
               $textColumns = 0;
               $results = 0;

               foreach ($rows as $row) {
                    
                    //skip not text columns
                    if ( strpos($row->Type, 'text') !== false || strpos($row->Type, 'char') !== false || strpos($row->Type, 'blob') !== false || strpos($row->Type, 'binary') !== false ) {
                         $textColumns++;

                         //Prepare the select row
                         $sql = 'SELECT * FROM '. $table . ' WHERE ';
                         $sql .= str_replace('%%%', $row->Field, $words);
                         $search = $wpdb->get_results($sql);
                         if (!empty($search))
                              $results += count($search);
                    }

               }


               $step++;
               $return[] = $step;
               $return[] = 0;
               $return[] = wp_create_nonce( 'scws_db_scan_nonce_'.$step );
               if ($textColumns == 0)
                    $return[] = __('No text columns on table', 'sensitive-chinese') . ' '. $table . '<br />';
               else {
                    if ($results == 0)
                         $return[] = __('No sensitive word found on table', 'sensitive-chinese') . ' '. $table . '<br />';
                    else
                         $return[] = $results . ' ' . __('sensitive words found on table', 'sensitive-chinese') . ' '. $table . '<br />';
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