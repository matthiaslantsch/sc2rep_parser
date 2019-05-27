<?php
/**
 * This file is part of the holonet sc2 replay parser library
 * (c) Matthias Lantsch
 *
 * class file for the InfoDecoder decoder class
 *
 * @package holonet sc2 replay parser library
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\Sc2repParser\decoders;

use holonet\Sc2repParser\utils as utils;

/**
 * The InfoDecoder class is used to decode the replay.info file contained in old replays (replay version 1)
 *
 * @author  matthias.lantsch
 * @package holonet\Sc2repParser\decoders
 */
class InfoDecoder extends DecoderBase {

	/**
	 * decode the replay.info file contained in the replay
	 * saves the data in the replay object
	 *
	 * @access protected
	 * @return void
	 */
	protected function doDecode() {
		$numSlots = $this->stream->readUint8();
		while ($numSlots--) {
			$ret["slots"][] = $this->decodePlayerSlot();
		}

		$ret["randomValue"] = $this->stream->readUint32();

		$ret["gameCacheName"] = $this->stream->readAlignedBytes(
			$this->stream->readUint8()
		);

		//we assume the same flags existed back then
		$ret["teamsLocked"] = $this->stream->readBoolean();
		$ret["teamsTogether"] = $this->stream->readBoolean();
		//archon mode??
		$ret["advancedSharedControl"] = $this->stream->readBoolean();
		$ret["randomRaces"] = $this->stream->readBoolean();
		$ret["battlenet"] = $this->stream->readBoolean();
		$ret["amm"] = $this->stream->readBoolean();
		if($ret["amm"]) {
			$this->replay->category = "Ladder";
		}

		//unknown byte (0x00)
		$this->stream->readAlignedBytes(1);
		$gamespeedInt = $this->stream->readUint8();
		$speekLookup = array(0 => "Slower", 1 => "Slow", 2 => "Normal", 3 => "Fast", 4 => "Faster");
		$this->replay->gamespeed = $speekLookup[$gamespeedInt];

		//11 unknown bytes
		$this->stream->readAlignedBytes(11);

		$ret["map"] = ["filename" => $this->stream->readAlignedBytes($this->stream->readUint8())];

		//686 unknown bytes
		$this->stream->readAlignedBytes(686);

		for ($i=0; $i < 5; $i++) {
			$ret["cacheHandles"][] = $this->parseCacheHandle();
		}

		//skip all unknown bytes until we find 2 0x00 in a row
		$one = $this->stream->readByte(true);
		$two = $this->stream->readByte(true);
		while (!(ord($one) == 0 && ord($two) == 0)) {
			$two = $one;
			$one = $this->stream->readByte(true);
		}
		//skip over the other three null bytes
		$this->stream->readAlignedBytes(3);

		//skip the map name length bytes, as for maps with multiple words, there's multiple bytes => unreliable
		do {
			$skippedByte = $this->stream->readByte(true);
		} while (ord($skippedByte) < 30);

		//now we have the first character of the map name in skippedByte
		$mapName = $skippedByte;
		while (ord($newByte = $this->stream->readByte(true)) != 0x0) {
			$mapName .= $newByte;
		}

		$ret["map"]["name"] = $mapName;

		$numPlayers = $this->stream->readUint8();
		$colorKeys = ["a", "r", "g", "b"];
		while($numPlayers--) {
			$pl = [
				"name" => $this->stream->readAlignedBytes(
					$this->stream->readUint8()
				),
				"race" => $this->stream->readAlignedBytes(
					$this->stream->readUint8()
				)
			];
			$color = explode(",", $this->stream->readAlignedBytes(
				$this->stream->readUint8()
			));

			if($pl["name"] === "") {
				//empty slot
				continue;
			}

			if(count($color) == 4) {
				$pl["color"] = array_combine($colorKeys, $color);
			}

			$ret["players"][] = $pl;
		}

		$this->replay->rawdata["info"] = $ret;

		$mapCache = end($ret["cacheHandles"]);
		$this->replay->mapHash = $mapCache["hash"];
		$this->replay->mapUrl = $mapCache["url"];
		$this->replay->region = $mapCache["region"];
		$this->replay->mapName = $ret["map"]["name"];

		$this->replay->reallength = utils\loopsToRealTime($this->replay->gameloops, $this->replay->gamespeed);
		$this->replay->gamelength = utils\createTimeString($this->replay->reallength);
	}

	/**
	 * small decoder function decoding a player block
	 *
	 * @access protected
	 * @return string with the read player slot
	 */
	private function decodePlayerSlot() {
		$nameLen = $this->stream->readUint8();
		$ret = $this->stream->readAlignedBytes($nameLen);
		//padding
		$this->stream->readBytes(5);
		return $ret;
	}

}
