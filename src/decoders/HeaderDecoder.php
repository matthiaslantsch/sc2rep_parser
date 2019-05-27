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
	 * usually decoded data is directly saved, exception here since the replay object doesn't exist yet
	 *
	 * @access protected
	 * @return array with decoded header data
	 */
	protected function doDecode() {
		$ret = [];

		$firstByte = $this->stream->readUint8();
		if($firstByte < 10) {
			$headerData = $this->parseSerializedData($firstByte);
			$ret["baseBuild"] = $headerData[1][5];
			$ret["versionString"] = sprintf(
				"%d.%d.%d.%d", //major.minor.fix.build
				$headerData[1][1], //major version number
				$headerData[1][2],//minor version number
				$headerData[1][3],//fix version number
				$headerData[1][4] //the build
			);
			$ret["gameloops"] = $headerData[3];
		} else {
			//fallback because old beta replay version
			$ret = $this->oldVersionDecode();
		}

		if($ret["baseBuild"] < 16195) {
			$ret["expansion"] = "WoL Beta";
		} elseif($ret["baseBuild"] <= 25092) {
			$ret["expansion"] = "WoL";
		} elseif($ret["baseBuild"] <= 25180) {
			$ret["expansion"] = "HotS Beta";
		} elseif($ret["baseBuild"] <= 38749) {
			$ret["expansion"] = "HotS";
		} elseif($ret["baseBuild"] <= 38996) {
			$ret["expansion"] = "LotV Beta";
		} else {
			$ret["expansion"] = "LotV";
		}

		return $ret;
	}

	/**
	 * small helper function used to decode older beta version replays
	 *
	 * @access protected
	 * @return array with decoded header data
	 */
	private function oldVersionDecode() {
		$this->stream->readBytes(23); // skip Starcraft II replay 0x1B 0x32 0x01 0x00

		//we assume its a replay from before phase 2
		$verMajor = $this->stream->readUint16(false);
		$build = $this->stream->readUint32();
		$ret["baseBuild"] = $this->stream->readUint32();
		$this->stream->readBytes(2); //skip 0200
		//apparently saved in seconds back then => times 16
		$ret["gameloops"] = intval($this->stream->readUint16() / 2) * 16;
		$ret["versionString"] = "0.{$verMajor}.0.{$build}";

		return $ret;
	}

}
