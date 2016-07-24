<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the AICommunicateEvent logic class
 */

namespace HIS5\lib\Sc2repParser\events;

/**
 * AICommunicateEvent is an event generated when a player signals commands to the AI
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\events
 */
class AICommunicateEvent extends EventBase {

	/**
	 * property containing the type of beacon sent to the ai
	 *
	 * @access 	public
	 * @var 	string beacon | the type of beacon sent to the ai
	 */
	public $beacon;

	/**
	 * property containing an ally integer
	 *
	 * @access 	public
	 * @var 	integer ally | player id of the ai getting the command
	 */
	public $ally;

	/**
	 * property containing the AI build identifier for the opener
	 *
	 * @access 	public
	 * @var 	string build | AI build identifier opener
	 */
	public $opener;

	/**
	 * property containing the AI build identifier for the lateGame
	 *
	 * @access 	public
	 * @var 	string build | AI build identifier lateGame
	 */
	public $lateGame;

	/**
	 * property containing the unique identifier of the target unit
	 * is 0 if the unit is in the fog of war
	 *
	 * @access 	public
	 * @var 	integer targetUnitId | unique identifier of the target unit
	 */
	public $targetUnitId;

	/**
	 * property containing the player id of the controlling player
	 *
	 * @access 	public
	 * @var 	integer controlPlayerId | player id of the player controlling the unit
	 */
	public $targetControlPlayerId;

	/**
	 * property containing the player id of the player paying upkeep
	 *
	 * @access 	public
	 * @var 	integer upkeepPlayerId | player id of the player paying upkeep
	 */
	public $targetUpkeepPlayerId;

	/**
	 * property containing the target location on the map
	 *
	 * @access 	public
	 * @var 	array location | array with the location with the x, y an z index
	 */
	public $location;

	/**
	 * constructor accepting the frame count
	 *
	 * @access public
	 * @param  integer frames | frame counter at the time the event happened
	 * @param  integer playerId | playerId of the player that caused the event
	 * @param  integer beacon | the type of beacon sent to the ai
	 * @param  integer ally | player id of the ai getting the command
	 * @param  integer build | AI build identifier
	 */
	public function __construct($frames, $playerId, $beacon, $ally, $build) {
		parent::__construct($frames, $playerId);
		die(var_dump("Parse beacon and ally integers"));

		if($beacon != -1) {
			switch ($beacon) {
				case 0:
					$this->beacon = "attack";
					break;
				case 5:
					$this->beacon = "clear";
					break;
				case 6:
					$this->beacon = "detect";
					break;
				case 7:
					$this->beacon = "scout";
					break;
				case 9:
					$this->beacon = "expand";
					break;
				default:
					die(var_dump("uNKNOWN ai beacon {$beacon}"));
					break;
			}
		}

		$this->ally = $ally;

		if($build != 0) {
			switch ($beacon) {
				case 2:
					$this->opener = "rush";
					break;
				case 3:
					$this->opener = "timing attack";
					break;
				case 4:
					$this->opener = "aggresive push";
					break;
				case 5:
					$this->opener = "economic focus";
					break;
				case 9:
					$this->lateGame = "create basic units";
					break;
				case 10:
					$this->lateGame = "create advanced units";
					break;
				case 11:
					$this->lateGame = "create air";
					break;
				case 12:
					$this->lateGame = "create casters";
					break;
				default:
					die(var_dump("uNKNOWN ai build {$build}"));
					break;
			}
		}
	}

}