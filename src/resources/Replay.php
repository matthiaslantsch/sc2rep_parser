<?php
/**
 * This file is part of the holonet sc2 replay parser library
 * (c) Matthias Lantsch
 *
 * class file for the Replay ressource object class
 *
 * @package holonet sc2 replay parser library
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\Sc2repParser\resources;

use holonet\Sc2repParser\ParserException;

/**
* The Replay class represents a parsed replay file and contains it's data,
* raw and ordered
 *
 * @author  matthias.lantsch
 * @package holonet\Sc2repParser\resources
 */
class Replay {

	/**
	 * property containing the base build number for the game (used for version specific code)
	 *
	 * @access public
	 * @var    integer $baseBuild Base build number
	 */
	public $baseBuild;

	/**
	 * property containing the full game version string for the current replay
	 *
	 * @access public
	 * @var    string $version Full game version string
	 */
	public $version;

	/**
	 * property containing the game expansion as a string
	 * one of [WoL Beta, WoL, HotS Beta, HotS, LotV Beta, LotV]
	 *
	 * @access public
	 * @var    string $expansion Expansion string determing the game version
	 */
	public $expansion;

	/**
	 * property containing the number of ingame engine game loops in the played game
	 *
	 * @access public
	 * @var    integer $gameloops Gameloop number for the entire game
	 */
	public $gameloops;

	/**
	 * property containing an integer marking how far this replay has been parsed yet
	 *
	 * @access public
	 * @var    integer $loadLevel Integer with the load level
	 */
	public $loadLevel;

	/**
	 * property containing arrays of raw data, as parsed from the replay subfiles
	 *
	 * @access public
	 * @var    array $rawdata Array with rawdata
	 */
	public $rawdata = array();

	/**
	 * constructor initialising the replay object with the inital data from the header
	 *
	 * @access public
	 * @param  integer $baseBuild The base build number of the game at that point
	 * @param  string $versionString A string identifying the version and build of the game in detail
	 * @param  integer $gameloops The counter of ingame engine game loops inside the replay
	 * @param  string $expansion Expansion string determing the game version
	 * @return void
	 */
	public function __construct(int $baseBuild, string $versionString, int $gameloops, string $expansion) {
		$this->baseBuild = $baseBuild;
		$this->version = $versionString;
		$this->gameloops = $gameloops;
		$this->expansion = $expansion;
		$this->loadLevel = 1;
	}

	/**
	 * getter method for an array of events
	 *
	 * @access public
	 * @param  string $type String specifying the kind of events requested
	 * @return array with all the events of the requested type in it
	 */
	public function eventArray($type) {
		if($this->loadLevel < 3) {
			//not all event files have been parsed yet
			//check if the requested type has been parsed
			if(!isset($this->rawdata["replay.{$type}.events"])) {
				throw new ParserException("Cannot list events without parsing event files first (load level 3 => decode)", 100);
			}
		}

		switch ($type) {
			case "message":
				$events = $this->rawdata["replay.message.events"];
				break;
			case "game":
				$events = $this->rawdata["replay.game.events"];
				break;
			default:
				throw new ParserException("Cannot list events for unknown event type: '{$type}'", 100);
		}

		$ret = [];
		foreach ($events as $ev) {
			$ret[$ev["gameloop"]][] = $ev;
		}
		return $ret;
	}

}
