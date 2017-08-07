<?php
/*
 * Output the DB Scan content
 */
function scws_menu_db_scan() {
     ?>
     
     <h1><?php _e('Sensitive Chinese Words Scanner Options', 'sensitive-chinese'); ?></h1>

     <h2><?php _e('DB Scan', 'sensitive-chinese'); ?></h2>

     <?php $report = get_option( 'scws_db_report', '' ); ?>

     <?php if (empty($report)) : ?>

          <p><?php _e('Go ahead and scan your site DB for the first time.', 'sensitive-chinese'); ?></p>

     <?php else : ?>

          <?php echo $report; ?>

     <?php endif; ?>

     <button id="scws_run_db_scan"><?php _e('Run DB Scan', 'sensitive-chinese'); ?></button>

     <?php
}