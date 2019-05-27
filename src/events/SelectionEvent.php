<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the SelectionEvent logic class
 */

namespace HIS5\lib\Sc2repParser\events;

/**
 * A SelectionEvent is fired whenever the selection of the player changes
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\events
 */
class SelectionEvent extends EventBase {

	/**
	 * property containing the control group index being modified
	 * 10 for active selection
	 *
	 * @access 	public
	 * @var 	integer controlGroupIndex | the control group index being modified
	 */
	public $controlGroupIndex;

	/**
	 * property containing the subgroup index ?!?!?
	 *
	 * @access 	public
	 * @var 	integer subgroupIndex | the subgroup index
	 */
	public $subgroupIndex;

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
	 * property containing an array with info about the newly added units
	 *
	 * @access 	public
	 * @var 	array newUnitInfo | array with info about the newly added units
	 */
	public $newUnitInfo;

	/**
	 * constructor accepting the gameloop count
	 *
	 * @access public
	 * @param  integer gameloops | gameloop counter at the time the event happened
	 * @param  integer playerId | playerId of the player that caused the event
	 * @param  integer controlGroup | the control group index being modified
	 * @param  integer subgroupIndex | the subgroup index
	 * @param  array removeMask | removal mask array with the mask type as key, and the mask data as value
	 * @param  array addSubGroups | array with unit type data for the new units
	 * @param  array addUnitTags | array with unit tags being added
	 */
	public function __construct($gameloops, $playerId, $controlGroup, $subgroupIndex, $removeMask, $addSubGroups, $addUnitTags) {
		parent::__construct($gameloops, $playerId);

		$this->controlGroup = $controlGroup;
		$this->subgroupIndex = $subgroupIndex;
		$this->removeMask = $removeMask;
		$this->newUnitInfo = [];
		foreach ($addSubGroups as $i => $unitTypeInfo) {
			$unitTypeInfo["unitTag"] = $addUnitTags[$i];
			$this->newUnitInfo[] = $addUnitTags;
		}
	}

}