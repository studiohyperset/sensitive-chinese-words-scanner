<?php
/*
 * Output the Active Scan content
 */
function scws_menu_active_scan() {
     ?>

     <h1><?php _e('Sensitive Chinese Words Scanner Options', 'sensitive-chinese'); ?></h1>

     <h2><?php _e('Active Scan', 'sensitive-chinese'); ?></h2>

     <?php $report = get_option( 'scws_active_report', '' ); ?>

     <?php if (empty($report)) : ?>

          <p><?php _e('No new content reviewed yet.', 'sensitive-chinese'); ?></p>

     <?php else : ?>

          <?php echo $report; ?>

     <?php endif; ?>

     <?php
}