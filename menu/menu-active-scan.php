<?php
/*
 * Output the Active Scan content
 */
function scws_menu_active_scan() {
     ?>

     <h1><?php _e('Active Scan Results', 'sensitive-chinese'); ?></h1>

     <h2><?php _e('Options', 'sensitive-chinese'); ?></h2>

     <table>
          <tr>
               <td>
                    <label for="enable_active_scan"><?php _e('Enable Active Scan?', 'sensitive-chinese'); ?></label>
               </td>
               <td>
                    <select name="enable_active_scan" id="enable_active_scan">
                         <?php $option = get_option( 'scws_enable_active_scan', 'no' ); ?>
                         <option value="yes" <?php selected( 'yes', $option, true ); ?>><?php _e('Yes', 'sensitive-chinese'); ?></option>
                         <option value="no" <?php selected( 'no', $option, true ); ?>><?php _e('No', 'sensitive-chinese'); ?></option>
                    </select>
                    <p><em><?php _e('Select "Yes" to actively monitor new page, post, and comment content for sensitive  keywords.', 'sensitive-chinese'); ?></em></p>
               </td>
          </tr>
          <tr>
               <td>
                    <label for="active_scan_warn"><?php _e('Send an email alert?', 'sensitive-chinese'); ?></label>
               </td>
               <td>
                    <select name="active_scan_warn" id="active_scan_warn">
                         <?php $option = get_option( 'scws_active_scan_warn', 'no' ); ?>
                         <option value="yes" <?php selected( 'yes', $option, true ); ?>><?php _e('Yes', 'sensitive-chinese'); ?></option>
                         <option value="no" <?php selected( 'no', $option, true ); ?>><?php _e('No', 'sensitive-chinese'); ?></option> 
                    </select>
                    <input type="email" name="active_scan_warn_email" id="active_scan_warn_email" value="<?php echo get_option( 'scws_active_scan_warn_email', '' ); ?>" placeholder="<?php _e('Email Address', 'sensitive-chinese'); ?>" />
                    <p><em><?php _e('Select "Yes" and enter your name and email address to receive an alert when the plugin detects a new sensitive keyword.', 'sensitive-chinese'); ?></em></p>
               </td>
          </tr>
          <tr>
               <td>
                    <label for="active_scan_autoreplace"><?php _e('Auto replace sensitive words?', 'sensitive-chinese'); ?></label>
               </td>
               <td>
                    <select name="active_scan_autoreplace" id="active_scan_autoreplace">
                         <?php $option = get_option( 'scws_active_scan_autoreplace', 'no' ); ?>
                         <option value="yes" <?php selected( 'yes', $option, true ); ?>><?php _e('Yes', 'sensitive-chinese'); ?></option>
                         <option value="no" <?php selected( 'no', $option, true ); ?>><?php _e('No', 'sensitive-chinese'); ?></option> 
                    </select>
                    <input type="text" name="active_scan_autoreplace_word" id="active_scan_autoreplace_word" value="<?php echo get_option( 'scws_active_scan_autoreplace_word', '' ); ?>" placeholder="" />
                    <p><em><?php _e('When a new detection is catch, the plugin will replace the sensitive word with this word.', 'sensitive-chinese'); ?></em></p>
               </td>
          </tr>
     </table>

     <div id="result"></div>

     <h2><?php _e('Recent Scans', 'sensitive-chinese'); ?></h2>

     <?php $report = get_option( 'scws_active_report', array() ); ?>

     <?php if (empty($report)) : ?>

          <p><?php _e('The plugin hasn\'t identified any sensitive content yet.', 'sensitive-chinese'); ?></p>

     <?php else : ?>

          <?php $report = array_reverse($report); ?>

          <?php foreach ($report as $r) : ?>
               
               <?php echo '<li class="recent-scan">'. $r . '</li>'; ?>
          
          <?php endforeach; ?>

     <?php endif; ?>

     
     <script type="text/javascript">
     jQuery(document).ready( function($){
     
          $('table select').change( function(){
               
               $('#result').addClass('loading');

               //Send the form data
               var data = 'action=scws_save_options&' + $(this).attr('name') + '=' + $(this).val();

               $.post( ajaxurl, data, function( result ) {

                    $('#result').removeClass('loading');
                    if ( result != '0')
                         $('#result').html('Saved!');
                    else
                         $('#result').html('Some error ocurred. Try again later!');

                    setTimeout(function() {
                         $('#result').html('');
                    }, 2000);

               });

          });

          var runningSaveEmail = false;
          $('#active_scan_warn_email').keyup( function(){

               $('#result').addClass('loading');

               //Send the form data
               var data = 'action=scws_save_options&' + $(this).attr('name') + '=' + $(this).val();

               clearTimeout(runningSaveEmail);
               runningSaveEmail = setTimeout(function() {
                    

                    $.post( ajaxurl, data, function( result ) {

                         $('#result').removeClass('loading');
                         if ( result != '0')
                              $('#result').html('Saved!');
                         else
                              $('#result').html('Some error ocurred. Try again later!');

                         setTimeout(function() {
                              $('#result').html('');
                         }, 2000);

                    });
               }, 500);

          });

          var runningSaveReplace = false;
          $('#active_scan_autoreplace_word').keyup( function(){
               
               $('#result').addClass('loading');

               //Send the form data
               var data = 'action=scws_save_options&' + $(this).attr('name') + '=' + $(this).val();

               clearTimeout(runningSaveReplace);
               runningSaveReplace = setTimeout(function() {
                    $.post( ajaxurl, data, function( result ) {

                         $('#result').removeClass('loading');
                         if ( result != '0')
                              $('#result').html('Saved!');
                         else
                              $('#result').html('Some error ocurred. Try again later!');

                         setTimeout(function() {
                              $('#result').html('');
                         }, 2000);

                    });
               }, 500);

          });

     });
     </script>
     <?php
}