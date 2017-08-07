<?php 
/*
**************************************************************************

Plugin Name:  Sensitive Chinese Words Scanner
Plugin URI:   #
Description:  Scan your website for banned sensitive words
Version:      0.0.1
Author:       Studio Hyperset
Author URI:   https://www.studiohyperset.com
Text Domain:  sensitive-chinese

**************************************************************************/

require('menu/menu-ui.php');

require('ajax/db-scan.php');


/*
 * This function returns an array with the chinese words
 */
function scws_get_words( $sql = false ) {

	$words = mb_convert_encoding( file_get_contents( plugin_dir_path( __FILE__ ) . 'assets/data/chinese.csv', FILE_TEXT ) , 'UTF-8', 'GB2312');
	
	$words = explode(PHP_EOL, $words);
	
	if ($sql !== false) {
		$sql = '';
		$i = 0;
		foreach ($words as $word) {
			
			if ($i != 0)
				$sql .= ' OR ';
			else
				$i++;

			$sql .= '%%% LIKE "%'. $word .'%"';
		}

		return $sql;
	} else
		return $words;

}