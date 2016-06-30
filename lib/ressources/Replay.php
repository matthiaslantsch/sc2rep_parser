<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the Replay ressource object
 */

namespace HIS5\lib\Sc2repParser\ressources;

use Rogiel\MPQ\MPQFile;
use HIS5\lib\Sc2repParser\decoders as decoders;

/**
 * The Replay class is a wrapper around the MPQ archive object and oversees a lot of the decoding process
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\ressources
 */
class Replay {

	/**
	 * property containing the major game version for the current replay
	 *
	 * @access 	public
	 * @var 	integer verMajor | major game version number
	 */
	public $verMajor = 0;

	/**
	 * property containing the full game version string for the current replay
	 *
	 * @access 	public
	 * @var 	string version | full game version string
	 */
	public $version = "";

	/**
	 * property containing the number of frames in the played game
	 *
	 * @access 	public
	 * @var 	integer frames | frames number for the entire game
	 */
	public $frames = "";

}