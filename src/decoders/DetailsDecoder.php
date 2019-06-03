<?php
/**
 * This file is part of the holonet sc2 replay parser library
 * (c) Matthias Lantsch
 *
 * class file for the DetailsDecoder decoder class
 *
 * @package holonet sc2 replay parser library
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\Sc2repParser\decoders;

use holonet\Sc2repParser\utils as utils;
use holonet\Sc2repParser\objects\Sc2CacheHandle;

/**
* The DetailsDecoder class is used to decode the replay.details subfile in a replay file
 *
 * @author  matthias.lantsch
 * @package holonet\Sc2repParser\decoders
 */
class DetailsDecoder extends DecoderBase {

	/**
	 * actually decode the details file
	 * saves the information in the contained replay object
	 *
	 * @access protected
	 * @return void
	 */
	protected function doDecode() {
		$details = $this->binaryFormatParse("details");

		die(Var_dump($details["cacheHandles"]));
		foreach ($details["cacheHandles"] as $i => $handle) {
			$details["cacheHandles"][$i] = new Sc2CacheHandle($handle);
		}

		$mapCache = end($details["cacheHandles"]);
		$this->replay->mapHash = $mapCache->hash;
		$this->replay->mapUrl = $mapCache->url;

		foreach ($details["playerList"] as $pl) {
			if($pl["control"] == 2) {
				//is a player and can be used to figure out the region
				$this->replay->region = utils\gatewayLookup($pl["toon"]["region"]);
			}

			$details["players"][] = $pl;
		}

		$this->replay->rawdata["details"] = $details;
		$this->replay->mapName = $details["mapFileName"];
		$this->replay->ntTimestamp = $details["timeUTC"];

		// The utc_adjustment is either the adjusted windows timestamp OR
		// the value required to get the adjusted timestamp. We know the upper
		// limit for any adjustment number so use that to distinguish between the two cases.
		$maxAdjustment = pow(10, 7) * 60 * 60 * 24;
		if($details["timeLocalOffset"] < $maxAdjustment) {
			$this->replay->timezone = $details["timeLocalOffset"] / pow(10, 7) * 60 * 60;
		} else {
			$this->replay->timezone = ($details["timeLocalOffset"] - $details["timeUTC"]) / pow(10, 7) * 60 * 60;
		}

		// This windows timestamp measures the number of 100 nanosecond periods since
		// January 1st, 1601.
		$this->replay->endTimestamp = intval($details["timeUTC"] / 10000000 - 11644473600);

		$this->replay->reallength = utils\loopsToRealTime($this->replay->gameloops, $this->replay->gamespeed);
		$this->replay->gamelength = utils\createTimeString($this->replay->reallength);
		$this->replay->startTimestamp = $this->replay->endTimestamp - $this->replay->reallength;
	}

}
