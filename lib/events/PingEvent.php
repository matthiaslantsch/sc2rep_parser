<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the PingEvent logic class
 */

namespace HIS5\lib\Sc2repParser\events;

/**
 * PingEvent is an event generated for player pings
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\events
 */
class PingEvent extends MessageEvent {

	/**
	 * property containing the ping location on the map
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
	 * @param  integer recipient | integer describing the recipient of the message
	 * @param  array location | array with the location with the x and the y index
	 */
	public function __construct($frames, $playerId, $recipient, $location) {
		parent::__construct($frames, $playerId, $recipient);

		$this->location = $location;
	}

}