<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the ResourceTradeEvent logic class
 */

namespace HIS5\lib\Sc2repParser\events;

/**
 * A ResourceTradeEvent is fired whenever a player sends resources to another player without a prior request
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\events
 */
class ResourceTradeEvent extends EventBase {

	/**
	 * property containing the player id of the recipient of the resources
	 *
	 * @access 	public
	 * @var 	integer recipientId | player id of the recipient
	 */
	public $recipientId;

	/**
	 * property containing an array of resources traded
	 *
	 * @access 	public
	 * @var 	array resources | array with the traded resources
	 */
	public $resources;

	/**
	 * constructor accepting the frame count
	 *
	 * @access public
	 * @param  integer frames | frame counter at the time the event happened
	 * @param  integer playerId | playerId of the player that caused the event
	 * @param  integer recipientId | player id of the recipient
	 * @param  array resources | array with the traded resources
	 */
	public function __construct($frames, $playerId, $recipientId, $resources) {
		parent::__construct($frames, $playerId);

		$this->recipientId = $recipientId;
		die(var_dump("parse resource at trade", $resources));
		$this->resources = $resources;
	}

}