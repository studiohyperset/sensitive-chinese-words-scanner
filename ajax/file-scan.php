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

     $file = explode('|||', $_POST['file_look']);
     
     $type = $file[0];
     $name = $file[1];
     unset($file);

     if ($type != 'T' && $type != 'P')
          scws_ajax_die( '', __('Error #2! Please try again later', 'sensitive-chinese'), 'div', '' );

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
          $allowed = array('txt', 'php', 'js', 'doc', 'html', 'xml');
          
          set_time_limit(30);
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
     
     //Let's scan each file for sensitive words
     var_dump($files);
     die();
     foreach($files as $file) {
          set_time_limit(30);

     }
}