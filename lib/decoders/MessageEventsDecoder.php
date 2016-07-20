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

		$totalFrame = 0;
		$messages = [];

		while(!$this->eof()) {
			$totalFrame += $this->readFrameCount();
			$playerId = $this->readBits(5);
			$flag = $this->readBits(4);
			switch ($flag) {
				case 0: //chat message
					$recipient = $this->readBits($this->replay->baseBuild >= 21955 ? 3 : 2);
					$msg = $this->readAlignedBytes($this->readBits(11));
					$messages[] = new events\ChatEvent($totalFrame, $playerId, $recipient, $msg);
					break;
				case 1: //player ping
					$recipient = $this->readBits($this->replay->baseBuild >= 21955 ? 3 : 2);
					$x = $this->readUint32() - 2147483648;
					$y = $this->readUint32() - 2147483648;
					$messages[] = new events\PingEvent($totalFrame, $playerId, $recipient, ["x" => $x, "y" => $y]);
					break;
				case 2: //loading progress
					$progress = $this->readUint32() - 2147483648;
					$messages[] = new events\ProgressEvent($totalFrame, $playerId, $progress);
					break;
				case 3: //server ping
					die(var_dump("SERVER PING"));
					break;
				case 4: //reconnect
					die(var_dump("SERVER PING"));
					$status = $this->readBits(2);
					break;
				default:
					break;
			}
			$this->align();
		}
		$this->replay->messages = $messages;
	}

}