<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the Replay ressource object
 */

namespace HIS5\lib\Sc2repParser\ressources;

use \HIS5\lib\Sc2repParser as parser;

/**
 * The Replay class is a wrapper around the MPQ archive object and oversees a lot of the decoding process
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\ressources
 */
class Replay {

	/**
	 * property containing the base build number for the game (used for version specific code)
	 *
	 * @access 	public
	 * @var 	integer baseBuild | base build number
	 */
	public $baseBuild;

	/**
	 * property containing the full game version string for the current replay
	 *
	 * @access 	public
	 * @var 	string version | full game version string
	 */
	public $version = "";

	/**
	 * property containing the game expansion as a string
	 * one of [WoL Beta, WoL, HotS Beta, HotS, LotV Beta, LotV]
	 *
	 * @access 	public
	 * @var 	string expansion | expansion string determing the game version
	 */
	public $expansion;

	/**
	 * property containing the number of ingame engine game loops in the played game
	 *
	 * @access 	public
	 * @var 	integer gameloops | gameloop number for the entire game
	 */
	public $gameloops;

	/**
	 * property containing an integer marking how far this replay has been parsed yet
	 *
	 * @access 	public
	 * @var 	integer frames | integer with the load level
	 */
	public $loadLevel;

	/**
	 * constructor initialising the replay object with the inital data from the header
	 *
	 * @access public
	 * @param  integer baseBuild | the base build number of the game at that point
	 * @param  string versionString | a string identifying the version and build of the game in detail
	 * @param  integer gameloops | the counter of ingame engine game loops inside the replay
	 * @param  string expansion | expansion string determing the game version
	 */
	public function __construct($baseBuild, $versionString, $gameloops, $expansion) {
		$this->baseBuild = $baseBuild;
		$this->version = $versionString;
		$this->gameloops = $gameloops;
		$this->expansion = $expansion;
		$this->loadLevel = 1;
		$this->rawdata = [];
	}

	/**
	 * getter method for an array of events
	 *
	 * @access public
	 * @param  string type | string specifying the kind of events requested
	 * @return array with all the events of the requested type in it
	 */
	public function eventArray($type) {	
		if($this->loadLevel < 3) {
			//not all event files have been parsed yet
			//check if the requested type has been parsed
			if(!isset($this->rawdata["replay.{$type}.events"])) {
				throw new parser\ParserException("Cannot list events without parsing event files first (load level 3 => decode)", 100);		
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
				throw new parser\ParserException("Cannot list events for unknown event type: {$type}", 100);
		}

		$ret = [];
		foreach ($events as $ev) {
			$ret[$ev["gameloop"]][] = $ev;
		}
		return $ret;
	}

}