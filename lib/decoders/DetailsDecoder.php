<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the DetailsDecoder decoder class
 */

namespace HIS5\lib\Sc2repParser\decoders;

use HIS5\lib\Sc2repParser\utils as utils;

/**
 * The DetailsDecoder class is used to decode the replay.details subfile in a replay file
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\decoders
 */
class DetailsDecoder extends BitwiseDecoderBase {

	/**
	 * actually decode the details file
	 *
	 * @access protected
	 */
	protected function doDecode() {
		$detailsData = $this->parseSerializedData();
		$details = [
			"mapName" => $detailsData[1],
			"difficulty" => $detailsData[2],
			"thumbnail" => $detailsData[3],
			"isBlizzardMap" => $detailsData[4],
			"fileTime" => $detailsData[5],
			"utcAdjustment" => $detailsData[6],
			"description" => $detailsData[7],
			"imageFilePath" => $detailsData[8],
			"mapFileName" => $detailsData[9],
			"cacheHandles" => $detailsData[10],
			"miniSave" => $detailsData[11],
			"gameSpeed" => $detailsData[12],
			"defaultDifficulty" => $detailsData[13]
			//forget the mod paths / campaign index numbers, they don't matter
		];

		foreach ($details["cacheHandles"] as $i => $handle) {
			$details["cacheHandles"][$i] = $this->parseCacheHandle($handle);
		}

		$mapCache = end($details["cacheHandles"]);
		$this->replay->mapHash = $mapCache["hash"];
		$this->replay->mapUrl = $mapCache["url"];

		if($this->replay->baseBuild >= 26490) {
			$details["restartAsTransitionMap"] = $detailsData[16];
		}

		foreach ($detailsData[0] as $pl) {
			$pl = $this->orderPlayerData($pl);
			if($pl["control"] == 2) {
				//is a player and can be used to figure out the region
				$this->replay->region = utils\gatewayLookup($pl["bnet"]["region"]);
			}

			$details["players"][] = $pl;
		}

		$this->replay->rawdata["details"] = $details;
		$this->replay->mapName = $details["mapName"];
		$this->replay->ntTimestamp = $details["fileTime"];

		// The utc_adjustment is either the adjusted windows timestamp OR
		// the value required to get the adjusted timestamp. We know the upper
		// limit for any adjustment number so use that to distinguish between the two cases.
		$maxAdjustment = pow(10, 7) * 60 * 60 * 24;
		if($details["utcAdjustment"] < $maxAdjustment) {
			$this->replay->timezone = $details["utcAdjustment"] / pow(10, 7) * 60 * 60;
		} else {
			$this->replay->timezone = ($details["utcAdjustment"] - $details["fileTime"]) / pow(10, 7) * 60 * 60;
		}

		// This windows timestamp measures the number of 100 nanosecond periods since
		// January 1st, 1601.
		$this->replay->endTimestamp = intval($details["fileTime"] / 10000000 - 11644473600);

		$this->replay->reallength = utils\framesToRealTime($this->replay->frames, $this->replay->gamespeed);
		$this->replay->startTimestamp = $this->replay->endTimestamp - $this->replay->reallength;
	}

	/**
	 * helper function indexing a player array with matching keys
	 *
	 * @access protected
	 * @param  array pl | array with the raw player data from the decoding process
	 * @return array with ordered keys indexing what data is what
	 */
	private function orderPlayerData($pl) {
		$ret = [
			"name" => $pl[0],
			"bnet" => [
				"region" => $pl[1][0],
				"programId" => $pl[1][1],
				"subregion" => $pl[1][2],
				"name" => isset($pl[1][3]) ? $pl[1][3] : false,
				"uid" => $pl[1][4]
			],
			"race" => $pl[2],
			"color" => [
				"a" => $pl[3][0],
				"r" => $pl[3][1],
				"g" => $pl[3][2],
				"b" => $pl[3][3],
			],
			"control" => $pl[4],
			"team" => $pl[5],
			"handicap" => $pl[6],
			"observe" => $pl[7],
			"result" => $pl[8]
		];

		if($this->replay->baseBuild >= 24764) {
			$ret["workingSetSlot"] = $pl[9];
		}

		//only avaible with heroes replays
		if($this->replay->baseBuild >= 34784 && isset($pl[10])) {
			$ret["hero"] = $pl[10];
		}

		return $ret;
	}

}