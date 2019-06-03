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
	 * decode the replay.game.events file contained in the replay
	 * saves the data directly into the replay object
	 *
	 * @access protected
	 * @return void
	 */
	protected function doDecode() {
		$this->replay->rawdata["game.events"] = $this->binaryFormatParse("gameevents");
	}

}
