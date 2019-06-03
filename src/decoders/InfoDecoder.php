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
		$data = $this->binaryFormatParse("info");

		$this->replay->rawdata["info"] = $data;

		$mapCache = end($data["cacheHandles"]);
		$this->replay->mapHash = $mapCache["hash"];
		$this->replay->mapUrl = $mapCache["url"];
		$this->replay->region = $mapCache["region"];
		$this->replay->mapName = $data["map"]["name"];

		$this->replay->reallength = utils\loopsToRealTime($this->replay->gameloops, $this->replay->gamespeed);
		$this->replay->gamelength = utils\createTimeString($this->replay->reallength);
	}

}
