<?php
/**
 * This file is part of the hdev activerecord library
 * (c) Matthias Lantsch
 *
 * class file for the Observer logic class
 */

namespace HIS5\lib\Sc2repParser\objects;

use HIS5\lib\Sc2repParser\utils as utils;

/**
 * the Observer class represents an observer in game (can be referee or not)
 * 
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\objects
 */
class Observer extends Entity {

	/**
	 * flag marking this observer as a referee or not
	 *
	 * @access  public
	 * @var     boolean isReferee | flag used to mark this as a referee
	 */
	public $isReferee = false;

}