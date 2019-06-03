<?php
/**
 * This file is part of the holonet sc2 replay parser library
 * (c) Matthias Lantsch
 *
 * This script is a utility script that interprets blizzard python type definitions
 * to write a binary format file for our parser
 *
 * @package holonet sc2 replay parser library
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\Sc2repParser\s2prot;

use holonet\common as co;
use holonet\bitstream\format\BinNode;
use holonet\bitstream\format\printer\BinaryFormatPrinter;
use holonet\Sc2repParser\format\Version;

//procedural entry point
if(in_array("help", $argv) || in_array("--help", $argv)) {
	usage();
}

include_once dirname(__DIR__).DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR."autoload.php";

const PYTHON_PATH = "C:\\Python27\\python.exe";

new S2ProtInterpreter();

/**
 * Logic class used to wrap all the organizer functions to be able to work from an object context
 *
 * @author  matthias.lantsch
 * @package holonet\Sc2repParser\s2prot
 */
class S2ProtInterpreter {

	/**
	 * property containing an array with versions that are available in the
	 * s2protocol git repository
	 *
	 * @access public
	 * @var    array $versions Array with protocol versions
	 */
	public $versions;

	/**
	 * constructor method overseeing the entire script process
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		logo();
		echo "\nInterpreting blizzard s2protocol type definitions...\n\n";
		$this->listVersions();
		$processed = 0;

		foreach ($this->versions as $version => $existing) {
			if(!$existing) {
				$this->interpretVersion($version);
				$processed++;
			}
		}

		echo "Processed {$processed} new version files\n";
	}

	/**
	 * small method getting a list of available versions using the versions.py script
	 *
	 * @access private
	 * @return void
	 */
	private function listVersions() {
		echo "Getting a list of available versions...\n";
		$json = json_decode($this->runPython("versions.py"), true);
		echo "\t+----------+----------+\n";
		printf("\t|%10s|%10s|\n", "Version", "Status");
		foreach ($json as $version) {
			$version = str_replace(array("protocol", ".py"), "", $version);
			$this->versions[$version] = file_exists(co\filepath(dirname($this->versionPath($version)), "Version{$version}.php"));
			printf("\t|%10s|%10s|\n",
				$version,
				$this->versions[$version] ? "Existing" : "New"
			);
		}
		echo "\t+----------+----------+\n";
	}

	/**
	 * method used to process a version number
	 * will generate binary format files and a version class
	 *
	 * @access private
	 * @param  string $version The version number to be interpreted
	 * @return void
	 */
	private function interpretVersion($version) {
		echo "Generating format files for new version {$version}\n";

		//get a json dump of the type info of that version
		$json = json_decode($this->runPython("dump.py {$version}"), true);
		$typeinfo = new TypeInfo($json);

		$formatMapping = array(
			"header" => null,
			"initdata" => null,
			"details" => null,
			"gameevents" => null,
			"messageevents" => null,
			"trackerevents" => null
		);

		if(($tree = $typeinfo->getHeaderTree()) !== null) {
			$formatMapping["header"] = $this->generateFormatFile($version, "header", $tree);
		}

		if(($tree = $typeinfo->getInitdataTree()) !== null) {
			$formatMapping["initdata"] = $this->generateFormatFile($version, "initdata", $tree);
		}

		if(($tree = $typeinfo->getDetailsTree()) !== null) {
			$formatMapping["details"] = $this->generateFormatFile($version, "details", $tree);
		}

		if(($tree = $typeinfo->getGameEventsTree()) !== null) {
			$formatMapping["gameevents"] = $this->generateFormatFile($version, "gameevents", $tree);
		}

		if(($tree = $typeinfo->getMessageEventsTree()) !== null) {
			$formatMapping["messageevents"] = $this->generateFormatFile($version, "messageevents", $tree);
		}

		if(($tree = $typeinfo->getTrackerEventsTree()) !== null) {
			$formatMapping["trackerevents"] = $this->generateFormatFile($version, "trackerevents", $tree);
		}

		//doesn't seem to change like ever
		$formatMapping["attributeevents"] = "base/attributeevents.bformat.php";

		$mappingString = array();
		foreach ($formatMapping as $key => $value) {
			$mappingString[] = "\"$key\" => ".($value !== null ? "\"{$value}\"" : "null");
		}
		$mappingString = implode(",\n\t\t", $mappingString);

		//generate the new Version class
		echo "\tGenerating version class 'Version{$version}'...\n";
		$versionClassCode = file_get_contents(co\filepath(__DIR__, "templates", "versionclass.tpl"));
		$versionClassCode = str_replace(
			array("{{MAPPING}}", "{{VERSION}}"),
			array($mappingString, $version),
			$versionClassCode
		);

		file_put_contents(
			co\filepath(dirname($this->versionPath($version)), "Version{$version}.php"),
			$versionClassCode
		);
	}

	/**
	 * diff the format tree for the given file to the last one and generate it
	 * if needed
	 * return the subpath of where to find the to be used format file
	 *
	 * @access private
	 * @param  string $version The version number to be interpreted
	 * @param  string $subfile The subfile to generate the format file for
	 * @param  BinNode $tree The binary tree for the file
	 * @return string with the subpath of the format file
	 */
	private function generateFormatFile($version, string $subfile, BinNode $tree) {
		$prevVersClass = Version::previous($version, $subfile);

		if($prevVersClass !== null) {
			$oldTree = $prevVersClass::getFileFormat($subfile);
			if($oldTree == $tree) {
				echo "\tThe '{$subfile}' format tree did not change since last version, skipping...\n";
				//just use the old one since it hasen't changed
				return $prevVersClass::$FORMATFILES[$subfile];
			}
		}

		//else write the new format file
		echo "\tGenerating '{$subfile}' format tree file...\n";
		co\dir_should_exist($this->versionPath($version));

		$formatFileCode = file_get_contents(co\filepath(__DIR__, "templates", "bformatfile.tpl"));
		$formatFileCode = str_replace(
			array("{{SUBFILE}}", "{{VERSION}}"),
			array($subfile, $version),
			$formatFileCode
		);

		$printer = new BinaryFormatPrinter($tree);
		$formatFileCode .= "{$printer->print()};";

		file_put_contents(
			co\filepath($this->versionPath($version), "{$subfile}.bformat.php"),
			$formatFileCode
		);
		return "{$version}/{$subfile}.bformat.php";
	}

	/**
	 * small helper method running a python script
	 *
	 * @access private
	 * @return void
	 */
	private function runPython(string $cmd) {
		$retVal = 0;
		$out = array();
		exec(PYTHON_PATH." {$cmd}", $out, $retVal);
		if($retVal !== 0) {
			throwError("Error executing python command {$cmd}: \n".implode("\n", $out));
		} else {
			return implode("\n", $out);
		}
	}

	/**
	 * small helper method returning the path for a certain version
	 *
	 * @access private
	 * @return void
	 */
	private function versionPath($version) {
		return co\dirpath(dirname(__DIR__), "src", "format", $version);
	}

}

/**
 * function used to print an error message
 * and stop the execution of the script with an error code
 *
 * @param string $errmsg Error message to throw
 * @return void
 */
function throwError($errmsg) {
	echo "ERROR $errmsg\n";
	exit(1);
}

/**
 * logo function used to print logo to the user
 *
 * @return void
 */
function logo() {
	echo <<<LOGO
---------------------------------------------------------------------------------------
      ___                  _          _       _                           _
     |__ \                | |        (_)     | |                         | |
  ___   ) |_ __  _ __ ___ | |_ ______ _ _ __ | |_ ___ _ __ _ __  _ __ ___| |_ ___ _ __
 / __| / /| '_ \| '__/ _ \| __|______| | '_ \| __/ _ \ '__| '_ \| '__/ _ \ __/ _ \ '__|
 \__ \/ /_| |_) | | | (_) | |_       | | | | | ||  __/ |  | |_) | | |  __/ ||  __/ |
 |___/____| .__/|_|  \___/ \__|      |_|_| |_|\__\___|_|  | .__/|_|  \___|\__\___|_|
          | |                                             | |
          |_|                                             |_|
---------------------------------------------------------------------------------------
LOGO;
}

/**
 * desc function used to print usage information to the user
 * and stop the execution of the script
 *
 * @return void
 */
function usage() {
	echo <<<DESC

replay-organizer can be used to reorganize a single replay file or a folder of replay files into a certain format

usage: replay-organizer.php [-t --target=value]
					  [-d --dateformat=value][-f --format=value]
					  [-p --playerformat=value]
					  [--help]

	target        Specify the path to either a single replay file or an entire directory of replays to reorganize.
	              The replays will be reorganized relative to the given target directory. If no target is provided, the script defaults to the current directory.
	dateformat    Specify the datetime format used for the {date} token in the name. Refer to the php documentation of date() to see valid codes.
	              Defaults to: "Y-m-d H:i:s"
	playerformat  Format used to represent a player in the {vs} token. Valid player information tokens include:
	                {clantag} => the players clan tag with [] around it
	                {name} => the player's name
	                {fullrace} => the player's race with the full name (e.g. Protoss)
	                {race} => the player's race with one letter (R for random)
	                {playrace} => the player's actual race with one letter (no random)
	                {bnetId} => the player's battle.net id number (will be left empty for AI players)
	                This will default to "{clantag}{name} ({race})"

	format        Build the format for the new filename of the replay files.
	              The name can contain any characters in addition to valid tokens except "/" is used as a path separator (For subfolders).
	              Unknown tokens will just be ignored.
	              Available tokens include:
	                {matchup} => Matchup of the played match (PvP, PvT usw...)
	                {date} => Date and time of the played match, format given by the dateformat argument
	                {gametype} => Gametype as in 1v1, 2v2, FFA usw...
	                {map} => Localized map name (with a german client, the german name for the map)
	                {vs} => String with all the teams seperated with a vs, where every player is outputted with the format specified in the playerformat argument
	               	    (e.g. [imba]DavidKimPls (T) vs Peter (P))
	               	{region} => the region the match was played on (e.g. EU)
	               	{version} => the full game version the match was played on (e.g. 3.4.0.44401)
	               	{basebuild} => the sc2 base build number of the version the match was played on (e.g. 44401)
	               	{gamelength} => the length of the game formatted in MM:SS
	               	{identifier} => the sc2rep unique identifier created from the timestamp of the match and a hash of the players
	               	{expansion} => the sc2 expansion the game was played on



	Example: sort replays in subfolders by the day the game was played on, then in subfolders per gametype and then in subfolders per matchup:
	    replay-organizer.php -t $MYREPLAYFOLDER -d "Y-m-d" -f "{date}/{gametype}/{matchup}/{vs} on {map}"

	help          Show this help text


DESC;
	exit(0);
}
