<?php
/**
 * This file is part of the holonet sc2 replay parser library
 * (c) Matthias Lantsch
 *
 * class file for the AttributeEventsDecoder decoder class
 *
 * @package holonet sc2 replay parser library
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\Sc2repParser\decoders;

use holonet\Sc2repParser\ParserException;
use holonet\Sc2repParser\data\DataLoader;

/**
* The AttributeEventsDecoder class is used to decode the replay.attributes.events file contained in the replay archive
 *
 * @author  matthias.lantsch
 * @package holonet\Sc2repParser\decoders
 */
class AttributeEventsDecoder extends DecoderBase {

	/**
	 * decode the replay.attributes.events file contained in the replay
	 *
	 * @access protected
	 * @return void
	 */
	protected function doDecode() {
		$attributeLookup = DataLoader::loadDataset("attributes", $this->replay->baseBuild);

		//skip start bytes
		$this->stream->readBytes($this->replay->baseBuild >= 17326 ? 5 : 4);

		$numAttributes = $this->stream->readUint32(false);
		$attributes = [];
		while ($numAttributes--) {
			$attr = [];
			$attr["header"] = $this->stream->readUint32(false);
			$attr["attrId"] = $this->stream->readUint32(false);
			$attr["playerId"] = $this->stream->readUint8();
			$val = trim(strrev($this->stream->readAlignedBytes(4)));

			if(isset($attributeLookup[$attr["attrId"]])) {
				$lookup = $attributeLookup[$attr["attrId"]];
				$attr["name"] = $lookup[0];
				$attr["value"] = $lookup[1][$val];
			} else {
				continue;
				throw new ParserException("Unknown attribute id '{$attr["attrId"]}'", 100);
			}

			//save it in our temporary array
			$attributes[$attr["playerId"]][$attr["name"]] = $attr["value"];
		}

		$this->replay->attributes = $attributes;
		$this->replay->gamespeed = $attributes[16]["Game Speed"];
		$this->replay->category = $attributes[16]["Game Mode"];
		$this->replay->gametype = $attributes[16]["Teams"];
		if(!isset($attributes[16]["Game Privacy"])) {
			$attributes[16]["Game Privacy"] = "Normal";
		}

		$this->replay->privacy = $attributes[16]["Game Privacy"];
	}

}
