<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * file for small convenience utility functions
 */

namespace HIS5\lib\Sc2repParser\utils;

/**
 * lookup from region id to get a region string (e.g. NA)
 *
 * @param  integer regionId | the id of the region to look up
 * @return short string representing the region (e.g. NA)
 */
function gatewayLookup($regionId) {
	switch ($regionId) {
		case 1:
			return "NA";
		case 2:
			return "EU";
		case 3:
			return "KR";
		case 5:
			return "CN";
		case 6:
			return "SEA";
		default:
			return "Unknown";
	}
}

/**
 * lookup from league number to real league name (e.g. 1 => Bronze)
 *
 * @param  integer league | the number of the league to look up
 * @return short string representing the league (e.g. Silver)
 */
function leagueLookup($league) {
	switch ($league) {
		case 1:
			return "Bronze";
		case 2:
			return "Silver";
		case 3:
			return "Gold";
		case 4:
			return "Platinum";
		case 5:
			return "Diamond";
		case 6:
			return "Master";
		case 7:
			return "Grandmaster";
		default:
			return "None";
	}
}

/**
 * lookup for known localized race names, hardcoded in here
 * if no translation is avaible, we will return the original name
 *
 * @param  string localizedRace | the race name in another language, hopefully there is a hardcode for it
 * @return string with the delocalized race (if possible)
 */
function delocalizeRace($localizedRace) {
	$LOCALIZEDRACES = [
		# enUS
		"Terran" => "Terran",
		"Protoss" => "Protoss",
		"Zerg" => "Zerg",

		# ruRU
		"Терран" => "Terran",
		"Протосс" => "Protoss",
		"Зерг" => "Zerg",

		# koKR
		"테란" => "Terran",
		"프로토스" => "Protoss",
		"저그" => "Zerg",

		# plPL
		"Terranie" => "Terran",
		"Protosi" => "Protoss",
		"Zergi" => "Zerg",

		# zhCH
		"人类" => "Terran",
		"星灵" => "Protoss",
		"异虫" => "Zerg",

		# zhTW
		"人類" => "Terran",
		"神族" => "Protoss",
		"蟲族" => "Zerg",

		# ???
		"Terrano" => "Terran",

		# deDE
		"Terraner" => "Terran",

		# esES - Spanish
		# esMX - Latin American
		# frFR - French - France
		# plPL - Polish Polish
		# ptBR - Brazilian Portuguese
	];

	if(isset($LOCALIZEDRACES[$localizedRace])) {
		return $LOCALIZEDRACES[$localizedRace];
	} else {
		return $localizedRace;
	}
}

/**
 * method calculating the real second count out of a ingame engine loop counter
 *
 * @param  integer loopCount | the number of loops passed
 * @return integer how many real life seconds have passed
 */
function loopsToRealTime($loopCount, $gameSpeed) {
	$GAMESPEEDFACTOR = [
	    "Slower" => 0.6,
	    "Slow" => 0.8,
	    "Normal" => 1.0,
	    "Fast" => 1.2,
	    "Faster" => 1.4
   	];

   	return floor(floor($loopCount / 16) / $GAMESPEEDFACTOR[$gameSpeed]);
}