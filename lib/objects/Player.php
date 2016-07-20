<?php
/**
 * This file is part of the hdev activerecord library
 * (c) Matthias Lantsch
 *
 * class file for the Player logic class
 */

namespace HIS5\lib\Sc2repParser\objects;

use HIS5\lib\Sc2repParser\utils as utils;

/**
 * the Player class represents a player in the game
 * 
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\objects
 */
class Player extends Entity {

	/**
	 * property containing the result for this player
	 * one of the following: Win, Loss, Unknown
	 *
	 * @access  public
	 * @var     string result | outcome of the map for this specific player
	 */
	public $result = "Unknown";

	/**
	 * property containing a string identifying the race the player picked prior to the game starting
	 * one of the following: Protoss, Zerg, Terran, Random
	 *
	 * @access  public
	 * @var     string pickRace | the race the player picked prior to the game start
	 */
	public $pickRace = "Unknown";

	/**
	 * property containing a string identifying the race the player played in the game
	 * one of the following: Protoss, Zerg, Terran
	 *
	 * @access  public
	 * @var     string pickRace | the race the player played in the game
	 */
	public $playRace = "Unknown";

	/**
	 * property containing the difficulty setting for this player (always Medium for real players)
	 *
	 * @access  public
	 * @var     string difficulty | difficulty setting for this player
	 */
	public $difficulty = "Unknown";

	/**
	 * property containing the color the player used as an array with rbga values
	 *
	 * @access  public
	 * @var     array color | array with rgba values with r, g, b and a as keys
	 */
	public $color;

	/**
	 * flag marking this player as either an AI computer or not
	 *
	 * @access  public
	 * @var     boolean isComputer | boolean determing wheter this player is an AI computer or not
	 */
	public $isComputer;

}