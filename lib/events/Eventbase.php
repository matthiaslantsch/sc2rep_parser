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
abstract class MessageEventsDecoder {

	/**
	 * property containing the number of frames at the time the event happened
	 *
	 * @access 	public
	 * @var 	integer frames | frames number at the time the event happened
	 */
	public $frames;

	/**
	 * protected constructor accepting the frame count from the child classes
	 * all game events must have a frame counter
	 *
	 * @access protected
	 * @param  integer frames | frame counter at the time the event happened
	 */
	protected function __construct($frames) {
		$this->frames = $frames;
	}

}