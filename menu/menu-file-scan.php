<?php
/*
 * Output the File Scan content
 */
function scws_menu_file_scan() {
     ?>

     <h1><?php _e('GFW Theme and Plugin Scan', 'sensitive-chinese'); ?></h1>

     <p><?php _e('GFW will scan all the following filetypes: txt, php, js, doc, html & xml.', 'sensitive-chinese'); ?></p>
          
     <form id="scws_run_file_scan">
          <input type="hidden" name="action" value="scws_file_scan" />
          <input type="hidden" name="scws_file_scan_nonce" value="<?php echo wp_create_nonce( 'scws_file_scan_nonce' ); ?>" />
          
          <?php
          $options = array();
          $themes = wp_get_themes( array('errors' => null, 'allowed' => null, 'blog_id' => 0) );
          if (count($themes) > 0) {
               foreach ($themes as $key => $value) {
                    $options[] = '<option value="T|||'. $key .'">[Theme] '. $value->display('Name') .'</option>';
               }
          }

          $plugins = get_plugins();
          if (count($plugins) > 0) {
               foreach ($plugins as $key => $value) {
                    $options[] = '<option value="P|||'. $key .'">[Plugin] '. $value['Name'] .'</option>';
               }
          }

          if (count($options) > 0) {
               echo '<select name="file_look">';
               foreach($options as $option) {
                    echo $option;
               }
               echo '</select>';
          }
          ?>
          <button><?php _e('Run File Scan', 'sensitive-chinese'); ?></button>
     </form>

     <div id="result"></div>

     <script type="text/javascript">
     jQuery(document).ready( function($){

          $('#scws_run_file_scan').submit( function(e){
               
               e.preventDefault();

               $('#result').addClass('loading');

               var select = $(this).children('select'),
                    data = $(this).serialize();

               select.attr('disabled', 'disabled');

               //Send the form data
               $.post( ajaxurl, data, function( result ) {

                    $('#result').append( result );
                    $('#result').removeClass('loading');
                    select.attr('disabled', false);

               });

          });

     });
     </script>

     <?php
}