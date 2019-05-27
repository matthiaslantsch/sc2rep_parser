<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the ControlGroupEvent logic class
 */

namespace HIS5\lib\Sc2repParser\events;

/**
 * A ControlGroupEvent is fired whenever a control group is changed/recalled
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\events
 */
class ControlGroupEvent extends EventBase {

	/**
	 * property containing the control group index being modified
	 *
	 * @access 	public
	 * @var 	integer controlGroupIndex | the control group index being modified
	 */
	public $controlGroupIndex;

	/**
	 * property containing the data for the removal mask (maskType as key, maskData as value)
	 *  "None" => null (no data)
	 *  "Mask" => integer with a bitmask where every bit indice is an include or exclude on a unit
	 *  "OneIndices" => array with integer indices being removed
	 *  "ZeroIndices" => array with integer indices marking the units to stay, the others will be removed
	 *
	 * @access 	public
	 * @var 	array removeMask | removal mask array with the mask type as key, and the mask data as value
	 */
	public $removeMask;

	/**
	 * property containing a string determing which kind of update is performed on the control group
	 * One of: Set, AddTo, Get, Unknown
	 *
	 * @access 	public
	 * @var 	string action | string determing the kind of update
	 */
	public $action = "Unknown";

	/**
	 * constructor accepting the gameloop count
	 *
	 * @access public
	 * @param  integer gameloops | gameloop counter at the time the event happened
	 * @param  integer playerId | playerId of the player that caused the event
	 * @param  integer controlGroup | the control group index being modified
	 * @param  array removeMask | removal mask array with the mask type as key, and the mask data as value
	 * @param  integer actionType | integer determing the kind of update performed on the group
	 */
	public function __construct($gameloops, $playerId, $controlGroup, $removeMask, $actionType) {
		parent::__construct($gameloops, $playerId);

		$this->controlGroup = $controlGroup;
		$this->removeMask = $removeMask;

		if($actionType == 0) {
			$this->action = "Set";
		} elseif($actionType == 1) {
			$this->action = "AddTo";
		} elseif($actionType == 2) {
			$this->action = "Get";
		}
	}

}