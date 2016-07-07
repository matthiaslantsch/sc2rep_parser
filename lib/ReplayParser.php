<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the ReplayParser class
 */

namespace HIS5\lib\Sc2repParser;

use Rogiel\MPQ\MPQFile;
use HIS5\lib\Sc2repParser\decoders as decoders;

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
		$header = new utils\StringStream($header);
		$string = "StarCraft II replay";
		//die(var_dump(0x2C));
		/*$v = 0;
		echo "\n";
		foreach (str_split($string) as $c) {
			echo str_pad(decbin(ord($c)), 8, "0", STR_PAD_LEFT)." ";
			$v++;
			if($v % 5 == 0) {
				echo "\n";
			}
		}
		echo "\n\n";
		$i = 75;
		while ($i--) {
			$byte = $header->readBytes(1);
			echo str_pad(decbin(ord($byte)), 8, "0", STR_PAD_LEFT)." ";
			if($i % 5 == 0) {
				echo "\n";
			}
		}
		die("stop");*/
		$decoder = new decoders\HeaderDecoder($header);
		$headerData = $decoder->decode(null); //null since we do not have a replay object yet
		$this->replay = new ressources\Replay($headerData["baseBuild"], $headerData["versionString"], $headerData["frames"], $headerData["expansion"]);
	}

	/**
	 * static interface method to identify a replay, will decode init data and replay details
	 *
	 * @access public
	 * @param  string path | the path to the replay to identify
	 */
	public static function identify($path) {
		$me = new static($path);
		return $me->doIdentify();
	}

	/**
	 * function used to increase the load level of the replay to an identify level
	 * will decode init data and replay details
	 *
	 * @access public
	 */
	public function doIdentify() {
		$this->decodeFile("replay.initdata", decoders\InitdataDecoder::class);
		$this->decodeFile("replay.details", decoders\DetailsDecoder::class);
	}

	/**
	 * private dispatcher method calling a decoder on a certain replay subfile
	 *
	 * @access private
	 * @param  string file | the name of the replay sub file that should be decoded
	 * @param  string decoder | the name of the decoder class to be used to decode the data
	 */
	private function decodeFile($file, $decoder) {
		$data = $this->archive->openStream($file);
		//instantiate the decoder class
		$decoder = new $decoder($data);
		//do the decoding
		$decoder->decode($this->replay);
	}

}