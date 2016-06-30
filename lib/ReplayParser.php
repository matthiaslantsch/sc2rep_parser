<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the ReplayParser class
 */

namespace HIS5\lib\Sc2repParser;

/**
 * The ReplayParser class is used as a center piece for the replay parsing process
 * it executes four different routines that reveal different levels of data about the replay:
 *   - header decoding (in the constructor) => game version, protocol
 *   - replay identifying (in identify()) => identify the people that played the replay/the map/timestamps
 *   - events decoding (in decode()) => decodes the additional event files in the replay to create raw event objects
 *   - game engine simulation (in simulate()) => simulates the events in order and calls plugins to extract additional data
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser
 */
class ReplayParser {

	/**
	 * property containing the mpq archive library object
	 *
	 * @access 	private
	 * @var 	MPQFile object | object of the opened mpq archive
	 */
	private $archive;

	/**
	 * property containing the Replay object
	 *
	 * @access 	public
	 * @var 	Replay object | object containing the parsed data
	 */
	public $replay;

	/**
	 * constructor method starting the replay parsing process
	 * will always do the header decoding part (decode the replay header)
	 *
	 * @access public
	 * @param  string path | path to the replay to be parsed
	 */
	public function __construct($path) {
		if(!file_exists($path) || !is_readable($path)) {
			throw new ParserException("The replay file '{$path}' could not be found/read", 10);
		}

		$this->archive = MPQFile::parseFile($path);
		//decode the header file
		$this->decodeHeader();
	}

	/**
	 * method used to parse the information contained inside header of the replay file:
	 *  - game version
	 *  - game frame counter
	 *
	 * @access private
	 */
	private function decodeHeader() {
		$header = $this->archive->getUserData()->getRawContent();
		$decoder = new decoders\BitPackedDecoder($header);
		$headerData = $decoder->parseSerializedData();
		$verMajor = $headerData[1][1];
		$versionString = sprintf(
			"%d.%d.%d.%d", //major.minor.fix.build
			$this->verMajor, //major version number
			$headerData[1][2],//minor version number
			$headerData[1][3],//fix version number
			$headerData[1][4] //the build
		);
		$frames = $headerData[3];

		$this->replay = new ressources\Replay($verMajor, $versionString, $frames);
	}

}