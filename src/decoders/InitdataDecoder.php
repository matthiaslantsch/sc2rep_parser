<?php
/**
 * This file is part of the holonet sc2 replay parser library
 * (c) Matthias Lantsch
 *
 * class file for the InitdataDecoder decoder class
 *
 * @package holonet sc2 replay parser library
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\Sc2repParser\decoders;

/**
 * The InitdataDecoder class is used to decode the "initdata" file contained within the replay archive
 * information on the format can be found here: https://github.com/GraylinKim/sc2reader/wiki/replay.initData
 *
 * @author  matthias.lantsch
 * @package holonet\Sc2repParser\decoders
 */
class InitdataDecoder extends DecoderBase {

	/**
	 * actually decode the initdata file
	 * saves decoded data directly in the replay object
	 *
	 * @access protected
	 * @return void
	 */
	protected function doDecode() {
		$ret = [];

		$numberPlayers = $this->stream->readBits(5);

		while ($numberPlayers--) {
			$ret["userInitialData"][] = $this->decodePlayerInitData();
		}


		$ret["gameDescription"] = $this->decodeGameDescription();

		if($this->replay->baseBuild > 15623) {
			$ret["lobbyState"] = $this->decodeLobbyState();
		}

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
		$ret = ["name" => $this->stream->readAlignedBytes($this->stream->readUint8())];

		if($this->replay->baseBuild >= 24764 && $this->stream->readBoolean()) {
			//supports and has a clan tag
			$ret["clanTag"] = $this->stream->readAlignedBytes(
				$this->stream->readUint8()
			);
		}

		if($this->replay->baseBuild >= 27950 && $this->stream->readBoolean()) {
			//supports and has a clan logo depot file
			$ret["clanlogoDepotfile"] = $this->parseCacheHandle();
		}

		if($this->replay->baseBuild >= 24764 && $this->stream->readBoolean()) {
			//supports and has a highest league archieved
			$ret["highestLeague"] = $this->stream->readUint8();
		}

		if($this->replay->baseBuild >= 24764 && $this->stream->readBoolean()) {
			//supports and has a combined race level
			$ret["combinedRaceLevels"] = $this->stream->readUint32();
		}

		$ret["randomSeed"] = $this->stream->readUint32();

		if($this->stream->readBoolean()) {
			$ret["racePreference"] = $this->stream->readUint8();
		}

		if($this->replay->baseBuild >= 16561 && $this->stream->readBoolean()) {
			$ret["teamPreference"] = $this->stream->readUint8();
		}

		$ret["testMap"] = $this->stream->readBoolean();
		$ret["testAuto"] = $this->stream->readBoolean();

		if($this->replay->baseBuild >= 21955) {
			$ret["examine"] = $this->stream->readBoolean();
		}

		if($this->replay->baseBuild >= 24764) {
			$ret["customInterface"] = $this->stream->readBoolean();
		}

		if($this->replay->baseBuild >= 34784) {
			$ret["testType"] = $this->stream->readUint32();
		}

		$ret["observe"] = $this->stream->readBits(2);

		if($this->replay->baseBuild >= 34784) {
			$ret["hero"] = $this->stream->readAlignedBytes($this->stream->readBits(9));
			$ret["skin"] = $this->stream->readAlignedBytes($this->stream->readBits(9));
			$ret["mount"] = $this->stream->readAlignedBytes($this->stream->readBits(9));
			$ret["toonHandle"] = $this->stream->readAlignedBytes($this->stream->readBits(7));
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
			"randomValue" => $this->stream->readUint32(),
			"gameCacheName" => $this->stream->readAlignedBytes($this->stream->readBits(10)),
			"options" => $this->decodeGameOptions(),
			"gameSpeed" => $this->stream->readBits(3),
			"gameType" => $this->stream->readBits(3),
			"limits" => [
				"users" => $this->stream->readBits(5),
				"observers" => $this->stream->readBits(5),
				"players" => $this->stream->readBits(5),
				//hardcoded + 1 since the number seems to always be one too small
				"teams" => $this->stream->readBits(4) + 1,
				"colors" => $this->stream->readBits(($this->replay->baseBuild >= 17266 ? 6 : 5)) + ($this->replay->baseBuild >= 17266 ? 0 : 1),
				"races" => $this->stream->readUint8() + 1,
				"controls" => $this->stream->readUint8() + ($this->replay->baseBuild >= 26490 ? 0 : 1)
			],
			"map" => [
				"sizeX" => $this->stream->readUint8(),
				"sizeY" => $this->stream->readUint8(),
				"fileSyncChecksum" => $this->stream->readUint32(),
				"filename" => $this->stream->readAlignedBytes($this->stream->readBits(($this->replay->baseBuild > 15623 ? 11 : 10)))
			],
		];

		if($this->replay->baseBuild > 15623) {
			$ret["map"]["author"] = $this->stream->readAlignedBytes($this->stream->readUint8());
		}
		$ret["modFileSyncChecksum"] = $this->stream->readUint32();

		$numberSlots = $this->stream->readBits(5);

		for ($i=0; $i < $numberSlots; $i++) {
			$ret["slotDescriptions"][$i] = [
				"allowedColors" => $this->stream->readBits($this->stream->readBits(6)),
				"allowedRaces" => $this->stream->readBits($this->stream->readUint8()),
				"allowedDifficulty" => $this->stream->readBits($this->stream->readBits(6)),
				"allowedControls" => $this->stream->readBits($this->stream->readUint8()),
				"allowedObserverTypes" => $this->stream->readBits($this->stream->readBits(2))
			];

			if($this->replay->baseBuild >= 23925) {
				$ret["slotDescriptions"][$i]["allowedAIBuilds"] = $this->stream->readBits(
					$this->stream->readBits(($this->replay->baseBuild >= 38749 ? 8 : 7))
				);
			}
		}

		$ret["defaultDifficulty"] = $this->stream->readBits(6);
		if($this->replay->baseBuild >= 23925) {
			$ret["defaultAIBuild"] = $this->stream->readBits(
				$this->stream->readBits(($this->replay->baseBuild >= 38749 ? 8 : 7))
			);
		}

		$numberCacheHandles = $this->stream->readBits(($this->replay->baseBuild >= 21955 ? 6 : 4));
		while ($numberCacheHandles--) {
			$ret["cacheHandles"][] = $this->parseCacheHandle();
		}

		if($this->replay->baseBuild >= 27950) {
			$ret["hasExtensionMod"] = $this->stream->readBoolean();
		}

		if($this->replay->baseBuild >= 42932) {
			$ret["hasNonBlizzardExtensionMod"] = $this->stream->readBoolean();
		}

		$ret["blizzardMap"] = $this->stream->readBoolean();
		$ret["premadeFFA"] = $this->stream->readBoolean();
		if($this->replay->baseBuild >= 23925) {
			$ret["coop"] = $this->stream->readBoolean();
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
			"teamsLocked" => $this->stream->readBoolean(),
			"teamsTogether" => $this->stream->readBoolean(),
			//archon mode??
			"advancedSharedControl" => $this->stream->readBoolean(),
			"randomRaces" => $this->stream->readBoolean(),
			"battlenet" => $this->stream->readBoolean(),
			"amm" => $this->stream->readBoolean()
		];

		if($this->replay->baseBuild >= 34784) {
			$ret["ranked"] = $this->stream->readBoolean();
		} else {
			$ret["ranked"] = false;
		}

		$ret["competitive"] = $this->stream->readBoolean();

		if($this->replay->baseBuild >= 34784) {
			$ret["practice"] = $this->stream->readBoolean();
		} else {
			$ret["practice"] = false;
		}

		if($this->replay->baseBuild >= 34784) {
			$ret["cooperative"] = $this->stream->readBoolean();
		} else {
			$ret["cooperative"] = false;
		}

		$ret["noVictoryOrDefeat"] = $this->stream->readBoolean();

		if($this->replay->baseBuild >= 34784) {
			$ret["allowHeroDuplicates"] = $this->stream->readBoolean();
		} else {
			$ret["allowHeroDuplicates"] = false;
		}

		$ret["fog"] = $this->stream->readBits(2);
		$ret["observers"] = $this->stream->readBits(2);
		$ret["difficulty"] = $this->stream->readBits(2);

		if($this->replay->baseBuild >= 22612) {
			$ret["debugFlags"] = $this->stream->readUint64();
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
			"phase" => $this->stream->readBits(3),
			"maxUsers" => $this->stream->readBits(5),
			"maxObservers" => $this->stream->readBits(5)
		];

		$numberSlots = $this->stream->readBits(5);
		for ($i=0; $i < $numberSlots; $i++) {
			$ret["slots"][$i] = [
				"control" => $this->stream->readUint8(),
				"userId" => ($this->stream->readBoolean() ? $this->stream->readBits(4) : null),
				"teamId" => $this->stream->readBits(4),
				"colorPreference" => ($this->stream->readBoolean() ? $this->stream->readBits(5) : null),
				"racePreference" => ($this->stream->readBoolean() ? $this->stream->readUint8() : null),
				"difficulty" => $this->stream->readBits(6)
			];

			if($this->replay->baseBuild >= 23925) {
				$ret["slots"][$i]["AIBuild"] = $this->stream->readBits(
					$this->stream->readBits(($this->replay->baseBuild >= 38749 ? 8 : 7))
				);
			}

			$ret["slots"][$i]["handicap"] = $this->stream->readBits(7);
			$ret["slots"][$i]["observe"] = $this->stream->readBits(2);

			if($this->replay->baseBuild >= 32283) {
				$ret["slots"][$i]["logoIndex"] = $this->stream->readUint32();
			}

			if($this->replay->baseBuild >= 34784) {
				$ret["slots"][$i]["hero"] = $this->stream->readAlignedBytes($this->stream->readBits(9));
				$ret["slots"][$i]["skin"] = $this->stream->readAlignedBytes($this->stream->readBits(9));
				$ret["slots"][$i]["mount"] = $this->stream->readAlignedBytes($this->stream->readBits(9));
				$numberArtifacts = $this->stream->readBits(4);
				while ($numberArtifacts--) {
					$ret["slots"][$i]["artifacts"][] = $this->stream->readAlignedBytes($this->stream->readBits(9));
				}
			}

			if($this->replay->baseBuild >= 24764) {
				$ret["slots"][$i]["workingSlotId"] = ($this->stream->readBoolean() ? $this->stream->readUint8() : null);
			}

			if($this->replay->baseBuild > 15623) {
				if($this->replay->baseBuild >= 34784) {
					$numberRewardBits = 17;
				} elseif($this->replay->baseBuild >= 24764) {
					$numberRewardBits = 6;
				} else {
					$numberRewardBits = 5;
				}

				$numberRewards = $this->stream->readBits($numberRewardBits);
				while ($numberRewards--) {
					$ret["slots"][$i]["rewards"][] = $this->stream->readUint32();
				}
			}

			if($this->replay->baseBuild >= 17266) {
				$ret["slots"][$i]["toonHandle"] = $this->stream->readAlignedBytes($this->stream->readBits(7));
			}

			if($this->replay->baseBuild >= 19132) {
				$numberLicenses = $this->stream->readBits(9);
				while ($numberLicenses--) {
					/*$ret["slots"][$i]["licenses"][] =*/ $this->stream->readUint32();
				}
			}

			if($this->replay->baseBuild >= 34784) {
				$ret["slots"][$i]["archonLeaderUserId"] = ($this->stream->readBoolean() ? $this->stream->readBits(4) : null);
				$ret["slots"][$i]["commander"]["name"] = $this->stream->readAlignedBytes($this->stream->readBits(9));
			}

			if($this->replay->baseBuild >= 36442) {
				$ret["slots"][$i]["commander"]["level"] = $this->stream->readUint32();
			}

			if($this->replay->baseBuild >= 38215) {
				$ret["slots"][$i]["silencePenalty"] = $this->stream->readBoolean();
			}

			if($this->replay->baseBuild >= 39576) {
				$ret["slots"][$i]["archonId"] = ($this->stream->readBoolean() ? $this->stream->readBits(4) : null);
			}

			if($this->replay->baseBuild >= 42932) {
				$ret["slots"][$i]["commander"]["masteryLevel"] = $this->stream->readUint32();
				$numberMasteryTalents = $this->stream->readBits(3);
				while ($numberMasteryTalents--) {
					$ret["slots"][$i]["commander"]["masteryTalents"][] = $this->stream->readUint32();
				}
			}
		}

		$ret["seed"] = $this->stream->readUint32();
		$ret["hostUserId"] = ($this->stream->readBoolean() ? $this->stream->readBits(4) : null);
		$ret["singleplayer"] = $this->stream->readBoolean();

		if($this->replay->baseBuild >= 36442) {
			$ret["pickedMapTag"] = $this->stream->readUint8();
		}

		$ret["gameDuration"] = $this->stream->readUint32();
		$ret["defaultDifficulty"] = $this->stream->readBits(6);

		if($this->replay->baseBuild >= 24764) {
			$ret["defaultAIBuild"] = $this->stream->readBits(($this->replay->baseBuild >= 38749 ? 8 : 7));
		}

		return $ret;
	}

}
