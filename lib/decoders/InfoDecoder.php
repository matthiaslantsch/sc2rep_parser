<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the InfoDecoder decoder class
 */

namespace HIS5\lib\Sc2repParser\decoders;

/**
 * The InfoDecoder class is used to decode the replay.info file contained in old replays (replay version 1)
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\decoders
 */
class InfoDecoder extends BitwiseDecoderBase {

	/**
	 * decode the replay.info file contained in the replay
	 *
	 * @access protected
	 */
	protected function doDecode() {
		$numSlots = $this->readUint8();
		while ($numSlots--) {
			$ret["slots"][] = $this->decodePlayerSlot();
		}

		$ret["randomValue"] = $this->readUint32();

		$ret["gameCacheName"] = $this->readAlignedBytes($this->readUint8());

		//we assume the same flags existed back then
		$ret["teamsLocked"] = $this->readBoolean();
		$ret["teamsTogether"] = $this->readBoolean();
		//archon mode??
		$ret["advancedSharedControl"] = $this->readBoolean();
		$ret["randomRaces"] = $this->readBoolean();
		$ret["battlenet"] = $this->readBoolean();
		$ret["amm"] = $this->readBoolean();

		//unknown byte (0x00)
		$this->readAlignedBytes(1);
		$ret["gameSpeed"] = $this->readUint8();
		//11 unknown bytes
		$this->readAlignedBytes(11);

		$ret["map"] = ["filename" => $this->readAlignedBytes($this->readUint8())];

		//686 unknown bytes
		$this->readAlignedBytes(686);

		for ($i=0; $i < 5; $i++) { 
			$ret["cacheHandles"][] = $this->parseCacheHandle();
		}

		//skip all unknown bytes until we find 2 0x00 in a row
		$one = $this->readByte();
		$two = $this->readByte();
		while (!(ord($one) == 0 && ord($two) == 0)) {
			$two = $one;
			$one = $this->readByte();
		}
		//skip over the other three null bytes
		$this->readAlignedBytes(3);

		//skip the map name length bytes, as for maps with multiple words, there's multiple bytes => unreliable
		do {
			$skippedByte = $this->readByte();
		} while (ord($skippedByte) < 30);

		//now we have the first character of the map name in skippedByte
		$mapName = $skippedByte; 
		while (ord($newByte = $this->readByte()) != 0x0) {
			$mapName .= $newByte;
		}

		$ret["map"]["name"] = $mapName;

		$numPlayers = $this->readUint8();
		$colorKeys = ["a", "r", "g", "b"];
		while($numPlayers--) {
			$pl = [
				"name" => $this->readAlignedBytes($this->readUint8()),
				"race" => $this->readAlignedBytes($this->readUint8())
			];
			$color = explode(",", $this->readAlignedBytes($this->readUint8()));

			if(strlen($pl["name"]) < 1) {
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
	}

	/**
	 * small decoder function decoding a player block
	 *
	 * @access protected
	 */
	private function decodePlayerSlot() {
		$nameLen = $this->readUint8();
		$ret = $this->readAlignedBytes($nameLen);
		//padding
		$this->readBytes(5);
		return $ret;
	}

}