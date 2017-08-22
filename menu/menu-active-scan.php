<?php
/*
 * Output the Active Scan content
 */
function scws_menu_active_scan() {
     ?>

     <h1><?php _e('Active Scan Results', 'sensitive-chinese'); ?></h1>

     <?php $report = get_option( 'scws_active_report', '' ); ?>

     <?php if (empty($report)) : ?>

          <p><?php _e('This plugin hans\'t reviewed any new content yet.', 'sensitive-chinese'); ?></p>

     <?php else : ?>

          <?php echo $report; ?>

     <?php endif; ?>

     <?php
}