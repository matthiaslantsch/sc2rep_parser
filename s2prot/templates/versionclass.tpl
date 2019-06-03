<?php
/**
 * This file is part of the holonet sc2 replay parser library
 * (c) Matthias Lantsch
 *
 * THIS FILE WAS AUTOMATICALLY GENERATED
 * class file for the Version{{VERSION}} class
 *
 * @package holonet sc2 replay parser library
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\Sc2repParser\format;

/**
 * The Version{{VERSION}} class links to version "{{VERSION}}" file formats
 *
 * @author  matthias.lantsch
 * @package holonet\Sc2repParser\format
 */
class Version{{VERSION}} extends Version {

	/**
	 * property containing a mapping of subfile names to binary format files
	 *
	 * @access public
	 * @var    array $FORMATFILES Mapping of filenames to format files
	 */
	public static $FORMATFILES = array(
		{{MAPPING}}
	);

	/**
	 * @access public
	 * @return integer with the {{VERSION}} version
	*/
	public function version() {
		return {{VERSION}};
	}

}
