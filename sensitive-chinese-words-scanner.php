<?php 
/*
**************************************************************************

Plugin Name:  Sensitive Chinese Words Scanner
Plugin URI:   #
Description:  Scan your website for banned sensitive words
Version:      0.1.0
Author:       Studio Hyperset
Author URI:   https://www.studiohyperset.com
Text Domain:  sensitive-chinese

**************************************************************************/

define('SCWS_URL', plugins_url('', __FILE__));

require('menu/menu-ui.php');

require('ajax/db-scan.php');


/*
 * This function returns an array with the chinese words
 */
function scws_get_words( $sql = false ) {

	$words = mb_convert_encoding( file_get_contents( plugin_dir_path( __FILE__ ) . 'assets/data/chinese.csv', FILE_TEXT ) , 'UTF-8', 'GB18030');
	
	$words = explode(PHP_EOL, $words);
	
	if ($sql !== false) {
		$sql = '';
		$i = 0;
		foreach ($words as $word) {
			
			if ($i != 0)
				$sql .= ' OR ';
			else
				$i++;

			$sql .= '%%% REGEXP "'. scws_get_regex( $word ) .'"';
		}
		
		return $sql;
	} else
		return $words;

}



/*
 * Standarize the regex search
 */
function scws_get_regex( $word, $sql = true ) {

	if ($sql)
		return '[[:<:]]'. $word .'[[:>:]]';
	else
		return '/\b'. $word .'\b/iu';

}


/*
 * Search for $words in $text
 * and returns the amount of words found
 */
function scws_search_words_in_text( $words = null, $text ) {
	set_time_limit(30);
	
	$return = array();

	//Get the default words
	if (empty($words) || !is_array($words))
		$words = scws_get_words();

	//Check if array
	if (is_array($text)) {

		//Check each value
		foreach( $text as $t ) {

			if ($t == '')
				continue;

			//Cycle through each word
			foreach ($words as $w) {
				if ($w == '')
					continue;

				$found = preg_match_all( scws_get_regex($w, false), $t, $founder );
				if ($found > 0) {
					$return[] = array($w, $found);
				}
			}

		}

	} else {

		//Cycle through each word
		foreach ($words as $w) {
			if ($w == '')
				continue;

			//Search for every word in the text
			$found = preg_match_all( scws_get_regex($w, false), $text, $founder );
			if ($found > 0) {
				$return[] = array($w, $found);
			}
		}

	}

	return $return;
}