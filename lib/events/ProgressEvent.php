<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the ProgressEvent logic class
 */

namespace HIS5\lib\Sc2repParser\events;

/**
 * A ProgressEvent is fired whenever a client sends packages to the other clients to inform them about the loading progress
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\events
 */
abstract class ProgressEvent {

	/**
	 * property containing the loading progress at the time
	 *
	 * @access 	public
	 * @var 	integer progress | the loading progress at the time
	 */
	public $progress;

	/**
	 * constructor accepting the frame count
	 *
	 * @access public
	 * @param  integer frames | frame counter at the time the event happened
	 */
	public function __construct($frames, $playerId, $progress) {
		$this->frames = $frames;
	}

}