<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the ReplayParser class
 */

namespace HIS5\lib\Sc2repParser;

use Rogiel\MPQ\MPQFile;

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
		$this->decodeFile("replay.attributes.events", decoders\AttributeEventsDecoder::class);
		$this->decodeFile("replay.details", decoders\DetailsDecoder::class);

		$identifyString = $this->replay->region;
		$entities = [];
		$detailsIndex = 0;
		$playerId = 1;
		$initData = $this->replay->rawdata["initdata"];
		$details = $this->replay->rawdata["details"];
		// Assume that the first X map slots starting at 1 are player slots
		// so that we can assign player ids without the map
		foreach ($initData["lobbyState"]["slots"] as $slotId => $slotData) {
			if($slotData["control"] != 2 && $slotData["control"] != 3) {
				//empty slot
				continue;
			}

			$userId = $slotData["userId"];

			if($slotData["observe"] == 0) {
				//player
				if($slotData["control"] == 3) {
					//it's an ai, create artifical initdata for it
					$playerInitData = ["name" => $details["players"][$detailsIndex]["name"]];
				} else {
					$playerInitData = $initData["userInitialData"][$userId];
				}
				$entities[$playerId] = new objects\Player(
					$playerId, //our counting player id
					$slotData, //slot data from replay.initdata
					$playerInitData, // userInitData from replay.initdata (or created here for an ai)
					$details["players"][$detailsIndex], //details data from replay.details
					$this->replay->attributes[$playerId] //the players attributes from replay.attributes.events
				);

				$detailsIndex++;
			} else {
				//observer => has no attribute data and no details data
				$entities[$playerId] = new objects\Observer(
					$playerId, //our counting player id
					$slotData, //slot data from replay.initdata
					$initData["userInitialData"][$userId] // userInitData from replay.initdata 
				);

			}
			//set up the identify string for the hash
			$identifyString .= $entities[$playerId]->name.$entities[$playerId]->bnetId;
			$playerId++;
		}

		$this->replay->identifier = $this->replay->startTimestamp.":".md5($identifyString);

		return [
			"frames" => $this->replay->frames,
			"repHash" => $this->replay->identifier,
			"mapHash" => $this->replay->mapHash,
			"mapUrl" => $this->replay->mapUrl
		];
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


	/**
	 * static method used to compare two replays
	 * neccessary because two computers recording the same match will never have the exact same timestamp on it
	 *
	 * @access public
	 * @param  string hashOne | the replay hash of the first replay
	 * @param  string hashTwo | the replay hash of the second replay
	 * @return boolean determing wheter the two replays are from the same match
	 */
	public static function compare($hashOne, $hashTwo) {
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