<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the TargetUnitCommandEvent logic class
 */

namespace HIS5\lib\Sc2repParser\events;

/**
 * TargetUnitCommandEvent is an event generated for player commands that targets a unit
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\events
 */
class TargetUnitCommandEvent extends TargetPointCommandEvent {

	/**
	 * property containing the flags to be applied to the target
	 *
	 * @access 	public
	 * @var 	integer targetFlags | flags applied to the target unit
	 */
	public $targetFlags;

	/**
	 * property containing a target timer
	 *
	 * @access 	public
	 * @var 	integer targetTimer | timer on the target
	 */
	public $targetTimer;

	/**
	 * property containing the unique identifier of the target unit
	 * is 0 if the unit is in the fog of war
	 *
	 * @access 	public
	 * @var 	integer targetUnitId | unique identifier of the target unit
	 */
	public $targetUnitId;

	/**
	 * property containing the player id of the controlling player
	 *
	 * @access 	public
	 * @var 	integer controlPlayerId | player id of the player controlling the unit
	 */
	public $controlPlayerId;

	/**
	 * property containing the player id of the player paying upkeep
	 *
	 * @access 	public
	 * @var 	integer upkeepPlayerId | player id of the player paying upkeep
	 */
	public $upkeepPlayerId;

}