<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the DataLoader wrapper class
 */

namespace HIS5\lib\Sc2repParser\data;

/**
 * The DataLoader class is used as a wrapper around the numerous json data files in the same directory
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\data
 */
class DataLoader {

	/**
	 * read a json data file and return it's content
	 *
	 * @access public
	 * @param  string file | the name of the data file to load
	 * @return array with data from the requested data file
	 */
	public static function loadFile($file) {
		$file = __DIR__.DIRECTORY_SEPARATOR."{$file}.json";
		if(file_exists($file)) {
			$content = file_get_contents($file);
			return json_decode($content, true);
		} else {
			return null;
		}
	}

}