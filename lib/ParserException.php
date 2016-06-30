<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the custom recoverable exception
 */

namespace HIS5\lib\Sc2repParser;

/**
 * The ParserException class is used to recoverable exceptions during replay parsing
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser
 */
class ParserException extends \Exception {
	
	/**
	 * constructor method for the exception
	 *
	 * @access public
	 * @param  string msg | Error message
	 * @param  int errorcode | Error code
	 */
	public function __construct($msg, $errorcode) {
		parent::__construct($msg, $errorcode);
	}

}