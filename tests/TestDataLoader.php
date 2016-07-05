<?php
/**
 * This file is part of the hdev activerecord library
 * (c) Matthias Lantsch
 *
 * class file for the TestDataLoader csv loader class
 */

namespace HIS5\lib\Sc2repParser\tests;

use HIS5\lib\common as co;
use HIS5\lib\Sc2repParser as parser;

/**
 * The TestDataLoader loads csv data from the corresponding directory
 * data for the corresponding decoder classes will be saved in csv files and then lodaded through this class
 * 
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\tests
 */
class TestDataLoader {

	/**
	 * property containing base name for the test replay (usually the version string)
	 *
	 * @access  private
	 * @var     string basename | base name for the test replay file (usually the version string)
	 */
	private $basename;

	/**
	 * constructor method for the test data loader
	 *
	 * @access public
	 * @param  string basename | the base name identifier for the test replay file (usually the version string)
	 */
	public function __construct($basename) {
		$this->basename = $basename;
	}

	/**
	 * actual dataset loader method
	 *
	 * @access public
	 * @param  string dataset | the name of the test dataset requested
	 */
	public function load($dataset) {
		$filename = __DIR__.DIRECTORY_SEPARATOR.$this->basename.DIRECTORY_SEPARATOR.$dataset."_test.json";
		if(!file_exists($filename)) {
			throw new \Exception("Error loading test data set $dataset from file $filename", 10);
		}

		$data = file_get_contents($filename);
		return json_decode($data, true);
	}

}