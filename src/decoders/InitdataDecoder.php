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

use holonet\Sc2repParser\ParserException;

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
		$data = $this->binaryFormatParse("initdata")["syncLobbyState"];

		$this->replay->rawdata["initdata"] = $data;

		$options = $data["gameDescription"]["gameOptions"];
		$this->replay->amm = $options["amm"];
		$this->replay->ranked = isset($options["ranked"]) ? $options["ranked"] : false;
		$this->replay->competitive = isset($options["competitive"]) ? $options["competitive"] : false;
		$this->replay->practice = isset($options["practice"]) ? $options["practice"] : false;
		$this->replay->cooperative = isset($options["cooperative"]) ? $options["cooperative"] : false;
		$this->replay->battlenet = $options["battleNet"];

	}

}
