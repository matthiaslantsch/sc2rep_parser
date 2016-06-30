<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the ReplayParser class
 */

namespace HIS5\lib\Sc2repParser;

/**
 * The ReplayParser class is used as a center piece for the replay parsing process
 * it executes four different routines that reveal different levels of data about the replay:
 *   - header decoding (in the constructor) => game version, protocol
 *   - replay identifying (in identify()) => identify the people that played the replay/the map/timestamps
 *   - events decoding (in decode()) => decodes the additional event files in the replay to create raw event objects
 *   - game engine simulation (in simulate()) => simulates the events in order and calls plugins to extract additional data
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser
 */
class ReplayParser {

	/**
	 * constructor method starting the replay parsing process
	 * will always do the header decoding part (decode the replay header)
	 *
	 * @access public
	 * @param  string path | path to the replay to be parsed
	 */
	public function __construct($path) {
		if(!file_exists($path) || is_readable($path)) {
			throw new ParserException("The replay file '{$path}' could not be found/read", 10);
		}
	}

}