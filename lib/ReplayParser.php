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
		$decoder = new decoders\BitPackedDecoder($header);
		$headerData = $decoder->parseSerializedData();
		$baseBuild = $headerData[1][5];
		$versionString = sprintf(
			"%d.%d.%d.%d", //major.minor.fix.build
			$headerData[1][1], //major version number
			$headerData[1][2],//minor version number
			$headerData[1][3],//fix version number
			$headerData[1][4] //the build
		);
		$frames = $headerData[3];

		$this->replay = new ressources\Replay($baseBuild, $versionString, $frames);
	}

	/**
	 * method used to parse the information contained inside the replay.initdata file:
	 *
	 * @access private
	 */
	private function decodeInitData() {
		$initdata = [];
		$data = $this->archive->openStream('replay.initdata');
		$playerCount = unpack("C", $data->readByte())[1];

		for ($i=0; $i < $playerCount; $i++) {
			//$nameLength = 
			$initdata[$i]["name"] = [
				utf8_decode($data->readBytes($data->readUint8()))
			];

			echo "Player $i";
		}

          /*  user_initial_data=[dict(
                name=data.read_aligned_string(data.read_uint8()),
                clan_tag=data.read_aligned_string(data.read_uint8()) if replay.base_build >= 24764 and data.read_bool() else None,
                clan_logo=DepotFile(data.read_aligned_bytes(40)) if replay.base_build >= 27950 and data.read_bool() else None,
                highest_league=data.read_uint8() if replay.base_build >= 24764 and data.read_bool() else None,
                combined_race_levels=data.read_uint32() if replay.base_build >= 24764 and data.read_bool() else None,
                random_seed=data.read_uint32(),
                race_preference=data.read_uint8() if data.read_bool() else None,
                team_preference=data.read_uint8() if replay.base_build >= 16561 and data.read_bool() else None,
                test_map=data.read_bool(),
                test_auto=data.read_bool(),
                examine=data.read_bool() if replay.base_build >= 21955 else None,
                custom_interface=data.read_bool() if replay.base_build >= 24764 else None,
                test_type=data.read_uint32() if replay.base_build >= 34784 else None,
                observe=data.read_bits(2),
                hero=data.read_aligned_string(data.read_bits(9)) if replay.base_build >= 34784 else None,
                skin=data.read_aligned_string(data.read_bits(9)) if replay.base_build >= 34784 else None,
                mount=data.read_aligned_string(data.read_bits(9)) if replay.base_build >= 34784 else None,
                toon_handle=data.read_aligned_string(data.read_bits(7)) if replay.base_build >= 34784 else None,
            ) for i in range(data.read_bits(5))],*/
		die(var_dump($playerCount));
	}

	/**
	 * function used to increase the load level of the replay to an identify level
	 * will decode init data and replay details
	 *
	 * @access public
	 */
	public function doIdentify() {
		$this->decodeInitData();
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

}