<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the Replay ressource object
 */

namespace HIS5\lib\Sc2repParser\ressources;

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
	 * property containing the number of frames in the played game
	 *
	 * @access 	public
	 * @var 	integer frames | frames number for the entire game
	 */
	public $frames = "";

	/**
	 * constructor initialising the replay object with the inital data from the header
	 *
	 * @access public
	 * @param  integer baseBuild | the base build number of the game at that point
	 * @param  string versionString | a string identifying the version and build of the game in detail
	 * @param  integer frames | the counter of game frames inside the replay
	 * @param  string expansion | expansion string determing the game version
	 */
	public function __construct($baseBuild, $versionString, $frames, $expansion) {
		$this->baseBuild = $baseBuild;
		$this->version = $versionString;
		$this->frames = $frames;
		$this->expansion = $expansion;
	}

}