<?php
/**
 * This file is part of the holonet sc2 replay parser library
 * (c) Matthias Lantsch
 *
 * class file for the Observer logic class
 *
 * @package holonet sc2 replay parser library
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\Sc2repParser\objects;

/**
 * the Observer class represents an observer in game (can be referee or not)
 *
 * @author  matthias.lantsch
 * @package holonet\Sc2repParser\objects
 */
class Observer extends Entity {

	/**
	 * flag marking this observer as a referee or not
	 *
	 * @access  public
	 * @var     boolean $isReferee Flag used to mark this as a referee
	 */
	public $isReferee = false;

}
