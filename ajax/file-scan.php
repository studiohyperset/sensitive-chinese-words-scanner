<?php

/*
 * This function executed the search in the DB
 */
add_action( 'wp_ajax_scws_file_scan', 'scws_file_scan' );
function scws_file_scan() {

     //Verify Nonce
     if (! wp_verify_nonce( $_POST['scws_file_scan_nonce'], 'scws_file_scan_nonce' ) )
          scws_ajax_die( '', __('Error #0! Please try again later', 'sensitive-chinese'), 'div', '' );
          
     if ( empty( $_POST['file_look'] ) )
          scws_ajax_die( '', __('Error #1! Please try again later', 'sensitive-chinese'), 'div', '' );

     if ( empty( $_POST['nextpiece'] ) )
          scws_ajax_die( '', __('Error #2! Please try again later', 'sensitive-chinese'), 'div', '' );
          
     if ( empty( $_POST['totalpieces'] ) )
          scws_ajax_die( '', __('Error #3! Please try again later', 'sensitive-chinese'), 'div', '' );
     
     $pieces = intval($_POST['totalpieces']);
     $next = intval($_POST['nextpiece']);

     if ( $pieces == 0 || $next == 0 )
          scws_ajax_die( '', __('Error #4! Please try again later', 'sensitive-chinese'), 'div', '' );

     if ( $pieces < $next ) {
          echo '0000';
          die();
     }
          

     $file = explode('|||', $_POST['file_look']);
     
     $type = $file[0];
     $name = $file[1];
     unset($file);

     if ($type != 'T' && $type != 'P')
          scws_ajax_die( '', __('Error #6! Please try again later', 'sensitive-chinese'), 'div', '' );

     //Check if we have this folder files saved
     if ( ( $files = get_transient( '_scws_files_'. $name ) ) === false ) {
          
          //Get the folder path to look for
          if ($type == 'T')
               $path = get_theme_root( $name ) . '/' . $name .'/';
          else {
               $pluginFolder = explode('/', $name);
               if (count($pluginFolder) > 1)
                    $path = str_replace('sensitive-chinese-words-scanner\ajax\file-scan.php', $pluginFolder[0] . '/', __FILE__);
               else
                    $path = str_replace('sensitive-chinese-words-scanner\ajax\file-scan.php', $name, __FILE__);
          }

          //Check if single file
          if ( substr($path, -1) != '/')
               $files = array( $path );
          else {

               $files = array();
               $folderFiles = array_slice(scandir($path), 2);

               $finisehd = false;
               $i = 0;
               $allowed = scws_get_file_types();
               
               //set_time_limit(30);
               //Get all files from folders and subfolders
               while (count($folderFiles) > 0) {

                    $current = '';

                    //Ignore rel path and git
                    if ( substr($folderFiles[$i], -4) != '.git' ) {
                         
                         $current = $path . $folderFiles[$i];

                         //Check if folder. Then append all files and subfolders 
                         if (is_dir($current)) {
                              $subfolder = array_slice(scandir($current), 2);
                              foreach($subfolder as $sub) {
                                   $folderFiles[] = $folderFiles[$i] . '/' . $sub;
                              }
                         }

                    }

                    //Check if file is proper extension
                    if ( in_array( pathinfo($current, PATHINFO_EXTENSION), $allowed ) )
                         $files[] = $current;

                    unset($folderFiles[$i]);
                    $i++;
               }
          }

          set_transient( '_scws_files_'. $name, $files, 600 );

     }

     //Let's break the pieces
     $total = count($files);
     if ( $pieces > $total ) {
          $pieces = $total;
     }
     if ($next > $pieces) {
          echo '0000';
          die();
     }
     $pieceSize = $total/$pieces;
     $next--;
     $files = array_slice( $files, $next * $pieceSize, $pieceSize  );
     
     //Let's scan each file for sensitive words
     $words = scws_get_words();
     $return = array();
     $remove = strlen($path);
     foreach($files as $file) {
          //set_time_limit(1);
          //Get current file content
          $text = file_get_contents($file);

          //Check if any sensitive word found
          $justFound = scws_search_words_in_text( $words, $text );
          if (!empty($justFound)) {

               //Cycle through them and indicates the string
               foreach ($justFound as $f) {
                    
                    $wordFound = $f[0];
                    $wordFoundAmount = $f[1];
                    $string = scws_feature_word( $text, $wordFound );

                    /*
                    * $return = array( FILE_NAME, WORD_FOUND, AMOUNT_FOUND, STRING )
                    */
                    $filename = $file;
                    if ($type == 'P'){
                         if ( count($pluginFolder) > 1)
                              $filename = substr($file, $remove);
                         else
                              $filename = $name;
                    } else
                         $filename = substr($file, $remove);
                    
                    $return[] = array( $filename, $wordFound, $wordFoundAmount, $string );
                    
               }
          }
     }

     //Lets output the result
     $next++;
     $columnResult = 'Result '. $next . ' of '. $pieces;
     //Starting mount the link to file editor
     $folder = explode('/', $name);

     if (is_multisite())
          $url = 'network_admin_url';
     else
          $url = 'admin_url';
          

     if ($type == 'T')
          $link = $url( 'theme-editor.php?theme='. $name .'&file=');
     else
          $link = $url( 'plugin-editor.php?plugin='. $name .'&file=');

     if (count($return) > 0) {
          foreach($return as $res) {
               //Mount the link to the file editor
               $edit = '';
               if (count($folder) > 1)
                    $edit = $folder[0] . '/' . $res[0];
               else
                    $edit = $res[0];
               
               $columnResult .= '<li>'. $res[1] .' <strong>('. $res[2] .')</strong> 
                    <div class="edit" data-plugin="'. $name .'" data-word="'. $res[1] .'" data-file="'. $res[0] .'">
                         <div class="text-block"><i>'. $res[0] .'</i> '. $res[3] .'</div>
                         <div class="text-change"><a href="'. $link . urlencode($edit) .'" target="_blank">'. __('Edit this file at the WP File Editor.', 'sensitive-chinese') . '</a></div>
                    </div>
               </li>';
          }
     } else {
          scws_ajax_die( '', __('No sensitive words found in this plugin.', 'sensitive-chinese'), 'div', '' );
     }

     scws_ajax_die('', $columnResult, 'div', '');

}