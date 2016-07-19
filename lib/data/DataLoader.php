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
	 * load a version specified dataset
	 * usually contains a base json with changes per version to overwrite
	 *
	 * @access public
	 * @param  string dataset | the name of the data set to load
	 * @param  integer baseBuild | the base build to load the data for
	 * @return array with data from the requested data set
	 */
	public static function loadDataset($dataset, $baseBuild) {
		$dir = __DIR__.DIRECTORY_SEPARATOR.$dataset.DIRECTORY_SEPARATOR;
		$ret = self::loadFile("{$dir}base.json");

		foreach (glob("$dir*") as $avaibleVersion) {
			$filesAvaible[] = str_replace(".json", "", basename($avaibleVersion));
		}
		sort($filesAvaible);

		foreach ($filesAvaible as $version) {
			if($baseBuild <= $version) {
				foreach (self::loadFile("{$dir}{$version}.json") as $key => $value) {
					$ret[$key] = $value;
				}
			}
		}

		return $ret;
	}

	/**
	 * read a specified json file and return it's contents as an array
	 *
	 * @access public
	 * @param  string file | the name of the file to load
	 * @return array with data from the requested data file
	 */
	private static function loadFile($file) {
		if(file_exists($file)) {
			$content = file_get_contents($file);
			return json_decode($content, true);
		} else {
			return null;
		}
	}

}