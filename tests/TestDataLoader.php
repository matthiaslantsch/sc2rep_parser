<?php
/**
 * This file is part of the holonet sc2 replay parser library
 * (c) Matthias Lantsch
 *
 * class file for the TestDataLoader csv loader class
 *
 * @package holonet sc2 replay parser library
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\Sc2repParser\tests;

use RuntimeException;

/**
 * The TestDataLoader loads csv data from the corresponding directory
 * data for the corresponding decoder classes will be saved in csv files and then lodaded through this class
 *
 * @author  matthias.lantsch
 * @package holonet\Sc2repParser\tests
 */
class TestDataLoader {

	/**
	 * property containing base name for the test replay (usually the version string)
	 *
	 * @access private
	 * @var    string $basename Base name for the test replay file (usually the version string)
	 */
	private $basename;

	/**
	 * constructor method for the test data loader
	 *
	 * @access public
	 * @param  string $basename The base name identifier for the test replay file (usually the version string)
	 * @return void
	 */
	public function __construct(string $basename) {
		$this->basename = $basename;
	}

	/**
	 * actual dataset loader method
	 *
	 * @access public
	 * @param  string $dataset The name of the test dataset requested
	 * @return void
	 */
	public function load($dataset) {
		$filename = __DIR__.DIRECTORY_SEPARATOR."test_replays".DIRECTORY_SEPARATOR.$this->basename.DIRECTORY_SEPARATOR.$dataset.".json";
		if(!file_exists($filename)) {
			throw new RuntimeException("Error loading test data set $dataset from file $filename", 10);
		}

		$data = file_get_contents($filename);
		return json_decode($data, true);
	}

}
