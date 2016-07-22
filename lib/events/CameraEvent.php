<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the CameraEvent logic class
 */

namespace HIS5\lib\Sc2repParser\events;

/**
 * CameraEvent is an event generated when a user moves his camera
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\events
 */
class CameraEvent extends EventBase {

	/**
	 * property containing an array with the location described as 2d point
	 *
	 * @access 	public
	 * @var 	array location | array with x and y coordinate
	 */
	public $location;

	/**
	 * property containing the distance to the camera target ??
	 *
	 * @access 	public
	 * @var 	integer distance | distance to the camera target
	 */
	public $distance;

	/**
	 * property containing the current pitch of the camera
	 *
	 * @access 	public
	 * @var 	integer pitch | current pitch of the camera
	 */
	public $pitch;

	/**
	 * property containing the current yaw of the camera
	 *
	 * @access 	public
	 * @var 	integer yaw | current yaw of the camera
	 */
	public $yaw;

	/**
	 * constructor accepting the frame count
	 *
	 * @access public
	 * @param  integer frames | frame counter at the time the event happened
	 * @param  integer playerId | playerId of the player that caused the event
	 * @param  array location | array with x and y coordinate
	 */
	public function __construct($frames, $playerId, $location) {
		parent::__construct($frames, $playerId);
		$this->location = $location;
	}

}