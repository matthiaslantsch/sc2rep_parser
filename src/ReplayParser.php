<?php
/**
 * This file is part of the holonet sc2 replay parser library
 * (c) Matthias Lantsch
 *
 * Class file for the ReplayParser class
 *
 * @package holonet sc2 replay parser library
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\Sc2repParser;

use MPQArchive;
use holonet\bitstream\StringStream;
use holonet\Sc2repParser\utils\BlockStream;
use holonet\Sc2repParser\resources\Replay;

/**
 * The ReplayParser class is used as a center piece for the replay parsing process
 * it executes four different routines that reveal different levels of data about the replay:
 *   - header decoding (in the constructor) => game version, protocol
 *   - replay identifying (in identify()) => identify the people that played the replay/the map/timestamps
 *   - events decoding (in decode()) => decodes the additional event files in the replay to create raw event objects
 *   - game engine simulation (in simulate()) => simulates the events in order and calls plugins to extract additional data
 *
 * @author  matthias.lantsch
 * @package holonet\Sc2repParser
 */
class ReplayParser {

	/**
	 * property containing the mpq archive library object
	 *
	 * @access private
	 * @var    MPQArchive $archive Object of the opened mpq archive
	 */
	private $archive;

	/**
	 * property containing the Replay object
	 *
	 * @access public
	 * @var    Replay $replay Object containing the parsed data
	 */
	public $replay;

	/**
	 * constructor method starting the replay parsing process
	 * will always do the header decoding part (decode the replay header)
	 *
	 * @access public
	 * @param  string $path Path to the replay to be parsed
	 * @return void
	 */
	public function __construct(string $path) {
		if(!file_exists($path) || !is_readable($path)) {
			throw new ParserException("The replay file '{$path}' could not be found/read", 10);
		}

		$this->archive = new MPQArchive($path);

		//decode the header file
		$this->decodeHeader();
	}

	/**
	 * method used to parse the information contained inside header of the replay file:
	 *  - game version
	 *  - game loop counter
	 *
	 * @access private
	 * @return void
	 */
	private function decodeHeader() {
		$header = new StringStream($this->archive->userData);
		$decoder = new decoders\HeaderDecoder($header);
		$headerData = $decoder->decode(null); //null since we do not have a replay object yet
		$this->replay = new Replay(
			$headerData["baseBuild"],
			$headerData["versionString"],
			$headerData["gameloops"],
			$headerData["expansion"]
		);
	}

	/**
	 * static interface method to identify a replay, will decode init data and replay details
	 *
	 * @access public
	 * @param  string $path The path to the replay to identify
	 * @return array with identify data
	 */
	public static function identify(string $path) {
		$me = new static($path);
		return $me->doIdentify();
	}

	/**
	 * static interface method to parse a replay, will identify it and then parse the game event files
	 *
	 * @access public
	 * @param  string $path The path to the replay to parse
	 * @return Replay object resulting from the decoded replay file
	 */
	public static function decode($path) {
		$me = new static($path);
		return $me->doParse();
	}

	/**
	 * function used to increase the load level of the replay to an identify level
	 * will decode init data and replay details
	 *
	 * @access public
	 * @return array with identify data
	 */
	public function doIdentify() {
		if($this->replay->loadLevel < 2) {
			if($this->replay->baseBuild < 15097) {
				//replay version 1
				$this->decodeFile("replay.info", decoders\InfoDecoder::class);
			} else {
				//replay version 2
				$this->decodeFile("replay.initdata", decoders\InitdataDecoder::class);
				$this->decodeFile("replay.attributes.events", decoders\AttributeEventsDecoder::class);
				$this->decodeFile("replay.details", decoders\DetailsDecoder::class);
			}

			utils\PlayerLoader::loadPlayers($this->replay);

			$identifyString = $this->replay->region.$this->replay->peoplestring;

			//well allow 100 seconds of time in between to make sure we get replays of the same match
			$timestampSubString = isset($this->replay->startTimestamp) ? substr($this->replay->startTimestamp, 0, 8) : "";

			if($this->replay->baseBuild < 16195) {
				$this->replay->identifier = "BETA{$timestampSubString}:".md5($identifyString);
			} else {
				$this->replay->identifier = "{$timestampSubString}:".md5($identifyString);
			}

			$this->replay->loadLevel = 2;
		}

		return [
			"gameloops" => $this->replay->gameloops,
			"repHash" => $this->replay->identifier,
			"mapHash" => $this->replay->mapHash,
			"mapUrl" => $this->replay->mapUrl,
			"version" => $this->replay->version
		];
	}

	/**
	 * function used to increase the load level of the replay to a parsed level
	 * will decode init data and replay details and all event files
	 *
	 * @access public
	 * @return Replay object resulting from the decoded replay file
	 */
	public function doDecode() {
		$this->doIdentify();
		if($this->replay->loadLevel < 3) {
			$this->decodeFile("replay.message.events", decoders\MessageEventsDecoder::class);
			$this->decodeFile("replay.game.events", decoders\GameEventsDecoder::class);

			$this->replay->loadLevel = 3;
		}

		return $this->replay;
	}

	/**
	 * dispatcher method calling a decoder on a certain replay subfile
	 *
	 * @access public
	 * @param  string $file The name of the replay sub file that should be decoded
	 * @param  string $decoder The name of the decoder class to be used to decode the data
	 * @return void
	 */
	public function decodeFile(string $file, string $decoder) {
		if(!in_array($file, array_keys($this->replay->rawdata))) {
			$data = $this->archive->readFile($file);
			$stream = new StringStream($data);
			//instantiate the decoder class
			$decoder = new $decoder($stream);
			//do the decoding
			$decoder->decode($this->replay);
		}
	}

	/**
	 * static method used to compare two replays
	 * neccessary because two computers recording the same match will never have the exact same timestamp on it
	 *
	 * @access public
	 * @param  string $hashOne The replay hash of the first replay
	 * @param  string $hashTwo The replay hash of the second replay
	 * @return boolean determing wheter the two replays are from the same match
	 */
	public static function compare($hashOne, $hashTwo) {
		if(strpos($hashOne, "BETA") === 0 || strpos($hashTwo, "BETA") === 0) {
			return false;
		}

		if(strstr($hashOne, ":") !== strstr($hashTwo, ":")) {
			return false;
		}

		$timestampOne = strstr($hashOne, ":", true);
		$timestampTwo = strstr($hashTwo, ":", true);

		//90 seconds tolerant
		if(abs($timestampOne - $timestampTwo) < 90) {
			return true;
		} else {
			return false;
		}
	}

}
