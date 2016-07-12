<?php
/**
 * This file is part of the hdev activerecord library
 * (c) Matthias Lantsch
 *
 * class file for the Observer logic class
 */

namespace HIS5\lib\Sc2repParser\objects;

use HIS5\lib\Sc2repParser\utils as utils;

/**
 * the Observer class represents an observer in game (can be referee or not)
 * 
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\objects
 */
class Observer extends Entity {

	/**
	 * flag marking this observer as a referee or not
	 *
	 * @access  public
	 * @var     boolean isReferee | flag used to mark this as a referee
	 */
	public $isReferee;

	/**
	 * constructor method for the Observer object accepting the slot initData
	 *
	 * @access public
	 * @param  integer pid | player id for this entity
	 * @param  array slotData | slotData coming from the replay.initdata file
	 * @param  array initData | initdata coming from the replay.initdata file
	 */
	public function __construct($pid, $slotData, $initData) {
		parent::__construct($pid, $slotData, $initData);

		//observers have no details data, so this fallback is necessary
		if(!isset($slotData["toonHandle"])) {
			$slotData["toonHandle"] = "0-S2-0-0";
		}

		$parts = split("-", $slotData["toonHandle"]);

		$this->region = utils\gatewayLookup($parts[0]);
		$this->subregion = $parts[2];
		$this->bnetId = $parts[3];

		$this->isReferee = ($slotData["observe"] == 2);
	}

}