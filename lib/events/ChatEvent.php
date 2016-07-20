<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the ChatEvent logic class
 */

namespace HIS5\lib\Sc2repParser\events;

/**
 * ChatEvent is an event generated for chat messages
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\events
 */
class ChatEvent extends MessageEvent {

	/**
	 * property containing the message string
	 *
	 * @access 	public
	 * @var 	string msg | the chat message
	 */
	public $msg;

	/**
	 * constructor accepting the frame count
	 *
	 * @access public
	 * @param  integer frames | frame counter at the time the event happened
	 * @param  integer playerId | playerId of the player that caused the event
	 * @param  integer recipient | integer describing the recipient of the message
	 * @param  string msg | string with the chat message
	 */
	public function __construct($frames, $playerId, $recipient, $msg) {
		parent::__construct($frames, $playerId, $recipient);

		$this->msg = $msg;
	}

}