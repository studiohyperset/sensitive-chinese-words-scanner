<?php
/*
 * Output the DB Scan content
 */
function scws_menu_db_scan() {
     ?>
     
     <h1><?php _e('Sensitive Chinese Words Scanner', 'sensitive-chinese'); ?> -  <?php _e('DB Scan', 'sensitive-chinese'); ?></h1>

     <?php $report = get_option( 'scws_db_report', '' ); ?>

     <?php if (empty($report)) : ?>

          <p><?php _e('Go ahead and scan your site DB.', 'sensitive-chinese'); ?></p>

     <?php else : ?>

          <?php echo $report; ?>

     <?php endif; ?>

     <form id="scws_run_db_scan">
          <input type="hidden" name="action" value="scws_db_scan" />
          <input type="hidden" name="step" value="2" />
          <input type="hidden" name="scws_db_scan_nonce" value="<?php echo wp_create_nonce( 'scws_db_scan_nonce_2' ); ?>" />
          <input type="hidden" name="additional" value="" />
          
          <?php
          global $wpdb;
          $rows = $wpdb->get_results('SHOW TABLES LIKE "'. $wpdb->prefix .'%"', OBJECT_K );

          if (count($rows) > 0) {
               echo '<select>';
               echo '<option value="0">All tables</option>';
               foreach ($rows as $key => $value) {
                    echo '<option value="'. $key .'">'. $key .'</option>';
               }
               echo '</select>';
          }
          ?>
          <button><?php _e('Run DB Scan', 'sensitive-chinese'); ?></button>
     </form>

     <div id="result"></div>

     <script type="text/javascript">
     jQuery(document).ready( function($){

          $('#scws_run_db_scan').submit( function(e){
               
               e.preventDefault();

               $('#result').addClass('loading');

               var select = $(this).children('select'),
                    addit = $(this).children('input[name=additional]'),
                    total = select.children('option').length - 1;

               select.attr('disabled', 'disabled');
               
               if (select.val() == '0')
                    addit.val( select.children('option:nth-of-type('+ $('#scws_run_db_scan input[name=step]').val() +')').val() );
               else
                    addit.val( select.val() );

               if ($('#scws_run_db_scan input[name=step]').val() > total) {
                    $('#result').append('<br />Finished!');
                    $('#result').removeClass('loading');
                    select.attr('disabled', false);
               }
               

               //Send the form data
               var data = $(this).serialize();
               $.post( ajaxurl, data, function( result ) {

                    results = result.split( '|||' );

                    //Check if error.
                    if ( results[0] == '0' ) {

                         $('#result').append( results[1] );
                         $('#result').append('<br />Finished!');
                         $('#result').removeClass('loading');
                         select.attr('disabled', false);
                    
                    //Otherwise it should output content and call it again
                    } else {

                         $('#scws_run_db_scan input[name=step]').val( results[1] );

                         $('#scws_run_db_scan input[name=scws_db_scan_nonce]').val( results[3] );

                         $('#result').append( results[4] );

                         if (select.val() == '0')
                              $('#scws_run_db_scan').submit();
                         else {
                              $('#result').removeClass('loading');
                              select.attr('disabled', false);
                         }
                    }

               });

          });

          $(document).on('click', '#result li', function(e){
               e.preventDefault();
               e.stopPropagation();
               $(this).toggleClass('open');
          });

          $(document).on('click', '#result li .edit', function(e){
               e.stopPropagation();
          });

          $(document).on('click', '#result li .edit button', function(e){
               e.stopPropagation();
               e.preventDefault();

               var form = $(this).parent().parent(),
                    replace = form.find('input').val(),
                    word = form.attr('data-word'),
                    key = form.attr('data-key'),
                    keyname = form.attr('data-keyname'),
                    column = form.attr('data-column'),
                    table = form.attr('data-table');
               
               if (replace == '') {
                    alert('Please insert a word for replace.');
                    return;
               }

               if (table == '' || column == '' || key == '' || word == '') {
                    alert('There was a problem. Reload the page and try again.');
                    return;
               }

               form.fadeTo(500,0.3);
               
               //Send the form data
               var data = 'action=scws_db_replace_ajax&replace=' + replace + '&word='+ word +'&key='+ key +'&column='+ column + '&table='+ table + '&keyname='+ keyname;
               if (confirm('Are you sure you want replace "'+ word +'" with "'+ replace +'" on table "'+ table +'"? This action cannot be reverted. Always do a backup from your DB before doing this.')) {
                    $.post( ajaxurl, data, function( result ) {

                         switch (result) {
                              case '0' : 
                              case '1' :
                              case '2' :
                              case '3' :
                              case '4' :
                                   alert('There was a problem. Reload the page and try again.');
                                   break;
                              default:
                                   form.html(result);
                                   break;
                         }

                         form.fadeTo(500,1);

                    });
               } else {
                    form.fadeTo(500,1);
               }
          });

     });
     </script>
     <?php
     
}