<?php
/**
 * This file is part of the holonet sc2 replay parser library
 * (c) Matthias Lantsch
 *
 * class file for the Player logic class
 *
 * @package holonet sc2 replay parser library
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\Sc2repParser\objects;

/**
 * class representing a player in the game
 *
 * @author  matthias.lantsch
 * @package holonet\Sc2repParser\objects
 */
class Player extends Entity {

	/**
	 * property containing the result for this player
	 * one of the following: Win, Loss, Unknown
	 *
	 * @access public
	 * @var    string $result Outcome of the map for this specific player
	 */
	public $result = "Unknown";

	/**
	 * property containing a string identifying the race the player picked prior to the game starting
	 * one of the following: Protoss, Zerg, Terran, Random
	 *
	 * @access public
	 * @var    string $pickRace The race the player picked prior to the game start
	 */
	public $pickRace;

	/**
	 * property containing a string identifying the race the player played in the game
	 * one of the following: Protoss, Zerg, Terran
	 *
	 * @access public
	 * @var    string $pickRace The race the player played in the game
	 */
	public $playRace;

	/**
	 * property containing the difficulty setting for this player (always Medium for real players)
	 *
	 * @access public
	 * @var    string $difficulty Difficulty setting for this player
	 */
	public $difficulty;

	/**
	 * property containing the color the player used as an array with rbga values
	 *
	 * @access public
	 * @var    array $color Array with rgba values with r, g, b and a as keys
	 */
	public $color;

	/**
	 * flag marking this player as either an AI computer or not
	 *
	 * @access public
	 * @var    boolean $isComputer Boolean determing wheter this player is an AI computer or not
	 */
	public $isComputer;

}
