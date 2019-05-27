<?php
/**
 * This file is part of the holonet sc2 replay parser library
 * (c) Matthias Lantsch
 *
 * class file for the MessageEventsDecoder decoder class
 *
 * @package holonet sc2 replay parser library
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\Sc2repParser\decoders;

use holonet\Sc2repParser\ParserException;

/**
 * The MessageEventsDecoder class is used to decode the replay.message.events file contained in the replay archive
 *
 * @author  matthias.lantsch
 * @package holonet\Sc2repParser\decoders
 */
class MessageEventsDecoder extends DecoderBase {

	/**
	 * decode the replay.message.events file contained in the replay
	 * saves the raw events right into the replay
	 *
	 * @access protected
	 * @return void
	 */
	protected function doDecode() {
		$loopCount = 0;
		$messages = [];

		while(!$this->stream->eof()) {
			$loopCount += $this->readLoopCount();

			$messageEvent = [
				"gameloop" => $loopCount,
				"playerId" => $this->stream->readBits(5)
			];

			$flag = $this->stream->readBits(4);

			switch ($flag) {
				case 0: //chat message
					$messageEvent["eventtype"] = "ChatMessage";
					$messageEvent["recipient"] = $this->stream->readBits($this->replay->baseBuild >= 21955 ? 3 : 2);
					$messageEvent["msg"] = $this->stream->readAlignedString($this->stream->readBits(11));
					break;
				case 1: //player ping
					$messageEvent["eventtype"] = "PingMessage";
					$messageEvent["recipient"] = $this->stream->readBits($this->replay->baseBuild >= 21955 ? 3 : 2);
					$x = $this->stream->readUint32() - 2147483648;
					$y = $this->stream->readUint32() - 2147483648;
					$messageEvent["location"] = ["x" => $x, "y" => $y];
					break;
				case 2: //loading progress
					$messageEvent["eventtype"] = "LoadingProgressMessage";
					$messageEvent["progress"] = $this->stream->readUint32() - 2147483648;
					break;
				case 3: //server ping
					$messageEvent["eventtype"] = "ServerPingMessage";
					break;
				case 4: //reconnect
					$messageEvent["eventtype"] = "ReconnectNotifyMessage";
					$messageEvent["status"] = $this->stream->readBits(2);
					break;
				default:
					throw new ParserException("Unknown message event type: {$flag} in version {$this->replay->baseBuild}", 100);
			}

			$messages[] = $messageEvent;
			$this->stream->align();
		}
		$this->replay->rawdata["replay.message.events"] = $messages;
	}

}
