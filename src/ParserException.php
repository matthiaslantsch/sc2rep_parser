<?php
/**
 * This file is part of the holonet sc2 replay parser library
 * (c) Matthias Lantsch
 *
 * Class file for the ParserException class
 *
 * @package holonet sc2 replay parser library
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */
namespace holonet\Sc2repParser;

use Exception;

/**
 * The ParserException class is used to recoverable exceptions during replay parsing
 *
 * @author  matthias.lantsch
 * @package holonet\Sc2repParser
 */
class ParserException extends Exception {

	/**
	 * constructor method for the exception
	 *
	 * @access public
	 * @param  string $msg Error message
	 * @param  int $errorcode Error code
	 * @return void
	 */
	public function __construct($msg, $errorcode) {
		parent::__construct($msg, $errorcode);
	}

}
