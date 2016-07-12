<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the InitdataDecoder decoder class
 */

namespace HIS5\lib\Sc2repParser\decoders;

/**
 * The InitdataDecoder class is used to decode the "initdata" file contained within the replay archive
 * information on the format can be found here: https://github.com/GraylinKim/sc2reader/wiki/replay.initData
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\decoders
 */
class InitdataDecoder extends BitwiseDecoderBase {

	/**
	 * actually decode the initdata file
	 * saves decoded data directly in the replay file
	 *
	 * @access protected
	 */
	protected function doDecode() {
		$ret = [];

		$numberPlayers = $this->readBits(5);

		while ($numberPlayers--) {
			$ret["userInitialData"][] = $this->decodePlayerInitData();
		}

		$ret["gameDescription"] = $this->decodeGameDescription();
		$ret["lobbyState"] = $this->decodeLobbyState();

		$this->replay->rawdata["initdata"] = $ret;
		$options = $ret["gameDescription"]["options"];

		$this->replay->amm = $options["amm"];
		$this->replay->ranked = $options["ranked"];
		$this->replay->competitive = $options["competitive"];
		$this->replay->practice = $options["practice"];
		$this->replay->cooperative = $options["cooperative"];
		$this->replay->battlenet = $options["battlenet"];
	}

	/**
	 * decodes the data for one player
	 *
	 * @access private
	 * @return array with init data for one player
	 */
	private function decodePlayerInitData() {
		$ret = ["name" => $this->readAlignedBytes($this->readUint8())];

		if($this->replay->baseBuild >= 24764 && $this->readBoolean()) {
			//supports and has a clan tag
			$ret["clanTag"] = $this->readAlignedBytes($this->readUint8());
		}

		if($this->replay->baseBuild >= 27950 && $this->readBoolean()) {
			//supports and has a clan logo depot file
			/*$ret["clanlogoDepotfile"] = $this->readAlignedBytes(40);*/
			//skip for now
			$this->readAlignedBytes(40);
		}

		if($this->replay->baseBuild >= 24764 && $this->readBoolean()) {
			//supports and has a highest league archieved
			$ret["highestLeague"] = $this->readUint8();
		}

		if($this->replay->baseBuild >= 24764 && $this->readBoolean()) {
			//supports and has a combined race level
			$ret["combinedRaceLevels"] = $this->readUint32();
		}

		$ret["randomSeed"] = $this->readUint32();

		if($this->readBoolean()) {
			$ret["racePreference"] = $this->readUint8();
		}

		if($this->replay->baseBuild >= 16561 && $this->readBoolean()) {
			$ret["teamPreference"] = $this->readUint8();
		}

		$ret["testMap"] = $this->readBoolean();
		$ret["testAuto"] = $this->readBoolean();

		if($this->replay->baseBuild >= 21955) {
			$ret["examine"] = $this->readBoolean();
		}

		if($this->replay->baseBuild >= 24764) {
			$ret["customInterface"] = $this->readBoolean();
		}

		if($this->replay->baseBuild >= 34784) {
			$ret["testType"] = $this->readUint32();
		}

		$ret["observe"] = $this->readBits(2);

		if($this->replay->baseBuild >= 34784) {
			$ret["hero"] = $this->readAlignedBytes($this->readBits(9));
			$ret["skin"] = $this->readAlignedBytes($this->readBits(9));
			$ret["mount"] = $this->readAlignedBytes($this->readBits(9));
			$ret["toonHandle"] = $this->readAlignedBytes($this->readBits(7));
		}

		return $ret;
	}

	/**
	 * decodes game description
	 *
	 * @access private
	 * @return array with game description data
	 */
	private function decodeGameDescription() {
		$ret = [
			"randomValue" => $this->readUint32(),
			"gameCacheName" => $this->readAlignedBytes($this->readBits(10)),
			"options" => $this->decodeGameOptions(),
			"gameSpeed" => $this->readBits(3),
			"gameType" => $this->readBits(3),
			"limits" => [
				"users" => $this->readBits(5),
				"observers" => $this->readBits(5),
				"players" => $this->readBits(5),
				//hardcoded + 1 since the number seems to always be one too small
				"teams" => $this->readBits(4) + 1,
				"colors" => $this->readBits(($this->replay->baseBuild >= 17266 ? 6 : 5)) + ($this->replay->baseBuild >= 17266 ? 0 : 1),
				"races" => $this->readUint8() + 1,
				"controls" => $this->readUint8() + ($this->replay->baseBuild >= 26490 ? 0 : 1)
			],
			"map" => [
				"sizeX" => $this->readUint8(),
				"sizeY" => $this->readUint8(),
				"fileSyncChecksum" => $this->readUint32(),
				"filename" => $this->readAlignedBytes($this->readBits(11)),
				"author" => $this->readAlignedBytes($this->readUint8())
			],
			"modFileSyncChecksum" => $this->readUint32()
		];

		$numberSlots = $this->readBits(5);

		for ($i=0; $i < $numberSlots; $i++) {
			$ret["slotDescriptions"][$i] = [
				"allowedColors" => $this->readBits($this->readBits(6)),
				"allowedRaces" => $this->readBits($this->readUint8()),
				"allowedDifficulty" => $this->readBits($this->readBits(6)),
				"allowedControls" => $this->readBits($this->readUint8()),
				"allowedObserverTypes" => $this->readBits($this->readBits(2))
			];

			if($this->replay->baseBuild >= 23925) {
				$ret["slotDescriptions"][$i]["allowedAIBuilds"] = $this->readBits($this->readBits(($this->replay->baseBuild >= 38749 ? 8 : 7)));
			}
		}

		$ret["defaultDifficulty"] = $this->readBits(6);
		if($this->replay->baseBuild >= 23925) {
			$ret["defaultAIBuild"] = $this->readBits($this->readBits(($this->replay->baseBuild >= 38749 ? 8 : 7)));
		}

		$numberCacheHandles = $this->readBits(($this->replay->baseBuild >= 21955 ? 6 : 4));
		while ($numberCacheHandles--) {
			$ret["cacheHandles"][] = $this->parseCacheHandle();
		}

		if($this->replay->baseBuild >= 27950) {
			$ret["hasExtensionMod"] = $this->readBoolean();
		}

		if($this->replay->baseBuild >= 42932) {
			$ret["hasNonBlizzardExtensionMod"] = $this->readBoolean();
		}

		$ret["blizzardMap"] = $this->readBoolean();
		$ret["premadeFFA"] = $this->readBoolean();
		if($this->replay->baseBuild >= 23925) {
			$ret["coop"] = $this->readBoolean();
		}

		return $ret;
	}

	/**
	 * decodes game options
	 *
	 * @access private
	 * @return array with game options data
	 */
	private function decodeGameOptions() {
		$ret = [
			"teamsLocked" => $this->readBoolean(),
			"teamsTogether" => $this->readBoolean(),
			//archon mode??
			"advancedSharedControl" => $this->readBoolean(),
			"randomRaces" => $this->readBoolean(),
			"battlenet" => $this->readBoolean(),
			"amm" => $this->readBoolean()
		];

		if($this->replay->baseBuild >= 34784) {
			$ret["ranked"] = $this->readBoolean();
		} else {
			$ret["ranked"] = false;
		}

		$ret["competitive"] = $this->readBoolean();

		if($this->replay->baseBuild >= 34784) {
			$ret["practice"] = $this->readBoolean();
		} else {
			$ret["practice"] = false;
		}

		if($this->replay->baseBuild >= 34784) {
			$ret["cooperative"] = $this->readBoolean();
		} else {
			$ret["cooperative"] = false;
		}

		$ret["noVictoryOrDefeat"] = $this->readBoolean();

		if($this->replay->baseBuild >= 34784) {
			$ret["allowHeroDuplicates"] = $this->readBoolean();
		} else {
			$ret["allowHeroDuplicates"] = false;
		}

		$ret["fog"] = $this->readBits(2);
		$ret["observers"] = $this->readBits(2);
		$ret["difficulty"] = $this->readBits(2);

		if($this->replay->baseBuild >= 22612) {
			$ret["debugFlags"] = $this->readUint64();
		}

		return $ret;
	}

	/**
	 * decodes lobby state information
	 *
	 * @access private
	 * @return array with information about the state of the lobby when the game was started
	 */
	private function decodeLobbyState() {
		$ret = [
			"phase" => $this->readBits(3),
			"maxUsers" => $this->readBits(5),
			"maxObservers" => $this->readBits(5)
		];

		$numberSlots = $this->readBits(5);
		for ($i=0; $i < $numberSlots; $i++) {
			$ret["slots"][$i] = [
				"control" => $this->readUint8(),
				"userId" => ($this->readBoolean() ? $this->readBits(4) : null),
				"teamId" => $this->readBits(4),
				"colorPreference" => ($this->readBoolean() ? $this->readBits(5) : null),
				"racePreference" => ($this->readBoolean() ? $this->readUint8() : null),
				"difficulty" => $this->readBits(6)
			];

			if($this->replay->baseBuild >= 23925) {
				$ret["slots"][$i]["AIBuild"] = $this->readBits($this->readBits(($this->replay->baseBuild >= 38749 ? 8 : 7)));
			}

			$ret["slots"][$i]["handicap"] = $this->readBits(7);
			$ret["slots"][$i]["observe"] = $this->readBits(2);

			if($this->replay->baseBuild >= 32283) {
				$ret["slots"][$i]["logoIndex"] = $this->readUint8();
			}

			if($this->replay->baseBuild >= 34784) {
				$ret["slots"][$i]["hero"] = $this->readAlignedBytes($this->readBits(9));
				$ret["slots"][$i]["skin"] = $this->readAlignedBytes($this->readBits(9));
				$ret["slots"][$i]["mount"] = $this->readAlignedBytes($this->readBits(9));
				$numberArtifacts = $this->readBits(4);
				while ($numberArtifacts--) {
					$ret["slots"][$i]["mount"]["artifacts"][] = $this->readAlignedBytes($this->readBits(9));
				}
			}

			if($this->replay->baseBuild >= 24764) {
				$ret["slots"][$i]["slotId"] = ($this->readBoolean() ? $this->readUint8() : null);
			}

			if($this->replay->baseBuild >= 34784) {
				$numberRewardBits = 17;
			} elseif($this->replay->baseBuild >= 24764) {
				$numberRewardBits = 6;
			} else {
				$numberRewardBits = 5;
			}

			$numberRewards = $this->readBits($numberRewardBits);
			while ($numberRewards--) {
				$ret["slots"][$i]["rewards"][] = $this->readUint32();
			}

			if($this->replay->baseBuild >= 17266) {
				$ret["slots"][$i]["logoIndex"] = $this->readAlignedBytes($this->readBits(7));
			}

			if($this->replay->baseBuild >= 19132) {
				$numberLicenses = $this->readBits(9);
				while ($numberLicenses--) {
					$ret["slots"][$i]["licenses"][] = $this->readUint32();
				}
			}

			if($this->replay->baseBuild >= 34784) {
				$ret["slots"][$i]["archonLeaderUserId"] = ($this->readBoolean() ? $this->readBits(4) : null);
				$ret["slots"][$i]["commander"]["name"] = $this->readAlignedBytes($this->readBits(9));
			}

			if($this->replay->baseBuild >= 36442) {
				$ret["slots"][$i]["commander"]["level"] = $this->readUint32();
			}

			if($this->replay->baseBuild >= 38215) {
				$ret["slots"][$i]["silencePenalty"] = $this->readBoolean();
			}

			if($this->replay->baseBuild >= 39576) {
				$ret["slots"][$i]["archonId"] = $this->readBits(4);
			}

			if($this->replay->baseBuild >= 34784) {
				$ret["slots"][$i]["commander"]["masteryLevel"] = $this->readUint32();
				$numberMasteryTalents = $this->readBits(3);
				while ($numberMasteryTalents--) {
					$ret["slots"][$i]["commander"]["masteryTalents"][] = $this->readUint32();
				}
			}
		}

		$ret["seed"] = $this->readUint32();
		$ret["hostUserId"] = ($this->readBoolean() ? $this->readBits(4) : null);
		$ret["singleplayer"] = $this->readBoolean();

		if($this->replay->baseBuild >= 36442) {
			$ret["pickedMapTag"] = $this->readUint8();
		}

		$ret["gameDuration"] = $this->readUint32();
		$ret["defaultDifficulty"] = $this->readBits(6);

		if($this->replay->baseBuild >= 24764) {
			$ret["defaultAIBuild"] = $this->readBits(($this->replay->baseBuild >= 38749 ? 8 : 7));
		}

		return $ret;
	}

}