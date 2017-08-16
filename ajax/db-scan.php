<?php

/*
 * This function executed the search in the DB
 */
add_action( 'wp_ajax_scws_db_scan', 'scws_db_scan_ajax' );
function scws_db_scan_ajax() {

     //Verify Step
     if ( empty( $_POST['step'] ) )
          scws_ajax_die( 0, __('Error! Please try again later', 'sensitive-chinese'), 'div' );

     $step = intval( $_POST['step'] );

     //Verify Nonce
     if (! wp_verify_nonce( $_POST['scws_db_scan_nonce'], 'scws_db_scan_nonce_'. $step ) )
          scws_ajax_die( 0, __('Error! Please try again later', 'sensitive-chinese'), 'div' );

     //Verify Step
     if ( empty($_POST['step']) )
          scws_ajax_die( 0, __('Error! Please try again later', 'sensitive-chinese'), 'div' );

     global $wpdb;
     $return = array();

     if ( empty( $_POST['additional'] ) )
          scws_ajax_die( 0, __('Error! Please try again later', 'sensitive-chinese'), 'div' );

     //Already Know Cases. Saving SQL Resources
     $table = $_POST['additional'];
     $columns = scws_db_know_tables($table);
     $key = scws_db_know_tables($table, true);

     if (count($columns) == 0)
          scws_ajax_die( 0, __('Error! We could not read data from your DB.', 'sensitive-chinese'), 'div' );

     //Get all expected words
     $words = scws_get_words( true );

     //This counter whill check if have any text column
     $textColumns = 0;
     $columnResult = '';
     $result = array();
     $foundTotal = 0;

     //Search in each column
     foreach ($columns as $column) {
          set_time_limit(60);
          
          //skip not text columns
          if ( strpos($column->Type, 'text') !== false || strpos($column->Type, 'char') !== false || strpos($column->Type, 'blob') !== false || strpos($column->Type, 'binary') !== false ) {
               $textColumns++;

               //Prepare the select row
               if ($key !== 0)
                    $sql = 'SELECT '. $key .', '. $column->Field .' FROM '. $table . ' WHERE ';
               else
                    $sql = 'SELECT '. $column->Field .' FROM '. $table . ' WHERE ';
               
               $sql .= str_replace('%%%', $column->Field, $words);
               $search = $wpdb->get_results($sql, ARRAY_N);
               
               //Found the word with DB Regex. Let's confirm with PHP Regex
               if (!empty($search)) {

                    //Cycle through each result from DB
                    foreach ($search as $row) {
                         
                         if ($key !== 0) {
                              $rowKey = $row[0];
                              $rowText = $row[1];
                         } else {
                              $rowKey = 0;
                              $rowText = $row[0];
                         }

                         //Search in the result row for PHP Regex
                         $found = scws_search_words_in_text( null, $rowText );

                         //PHP Regex confirmed. Let's add to our list
                         if (count($found) > 0) {

                              //Cycle through each found word in the row
                              foreach ($found as $f) {

                                   $wordFound = $f[0];
                                   $wordFoundAmount = $f[1];
                                   $foundTotal += $wordFoundAmount;
                                   $string = '';
                                   $rowText = htmlspecialchars($rowText);

                                   //If row too big, lets cut it
                                   if (strlen($rowText) > 100) {

                                        //Get the regex for the word
                                        $regex = scws_get_regex($wordFound, false);

                                        //Get the point where it is
                                        preg_match_all( $regex, $rowText, $matches, PREG_OFFSET_CAPTURE);
                                        
                                        //Replace the word for the bold version
                                        $rowText = preg_replace( $regex, '<strong>'. $wordFound .'</strong>', $rowText );
                                        
                                        foreach($matches[0] as $match) {
                                             $offset = $match[1];

                                             //Cut the string
                                             $string .= '[...] '. substr($rowText, $offset-50, 100) . ' [...] ';
                                        }
                                        
                                   } else {
                                        $string .= '<strong>'. $rowText . '</strong>';
                                   }

                                   /*
                                   * $return = array( TABLE_NAME, COLUMN_NAME, WORD_FOUND, AMOUNT_FOUND, STRING, KEY )
                                   */
                                   $result[] = array( $table, $column->Field, $wordFound, $wordFoundAmount, $string, $rowKey );
                                   
                              }
                              
                         }
                    }
               }
          }

     }

     if (count($result) > 0) {
          foreach($result as $res) {
               if ($key !== 0) {
                    $columnResult .= '<li>'. $res[2] .' <strong>('. $res[3] .')</strong> 
                         <div class="edit" data-table="'. $res[0] .'" data-column="'. $res[1] .'" data-word="'. $res[2] .'" data-key="'. $res[5] .'">
                              <div class="text-block"><i>ID: '. $res[5] .'</i><i>'. $res[1] .'</i>'. $res[4] .'</div>
                              <div class="text-change">Change "'. $res[2] .'" to <input type="text"></input><button>'. __('Change', 'sensitive-chinese') .'</button></div>
                         </div>
                    </li>';
               } else {
                    $columnResult .= '<li>'. $res[2] .' <strong>('. $res[3] .')</strong> 
                         <div class="edit" data-table="'. $res[0] .'" data-column="'. $res[1] .'" data-word="'. $res[2] .'" data-key="'. $res[5] .'">
                              <div class="text-block"><i>'. $res[1] .'</i>'. $res[4] .'</div>
                              <div class="text-change"><i>'. __('This Table has no Primary Key. You will need to edit it manually', 'sensitive-chinese') . '</i></div>
                         </div>
                    </li>';
               }
          }
     }


     $step++;
     $return[] = $step;
     $return[] = 0;
     $return[] = wp_create_nonce( 'scws_db_scan_nonce_'.$step );
     if ($textColumns == 0)
          $return[] = scws_wrap_element( __('No text columns on table', 'sensitive-chinese') . ' '. $table, 'div');
     else {
          if ($foundTotal == 0)
               $return[] = scws_wrap_element( __('No sensitive word found on table', 'sensitive-chinese') . ' '. $table, 'div');
          else
               $return[] = scws_wrap_element( $foundTotal . ' ' . __('sensitive words found on table', 'sensitive-chinese') . ' '. $table . ':<br />' . $columnResult, 'div' );
     }

     scws_ajax_die($step, $return, 'div');

     
}



/*
 * This function executed the replace in the DB
 */
add_action( 'wp_ajax_scws_db_replace_ajax', 'scws_db_replace_ajax' );
function scws_db_replace_ajax() {

     global $wpdb;

     die('0');

     if ( ! ( isset($_POST['keyword']) && isset($_POST['table']) && isset($_POST['replace']) ) )
          die('0');

     if ( ( empty($_POST['keyword']) || empty($_POST['table']) || empty($_POST['replace']) ) )
          die('1');
     
     //Already Know Cases. Saving SQL Resources
     $columns = scws_db_know_tables($_POST['table']);

     if (count($columns) == 0)
          die('2');

     //Check if keyword in on our list
     $words = scws_get_words( false );
     if (!in_array($_POST['keyword'], $words))
          die('3');

     //This counter whill check if have any text column
     $changes = 0;
     foreach ($columns as $column) {
          set_time_limit(60);
          
          //skip not text columns
          if ( strpos($column->Type, 'text') !== false || strpos($column->Type, 'char') !== false || strpos($column->Type, 'blob') !== false || strpos($column->Type, 'binary') !== false ) {

               //Prepare the update row
               $sql = 'UPDATE %s SET %s = trim(REPLACE(concat(" ",%s," "), %s, %s))';

               $search = $wpdb->get_results( $wpdb->prepare($sql, $_POST['table'], $column->Field, $column->Field, " " . $_POST['keyword'] . " ", " ". $_POST['replace'] . " ") );
               var_dump( $wpdb->prepare($sql, $_POST['table'], $column->Field, $column->Field, " " . $_POST['keyword'] . " ", " ". $_POST['replace'] . " "));
               var_dump($search);
               //Check if any replace was done
               if (!empty($search)) {

                    $changes += $wpdb->num_rows;

               }
          }

     }

     if ($changes == 0)
          die('4');

     die($changes .' replaces done on table '. $_POST['table'] );

     
}


/*
 * Retrieve the columns for search on a table
 */
function scws_db_know_tables($table, $pk = false) {

     global $wpdb;
     
     switch ($table) {
          case $wpdb->prefix . 'commentmeta':
               $columns = array(
                    (object) array (
                         'Type' => 'text',
                         'Field' => 'meta_value'
                    )
               );
               $key = 'meta_id';
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
               $key = 'comment_ID';
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
               $key = 'link_id';
          break;
          case $wpdb->prefix . 'options':
               $columns = array(
                    (object) array (
                         'Type' => 'text',
                         'Field' => 'option_value'
                    )
               );
               $key = 'option_id';
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
               $key = 'meta_id';
               if ($table == $wpdb->prefix . 'usermeta')
                    $key = 'umeta_id';
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
               $key = 'ID';
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
               $key = 'term_id';
          break;
          case $wpdb->prefix . 'term_relationships':
               $columns = array(
                    (object) array (
                         'Type' => 'int',
                         'Field' => 'object_id'
                    )
               );
               $key = 'object_id';
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
               $key = 'term_taxonomy_id';
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
               $key = 'ID';
          break;
          default:
               if ($pk === false) {
                    //Get all columns name from table
                    $columns = $wpdb->get_results('DESCRIBE '. $table);
               } else {
                    //Get Key from DB
                    $columns = $wpdb->get_results('SHOW INDEX FROM '. $table . ' WHERE Key_name = "PRIMARY"');
                    if (isset($columns[0]->Column_name))
                         $key = $columns[0]->Column_name;
                    else
                         $key = 0;
               }
          break;
     }

     if ($pk !== false)
          return $key;
     
     return $columns;

}



/*
 * Standarize the die function
 */
function scws_ajax_die( $number, $msg, $wrap = '', $glue = '|||' ) {

     echo $number;
     echo $glue;

     if (is_array($msg)) {
          
          echo implode($glue, $msg);

     } else {

          if ($wrap != '')
               echo scws_wrap_element($msg, $wrap);
          else
               echo $msg;

     }

     die();
}
function scws_wrap_element( $element, $wrap = '') {
     return '<'. $wrap .'>' . $element . '</'. $wrap .'>';
}