<?php
/*
 * Output the File Scan content
 */
function scws_menu_file_scan() {
     ?>

     <h1><?php _e('Sensitive Chinese Words Scanner Options', 'sensitive-chinese'); ?></h1>
          
     <h2><?php _e('Theme & Plugin Scan', 'sensitive-chinese'); ?></h2>

     <?php $report = get_option( 'scws_file_report', '' ); ?>

     <?php if (empty($report)) : ?>

          <p><?php _e('Go ahead and scan your theme & plugin files for the first time.', 'sensitive-chinese'); ?></p>

     <?php else : ?>

          <?php echo $report; ?>

     <?php endif; ?>

     <button id="scws_run_file_scan"><?php _e('Run Theme & Plugin Scan', 'sensitive-chinese'); ?></button>

     <?php
}