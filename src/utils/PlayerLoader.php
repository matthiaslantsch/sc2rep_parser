<?php
/**
 * This file is part of the holonet sc2 replay parser library
 * (c) Matthias Lantsch
 *
 * class file for the PlayerLoader logic class
 *
 * @package holonet sc2 replay parser library
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\Sc2repParser\utils;

use holonet\Sc2repParser\ParserException;
use holonet\Sc2repParser\resources\Replay;
use holonet\Sc2repParser\objects\Player;
use holonet\Sc2repParser\objects\Observer;

/**
* The PlayerLoader class contains logic to create player objects from the replay's raw data
 *
 * @author  matthias.lantsch
 * @package holonet\Sc2repParser\utils
 */
class PlayerLoader {

	/**
	 * public dispatcher method for a central interface for all versions of the replay file
	 * will save the player objects directly in the replay object
	 *
	 * @access public
	 * @param  Replay $replay Replay object containing the raw data and requesting the players
	 * @return void
	 */
	public static function loadPlayers(Replay $replay) {
		if($replay->baseBuild < 15097) {
			//replay version 1
			self::loadVersion1($replay);
		} elseif($replay->baseBuild < 18092) {
			//replay version 2
			self::loadVersion2($replay);
		} else {
			//replay version 3
			self::loadVersion3($replay);
		}

		$teams = [];
		foreach ($replay->entities as $entity) {
			if($entity instanceof Player) {
				$teams[$entity->teamId][] = $entity->playRace[0];
			}
		}

		$gametype = [];
		$matchup = [];
		foreach ($teams as $teamArr) {
			sort($teamArr);
			$gametype[] = count($teamArr);
			$matchup[] = implode("", $teamArr);
		}
		$replay->gametype = implode("v", $gametype);
		sort($matchup);
		$replay->matchup = implode("v", $matchup);
	}

	/**
	 * method loading the players for a replay of version 1
	 * player list is constructed off:
	 *   - replay.info subfile
	 *
	 * @access public
	 * @param  Replay $replay Replay object containing the raw data and requesting the players
	 * @return void
	 */
	public static function loadVersion1(Replay $replay) {
		if(!isset($replay->rawdata["info"])) {
			throw new ParserException("Cannot load players without raw data", 5);
		}

		$infodata = $replay->rawdata["info"];
		$entities = [];
		$peoplestring = "";
		foreach ($infodata["players"] as $pid => $pl) {
			$player = new Player($pid, $pl["name"]);
			$player->playRace = delocalizeRace($pl["race"]);
			//always Medium for real players
			$player->difficulty = "Medium";
			$player->color = $pl["color"];
			$player->region = $replay->region;
			$player->isComputer = false;

			if(!in_array($pl["name"], $infodata["slots"])) {
				//it's a bot
				$player->isComputer = true;
				$difficulties = ["Very Easy", "Easy", "Medium", "Very Hard", "Hard", "Harder", "Elite", "Insane", "Cheater 1 (Vision)", "Cheater 2 (Resources)"];
				foreach ($difficulties as $diff) {
					if(strpos($pl["name"], $diff) !== false) {
						//e.g. Very Easy AI
						$player->difficulty = $diff;
					}
				}
			}

			//set up the identify string for the hash
			$peoplestring .= $player->name.$player->bnetId;
			$entities[$pid] = $player;
		}

		//check for observers
		foreach ($infodata["slots"] as $name) {
			if($name !== "" && strpos($peoplestring, $name) === false) {
				$pid++;
				//it's in the slots, but not the player data => observer
				$observer = new Observer($pid, $name);
				$peoplestring .= $observer->name.$observer->bnetId;
				$entities[$pid] = $observer;
			}
		}

		$replay->peoplestring = $peoplestring;
		$replay->entities = $entities;
	}

	/**
	 * method loading the players for a replay of version 2
	 * player list is constructed off:
	 *   - replay.initdata subfile
	 *   - replay.details subfile
	 *   - replay.attributes.events subfile
	 *
	 * @access public
	 * @param  Replay $replay Replay object containing the raw data and requesting the players
	 * @return void
	 */
	public static function loadVersion2(Replay $replay) {
		if(!isset($replay->rawdata["initdata"]) || !isset($replay->rawdata["details"])) {
			throw new ParserException("Cannot load players without raw data", 5);
		}

		$replay->entities = [];
		$replay->peoplestring = "";
		$playerId = 1;
		$initData = $replay->rawdata["initdata"];
		$details = $replay->rawdata["details"];
		foreach ($initData["userInitialData"] as $data) {
			if($data["name"] === "") {
				//empty slot
				continue;
			}

			$isObserver = true;

			//player
			foreach ($details["players"] as $det) {
				if($det["name"] === $data["name"]) {
					$detailsData = $det;
					$isObserver = false;
					break;
				}
			}

			if(!$isObserver) {
				$replay->entities[$playerId] = new Player($playerId, $data["name"]);

				if($detailsData["result"] == 1) {
					$replay->entities[$playerId]->result = "Win";
				} elseif($detailsData["result"] == 2) {
					$replay->entities[$playerId]->result = "Loss";
				}

				if(isset($replay->attributes[$playerId]["Race"])) {
					$replay->entities[$playerId]->pickRace = $replay->attributes[$playerId]["Race"];
				}

				if(isset($replay->attributes[$playerId]["Difficulty"])) {
					$replay->entities[$playerId]->difficulty = $replay->attributes[$playerId]["Difficulty"];
				}

				$replay->entities[$playerId]->playRace = delocalizeRace($detailsData["race"]);
				$replay->entities[$playerId]->color = $detailsData["color"];

				$replay->entities[$playerId]->region = gatewayLookup($detailsData["bnet"]["region"]);
				$replay->entities[$playerId]->realId = $detailsData["bnet"]["name"];

				$replay->entities[$playerId]->isComputer = ($detailsData["control"] != 2);

				//save all the initdata
				$replay->entities[$playerId]->handicap = $detailsData["handicap"];
				$replay->entities[$playerId]->teamId = $detailsData["teamId"];
			} else {
				//observer => has no attribute data and no details data
				$replay->entities[$playerId] = new Observer($playerId, $data["name"]);
				$replay->entities[$playerId]->realId = $data["name"];
			}

			if($replay->entities[$playerId]->realId === false || $replay->entities[$playerId]->realId === null) {
				$replay->entities[$playerId]->realId = "{$replay->entities[$playerId]->name}#{$replay->entities[$playerId]->bnetId}";
			}

			//set up the identify string for the hash
			$replay->peoplestring .= $replay->entities[$playerId]->realId;
			$playerId++;
		}
	}

	/**
	 * method loading the players for a replay of version 3
	 * player list is constructed off:
	 *   - replay.initdata subfile
	 *   - replay.details subfile
	 *   - replay.attributes.events subfile
	 *
	 * @access public
	 * @param  Replay $replay Replay object containing the raw data and requesting the players
	 * @return void
	 */
	public static function loadVersion3(Replay $replay) {
		if(!isset($replay->rawdata["initdata"]) || !isset($replay->rawdata["details"])) {
			throw new ParserException("Cannot load players without raw data", 5);
		}

		$replay->entities = [];
		$replay->peoplestring = "";
		$detailsIndex = 0;
		$playerId = 1;
		$initData = $replay->rawdata["initdata"];
		$details = $replay->rawdata["details"];
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
				$detailsData = $details["players"][$detailsIndex];
				if($slotData["control"] == 3) {
					//it's an ai, create artifical initdata for it
					$name = $detailsData["name"];
				} else {
					$name = $initData["userInitialData"][$userId]["name"];
				}

				$replay->entities[$playerId] = new Player($playerId, $name);

				if($detailsData["result"] == 1) {
					$replay->entities[$playerId]->result = "Win";
				} elseif($detailsData["result"] == 2) {
					$replay->entities[$playerId]->result = "Loss";
				}

				if(isset($replay->attributes[$playerId]["Race"])) {
					$replay->entities[$playerId]->pickRace = $replay->attributes[$playerId]["Race"];
				}

				if(isset($replay->attributes[$playerId]["Difficulty"])) {
					$replay->entities[$playerId]->difficulty = $replay->attributes[$playerId]["Difficulty"];
				}

				$replay->entities[$playerId]->playRace = delocalizeRace($detailsData["race"]);
				$replay->entities[$playerId]->color = $detailsData["color"];

				$replay->entities[$playerId]->region = gatewayLookup($detailsData["bnet"]["region"]);
				$replay->entities[$playerId]->subregion = $detailsData["bnet"]["subregion"];
				$replay->entities[$playerId]->bnetId = $detailsData["bnet"]["uid"];

				$replay->entities[$playerId]->isComputer = ($slotData["control"] != 2);

				$detailsIndex++;
			} else {
				//observer => has no attribute data and no details data
				$replay->entities[$playerId] = new Observer($playerId, $initData["userInitialData"][$userId]["name"]);

				//observers have no details data, so this fallback is necessary
				if(!isset($slotData["toonHandle"])) {
					$slotData["toonHandle"] = "0-S2-0-0";
				}

				$parts = explode("-", $slotData["toonHandle"]);

				$replay->entities[$playerId]->region = gatewayLookup($parts[0]);
				$replay->entities[$playerId]->subregion = $parts[2];
				$replay->entities[$playerId]->bnetId = $parts[3];

				$replay->entities[$playerId]->isReferee = (isset($slotData["observe"]) && $slotData["observe"] == 2);
			}

			//save all the initdata
			$replay->entities[$playerId]->handicap = $slotData["handicap"];
			$replay->entities[$playerId]->teamId = $slotData["teamId"];

			if(isset($slotData["hero"])) {
				$replay->entities[$playerId]->hero = $slotData["hero"];
				$replay->entities[$playerId]->hero = $slotData["skin"];
				$replay->entities[$playerId]->hero = $slotData["mount"];
			}

			if(isset($slotData["archonLeaderUserId"])) {
				$replay->entities[$playerId]->archonLeaderId = $slotData["archonLeaderUserId"];
			}

			if(isset($slotData["clanTag"])) {
				$replay->entities[$playerId]->clanTag = $slotData["clanTag"];
			}


			if(isset($slotData["combinedRaceLevels"])) {
				$replay->entities[$playerId]->combinedRaceLevels = $slotData["combinedRaceLevels"];
			}

			if(isset($slotData["highestLeague"])) {
				$replay->entities[$playerId]->highestLeague = leagueLookup($slotData["highestLeague"]);
			}

			//set up the identify string for the hash
			$replay->peoplestring .= $replay->entities[$playerId]->name.$replay->entities[$playerId]->bnetId;
			$playerId++;
		}
	}

}
