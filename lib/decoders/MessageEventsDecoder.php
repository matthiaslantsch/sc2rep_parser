<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the MessageEventsDecoder decoder class
 */

namespace HIS5\lib\Sc2repParser\decoders;

use HIS5\lib\Sc2repParser\data\DataLoader;
use HIS5\lib\Sc2repParser\events as events;

/**
 * The MessageEventsDecoder class is used to decode the replay.message.events file contained in the replay archive
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\decoders
 */
class MessageEventsDecoder extends BitwiseDecoderBase {

	/**
	 * decode the replay.message.events file contained in the replay
	 *
	 * @access protected
	 */
	protected function doDecode() {
		$loopCount = 0;
		$messages = [];

		while(!$this->eof()) {
			$loopCount += $this->readLoopCount();

			$messageEvent = [
				"gameloop" => $loopCount,
				"playerId" => $this->readBits(5)
			];

			$flag = $this->readBits(4);
			switch ($flag) {
				case 0: //chat message
					$messageEvent["eventtype"] = "ChatMessage";
					$messageEvent["recipient"] = $this->readBits($this->replay->baseBuild >= 21955 ? 3 : 2);
					$messageEvent["msg"] = $this->readAlignedBytes($this->readBits(11));
					break;
				case 1: //player ping
					$messageEvent["eventtype"] = "PingMessage";
					$messageEvent["recipient"] = $this->readBits($this->replay->baseBuild >= 21955 ? 3 : 2);
					$x = $this->readUint32() - 2147483648;
					$y = $this->readUint32() - 2147483648;
					$messageEvent["location"] = ["x" => $x, "y" => $y];
					break;
				case 2: //loading progress
					$messageEvent["eventtype"] = "LoadingProgressMessage";
					$messageEvent["progress"] = $this->readUint32() - 2147483648;
					break;
				case 3: //server ping
					$messageEvent["eventtype"] = "ServerPingMessage";
					break;
				case 4: //reconnect
					$messageEvent["eventtype"] = "ReconnectNotifyMessage";
					$messageEvent["status"] = $this->readBits(2);
					break;
				default:
					die("Unknown message event type: {$flag}");
			}

			$messages[] = $messageEvent;
			$this->align();
		}
		$this->replay->rawdata["messages"] = $messages;
	}

}