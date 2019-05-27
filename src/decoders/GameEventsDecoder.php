<?php
/**
 * This file is part of the holonet sc2 replay parser library
 * (c) Matthias Lantsch
 *
 * class file for the GameEventsDecoder decoder class
 *
 * @package holonet sc2 replay parser library
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\Sc2repParser\decoders;

/**
 * The GameEventsDecoder class is used to decode the replay.game.events file contained in the replay archive
 *
 * @author  matthias.lantsch
 * @package holonet\Sc2repParser\decoders
 */
class GameEventsDecoder extends DecoderBase {

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

		if($this->replay->baseBuild <= 15623) {
			throw new ParserException("Game event parsing for beta versions of sc2 is incomplete and won't work properly", 500);
			$eventlookup[12] = "UnknownEvent"; //unknown
		}

		return $eventlookup;
	}

	/**
	 * decode the replay.game.events file contained in the replay
	 * saves the data directly into the replay object
	 *
	 * @access protected
	 * @return void
	 */
	protected function doDecode() {
		$eventLookup = $this->constructVersionedLookup();
		$loopCount = 0;
		$gameEvents = [];

		while(!$this->stream->eof()) {
			$loopCount += $this->readLoopCount();
			$playerId = $this->stream->readBits(5);
			$eventType = $this->stream->readBits(7);

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

			$this->stream->align();
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
		return ["unknown" => $this->stream->readBytes(2)];
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
			$options["gameFullyDownloaded"] = $this->stream->readBoolean();
		}

		$options["developmentCheatsEnabled"] = $this->stream->readBoolean();

		if($this->replay->baseBuild >= 34784) {
			$options["testCheatsEnabled"] = $this->stream->readBoolean();
		}

		$options["multiplayerCheatsEnabled"] = $this->stream->readBoolean();
		$options["syncChecksumEnabled"] = $this->stream->readBoolean();
		$options["isMapToMapTransition"] = $this->stream->readBoolean();

		if($this->replay->baseBuild > 23260 && $this->replay->baseBuild < 38215) {
			$options["startingRally"] = $this->stream->readBoolean();
		}

		if($this->replay->baseBuild >= 22612 && $this->replay->baseBuild < 23260) {
			$options["useAiBeacons"] = $this->stream->readBoolean();
		}

		if($this->replay->baseBuild >= 26490) {
			$options["debugPauseEnabled"] = $this->stream->readBoolean();
		}

		if($this->replay->baseBuild >= 34784) {
			$options["useGalaxyAssets"] = $this->stream->readBoolean();
			$options["platformMac"] = $this->stream->readBoolean();
			$options["cameraFollow"] = $this->stream->readBoolean();
		}

		if($this->replay->baseBuild > 23260) {
			$options["baseBuildNumber"] = $this->stream->readUint32();
		}

		if($this->replay->baseBuild >= 34784) {
			$options["buildNumber"] = $this->stream->readUint32();
			$options["versionFlags"] = $this->stream->readUint32();
			$options["hotkeyProfile"] = $this->stream->readAlignedBytes($this->stream->readBits(9));
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
			"name" => $this->stream->readAlignedBytes($this->stream->readBits(7))
		];
	}

	/**
	 * decode a LagMessageEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseLagMessageEvent() {
		return ["lagPlayerId" => $this->stream->readBits(4)];
	}

	/**
	 * decode a BankSectionEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseBankSectionEvent() {
		$read = ["name" => $this->stream->readAlignedBytes($this->stream->readBits(6))];
	}

	/**
	 * decode a BankKeyEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseBankKeyEvent() {
		return [
			"name" => $this->stream->readAlignedBytes($this->stream->readBits(6)),
			"type" => $this->stream->readUint32(),
			"data" => $this->stream->readAlignedBytes($this->stream->readBits(7))
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
			"type" => $this->stream->readUint32(),
			"name" => $this->stream->readAlignedBytes($this->stream->readBits(6)),
			"data" => $this->stream->readAlignedBytes($this->stream->readBits(12))
		];
	}

	/**
	 * decode a BankSignatureEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseBankSignatureEvent() {
		$numSignatures = $this->stream->readBits($this->replay->baseBuild >= 17326 ? 5 : 4);
		$read = ["signatures" => []];
		while ($numSignatures--) {
			$read["signatures"][] = $this->stream->readUint8();
		}

		if($this->replay->baseBuild >= 24247) {
			$read["toonHandle"] = $this->stream->readAlignedBytes($this->stream->readBits(7));
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
			"number" => $this->stream->readBits(3),
			"location" => [
				"x" => $this->stream->readUint16(),
				"y" => $this->stream->readUint16()
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
			"fileName" => $this->stream->readAlignedBytes($this->stream->readBits(11)),
			"automatic" => $this->stream->readBoolean(),
			"overwrite" => $this->stream->readBoolean(),
			"name" => $this->stream->readAlignedBytes($this->stream->readUint8()),
			"description" => $this->stream->readAlignedBytes($this->stream->readBits(10))
		];
	}

	/**
	 * decode a CommandManagerResetEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseCommandManagerResetEvent() {
		return ["sequence" => $this->stream->readUint32()];
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
				"x" => $this->stream->readUint32() - 2147483648,
				"y" => $this->stream->readUint32() - 2147483648
			],
			"time" => $this->stream->readUint32() - 2147483648,
			"verb" => $this->stream->readAlignedBytes($this->stream->readBits(10)),
			"arguments" => $this->stream->readAlignedBytes($this->stream->readBits(10))
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
		$ret["flags"] = $this->stream->readBits($flagsBitLength);

		if($this->replay->baseBuild < 16561 || $this->stream->readBoolean()) {
			$ret["ability"] = [
				"abilityLink" => $this->stream->readUint16(),
				"commandIndex" => $this->stream->readBits($this->replay->baseBuild >= 16561 ? 5 : 8),
				"commandData" => ($this->replay->baseBuild < 16561 || $this->stream->readBoolean() ? $this->stream->readUint8() : null)
			];
		}

		if($this->replay->baseBuild < 16561) {
			$bitsKind = 2;
		} else {
			$bitsKind = $this->stream->readBits(2);
		}

		switch ($bitsKind) {
			case 0:
				$ret["cmdType"] = "CommandEvent";
				break;
			case 1:
				$ret["cmdType"] = "TargetPointCommandEvent";
				$ret["location"] = [
					"x" => $this->stream->readBits(20),
					"y" => $this->stream->readBits(20),
					"z" => $this->stream->readUint32() - 2147483648
				];
				break;
			case 2:
				$ret["cmdType"] = "TargetUnitCommandEvent";
				if($this->replay->baseBuild >= 34784) {
					$ret["targetFlags"] = $this->stream->readUint16();
				} else {
					$ret["targetFlags"] = $this->stream->readUint8();
				}

				$ret["targetTimer"] = $this->stream->readUint8();

				if($this->replay->baseBuild < 16561) {
					//this was in front of the other data in those old versions
					$ret["otherUnitId"] = $this->stream->readUint32();
				}

				$ret["targetUnitId"] = $this->stream->readUint32();
				$ret["targetUnitLink"] = $this->stream->readUint16();

				if($this->replay->baseBuild >= 19595 && $this->stream->readBoolean()) {
					$ret["controlPlayerId"] = $this->stream->readBits(4);
				}

				if($this->stream->readBoolean()) {
					$ret["upkeepPlayerId"] = $this->stream->readBits(4);
				}

				if($this->replay->baseBuild < 16561) {
					$ret["location"] = [
						"x" => $this->stream->readUint32() - 2147483648,
						"y" => $this->stream->readUint32() - 2147483648,
						"z" => $this->stream->readUint32() - 2147483648
					];
				} else {
					$ret["location"] = [
						"x" => $this->stream->readBits(20),
						"y" => $this->stream->readBits(20),
						"z" => $this->stream->readUint32() - 2147483648
					];
				}
				break;
			case 3:
				$ret["cmdType"] = "DataCommandEvent";
				$ret["data"] = $this->stream->readUint32();
				break;
			default:
				die("Unknown bit kind command event {$bitsKind}");
				break;
		}

		if($this->replay->baseBuild >= 34784) {
			$ret["sequence"] = $this->stream->readUint32() + 1;
		}

		if($this->replay->baseBuild >= 16561 && $this->stream->readBoolean()) {
			//this was after the other data in the newer versions
			$ret["otherUnitId"] = $this->stream->readUint32();
		}

		if($this->replay->baseBuild >= 34784 && $this->stream->readBoolean()) {
			$ret["unitGroup"] = $this->stream->readUint32();
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
		$ret["controlGroupIndex"] = $this->stream->readBits(4);
		$ret["subGroupIndex"] = $this->stream->readBits($this->replay->baseBuild >= 22612 ? 9 : 8);
		$ret["removeMask"] = $this->readRemoveBitmask(true);

		$numAddSubGroupEntries = $this->stream->readBits($this->replay->baseBuild >= 22612 ? 9 : 8);
		$ret["addSubGroups"] = [];
		while($numAddSubGroupEntries--) {
			$subGroupentry = ["unitLink" => $this->stream->readUint16()];
			if($this->replay->baseBuild > 23260) {
				$subGroupentry["subGroupPriority"] = $this->stream->readUint8();
			}
			$subGroupentry["intraSubGroupPriority"] = $this->stream->readUint8();
			$subGroupentry["count"] = $this->stream->readBits($this->replay->baseBuild >= 22612 ? 9 : 8);
			$ret["addSubGroups"][] = $subGroupentry;
		}

		$numAddUnitTags = $this->stream->readBits($this->replay->baseBuild >= 22612 ? 9 : 8);
		$ret["addUnitTags"] = [];
		while($numAddUnitTags--) {
			$ret["addUnitTags"][] = $this->stream->readUint32();
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
		if($this->replay->baseBuild <= 15623) {
			//unknown control group update event
			return [];
		}

		return [
			"controlGroupIndex" => $this->stream->readBits(4),
			"updateType" => $this->stream->readBits($this->replay->baseBuild >= 36442 ? 3 : 2),
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
			"controlGroupIndex" => $this->stream->readBits(4),
			"selectionSyncData" => [
				"count" => $this->stream->readBits($this->replay->baseBuild >= 22612 ? 9 : 8),
				"subGroupCount" => $this->stream->readBits($this->replay->baseBuild >= 22612 ? 9 : 8),
				"activeSubgroupIndex" => $this->stream->readBits($this->replay->baseBuild >= 22612 ? 9 : 8),
				"unitTagsChecksum" => $this->stream->readUint32(),
				"subGroupIndicesChecksum" => $this->stream->readUint32(),
				"subGroupCHecksum" => $this->stream->readUint32()
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
			"recipientId" => $this->stream->readBits(4),
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
		return ["message" => $this->stream->readAlignedBytes($this->stream->readBits(10))];
	}

	/**
	 * decode a AICommunicateEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseAICommunicateEvent() {
		$ret = [
			"beacon" => $this->stream->readUint8() - 128,
			"ally" => $this->stream->readUint8() - 128,
			"flags" => $this->stream->readUint8() - 128
		];

		if($this->replay->baseBuild >= 22612) {
			$ret["build"] = $this->stream->readUint8() - 128;
		}

		$ret["targetUnitId"] = $this->stream->readUint32();
		$ret["targetUnitLink"] = $this->stream->readUint16();

		if($this->replay->baseBuild < 22612 && $this->stream->readBoolean()) {
			$ret["targetUpkeepPlayerId"] = $this->stream->readBits(4);
		} else {
			$ret["targetUpkeepPlayerId"] = $this->stream->readUint8() - 128;
		}

		if($this->replay->baseBuild >= 19595 && $this->replay->baseBuild < 22612 && $this->stream->readBoolean()) {
			$ret["targetControlPlayerId"] = $this->stream->readBits(4);
		} else {
			$ret["targetControlPlayerId"] = $this->stream->readUint8() - 128;
		}

		$ret["location"] = [
			"x" => $this->stream->readUint32() - 2147483648,
			"y" => $this->stream->readUint32() - 2147483648,
			"z" => $this->stream->readUint32() - 2147483648
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
		return ["speed" => $this->stream->readBits(3)];
	}

	/**
	 * decode a AddAbsoluteGameSpeedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseAddAbsoluteGameSpeedEvent() {
		return ["speedDelta" => $this->stream->readUint8() - 128];
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
				"x" => $this->stream->readUint32() - 2147483648,
				"y" => $this->stream->readUint32() - 2147483648
			],
			"unitId" => $this->stream->readUint32()
		];

		if($this->replay->baseBuild >= 38215) {
			$read["unitLink"] = $this->stream->readUint16();
			if($this->stream->readBoolean()) {
				$read["unitControlPlayerId"] = $this->stream->readBits(4);
			}
			if($this->stream->readBoolean()) {
				$read["unitUpkeepPlayerId"] = $this->stream->readBits(4);
			}

			$read["unitPosition"] = [
				"x" => $this->stream->readBits(20),
				"y" => $this->stream->readBits(20),
				"z" => $this->stream->readUint32() - 2147483648
			];

			if($this->replay->baseBuild >= 38996) {
				$read["unitUnderConstruction"] = $this->stream->readBoolean();
			}
		}

		$read["pingedMinimap"] = $this->stream->readBoolean();

		if($this->replay->baseBuild >= 34784) {
			$read["option"] = $this->stream->readUint32() - 2147483648;
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
			"verb" => $this->stream->readAlignedBytes($this->stream->readBits(10)),
			"arguments" => $this->stream->readAlignedBytes($this->stream->readBits(10))
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
			"alliance" => $this->stream->readUint32(),
			"control" => $this->stream->readUint32()
		];
	}

	/**
	 * decode a UnitClickEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseUnitClickEvent() {
		return ["unitId" => $this->stream->readUint32()];
	}

	/**
	 * decode a UnitHighlightEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseUnitHighlightEvent() {
		return [
			"unitTag" => $this->stream->readUint32(),
			"flags" => $this->stream->readUint8()
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
			"conversationId" => $this->stream->readUint32() - 2147483648,
			"replyId" => $this->stream->readUint32() - 2147483648
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
		$numUserInfos = $this->stream->readBits(5);
		while ($numUserInfos--) {
			$userInfo = [
				"gameUserId" => $this->stream->readBits(4),
				"observe" => $this->stream->readBits(2),
				"name" => $this->stream->readAlignedBytes($this->stream->readUint8()),
			];

			if($this->stream->readBoolean()) {
				$userInfo["toonHandle"] = $this->stream->readAlignedBytes($this->stream->readBits(7));
			}

			if($this->stream->readBoolean()) {
				$userInfo["clanTag"] = $this->stream->readAlignedBytes($this->stream->readUint8());
			}

			if($this->replay->baseBuild >= 27950 && $this->stream->readBoolean()) {
				$userInfo["clanLogo"] = $this->readCacheHandle();

			}
			$ret["userInfos"][] = $userInfo;
		}
		$ret["method"] = $this->stream->readBits(1);

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
			"soundHash" => $this->stream->readUint32(),
			"length" => $this->stream->readUint32()
		];
	}

	/**
	 * decode a TriggerSoundOffsetEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerSoundOffsetEvent() {
		return ["sound" => $this->stream->readUint32()];
	}

	/**
	 * decode a TriggerTransmissionCompleteEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerTransmissionCompleteEvent() {
		return ["transmissionId" => $this->stream->readUint32() - 2147483648];
	}

	/**
	 * decode a TriggerTransmissionOffsetEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerTransmissionOffsetEvent() {
		return ["transmissionId" => $this->stream->readUint32() - 2147483648];
	}

	/**
	 * decode a player camera event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseCameraUpdateEvent() {
		$ret = [];
		if($this->replay->baseBuild <= 23260 || $this->stream->readBoolean()) {
			$ret["location"] = [
				"x" => $this->stream->readUint16(),
				"y" => $this->stream->readUint16()
			];
		}

		if($this->replay->baseBuild <= 15623) {
			//unknown values, skip 16 more bytes and return
			$this->stream->readAlignedBytes(16);
			return $ret;
		}

		if($this->stream->readBoolean()) {
			$ret["distance"] = $this->stream->readUint16();
		}

		if($this->stream->readBoolean()) {
			$ret["pitch"] = $this->stream->readUint16();
		}

		if($this->stream->readBoolean()) {
			$ret["yaw"] = $this->stream->readUint16();
		}

		if($this->replay->baseBuild >= 27950 && $this->stream->readBoolean()) {
			$ret["reason"] = $this->stream->readUint8() - 128;
		}

		if($this->replay->baseBuild >= 34784) {
			$ret["follow"] = $this->stream->readBoolean();
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
		return ["purchaseItemId" => $this->stream->readUint32() - 2147483648];
	}

	/**
	 * decode a TriggerPlanetMissionLaunchedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerPlanetMissionLaunchedEvent() {
		return ["difficultyLevel" => $this->stream->readUint32() - 2147483648];
	}

	/**
	 * decode a TriggerDialogControlEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerDialogControlEvent() {
		$read = [
			"controlId" => $this->stream->readUint32() - 2147483648,
			"eventType" => $this->stream->readUint32() - 2147483648
		];

		$eventDataType = $this->stream->readBits(3);
		switch ($eventDataType) {
			case 0:
				$read["eventData"] = ["None" => null];
				break;
			case 1:
				$read["eventData"] = ["Checked" => $this->stream->readBoolean()];
				break;
			case 2:
				$read["eventData"] = ["ValueChanged" => $this->stream->readUint32()];
				break;
			case 3:
				$read["eventData"] = ["SelectionChanged" => $this->stream->readUint32() - 2147483648];
				break;
			case 4:
				$read["eventData"] = ["TextChanged" => $this->stream->readAlignedBytes($this->stream->readBits(11))];
				break;
			case 5:
				if($this->replay->baseBuild > 23260) {
					$read["eventData"] = ["MouseButton" => $this->stream->readUint32()];
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

		$numSoundHash = $this->stream->readBits(($this->replay->baseBuild >= 23260 ? 7 : 8));
		while ($numSoundHash--) {
			$read["syncInfo"]["soundHash"][] = $this->stream->readUint32();
		}

		$numLengths = $this->stream->readBits(($this->replay->baseBuild >= 23260 ? 7 : 8));
		while ($numLengths--) {
			$read["syncInfo"]["length"][] = $this->stream->readUint32();
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
		return ["skipType" => $this->stream->readBits(1)];
	}

	/**
	 * decode a TriggerMouseClickedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerMouseClickedEvent() {
		$read = [
			"button" => $this->stream->readUint32(),
			"down" => $this->stream->readBoolean()
		];

		if($this->replay->baseBuild < 17326) {
			$read["positionUI"] = [
				"x" => $this->stream->readUint32(),
				"y" => $this->stream->readUint32()
			];
			$read["positionWorld"] = [
				"x" => $this->stream->readUint32() - 2147483648,
				"y" => $this->stream->readUint32() - 2147483648,
				"z" => $this->stream->readUint32() - 2147483648
			];
		} else {
			$read["positionUI"] = [
				"x" => $this->stream->readBits(11),
				"y" => $this->stream->readBits(11)
			];
			$read["positionWorld"] = [
				"x" => $this->stream->readBits(20),
				"y" => $this->stream->readBits(20),
				"z" => $this->stream->readUint32() - 2147483648
			];
		}

		if($this->replay->baseBuild >= 26490) {
			$read["flags"] = $this->stream->readUint8() - 128;
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
				"x" => $this->stream->readBits(11),
				"y" => $this->stream->readBits(11)
			],
			"positionWorld" => [
				"x" => $this->stream->readBits(20),
				"y" => $this->stream->readBits(20),
				"z" => $this->stream->readUint32() - 2147483648
			]
		];

		if($this->replay->baseBuild >= 26490) {
			$read["flags"] = $this->stream->readUint8() - 128;
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
		return ["achievementLink" => $this->stream->readUint16()];
	}

	/**
	 * decode a TriggerHotkeyPressedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerHotkeyPressedEvent() {
		return [
			"hotkey" => $this->stream->readUint32(),
			"down" => $this->stream->readBoolean()
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
			"abilityLink" => $this->stream->readUint16(),
			"abilityCommandIndex" => $this->stream->readBits(5),
			"state" => $this->stream->readUint8() - 128
		];
	}

	/**
	 * decode a TriggerSoundtrackDoneEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerSoundtrackDoneEvent() {
		return ["soundtrack" => $this->stream->readUint32()];
	}

	/**
	 * decode a PlanetMissionSelectedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerPlanetMissionSelectedEvent() {
		return ["planetId" => $this->stream->readUint32() - 2147483648];
	}

	/**
	 * decode a TriggerKeyPressedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerKeyPressedEvent() {
		return [
			"key" => $this->stream->readUint8() - 128,
			"flags" => $this->stream->readUint8() - 128
		];
	}

	/**
	 * decode a TriggerMovieFunctionEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerMovieFunctionEvent() {
		return ["functionName" => $this->stream->readAlignedBytes($this->stream->readBits(7))];
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
		return ["requestId" => $this->stream->readUint32() - 2147483648];
	}

	/**
	 * decode a ResourceRequestFulfillEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseResourceRequestFulfillEvent() {
		return ["requestId" => $this->stream->readUint32() - 2147483648];
	}

	/**
	 * decode TriggerMercenaryPanelSelectionChangedEvent
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerMercenaryPanelSelectionChangedEvent() {
		return ["itemId" => $this->stream->readUint32() - 2147483648];
	}

	/**
	 * decode PurchasePanelSelectedPurchaseItemChangedEvent
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parsePurchasePanelSelectedPurchaseItemChangedEvent() {
		return ["itemId" => $this->stream->readUint32() - 2147483648];
	}

	/**
	 * decode TriggerResearchPanelSelectionChangedEvent
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerResearchPanelSelectionChangedEvent() {
		return ["itemId" => $this->stream->readUint32() - 2147483648];
	}

	/**
	 * decode a TriggerCommandErrorEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerCommandErrorEvent() {
		$read = ["error" =>  $this->stream->readUint32() - 2147483648];
		if($this->stream->readBoolean()) {
			$read["ability"] = [
				"abilityLink" => $this->stream->readUint16(),
				"abilityCommandIndex" => $this->stream->readBits(5)
			];
			if($this->stream->readBoolean()) {
				$read["ability"]["abilityCommandData"] = $this->stream->readUint8();
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
			"battleReportId" => $this->stream->readUint32() - 2147483648,
			"difficultyLevel" => $this->stream->readUint32() - 2147483648
		];
	}

	/**
	 * decode a TriggerBattleReportPanelPlaySceneEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerBattleReportPanelPlaySceneEvent() {
		return ["battleReportId" => $this->stream->readUint32() - 2147483648];
	}

	/**
	 * decode a BattleReportPanelSelectionChangedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerBattleReportPanelSelectionChangedEvent() {
		return ["battleReportId" => $this->stream->readUint32() - 2147483648];
	}

	/**
	 * decode a TriggerVictoryPanelPlayMissionAgainEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerVictoryPanelPlayMissionAgainEvent() {
		return ["difficultyLevel" => $this->stream->readUint32() - 2147483648];
	}

	/**
	 * decode a DecrementGameTimeRemainingEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseDecrementGameTimeRemainingEvent() {
		if($this->replay->baseBuild >= 16561 && $this->replay->baseBuild < 41743) {
			return ["decrementMs" => $this->stream->readBits(19)];
		} else {
			return ["decrementMs" => $this->stream->readUint32()];
		}
	}

	/**
	 * decode a TriggerPortraitLoadedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerPortraitLoadedEvent() {
		return ["portraitId" => $this->stream->readUint32() - 2147483648];
	}

	/**
	 * decode a TriggerCustomDialogDismissedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerCustomDialogDismissedEvent() {
		return ["result" => $this->stream->readUint32() - 2147483648];
	}

	/**
	 * decode a TriggerGameMenuItemSelectedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerGameMenuItemSelectedEvent() {
		return ["gameMenuItemIndex" => $this->stream->readUint32() - 2147483648];
	}

	/**
	 * decode a TriggerMouseWheelEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerMouseWheelEvent() {
		return [
			"wheelSpin" => $this->stream->readUint16() - 32768,
			"flags" => $this->stream->readUint8() - 128
		];
	}

	/**
	 * decode a TriggerPurchasePanelSelectedPurchaseCategoryChangedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerPurchasePanelSelectedPurchaseCategoryChangedEvent() {
		return ["categoryId" => $this->stream->readUint32() - 2147483648];
	}

	/**
	 * decode a TriggerButtonPressedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerButtonPressedEvent() {
		return ["button" => $this->stream->readUint16()];
	}

	/**
	 * decode a TriggerCutsceneBookmarkFiredEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerCutsceneBookmarkFiredEvent() {
		return [
			"cutsceneId" => $this->stream->readUint32() - 2147483648,
			"bookmarkName" => $this->stream->readAlignedBytes($this->stream->readBits(7))
		];
	}

	/**
	 * decode a TriggerCutsceneEndSceneFiredEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerCutsceneEndSceneFiredEvent() {
		return ["cutsceneId" => $this->stream->readUint32() - 2147483648];
	}

	/**
	 * decode a TriggerCutsceneConversationLineEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerCutsceneConversationLineEvent() {
		return [
			"cutsceneId" => $this->stream->readUint32() - 2147483648,
			"conversationLine" => $this->stream->readAlignedBytes($this->stream->readBits(7)),
			"altConversationLine" => $this->stream->readAlignedBytes($this->stream->readBits(7))
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
			"cutsceneId" => $this->stream->readUint32() - 2147483648,
			"conversationLine" => $this->stream->readAlignedBytes($this->stream->readBits(7))
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
			return ["leaveReason" => $this->stream->readBits(4)];
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
			"observe" => $this->stream->readBits(2),
			"name" => $this->stream->readAlignedBytes($this->stream->readBits(8)),
		];
		if($this->stream->readBoolean()) {
			$ret["toonHandle"] = $this->stream->readAlignedBytes($this->stream->readBits(7));
		}
		if($this->stream->readBoolean()) {
			$ret["clanTag"] = $this->stream->readAlignedBytes($this->stream->readBits(8));
		}
		if($this->replay->baseBuild >= 27950 && $this->stream->readBoolean()) {
			$ret["clanLogo"] = $this->parseCacheHandle();
		}
		if($this->replay->baseBuild >= 34784) {
			$ret["hijack"] = $this->stream->readBoolean();
			if($this->stream->readBoolean()) {
				$ret["hijackCloneGameUserId"] = $this->stream->readBits(4);
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
		$read = ["state" => $this->stream->readBits(2)];
		if($this->stream->readBoolean()) {
			$read["sequence"] = $this->stream->readUint32() + 1;
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
		return ["reason" => $this->stream->readUint8() - 128];
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
				"x" => $this->stream->readBits(20),
				"y" => $this->stream->readBits(20),
				"z" => $this->stream->readUint32() - 2147483648
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
			"targetUnitFlags" => $this->stream->readUint16(),
			"timer" => $this->stream->readUint8(),
			"tag" => $this->stream->readUint32(),
			"snapshotUnitLink" => $this->stream->readUint16()
		];

		if($this->stream->readBoolean()) {
			$read["snapshotControlPlayerId"] = $this->stream->readBits(4);
		}

		if($this->stream->readBoolean()) {
			$read["snapshotUpkeepPlayerId"] = $this->stream->readBits(4);
		}

		$read["snapshotLocation"] = [
			"x" => $this->stream->readBits(20),
			"y" => $this->stream->readBits(20),
			"z" => $this->stream->readUint32() - 2147483648
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
			"queryId" => $this->stream->readUint16(),
			"lengthMs" => $this->stream->readUint32(),
			"finishGameLoop" => $this->stream->readUint32()
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
			"queryId" => $this->stream->readUint16(),
			"lengthMs" => $this->stream->readUint32()
		];
	}

	/**
	 * decode a TriggerAnimOffsetEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseTriggerAnimOffsetEvent() {
		return ["animWaitQueryId" => $this->stream->readUint16()];
	}

	/**
	 * decode a CatalogModifyEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseCatalogModifyEvent() {
		return [
			"catalog" => $this->stream->readUint8(),
			"entry" => $this->stream->readUint16(),
			"field" => $this->stream->readAlignedBytes($this->readUint8()),
			"value" => $this->stream->readAlignedBytes($this->stream->readUint8())
		];
	}

	/**
	 * decode a HeroTalentTreeSelectedEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseHeroTalentTreeSelectedEvent() {
		return ["index" => $this->stream->readUint32()];
	}

	/**
	 * decode a HeroTalentTreeSelectionPanelToggledEvent event
	 *
	 * @access private
	 * @return array with parsed event data
	 */
	private function parseHeroTalentTreeSelectionPanelToggledEvent() {
		return ["shown" => $this->stream->readBoolean()];
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
	 * @param  boolean $forceMask Boolean allowing selectiondeltaevents to be treated like newer versions
	 * @return array with the removal mask read from the stream
	 */
	private function readRemoveBitmask($forceMask = false) {
		if($this->replay->baseBuild >= 16561) {
			$removeMask = $this->stream->readBits(2);
		} else {
			$removeMask = 1;
		}

		switch ($removeMask) {
			case 0:
				$removeMask = ["None" => null];
				break;
			case 1:
				if($this->replay->baseBuild >= 16561 || $forceMask || $this->stream->readBoolean()) {
					$numBits = $this->stream->readBits($this->replay->baseBuild >= 22612 ? 9 : 8);
					$removeMask = ["Mask" => $this->stream->readBits($numBits)];
					if($removeMask["Mask"] === "") {
						$removeMask["Mask"] = 0;
					}
				} else {
					$removeMask = ["None" => null];
				}
				break;
			case 2:
				$removeMask = ["OneIndices" => []];
				$numUnitTag = $this->stream->readBits($this->replay->baseBuild >= 22612 ? 9 : 8);
				while($numUnitTag--) {
					$removeMask["OneIndices"][] = $this->stream->readBits($this->replay->baseBuild >= 22612 ? 9 : 8);
				}
				break;
			case 3:
				$removeMask = ["ZeroIndices" => []];
				$numUnitTag = $this->stream->readBits($this->replay->baseBuild >= 22612 ? 9 : 8);
				while($numUnitTag--) {
					$removeMask["ZeroIndices"][] = $this->stream->readBits($this->replay->baseBuild >= 22612 ? 9 : 8);
				}
				break;
			default:
				throw new ParserException("Strange selection delta mask id", 100);
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
		$numResources = $this->stream->readBits(3);
		for ($i=0; $i < $numResources; $i++) {
			$resCount = $this->stream->readUint32() - 2147483648;
			if(!isset($resourceNames[$i])) {
				throw new ParserException("Unknown resource: '{$resCount}'", 200);
			}
			$resources[$resourceNames[$i]] = $resCount;
		}

		return $resources;
	}

}
