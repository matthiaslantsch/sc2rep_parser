<?php
/**
 * This file is part of the hdev activerecord library
 * (c) Matthias Lantsch
 *
 * class file for the abstract Entity base class
 */

namespace HIS5\lib\Sc2repParser\objects;

use HIS5\lib\Sc2repParser\utils as utils;

/**
 * the abstract Entity base class is the base class for any user in the replay
 * meaning it can be any of: observer, referee, player, computer
 * 
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\objects
 */
class Entity {

	/**
	 * property containing the player id of this entity
	 *
	 * @access  public
	 * @var     integer playerId | integer containing the player id of this entity
	 */
	public $playerId;

	/**
	 * property containing the amount of handicap this entity has set
	 *
	 * @access  public
	 * @var     integer handicap | integer indicating how much handicap this entity has, ranges from 50-100
	 */
	public $handicap;

	/**
	 * property containing the teamId for the team the entity is on (null for observers)
	 *
	 * @access  public
	 * @var     integer teamId | the id of the team the entity is on (null for observers)
	 */
	public $teamId;

	/**
	 * property containing the battle.net region the entity is registered to
	 *
	 * @access  public
	 * @var     string region | battle.net region identifier
	 */
	public $region;

	/**
	 * property containing the battle.net subregion the entity is registered to
	 *
	 * @access  public
	 * @var     string subregion | battle.net subregion identifier
	 */
	public $subregion;

	/**
	 * property containing the battle.net toon id the entity is registered to
	 *
	 * @access  public
	 * @var     string bnetId | battle.net id identifier
	 */
	public $bnetId;

	/**
	 * property containing the clan tag for this entity at the time of the game
	 *
	 * @access  public
	 * @var     string clanTag | clan tag for this entity
	 */
	public $clanTag;

	/**
	 * property containing the battle.net name for this entity at the time of the game
	 *
	 * @access  public
	 * @var     string name | battle.net name for this entity
	 */
	public $name;

	/**
	 * property containing the player id of the archon team leader
	 *
	 * @access  public
	 * @var     integer archonLeaderId | player id of the archon team leader
	 */
	public $archonLeaderId;

	/**
	 * property containing the players combined level with all races
	 *
	 * @access  public
	 * @var     integer combinedRaceLevels | player's combined level across all races
	 */
	public $combinedRaceLevels;

	/**
	 * property containing the players highest league that he archieved this season
	 *
	 * @access  public
	 * @var     string highestLeague | player's highest league archieved this season
	 */
	public $highestLeague;

	/**
	 * constructor method for the entity object
	 *
	 * @access public
	 * @param  integer pid | player id for this entity
	 * @param  string name | the name for this entity
	 */
	public function __construct($pid, $name) {
		$this->playerId = $pid;
		$this->name = $name;
	}

	/**
	 * method called to construct a battle.net url from the battle.net information contained in the entity data
	 *
	 * @access public
	 * @return string with the battle.net url or false if not applicable (e.g. battle.net 1 entity)
	 */
	public function bnetUrl() {
		if(isset($this->realId) || $this->region === false || $this->subregion === false || $this->bnetId === false || $this->name === false) {
			return false;
		}

		return sprintf("http://%s.battle.net/sc2/en/profile/%d/%d/%s/", $this->region, $this->bnetId, $this->subregion, $this->name);
	}

}