<?php
/*
 * Output the DB Scan content
 */
function scws_menu_db_scan() {
     ?>
     
     <h1><?php _e('Sensitive Chinese Words Scanner', 'sensitive-chinese'); ?> -  <?php _e('DB Scan', 'sensitive-chinese'); ?></h1>

     <?php $report = get_option( 'scws_db_report', '' ); ?>

     <?php if (empty($report)) : ?>

          <p><?php _e('Go ahead and scan your site DB for the first time.', 'sensitive-chinese'); ?></p>

     <?php else : ?>

          <?php echo $report; ?>

     <?php endif; ?>

     <form id="scws_run_db_scan">
          <input type="hidden" name="action" value="scws_db_scan" />
          <input type="hidden" name="step" value="1" />
          <input type="hidden" name="additional" value="" />
          <input type="hidden" name="scws_db_scan_nonce" value="<?php echo wp_create_nonce( 'scws_db_scan_nonce_1' ); ?>" />

          <button><?php _e('Run DB Scan', 'sensitive-chinese'); ?></button>
     </form>

     <div id="result"></div>

     <script type="text/javascript">
     jQuery(document).ready( function($){

          $('#scws_run_db_scan').submit( function(e){
               
               e.preventDefault();

               //Send the form data
               var data = $(this).serialize();
               $.post( ajaxurl, data, function( result ) {

                    results = result.split( '|||' );

                    //Check the result of Ajax. If 0 it ended
                    if ( results[0] == '0' ) {

                         $('#result').append( results[1] );
                         $('#result').append('<br />Finished!');
                    
                    //Otherwise it should output content and call it again
                    } else {

                         $('#scws_run_db_scan input[name=step]').val( results[1] );

                         if (results[2] != '0')
                              $('#scws_run_db_scan input[name=additional]').val( results[2] );

                         $('#scws_run_db_scan input[name=scws_db_scan_nonce]').val( results[3] );

                         $('#result').append( results[4] );
                         
                         $('#scws_run_db_scan').submit();

                    }

               });

          });

     });
     </script>
     <?php
     scws_get_words();
}