<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the CameraSaveEvent logic class
 */

namespace HIS5\lib\Sc2repParser\events;

/**
 * CameraSaveEvent is an event that is being triggered when a location hotkey is being set
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\events
 */
class CameraSaveEvent extends EventBase {

	/**
	 * property containing the location hotkey number
	 *
	 * @access 	public
	 * @var 	string number | the camera hotkey number
	 */
	public $number;

	/**
	 * property containing the saved location on the map
	 *
	 * @access 	public
	 * @var 	array location | array with the location with the x and the y index
	 */
	public $location;

	/**
	 * constructor accepting the frame count
	 *
	 * @access public
	 * @param  integer frames | frame counter at the time the event happened
	 * @param  integer playerId | playerId of the player that caused the event
	 * @param  integer number | integer describing the number of which hotkey was set
	 * @param  array location | array with the location with the x and the y index
	 */
	public function __construct($frames, $playerId, $number, $location) {
		parent::__construct($frames, $playerId);

		$this->number = $number;
		$this->location = $location;
	}

}