<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the GameEventsDecoder decoder class
 */

namespace HIS5\lib\Sc2repParser\decoders;

use HIS5\lib\Sc2repParser\data\DataLoader;
use HIS5\lib\Sc2repParser\events as events;

/**
 * The GameEventsDecoder class is used to decode the replay.game.events file contained in the replay archive
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\decoders
 */
class GameEventsDecoder extends BitwiseDecoderBase {

	/**
	 * property used to keep track of the current frame count on an object level
	 *
	 * @access 	private
	 * @var 	integer frameCount | current frame count
	 */
	private $frameCount;

	/**
	 * decode the replay.game.events file contained in the replay
	 *
	 * @access protected
	 */
	protected function doDecode() {
		$eventLookup = DataLoader::loadDataset("gameeventtypes", $this->replay->baseBuild);
		$this->frameCount = 0;
		$gameEvents = [];

		while(!$this->eof()) {
			$this->frameCount += $this->readFrameCount();
			$playerId = $this->readBits(5);
			$eventType = $this->readBits(7);
			if(isset($eventLookup[$eventType])) {
				if(!isset($eventLookup[$eventType][1])) {
					continue;
				}
				$method = $eventLookup[$eventType][1];
				if(method_exists($this, $method)) {
					$event = $this->$method($playerId);
					if($event !== null) {
						$gameEvents[] = $event;
					}
				} else {
					die("implements {$method}\n");
				}
			} else {
				//die(var_dump("Unknown event type {$eventType}"));
			}

			$this->align();
		}
	}

	/**
	 * decode a player camera event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 * @return CameraEvent object from the parsed data
	 */
	private function parseCameraUpdateEvent($playerId) {
		if($this->replay->baseBuild < 23260 || $this->readBoolean()) {
			$location = [
				"x" => $this->readUint16(),
				"y" => $this->readUint16()
			];
		} else {
			$location = null;
		}

		$ret = new events\CameraEvent($this->frameCount, $playerId, $location);

		if($this->readBoolean()) {
			$ret->distance = $this->readUint16();
		}

		if($this->readBoolean()) {
			$ret->pitch = $this->readUint16();
		}

		if($this->readBoolean()) {
			$ret->yaw = $this->readUint16();
		}

		if($this->replay->baseBuild >= 27950 && $this->readBoolean()) {
			$ret->reason = $this->readUint8() - 128;
		}

		if($this->replay->baseBuild >= 34784) {
			$ret->follow = $this->readBoolean();
		}

		return $ret;
	}

	/**
	 * decode a TriggerMercenaryPanelSelectionChangedEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseTriggerMercenaryPanelSelectionChangedEvent($playerId) {
		$read = ["itemId" => $this->readUint32() - 2147483648];
	}

	/**
	 * decode a TriggerPurchasePanelSelectedPurchaseItemChangedEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseTriggerPurchasePanelSelectedPurchaseItemChangedEvent($playerId) {
		$read = ["itemId" => $this->readUint32() - 2147483648];
	}

	/**
	 * decode a SetAbsoluteGameSpeedEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseSetAbsoluteGameSpeedEvent($playerId) {
		$speed = $this->readBits(3);
	}

	/**
	 * decode a TriggerConversationSkippedEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseTriggerConversationSkippedEvent($playerId) {
		$skipType = $this->readBits(1);
	}

	/**
	 * decode a ResourceRequestEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseResourceRequestEvent($playerId) {
		$numResources = $this->readBits(3);
		while ($numResources--) {
			$resources[] = $this->readUint32() - 2147483648;
		}
	}

	/**
	 * decode [SResourceRequestCancelEvent, SResourceRequestFulfillEvent]
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseResourceRequestId($playerId) {
		$requestId = $this->readUint32() - 2147483648;
	}

	/**
	 * decode a TriggerCommandErrorEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseTriggerCommandErrorEvent($playerId) {
		$read = ["error" =>  $this->readUint32() - 2147483648];
		if($this->readBoolean()) {
			$read["ability"] = [
				"abilityLink" => $this->readUint16(),
				"abilityCommandIndex" => $this->readBits(5)
			];
			if($this->readBoolean()) {
				$read["ability"]["abilityCommandData"] = $this->readUint8();
			}
		}
	}

	/**
	 * decode a TriggerMouseClickedEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseTriggerMouseClickedEvent($playerId) {
		$read = [
			"button" => $this->readUint32(),
			"down" => $this->readBoolean()
		];

		if($this->replay->baseBuild < 17326) {
			$read["positionUI"] = [
				"x" => $this->readUint32(),
				"y" => $this->readUint32()
			];
			$read["positionWorld"] = [
				"x" => $this->readUint32() - 2147483648,
				"y" => $this->readUint32() - 2147483648,
				"z" => $this->readUint32() - 2147483648
			];
		} else {
			$read["positionUI"] = [
				"x" => $this->readBits(11),
				"y" => $this->readBits(11)
			];
			$read["positionWorld"] = [
				"x" => $this->readBits(20),
				"y" => $this->readBits(20),
				"z" => $this->readUint32() - 2147483648
			];
		}

		if($this->replay->baseBuild >= 26490) {
			$read["flags"] = $this->readUint8() - 128;
		}           
	}

	/**
	 * decode a TriggerMouseMovedEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseTriggerMouseMovedEvent($playerId) {
		$read = [
			"positionUI" => [
				"x" => $this->readBits(11),
				"y" => $this->readBits(11)
			],
			"positionWorld" => [
				"x" => $this->readBits(20),
				"y" => $this->readBits(20),
				"z" => $this->readUint32() - 2147483648
			]
		];

		if($this->replay->baseBuild >= 26490) {
			$read["flags"] = $this->readUint8() - 128;
		}
	}

	/**
	 * decode a UnitHighlightEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseUnitHighlightEvent($playerId) {
		$read = [
			"unitTag" => $this->readUint32(),
			"flags" => $this->readUint8()
		];
	}

	/**
	 * decode a TriggerSoundLengthSyncEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseTriggerSoundLengthSyncEvent($playerId) {
		$read = ["syncInfo" => []];

		$numSoundHash = $this->readBits(($this->replay->baseBuild >= 23260 ? 7 : 8));
		while ($numSoundHash--) {
			$read["syncInfo"]["soundHash"][] = $this->readUint32();
		}

		$numLengths = $this->readBits(($this->replay->baseBuild >= 23260 ? 7 : 8));
		while ($numLengths--) {
			$read["syncInfo"]["length"][] = $this->readUint32();
		}
	}

	/**
	 * decode a AchievementAwardedEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseAchievementAwardedEvent($playerId) {
		$achievementLink = $this->readUint16();
	}

	/**
	 * decode a BankSectionEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseBankSectionEvent($playerId) {
		$read = ["name" => $this->readAlignedBytes($this->readBits(6))];
	}

	/**
	 * decode a BankValueEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseBankValueEvent($playerId) {
		$read = [
			"type" => $this->readUint32(),
			"name" => $this->readAlignedBytes($this->readBits(6)),
			"data" => $this->readAlignedBytes($this->readBits(12))
		];
	}

	/**
	 * decode a BankKeyEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseBankKeyEvent($playerId) {
		$read = [
			"name" => $this->readAlignedBytes($this->readBits(6)),
			"type" => $this->readUint32(),
			"data" => $this->readAlignedBytes($this->readBits(7))
		];
	}

	/**
	 * decode a BankFileEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseBankFileEvent($playerId) {
		$read = [
			"name" => $this->readAlignedBytes($this->readBits(7))
		];
	}

	/**
	 * decode a BankSignatureEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseBankSignatureEvent($playerId) {
		$numSignatures = $this->readBits($this->replay->baseBuild >= 17326 ? 5 : 4);
		$read = ["signatures" => []];
		while ($numSignatures--) {
			$read["signatures"][] = $this->readUint8();
		}

		if($this->replay->baseBuild >= 24247) {
			$read["toonHandle"] = $this->readAlignedBytes($this->readBits(7));
		}                      
	}

	/**
	 * decode a BroadcastCheatEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseBroadcastCheatEvent($playerId) {
		$read = [
			"verb" => $this->readAlignedBytes($this->readBits(10)),
			"arguments" => $this->readAlignedBytes($this->readBits(10))
		];
	}

	/**
	 * decode a SelectionSyncCheckEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseSelectionSyncCheckEvent($playerId) {
		$read = [
			"controlGroupIndex" => $this->readBits(4),
			"selectionSyncData" => [
				"count" => $this->readBits($this->replay->baseBuild >= 22612 ? 9 : 8),
				"subGroupCount" => $this->readBits($this->replay->baseBuild >= 22612 ? 9 : 8),
				"activeSubgroupIndex" => $this->readBits($this->replay->baseBuild >= 22612 ? 9 : 8),
				"unitTagsChecksum" => $this->readUint32(),
				"subGroupIndicesChecksum" => $this->readUint32(),
				"subGroupCHecksum" => $this->readUint32()
			]
		];
	}

	/**
	 * decode a ResourceTradeEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseResourceTradeEvent($playerId) {
		$recipientId = $this->readBits(4);
		$numResources = $this->readBits(3);
		while ($numResources--) {
			$resources[] = $this->readUint32() - 2147483648;
		}

		return new events\ResourceTradeEvent($recipientId, $resources);
	}

	/**
	 * decode a TriggerKeyPressedEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseTriggerKeyPressedEvent($playerId) {
		$read = [
			"key" => $this->readUint8() - 128,
			"flags" => $this->readUint8() - 128
		];
	}

	/**
	 * decode [TriggerTransmissionCompleteEvent, TriggerTransmissionOffsetEvent] events
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseTriggerTransmissionEvents($playerId) {
		$read = [
			"transmissionId" => $this->readUint32() - 2147483648
		];
	}

	/**
	 * decode a CommandManagerResetEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseCommandManagerResetEvent($playerId) {
		$sequence = $tis->readUint32();
	}

	/**
	 * decode a CommandManagerStateEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseCommandManagerStateEvent($playerId) {
		$read = ["state" => $this->readBits(2)];
		if($this->readBoolean()) {
			$read["sequence"] = $this->readUint32() + 1;
		}
	}

	/**
	 * decode a GameCheatEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseGameCheatEvent($playerId) {
		$read = [
			"point" => [
				"x" => $this->readUint32() - 2147483648,
				"y" => $this->readUint32() - 2147483648
			],
			"time" => $this->readUint32() - 2147483648,
			"verb" => $this->readAlignedBytes($this->readBits(10)),
			"arguments" => $this->readAlignedBytes($this->readBits(10))
		];
	}

	/**
	 * decode a CmdUpdateTargetPointEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseCmdUpdateTargetPointEvent($playerId) {
		$location = [
			"x" => $this->readBits(20),
			"y" => $this->readBits(20),
			"z" => $this->readUint32() - 2147483648
		];
	}

	/**
	 * decode a TriggerTargetModeUpdateEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseTriggerTargetModeUpdateEvent($playerId) {
		$read = [
			"abilityLink" => $this->readUint16(),
			"abilityCommandIndex" => $this->readBits(5),
			"state" => $this->readUint8() - 128
		];
	}

	/**
	 * decode a CmdUpdateTargetUnitEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseCmdUpdateTargetUnitEvent($playerId) {
		$read = [
			"targetUnitFlags" => $this->readUint16(),
			"timer" => $this->readUint8(),
			"tag" => $this->readUint32(),
			"snapshotUnitLink" => $this->readUint16()
		];

		if($this->readBoolean()) {
			$read["snapshotControlPlayerId"] = $this->readBits(4);
		}

		if($this->readBoolean()) {
			$read["snapshotUpkeepPlayerId"] = $this->readBits(4);
		}

		$read["snapshotLocation"] = [
			"x" => $this->readBits(20),
			"y" => $this->readBits(20),
			"z" => $this->readUint32() - 2147483648
		];
	}

	/**
	 * decode a HeroTalentTreeSelectionPanelToggledEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseHeroTalentTreeSelectionPanelToggledEvent($playerId) {
		$shown = $this->readBoolean();
	}

	/**
	 * decode a TriggerChatMessageEvent event
	 * will not be used since we parse the replay.message.events anyway
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseTriggerChatMessageEvent($playerId) {
		$message = $this->readAlignedBytes($this->readBits(10));
	}

	/**
	 * decode a AICommunicateEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseAICommunicateEvent($playerId) {
			beacon=data.read_uint8()-128,
			ally=data.read_uint8()-128,
			flags=data.read_uint8()-128,
			build=None,
			target_unit_tag=data.read_uint32(),
			target_unit_link=data.read_uint16(),
			target_upkeep_player_id=data.read_bits(4) if data.read_bool() else None,
			target_control_player_id=None,
			target_point=dict(
				x=data.read_uint32()-2147483648,
				y=data.read_uint32()-2147483648,
				z=data.read_uint32()-2147483648,
			),
19595
			beacon=data.read_uint8()-128,
			ally=data.read_uint8()-128,
			flags=data.read_uint8()-128,  # autocast??
			build=None,
			target_unit_tag=data.read_uint32(),
			target_unit_link=data.read_uint16(),
			target_upkeep_player_id=data.read_bits(4) if data.read_bool() else None,
			target_control_player_id=data.read_bits(4) if data.read_bool() else None,
			target_point=dict(
				x=data.read_uint32()-2147483648,
				y=data.read_uint32()-2147483648,
				z=data.read_uint32()-2147483648,
			),
22612
			beacon=data.read_uint8()-128,
			ally=data.read_uint8()-128,
			flags=data.read_uint8()-128,
			build=data.read_uint8()-128,
			target_unit_tag=data.read_uint32(),
			target_unit_link=data.read_uint16(),
			target_upkeep_player_id=data.read_uint8(),
			target_control_player_id=data.read_uint8(),
			target_point=dict(
				x=data.read_uint32()-2147483648,
				y=data.read_uint32()-2147483648,
				z=data.read_uint32()-2147483648,
			),
	}

	/**
	 * decode a CameraSaveEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseCameraSaveEvent($playerId) {
		$number = $this->readBits(3);
		$location = [
			"x" => $this->readUint16(),
			"y" => $this->readUint16()
		];
		return new events\CameraSaveEvent($this->frameCount, $playerId, $number, $location);
	}

	/**
	 * decode a UserOptionsEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseUserOptionsEvent($playerId) {
		$options = [];

		if($this->replay->baseBuild >= 22612) {
			$options["gameFullyDownloaded"] = $this->readBoolean();
		}

		$options["developmentCheatsEnabled"] = $this->readBoolean();

		if($this->replay->baseBuild >= 34784) {
			$options["testCheatsEnabled"] = $this->readBoolean();
		}

		$options["multiplayerCheatsEnabled"] = $this->readBoolean();
		$options["syncChecksumEnabled"] = $this->readBoolean();
		$options["isMapToMapTransition"] = $this->readBoolean();

		if($this->replay->baseBuild >= 22612 && $this->replay->baseBuild < 23260) {
			$options["useAiBeacons"] = $this->readBoolean();
		}

		if($this->replay->baseBuild > 23260 && $this->replay->baseBuild < 38215) {
			$options["startingRally"] = $this->readBoolean();
		}

		if($this->replay->baseBuild >= 26490) {
			$options["debugPauseEnabled"] = $this->readBoolean();
		}

		if($this->replay->baseBuild >= 34784) {
			$options["useGalaxyAssets"] = $this->readBoolean();
			$options["platformMac"] = $this->readBoolean();
			$options["cameraFollow"] = $this->readBoolean();
		}

		if($this->replay->baseBuild > 23260) {
			$options["baseBuildNumber"] = $this->readUint32();
		}

		if($this->replay->baseBuild >= 34784) {
			$options["buildNumber"] = $this->readUint32();
			$options["versionFlags"] = $this->readUint32();
			$options["hotkeyProfile"] = $this->readAlignedBytes($this->readBits(9));
		}

		return new events\UserOptionsEvent($this->frameCount, $playerId, $options);
	}

	/**
	 * decode a TriggerPlanetMissionLaunchedEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseTriggerPlanetMissionLaunchedEvent($playerId) {
		$difficultyLevel = $this->readUint32() - 2147483648;
	}

	/**
	 * decode a SelectionDeltaEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseSelectionDeltaEvent($playerId) {
		$controlGroupIndex = $this->readBits(4);
		$subGroupIndex = $this->readBits($this->replay->baseBuild >= 22612 ? 9 : 8);

		$removeMask = $this->readRemoveBitmask();

		$numAddSubGroupEntries = $this->readBits(9);
		$addSubGroups = [];
		while($numAddSubGroupEntries--) {
			$addSubGroups[] = [
				"unitLink" => $this->readUint16(),
				"subGroupPriority" => ($this->replay->baseBuild > 23260 ? $this->readUint8() : null),
				"intraSubGroupPriority" => $this->readUint8(),
				"count" => $this->readBits($this->replay->baseBuild >= 22612 ? 9 : 8)
			];
		}

		$numAddUnitTags = $this->readBits($this->replay->baseBuild >= 22612 ? 9 : 8);
		$addUnitTags = [];
		while($numAddUnitTags--) {
			$addUnitTags[] = $this->readUint32();
		}

		return new events\SelectionEvent($this->frameCount, $playerId, $controlGroupIndex, $subGroupIndex, $removeMask, $addSubGroups, $addUnitTags);
	}

	/**
	 * decode a CmdEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseCmdEvent($playerId) {
		if($this->replay->baseBuild < 16561) {
			$flagsBitLength = 32;
		} elseif($this->replay->baseBuild >= 16561) {
			$flagsBitLength = 17;
		} elseif($this->replay->baseBuild >= 18574) {
			$flagsBitLength = 18;
		} elseif($this->replay->baseBuild >= 22612) {
			$flagsBitLength = 20;
		} elseif($this->replay->baseBuild >= 34784) {
			$flagsBitLength = 23;
		} else {
			$flagsBitLength = 25;
		}
		$flags = $this->readBits($flagsBitLength);

		if($this->replay->baseBuild < 16561 || $this->readBoolean()) {
			$ability = [
				"abilityLink" => $this->readUint16(),
				"commandIndex" => $this->readBits($this->replay->baseBuild >= 16561 ? 5 : 8),
				"commandData" => ($this->replay->baseBuild < 16561 || $this->readBoolean() ? $this->readUint8() : null)
			];
		} else {
			$ability = null;
		}

		if($this->replay->baseBuild < 16561) {
			$bitsKind = 2;
		} else {
			$bitsKind = $this->readBits(2);
		}

		switch ($bitsKind) {
			case 0:
				$ret = new events\CommandEvent($this->frameCount, $playerId, $flags, $ability);
				break;
			case 1:
				$ret = new events\TargetPointCommandEvent($this->frameCount, $playerId, $flags, $ability);
				$ret->location = [
					"x" => $this->readBits(20),
					"y" => $this->readBits(20),
					"z" => $this->readUint32() - 2147483648
				];
				break;
			case 2:
				$ret = new events\TargetUnitCommandEvent($this->frameCount, $playerId, $flags, $ability);
				$ret->targetFlags = $this->readUint8();
				$ret->targetTimer = $this->readUint8();

				if($this->replay->baseBuild < 16561) {
					//this was in front of the other data in those old versions
					$ret->otherUnitId = $this->readUint32();
				}

				$ret->targetUnitId = $this->readUint32();
				$unitLink = $this->readUint16();


				if($this->replay->baseBuild >= 19595) {
					$ret->controlPlayerId = $this->readBits(4);
				}

				if($this->readBoolean()) {
					$ret->upkeepPlayerId = $this->readBits(4);
				}

				if($this->replay->baseBuild < 16561) {
					$ret->location = [
						"x" => $this->readUint32() - 2147483648,
						"y" => $this->readUint32() - 2147483648,
						"z" => $this->readUint32() - 2147483648
					];
				} else {
					$ret->location = [
						"x" => $this->readBits(20),
						"y" => $this->readBits(20),
						"z" => $this->readUint32() - 2147483648
					];					
				}
			case 3:
				$data = $this->readUint32();
				$ret = new events\CommandEvent($this->frameCount, $playerId, $flags, $ability);
				break;
			default:
				die("Unknown bit kind command event {$bitsKind}");
				break;
		}

		if($this->replay->baseBuild >= 34784) {
			$sequence = $this->readUint32() + 1;
		}

		if($this->replay->baseBuild >= 16561 && $this->readBoolean()) {
			//this was after the other data in the newer versions
			$ret->otherUnitId = $this->readUint32();
		}

		if($this->replay->baseBuild >= 34784 && $this->readBoolean()) {
			$unitGroup = $this->readUint32();
		}

		return $ret;
	}

	/**
	 * decode a HijackReplayGameEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseHijackReplayGameEvent($playerId) {
		$userInfos = [];
		$numUserInfos = $this->readBits(5);
		while ($numUserInfos--) {
			$userInfo = [
				"gameUserId" => $this->readBits(4),
				"observe" => $this->readBits(2),
				"name" => $this->readAlignedBytes($this->readUint8()),
			];

			if($this->readBoolean()) {
				$userInfo["toonHandle"] = $this->readAlignedBytes($this->readBits(7));
			}

			if($this->readBoolean()) {
				$userInfo["clanTag"] = $this->readAlignedBytes($this->readUint8());
			}

			if($this->replay->baseBuild >= 27950 && $this->readBoolean()) {
				$userInfo["clanLogo"] = $this->readCacheHandle();

			}
			$userInfos[] = $userInfo;
		}
		$method = $this->readBits(1);

		return new events\HijackReplayGameEvent($this->frameCount, $playerId, $method, $userInfos);
	}

	/**
	 * decode a TriggerDialogControlEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseTriggerDialogControlEvent($playerId) {
		$read = [
			"controlId" => $this->readUint32() - 2147483648,
			"eventType" => $this->readUint32() - 2147483648
		];

		$eventDataType = $this->readBits(3);
		switch ($eventDataType) {
			case 0:
				$read["eventType"] = ["None" => null];
				break;
			case 1:
				$read["eventType"] = ["Checked" => $this->readBoolean()];
				break;
			case 2:
				$read["eventType"] = ["ValueChanged" => $this->readUint32()];
				break;
			case 3:
				$read["eventType"] = ["SelectionChanged" => $this->readUint32() - 2147483648];
				break;
			case 4:
				$read["eventType"] = ["TextChanged" => $this->readAlignedBytes($this->readBits(11))];
				break;
			case 5:
				if($this->replay->baseBuild > 23260) {
					$read["eventType"] = ["MouseButton" => $this->readUint32()];
				}
				break;
		}
	}

	/**
	 * decode a TriggerCutsceneEndSceneFiredEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseTriggerCutsceneEndSceneFiredEvent($playerId) {
		$cutsceneId = $this->readUint32() - 2147483648;
	}

	/**
	 * decode a SaveGameEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseSaveGameEvent($playerId) {
		$read = [
			"fileName" => $this->readAlignedBytes($this->readBits(11)),
			"automatic" => $this->readBoolean(),
			"overwrite" => $this->readBoolean(),
			"name" => $this->readAlignedBytes($this->readUint8()),
			"description" => $this->readAlignedBytes($this->readBits(10))
		];
	}

	/**
	 * decode a TriggerMovieFunctionEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseTriggerMovieFunctionEvent($playerId) {
		$functionName = $this->readAlignedBytes($this->readBits(7));
	}

	/**
	 * decode a TriggerAnimLengthQueryByNameEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseTriggerAnimLengthQueryByNameEvent($playerId) {
		$read = [
			"queryId" => $this->readUint16(),
			"lengthMs" => $this->readUint32(),
			"finishGameLoop" => $this->readUint32()
		];
	}

	/**
	 * decode a TriggerSoundtrackDone event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseTriggerSoundtrackDoneEvent($playerId) {
		$soundtrack = $this->readUint32();
	}

	/**
	 * decode a ControlGroupUpdateEvent event
	 *
	 * @access private
	 * @param  integer playerId | the id of the player that triggered this event
	 */
	private function parseControlGroupUpdateEvent($playerId) {
		$controlGroupIndex = $this->readBits(4);
		$updateType = $this->readBits($this->replay->baseBuild >= 36442 ? 3 : 2);
		$removeMask = $this->readRemoveBitmask();
		return new events\ControlGroupEvent($this->frameCount, $playerId, $controlGroupIndex, $removeMask, $updateType);
	}

	/**
	 * helper function to read a removal bit mask (used by selection and control group event parsers)
	 *
	 * @access private
	 * @return array with the removal mask read from the stream
	 */
	private function readRemoveBitmask() {
		if($this->replay->baseBuild >= 16561) {
			$removeMask = $this->readBits(2);
		} else {
			$removeMask = 1;
		}

		switch ($removeMask) {
			case 0:
				$removeMask = ["None" => null];
				break;
			case 1:
				$removeMask = ["Mask" => $this->readBits($this->readBits($this->replay->baseBuild >= 22612 ? 9 : 8))];
				break;
			case 2:
				$removeMask = ["OneIndices" => []];
				$numUnitTag = $this->readBits($this->replay->baseBuild >= 22612 ? 9 : 8);
				while($numUnitTag--) {
					$removeMask["OneIndices"][] = $this->readBits($this->replay->baseBuild >= 22612 ? 9 : 8);
				}
				break;
			case 3:
				$removeMask = ["ZeroIndices" => []];
				$numUnitTag = $this->readBits($this->replay->baseBuild >= 22612 ? 9 : 8);
				while($numUnitTag--) {
					$removeMask["ZeroIndices"][] = $this->readBits($this->replay->baseBuild >= 22612 ? 9 : 8);
				}
				break;
			default:
				die(var_dump("Strange selection delta mask id"));
				break;
		}
		return $removeMask;
	}

}