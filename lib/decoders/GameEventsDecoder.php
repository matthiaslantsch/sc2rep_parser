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
	 * update the event lookup array to the game version the replay was recorded in
	 *
	 * @access private
	 * @return array with event lookup adapted for the version the game was recorded in
	 */
	private function constructVersionedLookup() {
		$eventlookup = [
			0 => "UnknownEvent",
			5 => "UserFinishedLoadingSyncEvent",
			7 => "UserOptionsEvent",
			9 => "BankFileEvent",
			10 => "BankSectionEvent",
			11 => "BankKeyEvent",
			12 => "BankValueEvent",
			13 => "BankSignatureEvent",
			14 => "CameraSaveEvent",
			21 => "SaveGameEvent",
			22 => "SaveGameDoneEvent",
			23 => "LoadGameDoneEvent",
			25 => "CommandManagerResetEvent",
			26 => "GameCheatEvent",
			27 => "CmdEvent",
			28 => "SelectionDeltaEvent",
			29 => "ControlGroupUpdateEvent",
			30 => "SelectionSyncCheckEvent",
			31 => "ResourceTradeEvent",
			32 => "TriggerChatMessageEvent",
			33 => "AICommunicateEvent",
			34 => "SetAbsoluteGameSpeedEvent",
			35 => "AddAbsoluteGameSpeedEvent",
			36 => "TriggerPingEvent",
			37 => "BroadcastCheatEvent",
			38 => "AllianceEvent",
			39 => "UnitClickEvent",
			40 => "UnitHighlightEvent",
			41 => "TriggerReplySelectedEvent",
			43 => "HijackReplayGameEvent",
			44 => "TriggerSkippedEvent",
			45 => "TriggerSoundLengthQueryEvent",
			46 => "TriggerSoundOffsetEvent",
			47 => "TriggerTransmissionOffsetEvent",
			48 => "TriggerTransmissionCompleteEvent",
			49 => "CameraUpdateEvent",
			50 => "TriggerAbortMissionEvent",
			51 => "TriggerPurchaseMadeEvent",
			52 => "TriggerPurchaseExitEvent",
			53 => "TriggerPlanetMissionLaunchedEvent",
			54 => "TriggerPlanetPanelCanceledEvent",
			55 => "TriggerDialogControlEvent",
			56 => "TriggerSoundLengthSyncEvent",
			57 => "TriggerConversationSkippedEvent",
			58 => "TriggerMouseClickedEvent",
			59 => "TriggerMouseMovedEvent",
			60 => "AchievementAwardedEvent",
			61 => "TriggerHotkeyPressedEvent",
			62 => "TriggerTargetModeUpdateEvent",
			63 => "TriggerPlanetPanelReplayEvent",
			64 => "TriggerSoundtrackDoneEvent",
			65 => "TriggerPlanetMissionSelectedEvent",
			66 => "TriggerKeyPressedEvent",
			67 => "TriggerMovieFunctionEvent",
			68 => "TriggerPlanetPanelBirthCompleteEvent",
			69 => "TriggerPlanetPanelDeathCompleteEvent",
			70 => "ResourceRequestEvent",
			71 => "ResourceRequestFulfillEvent",
			72 => "ResourceRequestCancelEvent",
			73 => "TriggerResearchPanelExitEvent",
			74 => "TriggerResearchPanelPurchaseEvent",
			75 => "TriggerResearchPanelSelectionChangedEvent",
			76 => "TriggerCommandErrorEvent",
			77 => "TriggerMercenaryPanelExitEvent",
			78 => "TriggerMercenaryPanelPurchaseEvent",
			79 => "TriggerMercenaryPanelSelectionChangedEvent",
			80 => "TriggerVictoryPanelExitEvent",
			81 => "TriggerBattleReportPanelExitEvent",
			82 => "TriggerBattleReportPanelPlayMissionEvent",
			83 => "TriggerBattleReportPanelPlaySceneEvent",
			84 => "TriggerBattleReportPanelSelectionChangedEvent",
			85 => "TriggerVictoryPanelPlayMissionAgainEvent",
			86 => "TriggerMovieStartedEvent",
			87 => "TriggerMovieFinishedEvent",
			88 => "DecrementGameTimeRemainingEvent",
			89 => "TriggerPortraitLoadedEvent",
			90 => "TriggerCustomDialogDismissedEvent",
			91 => "TriggerGameMenuItemSelectedEvent",
			92 => "TriggerMouseWheelEvent",
			93 => "TriggerPurchasePanelSelectedPurchaseItemChangedEvent",
			94 => "TriggerPurchasePanelSelectedPurchaseCategoryChangedEvent",
			95 => "TriggerButtonPressedEvent",
			96 => "TriggerGameCreditsFinishedEvent",
			97 => "TriggerCutsceneBookmarkFiredEvent",
			98 => "TriggerCutsceneEndSceneFiredEvent",
			99 => "TriggerCutsceneConversationLineEvent",
			100 => "TriggerCutsceneConversationLineMissingEvent",
			101 => "GameUserLeaveEvent",
			102 => "GameUserJoinEvent",
			103 => "CommandManagerStateEvent",
 			104 => "CmdUpdateTargetPointEvent",
			105 => "CmdUpdateTargetUnitEvent",
			106 => "TriggerAnimLengthQueryByNameEvent",
			107 => "TriggerAnimLengthQueryByPropsEvent",
			108 => "TriggerAnimOffsetEvent",
			109 => "CatalogModifyEvent",
			110 => "HeroTalentTreeSelectedEvent",
			111 => "TriggerProfilerLoggingFinishedEvent",
			112 => "HeroTalentTreeSelectionPanelToggledEvent"
		];

		//directly return in most cases
		if($this->replay->baseBuild >= 38215) {
			return $eventlookup;
		}

		if($this->replay->baseBuild < 38215) {
			unset($eventlookup[76]); //TriggerCommandErrorEvent
			unset($eventlookup[92]); //TriggerMouseWheelEvent
		} else {
			return $eventlookup;
		}

		if($this->replay->baseBuild < 34784) {
			unset($eventlookup[25]); //CommandManagerResetEvent
			unset($eventlookup[61]); //TriggerHotkeyPressedEvent
			unset($eventlookup[103]); //CommandManagerResetEvent
			unset($eventlookup[104]); //CmdUpdateTargetPointEvent
			unset($eventlookup[105]); //CmdUpdateTargetUnitEvent
			unset($eventlookup[106]); //TriggerAnimLengthQueryByNameEvent
			unset($eventlookup[107]); //TriggerAnimLengthQueryByPropsEvent
			unset($eventlookup[108]); //TriggerAnimOffsetEvent
			unset($eventlookup[109]); //CatalogModifyEvent
			unset($eventlookup[110]); //HeroTalentTreeSelectedEvent
			unset($eventlookup[111]); //TriggerProfilerLoggingFinishedEvent
			unset($eventlookup[112]); //HeroTalentTreeSelectionPanelToggledEvent
		} else {
			return $eventlookup;
		}

		if($this->replay->baseBuild < 26490) {
			$eventlookup[92] = "TriggerCameraMoveEvent"; //new
		} else {
			return $eventlookup;
		}

		if($this->replay->baseBuild < 24944) {
			unset($eventlookup[14]); //CameraSaveEvent
		} else {
			return $eventlookup;
		}

		if($this->replay->baseBuild <= 23925) {
			$eventlookup[7] = "BankFileEvent"; //overwrite UserOptionsEvent
			$eventlookup[8] = "BankSectionEvent"; //new
			$eventlookup[9] = "BankKeyEvent"; //overwrite BankFileEvent
			$eventlookup[10] = "BankValueEvent"; //overwrite BankSectionEvent
			$eventlookup[11] = "BankSignatureEvent"; //overwrite BankKeyEvent
			$eventlookup[12] = "UserOptionsEvent"; //overwrite BankValueEvent
			unset($eventlookup[13]); //BankSignatureEvent
			unset($eventlookup[21]); //SaveGameEvent
			$eventlookup[22] = "SaveGameEvent"; //overwrite SaveGameDoneEvent
			$eventlookup[23] = "SaveGameDoneEvent"; //overwrite LoadGameDoneEvent
			$eventlookup[25] = "PlayerLeaveEvent"; //new
			unset($eventlookup[43]); //HijackReplayGameEvent
			unset($eventlookup[62]); //TriggerTargetModeUpdateEvent
			$eventlookup[76] = "LagMessageEvent"; //new
			unset($eventlookup[101]); //GameUserLeaveEvent
			unset($eventlookup[102]); //GameUserJoinEvent
		} else {
			return $eventlookup;
		}

		if($this->replay->baseBuild < 21995) {
			unset($eventlookup[36]); //TriggerPingEvent
			unset($eventlookup[60]); //AchievementAwardedEvent
			unset($eventlookup[97]); //TriggerCutsceneBookmarkFiredEvent
			unset($eventlookup[98]); //TriggerCutsceneEndSceneFiredEvent
			unset($eventlookup[99]); //TriggerCutsceneConversationLineEvent
			unset($eventlookup[100]); //TriggerCutsceneConversationLineMissingEvent
		} else {
			return $eventlookup;
		}

		if($this->replay->baseBuild < 17266) {
			$eventlookup[11] = "UserOptionsEvent"; //overwrite BankSignatureEvent
			unset($eventlookup[12]); //UserOptionsEvent
			unset($eventlookup[59]); //TriggerMouseMovedEvent
		} else {
			return $eventlookup;
		}

		if($this->replay->baseBuild < 15405) {
			die("beta event lookup table!!");
		} else {
			return $eventlookup;
		}

	}

	/**
	 * decode the replay.game.events file contained in the replay
	 *
	 * @access protected
	 */
	protected function doDecode() {
		$eventLookup = $this->constructVersionedLookup();
		$loopCount = 0;
		$gameEvents = [];

		while(!$this->eof()) {
			$loopCount += $this->readLoopCount();
			$playerId = $this->readBits(5);
			$eventType = $this->readBits(7);
			if(isset($eventLookup[$eventType])) {
				//echo "$loopCount - {$eventLookup[$eventType]}({$eventType})\n";
				$method = "parse{$eventLookup[$eventType]}";
				$event = $this->$method();
			} else {
				die("Unknown event type {$eventType} in version {$this->replay->baseBuild}\n");
			}

			$event["playerId"] = $playerId;
			$event["gameloop"] = $loopCount;
			$event["eventtype"] = $eventLookup[$eventType];
			$gameEvents[] = $event;

			$this->align();
		}
		$this->replay->rawdata["replay.game.events"] = $gameEvents;
	}

	/**
	 * skip to bytes for an unknown event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseUnknownEvent() {
		return ["unknown" => $this->readBytes(2)];
	}

	/**
	 * decode a UserOptionsEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseUserOptionsEvent() {
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

		if($this->replay->baseBuild > 23260 && $this->replay->baseBuild < 38215) {
			$options["startingRally"] = $this->readBoolean();
		}

		if($this->replay->baseBuild >= 22612 && $this->replay->baseBuild < 23260) {
			$options["useAiBeacons"] = $this->readBoolean();
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

		return ["options" => $options];
	}

	/**
	 * decode a BankFileEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseBankFileEvent() {
		return [
			"name" => $this->readAlignedBytes($this->readBits(7))
		];
	}

	/**
	 * decode a LagMessageEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseLagMessageEvent() {
		return ["lagPlayerId" => $this->readBits(4)];
	}

	/**
	 * decode a BankSectionEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseBankSectionEvent() {
		$read = ["name" => $this->readAlignedBytes($this->readBits(6))];
	}

	/**
	 * decode a BankKeyEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseBankKeyEvent() {
		return [
			"name" => $this->readAlignedBytes($this->readBits(6)),
			"type" => $this->readUint32(),
			"data" => $this->readAlignedBytes($this->readBits(7))
		];
	}

	/**
	 * decode a BankValueEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseBankValueEvent() {
		return [
			"type" => $this->readUint32(),
			"name" => $this->readAlignedBytes($this->readBits(6)),
			"data" => $this->readAlignedBytes($this->readBits(12))
		];
	}

	/**
	 * decode a BankSignatureEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseBankSignatureEvent() {
		$numSignatures = $this->readBits($this->replay->baseBuild >= 17326 ? 5 : 4);
		$read = ["signatures" => []];
		while ($numSignatures--) {
			$read["signatures"][] = $this->readUint8();
		}

		if($this->replay->baseBuild >= 24247) {
			$read["toonHandle"] = $this->readAlignedBytes($this->readBits(7));
		}

		return $read;
	}

	/**
	 * decode a CameraSaveEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseCameraSaveEvent() {
		return [
			"number" => $this->readBits(3),
			"location" => [
				"x" => $this->readUint16(),
				"y" => $this->readUint16()
			]
		];
	}

	/**
	 * decode a SaveGameEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseSaveGameEvent() {
		return [
			"fileName" => $this->readAlignedBytes($this->readBits(11)),
			"automatic" => $this->readBoolean(),
			"overwrite" => $this->readBoolean(),
			"name" => $this->readAlignedBytes($this->readUint8()),
			"description" => $this->readAlignedBytes($this->readBits(10))
		];
	}

	/**
	 * decode a CommandManagerResetEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseCommandManagerResetEvent() {
		return ["sequence" => $this->readUint32()];
	}

	/**
	 * decode a GameCheatEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseGameCheatEvent() {
		return [
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
	 * decode a CmdEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseCmdEvent() {
		$ret = [];
		if($this->replay->baseBuild < 16561) {
			$flagsBitLength = 32;
		} elseif($this->replay->baseBuild < 18574) {
			$flagsBitLength = 17;
		} elseif($this->replay->baseBuild < 22612) {
			$flagsBitLength = 18;
		} elseif($this->replay->baseBuild < 34784) {
			$flagsBitLength = 20;
		} elseif($this->replay->baseBuild < 38215) {
			$flagsBitLength = 23;
		} else {
			$flagsBitLength = 25;
		}
		$ret["flags"] = $this->readBits($flagsBitLength);

		if($this->replay->baseBuild < 16561 || $this->readBoolean()) {
			$ret["ability"] = [
				"abilityLink" => $this->readUint16(),
				"commandIndex" => $this->readBits($this->replay->baseBuild >= 16561 ? 5 : 8),
				"commandData" => ($this->replay->baseBuild < 16561 || $this->readBoolean() ? $this->readUint8() : null)
			];
		}

		if($this->replay->baseBuild < 16561) {
			$bitsKind = 2;
		} else {
			$bitsKind = $this->readBits(2);
		}

		switch ($bitsKind) {
			case 0:
				$ret["cmdType"] = "CommandEvent";
				break;
			case 1:
				$ret["cmdType"] = "TargetPointCommandEvent";
				$ret["location"] = [
					"x" => $this->readBits(20),
					"y" => $this->readBits(20),
					"z" => $this->readUint32() - 2147483648
				];
				break;
			case 2:
				$ret["cmdType"] = "TargetUnitCommandEvent";
				if($this->replay->baseBuild >= 34784) {
					$ret["targetFlags"] = $this->readUint16();
				} else {
					$ret["targetFlags"] = $this->readUint8();
				}

				$ret["targetTimer"] = $this->readUint8();

				if($this->replay->baseBuild < 16561) {
					//this was in front of the other data in those old versions
					$ret["otherUnitId"] = $this->readUint32();
				}

				$ret["targetUnitId"] = $this->readUint32();
				$ret["targetUnitLink"] = $this->readUint16();

				if($this->replay->baseBuild >= 19595 && $this->readBoolean()) {
					$ret["controlPlayerId"] = $this->readBits(4);
				}

				if($this->readBoolean()) {
					$ret["upkeepPlayerId"] = $this->readBits(4);
				}

				if($this->replay->baseBuild < 16561) {
					$ret["location"] = [
						"x" => $this->readUint32() - 2147483648,
						"y" => $this->readUint32() - 2147483648,
						"z" => $this->readUint32() - 2147483648
					];
				} else {
					$ret["location"] = [
						"x" => $this->readBits(20),
						"y" => $this->readBits(20),
						"z" => $this->readUint32() - 2147483648
					];
				}
				break;
			case 3:
				$ret["cmdType"] = "DataCommandEvent";
				$ret["data"] = $this->readUint32();
				break;
			default:
				die("Unknown bit kind command event {$bitsKind}");
				break;
		}

		if($this->replay->baseBuild >= 34784) {
			$ret["sequence"] = $this->readUint32() + 1;
		}

		if($this->replay->baseBuild >= 16561 && $this->readBoolean()) {
			//this was after the other data in the newer versions
			$ret["otherUnitId"] = $this->readUint32();
		}

		if($this->replay->baseBuild >= 34784 && $this->readBoolean()) {
			$ret["unitGroup"] = $this->readUint32();
		}
		return $ret;
	}

	/**
	 * decode a SelectionDeltaEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseSelectionDeltaEvent() {
		$ret["controlGroupIndex"] = $this->readBits(4);
		$ret["subGroupIndex"] = $this->readBits($this->replay->baseBuild >= 22612 ? 9 : 8);
		$ret["removeMask"] = $this->readRemoveBitmask(true);

		$numAddSubGroupEntries = $this->readBits($this->replay->baseBuild >= 22612 ? 9 : 8);
		$ret["addSubGroups"] = [];
		while($numAddSubGroupEntries--) {
			$subGroupentry = ["unitLink" => $this->readUint16()];
			if($this->replay->baseBuild > 23260) {
				$subGroupentry["subGroupPriority"] = $this->readUint8();
			}
			$subGroupentry["intraSubGroupPriority"] = $this->readUint8();
			$subGroupentry["count"] = $this->readBits($this->replay->baseBuild >= 22612 ? 9 : 8);
			$ret["addSubGroups"][] = $subGroupentry;
		}

		$numAddUnitTags = $this->readBits($this->replay->baseBuild >= 22612 ? 9 : 8);
		$ret["addUnitTags"] = [];
		while($numAddUnitTags--) {
			$ret["addUnitTags"][] = $this->readUint32();
		}

		return $ret;
	}

	/**
	 * decode a ControlGroupUpdateEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseControlGroupUpdateEvent() {
		return [
			"controlGroupIndex" => $this->readBits(4),
			"updateType" => $this->readBits($this->replay->baseBuild >= 36442 ? 3 : 2),
			"removeMask" => $this->readRemoveBitmask()
		];
	}

	/**
	 * decode a SelectionSyncCheckEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseSelectionSyncCheckEvent() {
		return [
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
	 * @return array with parsed event data
	 */
	private function parseResourceTradeEvent() {
		return [
			"recipientId" => $this->readBits(4),
			"resources" => $this->readResourceCounts()
		];
	}

	/**
	 * decode a TriggerChatMessageEvent event
	 * will not be used since we parse the replay.message.events anyway
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerChatMessageEvent() {
		return ["message" => $this->readAlignedBytes($this->readBits(10))];
	}

	/**
	 * decode a AICommunicateEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseAICommunicateEvent() {
		$ret = [
			"beacon" => $this->readUint8() - 128,
			"ally" => $this->readUint8() - 128,
			"flags" => $this->readUint8() - 128
		];

		if($this->replay->baseBuild >= 22612) {
			$ret["build"] = $this->readUint8() - 128;
		}

		$ret["targetUnitId"] = $this->readUint32();
		$ret["targetUnitLink"] = $this->readUint16();

		if($this->replay->baseBuild < 22612 && $this->readBoolean()) {
			$ret["targetUpkeepPlayerId"] = $this->readBits(4);
		} else {
			$ret["targetUpkeepPlayerId"] = $this->readUint8() - 128;
		}

		if($this->replay->baseBuild >= 19595 && $this->replay->baseBuild < 22612 && $this->readBoolean()) {
			$ret["targetControlPlayerId"] = $this->readBits(4);
		} else {
			$ret["targetControlPlayerId"] = $this->readUint8() - 128;
		}

		$ret["location"] = [
			"x" => $this->readUint32() - 2147483648,
			"y" => $this->readUint32() - 2147483648,
			"z" => $this->readUint32() - 2147483648
		];
		return $ret;
	}

	/**
	 * decode a SetAbsoluteGameSpeedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseSetAbsoluteGameSpeedEvent() {
		return ["speed" => $this->readBits(3)];
	}

	/**
	 * decode a AddAbsoluteGameSpeedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseAddAbsoluteGameSpeedEvent() {
		return ["speedDelta" => $this->readUint8() - 128];
	}

	/**
	 * decode a TriggerPingEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerPingEvent() {
		$read = [
			"point" => [
				"x" => $this->readUint32() - 2147483648,
				"y" => $this->read_uint32() - 2147483648
			],
			"unitId" => $this->readUint32()
		];

		if($this->replay->baseBuild >= 38215) {
			$read["unitLink"] = $this->readUint16();
			if($this->readBoolean()) {
				$read["unitControlPlayerId"] = $this->readBits(4);
			}
			if($this->readBoolean()) {
				$read["unitUpkeepPlayerId"] = $this->readBits(4);
			}

			$read["unitPosition"] = [
				"x" => $this->readBits(20),
				"y" => $this->readBits(20),
				"z" => $this->readUint32() - 2147483648
			];

			if($this->replay->baseBuild >= 38996) {
				$read["unitUnderConstruction"] = $this->readBoolean();
			}
		}

		$read["pingedMinimap"] = $this->readBoolean();

		if($this->replay->baseBuild >= 34784) {
			$read["option"] = $this->readUint32() - 2147483648;
		}
		return $read;
	}

	/**
	 * decode a BroadcastCheatEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseBroadcastCheatEvent() {
		return [
			"verb" => $this->readAlignedBytes($this->readBits(10)),
			"arguments" => $this->readAlignedBytes($this->readBits(10))
		];
	}

	/**
	 * decode a AllianceEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseAllianceEvent() {
		return [
			"alliance" => $this->readUint32(),
			"control" => $this->readUint32()
		];
	}

	/**
	 * decode a UnitClickEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseUnitClickEvent() {
		return ["unitId" => $this->readUint32()];
	}

	/**
	 * decode a UnitHighlightEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseUnitHighlightEvent() {
		return [
			"unitTag" => $this->readUint32(),
			"flags" => $this->readUint8()
		];
	}

	/**
	 * decode a UnitHighlightEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerReplySelectedEvent() {
		return [
			"conversationId" => $this->readUint32() - 2147483648,
			"replyId" => $this->readUint32() - 2147483648
		];
	}

	/**
	 * decode a HijackReplayGameEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseHijackReplayGameEvent() {
		$ret["userInfos"] = [];
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
			$ret["userInfos"][] = $userInfo;
		}
		$ret["method"] = $this->readBits(1);

		return $ret;
	}

	/**
	 * decode a TriggerSoundLengthQueryEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerSoundLengthQueryEvent() {
		return [
			"soundHash" => $this->readUint32(),
			"length" => $this->readUint32()
		];
	}

	/**
	 * decode a TriggerSoundOffsetEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerSoundOffsetEvent() {
		return ["sound" => $this->readUint32()];
	}

	/**
	 * decode a TriggerTransmissionCompleteEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerTransmissionCompleteEvent() {
		return ["transmissionId" => $this->readUint32() - 2147483648];
	}

	/**
	 * decode a TriggerTransmissionOffsetEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerTransmissionOffsetEvent() {
		return ["transmissionId" => $this->readUint32() - 2147483648];
	}

	/**
	 * decode a player camera event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseCameraUpdateEvent() {
		$ret = [];
		if($this->replay->baseBuild <= 23260 || $this->readBoolean()) {
			$ret["location"] = [
				"x" => $this->readUint16(),
				"y" => $this->readUint16()
			];
		}

		if($this->readBoolean()) {
			$ret["distance"] = $this->readUint16();
		}

		if($this->readBoolean()) {
			$ret["pitch"] = $this->readUint16();
		}

		if($this->readBoolean()) {
			$ret["yaw"] = $this->readUint16();
		}

		if($this->replay->baseBuild >= 27950 && $this->readBoolean()) {
			$ret["reason"] = $this->readUint8() - 128;
		}

		if($this->replay->baseBuild >= 34784) {
			$ret["follow"] = $this->readBoolean();
		}

		return $ret;
	}

	/**
	 * decode a TriggerPurchaseMadeEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerPurchaseMadeEvent() {
		return ["purchaseItemId" => $this->readUint32() - 2147483648];
	}

	/**
	 * decode a TriggerPlanetMissionLaunchedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerPlanetMissionLaunchedEvent() {
		return ["difficultyLevel" => $this->readUint32() - 2147483648];
	}

	/**
	 * decode a TriggerDialogControlEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerDialogControlEvent() {
		$read = [
			"controlId" => $this->readUint32() - 2147483648,
			"eventType" => $this->readUint32() - 2147483648
		];

		$eventDataType = $this->readBits(3);
		switch ($eventDataType) {
			case 0:
				$read["eventData"] = ["None" => null];
				break;
			case 1:
				$read["eventData"] = ["Checked" => $this->readBoolean()];
				break;
			case 2:
				$read["eventData"] = ["ValueChanged" => $this->readUint32()];
				break;
			case 3:
				$read["eventData"] = ["SelectionChanged" => $this->readUint32() - 2147483648];
				break;
			case 4:
				$read["eventData"] = ["TextChanged" => $this->readAlignedBytes($this->readBits(11))];
				break;
			case 5:
				if($this->replay->baseBuild > 23260) {
					$read["eventData"] = ["MouseButton" => $this->readUint32()];
				}
				break;
		}
		return $read;
	}

	/**
	 * decode a TriggerSoundLengthSyncEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerSoundLengthSyncEvent() {
		$read = ["syncInfo" => []];

		$numSoundHash = $this->readBits(($this->replay->baseBuild >= 23260 ? 7 : 8));
		while ($numSoundHash--) {
			$read["syncInfo"]["soundHash"][] = $this->readUint32();
		}

		$numLengths = $this->readBits(($this->replay->baseBuild >= 23260 ? 7 : 8));
		while ($numLengths--) {
			$read["syncInfo"]["length"][] = $this->readUint32();
		}
		return $read;
	}

	/**
	 * decode a TriggerConversationSkippedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerConversationSkippedEvent() {
		return ["skipType" => $this->readBits(1)];
	}

	/**
	 * decode a TriggerMouseClickedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerMouseClickedEvent() {
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
		return $read;
	}

	/**
	 * decode a TriggerMouseMovedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerMouseMovedEvent() {
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
		return $read;
	}

	/**
	 * decode a AchievementAwardedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseAchievementAwardedEvent() {
		return ["achievementLink" => $this->readUint16()];
	}

	/**
	 * decode a TriggerHotkeyPressedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerHotkeyPressedEvent() {
		return [
			"hotkey" => $this->readUint32(),
			"down" => $this->readBoolean()
		];
	}

	/**
	 * decode a TriggerTargetModeUpdateEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerTargetModeUpdateEvent() {
		return [
			"abilityLink" => $this->readUint16(),
			"abilityCommandIndex" => $this->readBits(5),
			"state" => $this->readUint8() - 128
		];
	}

	/**
	 * decode a TriggerSoundtrackDoneEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerSoundtrackDoneEvent() {
		return ["soundtrack" => $this->readUint32()];
	}

	/**
	 * decode a PlanetMissionSelectedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerPlanetMissionSelectedEvent() {
		return ["planetId" => $this->readUint32() - 2147483648];
	}

	/**
	 * decode a TriggerKeyPressedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerKeyPressedEvent() {
		return [
			"key" => $this->readUint8() - 128,
			"flags" => $this->readUint8() - 128
		];
	}

	/**
	 * decode a TriggerMovieFunctionEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerMovieFunctionEvent() {
		return ["functionName" => $this->readAlignedBytes($this->readBits(7))];
	}

	/**
	 * decode a ResourceRequestEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseResourceRequestEvent() {
		return ["resources" => $this->readResourceCounts()];
	}

	/**
	 * decode a ResourceRequestCancelEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseResourceRequestCancelEvent() {
		return ["requestId" => $this->readUint32() - 2147483648];
	}

	/**
	 * decode a ResourceRequestFulfillEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseResourceRequestFulfillEvent() {
		return ["requestId" => $this->readUint32() - 2147483648];
	}

	/**
	 * decode TriggerMercenaryPanelSelectionChangedEvent
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerMercenaryPanelSelectionChangedEvent() {
		return ["itemId" => $this->readUint32() - 2147483648];
	}

	/**
	 * decode PurchasePanelSelectedPurchaseItemChangedEvent
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parsePurchasePanelSelectedPurchaseItemChangedEvent() {
		return ["itemId" => $this->readUint32() - 2147483648];
	}

	/**
	 * decode TriggerResearchPanelSelectionChangedEvent
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerResearchPanelSelectionChangedEvent() {
		return ["itemId" => $this->readUint32() - 2147483648];
	}

	/**
	 * decode a TriggerCommandErrorEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerCommandErrorEvent() {
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
		return $read;
	}

	/**
	 * decode a TriggerBattleReportPanelPlayMissionEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerBattleReportPanelPlayMissionEvent() {
		return [
			"battleReportId" => $this->readUint32() - 2147483648,
			"difficultyLevel" => $this->readUint32() - 2147483648
		];
	}

	/**
	 * decode a TriggerBattleReportPanelPlaySceneEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerBattleReportPanelPlaySceneEvent() {
		return ["battleReportId" => $this->readUint32() - 2147483648];
	}

	/**
	 * decode a BattleReportPanelSelectionChangedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerBattleReportPanelSelectionChangedEvent() {
		return ["battleReportId" => $this->readUint32() - 2147483648];
	}

	/**
	 * decode a TriggerVictoryPanelPlayMissionAgainEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerVictoryPanelPlayMissionAgainEvent() {
		return ["difficultyLevel" => $this->readUint32() - 2147483648];
	}

	/**
	 * decode a DecrementGameTimeRemainingEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseDecrementGameTimeRemainingEvent() {
		if($this->replay->baseBuild >= 16561 && $this->replay->baseBuild < 41743) {
			return ["decrementMs" => $this->readBits(19)];
		} else {
			return ["decrementMs" => $this->readUint32()];
		}
	}

	/**
	 * decode a TriggerPortraitLoadedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerPortraitLoadedEvent() {
		return ["portraitId" => $this->readUint32() - 2147483648];
	}

	/**
	 * decode a TriggerCustomDialogDismissedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerCustomDialogDismissedEvent() {
		return ["result" => $this->readUint32() - 2147483648];
	}

	/**
	 * decode a TriggerGameMenuItemSelectedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerGameMenuItemSelectedEvent() {
		return ["gameMenuItemIndex" => $this->readUint32() - 2147483648];
	}

	/**
	 * decode a TriggerMouseWheelEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerMouseWheelEvent() {
		return [
			"wheelSpin" => $this->readUint16() - 32768,
			"flags" => $this->readUint8() - 128
		];
	}

	/**
	 * decode a TriggerPurchasePanelSelectedPurchaseCategoryChangedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerPurchasePanelSelectedPurchaseCategoryChangedEvent() {
		return ["categoryId" => $this->readUint32() - 2147483648];
	}

	/**
	 * decode a TriggerButtonPressedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerButtonPressedEvent() {
		return ["button" => $this->readUint16()];
	}

	/**
	 * decode a TriggerCutsceneBookmarkFiredEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerCutsceneBookmarkFiredEvent() {
		return [
			"cutsceneId" => $this->readUint32() - 2147483648,
			"bookmarkName" => $this->readAlignedBytes($this->readBits(7))
		];
	}

	/**
	 * decode a TriggerCutsceneEndSceneFiredEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerCutsceneEndSceneFiredEvent() {
		return ["cutsceneId" => $this->readUint32() - 2147483648];
	}

	/**
	 * decode a TriggerCutsceneConversationLineEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerCutsceneConversationLineEvent() {
		return [
			"cutsceneId" => $this->readUint32() - 2147483648,
			"conversationLine" => $this->readAlignedBytes($this->readBits(7)),
			"altConversationLine" => $this->readAlignedBytes($this->readBits(7))
		];
	}

	/**
	 * decode a TriggerCutsceneConversationLineMissingEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerCutsceneConversationLineMissingEvent() {
		return [
			"cutsceneId" => $this->readUint32() - 2147483648,
			"conversationLine" => $this->readAlignedBytes($this->readBits(7))
		];
	}

	/**
	 * decode a GameUserLeaveEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseGameUserLeaveEvent() {
		if($this->replay->baseBuild < 34784) {
			return [];
		} else {
			return ["leaveReason" => $this->readBits(4)];
		}
	}

	/**
	 * decode a GameUserJoinEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseGameUserJoinEvent() {
		$ret = [
			"observe" => $this->readBits(2),
			"name" => $this->readAlignedBytes($this->readBits(8)),
		];
		if($this->readBoolean()) {
			$ret["toonHandle"] = $this->readAlignedBytes($this->readBits(7));
		}
		if($this->readBoolean()) {
			$ret["clanTag"] = $this->readAlignedBytes($this->readBits(8));
		}
		if($this->replay->baseBuild >= 27950 && $this->readBoolean()) {
			$ret["clanLogo"] = $this->parseCacheHandle();
		}
		if($this->replay->baseBuild >= 34784) {
			$ret["hijack"] = $this->readBoolean();
			if($this->readBoolean()) {
				$ret["hijackCloneGameUserId"] = $this->readBits(4);
			}
		}
		return $ret;
	}

	/**
	 * decode a CommandManagerStateEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseCommandManagerStateEvent() {
		$read = ["state" => $this->readBits(2)];
		if($this->readBoolean()) {
			$read["sequence"] = $this->readUint32() + 1;
		}
		return $read;
	}

	/**
	 * decode a TriggerCameraMoveEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerCameraMoveEvent() {
		return ["reason" => $this->readUint8() - 128];
	}

	/**
	 * decode a CmdUpdateTargetPointEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseCmdUpdateTargetPointEvent() {
		return [
			"location" => [
				"x" => $this->readBits(20),
				"y" => $this->readBits(20),
				"z" => $this->readUint32() - 2147483648
			]
		];
	}

	/**
	 * decode a CmdUpdateTargetUnitEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseCmdUpdateTargetUnitEvent() {
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
		return $read;
	}

	/**
	 * decode a TriggerAnimLengthQueryByNameEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerAnimLengthQueryByNameEvent() {
		return [
			"queryId" => $this->readUint16(),
			"lengthMs" => $this->readUint32(),
			"finishGameLoop" => $this->readUint32()
		];
	}

	/**
	 * decode a TriggerAnimLengthQueryByPropsEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerAnimLengthQueryByPropsEvent() {
		return [
			"queryId" => $this->readUint16(),
			"lengthMs" => $this->readUint32()
		];
	}

	/**
	 * decode a TriggerAnimOffsetEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerAnimOffsetEvent() {
		return ["animWaitQueryId" => $this->readUint16()];
	}

	/**
	 * decode a CatalogModifyEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseCatalogModifyEvent() {
		return [
			"catalog" => $this->readUint8(),
			"entry" => $this->readUint16(),
			"field" => $this->readAlignedBytes($this->read_uint8()),
			"value" => $this->readAlignedBytes($this->readUint8())
		];
	}

	/**
	 * decode a HeroTalentTreeSelectedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseHeroTalentTreeSelectedEvent() {
		return ["index" => $this->readUint32()];
	}

	/**
	 * decode a HeroTalentTreeSelectionPanelToggledEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseHeroTalentTreeSelectionPanelToggledEvent() {
		return ["shown" => $this->readBoolean()];
	}

	/**
	 * decode a UserFinishedLoadingSyncEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseUserFinishedLoadingSyncEvent() {
		return [];
	}

	/**
	 * decode a SaveGameDoneEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseSaveGameDoneEvent() {
		return [];
	}

	/**
	 * decode a LoadGameDoneEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseLoadGameDoneEvent() {
		return [];
	}

	/**
	 * decode a TriggerSkippedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerSkippedEvent() {
		return [];
	}

	/**
	 * decode a TriggerAbortMissionEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerAbortMissionEvent() {
		return [];
	}

	/**
	 * decode a PlayerLeaveEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parsePlayerLeaveEvent() {
		return [];
	}

	/**
	 * decode a TriggerPurchaseExitEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerPurchaseExitEvent() {
		return [];
	}

	/**
	 * decode a TriggerPlanetPanelCanceledEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerPlanetPanelCanceledEvent() {
		return [];
	}

	/**
	 * decode a TriggerPlanetPanelReplayEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerPlanetPanelReplayEvent() {
		return [];
	}

	/**
	 * decode a TriggerPlanetPanelBirthCompleteEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerPlanetPanelBirthCompleteEvent() {
		return [];
	}

	/**
	 * decode a TriggerPlanetPanelDeathCompleteEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerPlanetPanelDeathCompleteEvent() {
		return [];
	}

	/**
	 * decode a TriggerResearchPanelExitEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerResearchPanelExitEvent() {
		return [];
	}

	/**
	 * decode a TriggerResearchPanelPurchaseEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerResearchPanelPurchaseEvent() {
		return [];
	}

	/**
	 * decode a TriggerMercenaryPanelExitEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerMercenaryPanelExitEvent() {
		return [];
	}

	/**
	 * decode a TriggerMercenaryPanelPurchaseEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerMercenaryPanelPurchaseEvent() {
		return [];
	}

	/**
	 * decode a TriggerVictoryPanelExitEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerVictoryPanelExitEvent() {
		return [];
	}

	/**
	 * decode a TriggerBattleReportPanelExitEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerBattleReportPanelExitEvent() {
		return [];
	}

	/**
	 * decode a TriggerMovieStartedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerMovieStartedEvent() {
		return [];
	}

	/**
	 * decode a TriggerMovieFinishedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerMovieFinishedEvent() {
		return [];
	}

	/**
	 * decode a TriggerPurchasePanelSelectedPurchaseItemChangedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerPurchasePanelSelectedPurchaseItemChangedEvent() {
		return [];
	}

	/**
	 * decode a TriggerGameCreditsFinishedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerGameCreditsFinishedEvent() {
		return [];
	}

	/**
	 * decode a TriggerProfilerLoggingFinishedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerProfilerLoggingFinishedEvent() {
		return [];
	}

	/**
	 * helper function to read a removal bit mask (used by selection and control group event parsers)
	 *
	 * @access private
	 * @param  boolean forceMask | boolean allowing selectiondeltaevents to be treated like newer versions
	 * @return array with the removal mask read from the stream
	 */
	private function readRemoveBitmask($forceMask = false) {
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
				if($this->replay->baseBuild >= 16561 || $forceMask || $this->readBoolean()) {
					$numBits = $this->readBits($this->replay->baseBuild >= 22612 ? 9 : 8);
					$removeMask = ["Mask" => $this->readBits($numBits)];
				} else {
					$removeMask = ["None" => null];
				}
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

	/**
	 * reads a group of resource counts and returns them indexed by resource name
	 *
	 * @access private
	 * @return array with resource counts indexed by resource name
	 */
	private function readResourceCounts() {
		$resourceNames = ["Minerals", "Vespene", "Terrazine", "Custom"];
		$numResources = $this->readBits(3);
		for ($i=0; $i < $numResources; $i++) {
			$resCount = $this->readUint32() - 2147483648;
			if(!isset($resourceNames[$i])) {
				die(var_dump("Unknown resource: {$resCount}"));
			}
			$resources[$resourceNames[$i]] = $resCount;
		}

		return $resources;
	}

}