<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the abstract Event base class
 */

namespace HIS5\lib\Sc2repParser\events;

/**
 * The abstract Event base class is used as a base class for all game events
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\events
 */
abstract class EventBase {

	/**
	 * property containing the number of frames at the time the event happened
	 *
	 * @access 	public
	 * @var 	integer frame | frames number at the time the event happened
	 */
	public $frame;

	/**
	 * property containing the player id of the player that caused the event
	 *
	 * @access 	public
	 * @var 	integer playerId | player id of the player that caused the event
	 */
	public $playerId;

	/**
	 * protected constructor accepting the frame count from the child classes
	 * all game events must have a frame counter
	 *
	 * @access protected
	 * @param  integer frames | frame counter at the time the event happened
	 * @param  integer playerId | playerId of the player that caused the event
	 */
	protected function __construct($frames, $playerId) {
		$this->frame = $frames;
		$this->playerId = $playerId;
	}

}