<?php
/**
 * This file is part of the holonet sc2 replay parser library
 * (c) Matthias Lantsch
 *
 * class file for the Sc2CacheHandle logic class
 *
 * @package holonet sc2 replay parser library
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\Sc2repParser\objects;

/**
 * class representing a cache handle in the game
 *
 * @author  matthias.lantsch
 * @package holonet\Sc2repParser\objects
 */
class Sc2CacheHandle {

	/**
	* property containing the file extension of the resource
	*
	* @access public
	* @var    string $extension File extension
	*/
	public $extension;

	/**
	 * property containing the battle.net region the resource is registered on
	 *
	 * @access public
	 * @var    string $region Battle.net region identifier
	 */
	public $region;

	/**
	 * property containing the hash of the resource
	 *
	 * @access public
	 * @var    string $hash Battle.net resource hash
	 */
	public $hash;

	/**
	 * property containing the battle.net url the resource can be found at
	 *
	 * @access public
	 * @var    string $url Battle.net url
	 */
	public $url;

	/**
	 * method used to decode a cache handler string
	 *
	 * @access public
	 * @param  string $bytes Binary string with the cache handle
	 * @return void
	 */
	public function __construct(string $bytes) {
		$this->extension = substr($bytes, 0, 4);
		$this->region = trim(substr($bytes, 4, 4));
		// There is no SEA server, use US instead
		if($this->region == "SEA") {
			$this->region = "US";
		}

		$this->hash = bin2hex(substr($bytes, 8));
		$this->url = sprintf("http://%s.depot.battle.net:1119/%s.%s", $this->region, $this->hash, $this->extension);
	}

}
