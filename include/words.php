<?php
/********************************
 *
 * This file include help functions to work
 * with word search and replace
 *
 ********************************/



/*
 * This function returns an array with the chinese words
 */
function scws_get_words( $sql = false ) {
     
     $words = mb_convert_encoding( file_get_contents( SCWS_PATH . '/assets/data/chinese.csv', FILE_TEXT ) , 'UTF-8', 'GB18030');
     
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
		return '/\b'. preg_quote($word, '/') .'\b/iu';

}



/*
 * Search for $words in $text
 * and returns the amount of words found in format
 * array( $word , $amount )
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



/*
 * This function check a string and feature
 * where the word is at.
 */
function scws_feature_word( $text, $word ) {

	//Remove special chars for output purpouses
	$text = htmlspecialchars($text);

	//Get the regex for the word
	$regex = scws_get_regex($word, false);
	
	//Get the point where it is
	preg_match_all( $regex, $text, $matches, PREG_OFFSET_CAPTURE);
	
	//Replace the word for the bold version
	$text = preg_replace( $regex, '<strong>'. $word .'</strong>', $text );
	
	//Check if text is big enough for a cut
	if (strlen($text) > 100) {
		
		$return = '';
		$i = 0;

		foreach($matches[0] as $match) {
			$offset = $match[1];

			//Increase offset by number of matches.
			//This is necessary due to the insertion of strong tag
			$offset += $i*17;
			$i++;

			//Cut the string
			$return .= '[...] '. substr($text, $offset-50, 100) . ' [...] ';
		}
		
	} else {
		
		$return = $text;

	}

	return $return;

}