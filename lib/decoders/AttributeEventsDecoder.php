<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the AttributeEventsDecoder decoder class
 */

namespace HIS5\lib\Sc2repParser\decoders;

use HIS5\lib\Sc2repParser\data\DataLoader;
use \HIS5\lib\Sc2repParser\ParserException;


/**
 * The AttributeEventsDecoder class is used to decode the replay.attributes.events file contained in the replay archive
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\decoders
 */
class AttributeEventsDecoder extends BitwiseDecoderBase {

	/**
	 * decode the replay.attributes.events file contained in the replay
	 *
	 * @access protected
	 */
	protected function doDecode() {
		$attributeLookup = DataLoader::loadDataset("attributes", $this->replay->baseBuild);

		//skip start bytes
		$this->readBytes($this->replay->baseBuild >= 17326 ? 5 : 4);

		$numAttributes = $this->readUint32(false);
		$attributes = [];
		while ($numAttributes--) {
			$attr = [];
			$attr["header"] = $this->readUint32(false);
			$attr["attrId"] = $this->readUint32(false);
			$attr["playerId"] = $this->readUint8();
			$val = trim(strrev($this->readAlignedBytes(4)));

			if(isset($attributeLookup[$attr["attrId"]])) {
				$lookup = $attributeLookup[$attr["attrId"]];
				$attr["name"] = $lookup[0];
				$attr["value"] = $lookup[1][$val];
			} else {
				continue;
				//throw new ParserException("Unknown attribute id '{$attr["attrId"]}'", 100);
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