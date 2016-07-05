<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the HeaderDecoder decoder class
 */

namespace HIS5\lib\Sc2repParser\decoders;

/**
 * The HeaderDecoder class is used to decode the header in an mpq replay archive file:
 *  - game version
 *  - game frame counter
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\decoders
 */
class HeaderDecoder extends BitwiseDecoderBase {

	/**
	 * actually decode the header
	 * usually decoded data is directly saved, exception here since the replay object doesn't exist yet
	 *
	 * @access protected
	 * @return array with decoded header data
	 */
	protected function doDecode() {
		$ret = [];
		$headerData = $this->parseSerializedData();
		$ret["baseBuild"] = $headerData[1][5];
		$ret["versionString"] = sprintf(
			"%d.%d.%d.%d", //major.minor.fix.build
			$headerData[1][1], //major version number
			$headerData[1][2],//minor version number
			$headerData[1][3],//fix version number
			$headerData[1][4] //the build
		);
		$ret["frames"] = $headerData[3];

		return $ret;
	}

}