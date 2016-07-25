<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the UserOptionsEvent logic class
 */

namespace HIS5\lib\Sc2repParser\events;

/**
 * A UserOptionsEvent is fired at the start of a game with user options 
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\events
 */
class UserOptionsEvent extends EventBase {

	/**
	 * property containing an array with user options, indexed by string key
	 *
	 * @access 	public
	 * @var 	array useroptions | associative array with user options
	 */
	public $useroptions;

	/**
	 * constructor accepting the gameloop count
	 *
	 * @access public
	 * @param  integer gameloops | gameloop counter at the time the event happened
	 * @param  integer playerId | playerId of the player that caused the event
	 * @param  array useroptions | associative array with user options
	 */
	public function __construct($gameloops, $playerId, $useroptions) {
		parent::__construct($gameloops, $playerId);

		$this->useroptions = $useroptions;
	}

}