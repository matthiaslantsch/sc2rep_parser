<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the TargetPointCommandEvent logic class
 */

namespace HIS5\lib\Sc2repParser\events;

/**
 * TargetPointCommandEvent is an event generated for player commands that point to a location
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\events
 */
class TargetPointCommandEvent extends CommandEvent {

	/**
	 * property containing the command target location on the map
	 *
	 * @access 	public
	 * @var 	array location | array with the location with the x, y an z index
	 */
	public $location;

}