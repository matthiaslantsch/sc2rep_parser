<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the HijackReplayGameEvent logic class
 */

namespace HIS5\lib\Sc2repParser\events;

/**
 * HijackReplayGameEvent is an event generated when players take over from replay
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\events
 */
class HijackReplayGameEvent extends EventBase {

	/**
	 * property containing the method used to resume from the replay
	 *
	 * @access 	public
	 * @var 	integer method | integer marking the method used to resume
	 */
	public $method;

	/**
	 * property containing the user info data about the players taking over
	 *
	 * @access 	public
	 * @var 	array userInfo | user info data about the players taking over
	 */
	public $userInfo = [];

	/**
	 * constructor accepting the gameloop count
	 *
	 * @access public
	 * @param  integer gameloops | gameloop counter at the time the event happened
	 * @param  integer playerId | playerId of the player that caused the event
	 * @param  integer method | integer marking the method used to resume
	 * @param  array userInfo | user info data about the players taking over
	 */
	public function __construct($gameloops, $playerId, $method, $userInfo) {
		parent::__construct($gameloops, $playerId);

		$this->method = $method;
		$this->userInfo = $userInfo;
	}

}