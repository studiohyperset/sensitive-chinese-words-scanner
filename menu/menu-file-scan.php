<?php
/*
 * Output the File Scan content
 */
function scws_menu_file_scan() {

    if (scws_menu_check_activation() === false) return;
    ?>

    <h1><?php echo get_option('scws_activation_company', '') . "'s "; ?><?php _e('File Scan', 'sensitive-chinese'); ?></h1>

    <p><?php _e('GFW will scan all the following filetypes: txt, php, js, doc, html & xml.', 'sensitive-chinese'); ?></p>
        
         <p style="margin-right:20px"><?php _e('If you experience timeout issues when trying to scan a large directory, use the "Number of Searches" field to break that search into smaller units. For example, if you entered "2" in that field, GFW would break the scan into two searches. Feel free to use "1" for smaller directories.'); ?></p>
          
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
        <input type="number" value="" style="width:300px" min="1" placeholder="Number of Searches" name="totalpieces" required/>
        <button><?php _e('Run File Scan', 'sensitive-chinese'); ?></button>
    </form>

    <div id="result"></div>

    <script type="text/javascript">
    jQuery(document).ready( function($){

        var currentPiece = 1,
            currentRunning = false;

        $('#scws_run_file_scan').submit( function(e){

            e.preventDefault();
            
            if (currentRunning === true && currentPiece == 1)
                return;
            currentRunning = true;

            $('#result').addClass('loading');

            var select = $(this).children('select'),
                field = $(this).find('input[name="totalpieces"]'),
                data = $(this).serialize(),
                pieces = parseInt(field.val());

            data += '&nextpiece='+ currentPiece;

            select.attr('disabled', 'disabled');
            field.attr('disabled', 'disabled');

            //Send the form data
            $.post( ajaxurl, data, function( result ) {

                if ( result == '0000') {

                        $('#result').append( '<div>Finished search early. It was not necessary to use the number of individual searches you requested.</div>' );
                        $('#result').removeClass('loading');
                        select.attr('disabled', false);
                        field.attr('disabled', false);
                        currentPiece = 1;
                        currentRunning = false;
                        
                } else {

                        $('#result').append( result );
                        $('#result').removeClass('loading');
                        select.attr('disabled', false);
                        field.attr('disabled', false);

                        if (currentPiece == pieces) {
                            currentPiece = 1;
                        } else {
                            currentPiece++;
                            $('#scws_run_file_scan').submit();
                        }
                        currentRunning = false;

                }

            }).fail(function(){
                $('#result').append( '<div>The server timed out. Try entering a larger number in the "Number of Searches" field.</div>' );
                $('#result').removeClass('loading');
                select.attr('disabled', false);
                field.attr('disabled', false);
                currentRunning = false;
            });

        });

        $(document).on('click', '#result li', function(e){
            e.preventDefault();
            e.stopPropagation();
            $(this).toggleClass('open');
        });

        $(document).on('click', '#result li a', function(e){
            e.stopPropagation();
        });

    });
    </script>

    <?php
}