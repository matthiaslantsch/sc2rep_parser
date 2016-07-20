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
class ProgressEvent extends EventBase {

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
	 * @param  integer playerId | playerId of the player that caused the event
	 * @param  integer progress | progress of at the moment of the event
	 */
	public function __construct($frames, $playerId, $progress) {
		parent::__construct($frames, $playerId);

		$this->progress = $progress;
	}

}