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
		$this->replay->rawdata["message.events"] = $this->binaryFormatParse("messageevents");
	}

}
