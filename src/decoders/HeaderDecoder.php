<?php
/**
 * This file is part of the holonet sc2 replay parser library
 * (c) Matthias Lantsch
 *
 * class file for the HeaderDecoder decoder class
 *
 * @package holonet sc2 replay parser library
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\Sc2repParser\decoders;

use holonet\Sc2repParser\format\VersionBase;
use holonet\bitstream\format\BinaryFormatParser;

/**
 * The HeaderDecoder class is used to decode the header in an mpq replay archive file:
 *  - game version
 *  - game loops counter
 *
 * @author  matthias.lantsch
 * @package holonet\Sc2repParser\decoders
 */
class HeaderDecoder extends DecoderBase {

	/**
	 * actually decode the header
	 * usually decoded data is directly saved, not here since the replay object doesn't exist yet
	 *
	 * @access protected
	 * @return array with decoded header data
	 */
	protected function doDecode() {
		$ret = array();

		//try to decode with the new packed header format
		$formatTree = VersionBase::getFileFormat("header");
		$parser = new BinaryFormatParser($this->stream, $formatTree);
		$data = $parser->parse();

		if(isset($data["signature"])) {
			$data["versionString"] = sprintf(
				"%d.%d.%d.%d", //major.minor.fix.build
				$data["version"]["major"], //major version number
				$data["version"]["minor"],//minor version number
				$data["version"]["revision"],//fix version number
				$data["version"]["build"] //the build
			);
		} else {
			//must be an older replay, try again with the old parser
			$this->stream->rewind();
			$formatTree = VersionBase::getFileFormat("oldheader");
			$parser = new BinaryFormatParser($this->stream, $formatTree);
			$data = $parser->parse();
		}

		if($data["version"]["baseBuild"] < 16195) {
			$data["expansion"] = "WoL Beta";
		} elseif($data["version"]["baseBuild"] <= 25092) {
			$data["expansion"] = "WoL";
		} elseif($data["version"]["baseBuild"] <= 25180) {
			$data["expansion"] = "HotS Beta";
		} elseif($data["version"]["baseBuild"] <= 38749) {
			$data["expansion"] = "HotS";
		} elseif($data["version"]["baseBuild"] <= 38996) {
			$data["expansion"] = "LotV Beta";
		} else {
			$data["expansion"] = "LotV";
		}

		return $data;
	}

}
