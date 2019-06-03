<?php
/**
 * This file is part of the holonet sc2 replay parser library
 * (c) Matthias Lantsch
 *
 * Class file for the TypeInfo class
 *
 * @package holonet sc2 replay parser library
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\Sc2repParser\s2prot;

use holonet\bitstream\format\BinNode;
use holonet\bitstream\format\BinStruct;
use holonet\bitstream\format\BinInteger;
use holonet\bitstream\format\BinSkip;
use holonet\bitstream\format\BinTranslate;
use holonet\bitstream\format\BinArray;
use holonet\bitstream\format\BinCallback;
use holonet\bitstream\format\BinOptional;
use holonet\bitstream\format\BinBlob;
use holonet\bitstream\format\BinDelta;
use holonet\bitstream\format\BinChoice;
use holonet\bitstream\format\BinBoolean;
use holonet\Sc2repParser\format\BinSerialisedData;

/**
 * The TypeInfo class is used to interpret the python protocol type info from s2protocol
 *
 * @author  matthias.lantsch
 * @package holonet\Sc2repParser\s2prot
 */
class TypeInfo {

	/**
	 * property containing the raw type info array from the python script
	 *
	 * @access private
	 * @var    array $typeinfo Array dumped by the python script
	 */
	private $typeinfo;

	/**
	 * property containing the raw type tree as interpreted from the python file
	 *
	 * @access private
	 * @var    array $types Array with a binary stream tree
	 */
	private $types = array();

	/**
	 * constructor method for the TypeInfo sorting class
	 *
	 * @access public
	 * @param  array $typeinfo Array dumped by the python script
	 * @return void
	 */
	public function __construct(array $typeinfo) {
		$this->typeinfo = $typeinfo;

		//interpret the type info array given by blizzard
		foreach ($this->typeinfo["typeinfos"] as $i => $type) {
			list($type, $def) = $type;
			$this->types[$i] = $this->interpretType($type, $def);
		}
	}

	/**
	 * method used to interpret one type definition
	 *
	 * @access private
	 * @param  string $type The type as defined in the python file
	 * @param  array $def The definiton given for the type
	 * @return BinNode interpreted from the python type definiton
	 */
	private function interpretType(string $type, $def) {
		if($type === "_int") {
			list($constant, $size) = array_shift($def);
			return new BinInteger($size, $constant);
		} elseif($type === "_choice") {
			list($constant, $size) = array_shift($def);
			$map = array();
			foreach (array_shift($def) as $key => $linkId) {
				//the second member is a type reference integer
				$map[$key] = $this->types[$linkId[1]];
			}
			return new BinChoice(new BinInteger($size, $constant), $map);
		} elseif($type === "_blob") {
			list($constant, $size) = array_shift($def);
			return new BinBlob(new BinInteger($size, $constant));
		} elseif($type === "_struct") {
			$structItems = array();
			$tagged = false;
			foreach (array_shift($def) as $item) {
				list($key, $linkId, $tag) = $item;
				$key = str_replace("m_", "", $key);
				//if we are working with tagged structs, just go like that
				if($tag >= 0) {
					$index = $this->getIndexes($key, $this->types[$linkId]);
					if(is_string($index)) {
						$structItems[] = $index;
					}  else {
						$structItems[$key] = $index;
					}
					$tagged = true;
				} else {
					$structItems[$key] = $this->types[$linkId];
				}
			}
			if($tagged) {
				return new BinSerialisedData($structItems);
			} else {
				return new BinStruct($structItems);
			}
		} elseif($type === "_fourcc") {
			return new BinBlob(4);
		} elseif($type === "_array") {
			list($constant, $size) = array_shift($def);
			return new BinArray(
				new BinInteger($size, $constant),
				//the second member is a type reference integer
				$this->types[array_shift($def)]
			);
		} elseif($type === "_optional") {
			return new BinOptional(
				$this->types[array_shift($def)],
				new BinBoolean()
			);
		} elseif($type === "_bool") {
			return new BinBoolean();
		} elseif($type === "_bitarray") {
			list($constant, $size) = array_shift($def);
			return new BinInteger(new BinInteger($size, $constant));
		} elseif($type === "_null") {
			return null;
		} else {
			throw new RuntimeException("Error interpreting python type definition with type '{$type}'", 100);
		}
	}

	/**
	 * method used to get a key indexing array
	 *
	 * @access public
	 * @param  string $key The key to return if it's alreay a scalar value
	 * @param  BinNode|null interpreted from the python type definiton
	 * @return array with key indexes
	 */
	public function getIndexes(string $key, $node) {
		if($node instanceof BinStruct) {
			return $node->tree;
		} elseif($node instanceof BinArray) {
			return [$this->getIndexes($key, $node->tree)];
		} elseif($node instanceof BinOptional) {
			return $this->getIndexes($key, $node->tree);
		} else {
			return $key;
		}
	}

	/**
	 * method used to get a tree for parsing the replay header
	 * returns null if the given type is not supported by the replay version
	 *
	 * @access public
	 * @return BinNode|null interpreted from the python type definiton
	 */
	public function getHeaderTree() {
		if($this->typeinfo["replay_header_typeid"] !== null) {
			return $this->types[$this->typeinfo["replay_header_typeid"]];
		} else {
			return null;
		}
	}

	/**
	 * method used to get a tree for parsing the replay initdata subfile
	 * returns null if the given type is not supported by the replay version
	 *
	 * @access public
	 * @return BinNode|null interpreted from the python type definiton
	 */
	public function getInitdataTree() {
		if($this->typeinfo["replay_initdata_typeid"] !== null) {
			return $this->types[$this->typeinfo["replay_initdata_typeid"]];
		} else {
			return null;
		}
	}

	/**
	 * method used to get a tree for parsing the replay details subfile
	 * returns null if the given type is not supported by the replay version
	 *
	 * @access public
	 * @return BinNode|null interpreted from the python type definiton
	 */
	public function getDetailsTree() {
		if($this->typeinfo["game_details_typeid"] !== null) {
			return $this->types[$this->typeinfo["game_details_typeid"]];
		} else {
			return null;
		}
	}

	/**
	 * method used to get a tree for parsing the replay game tracker events subfile
	 *
	 * @access public
	 * @return BinNode|null interpreted from the python type definiton
	 */
	public function getGameEventsTree() {
		if($this->typeinfo["game_eventid_typeid"] !== null) {
			return $this->initEventParser(
				//the BinNode type to read to get the game event type id
				$this->types[$this->typeinfo["game_eventid_typeid"]],
				//mapping of eventid to type and name
				$this->typeinfo["game_event_types"],
				//decode user ids
				true
			);
		} else {
			return null;
		}
	}

	/**
	 * method used to get a tree for parsing the replay message events subfile
	 * returns null if the given type is not supported by the replay version
	 *
	 * @access public
	 * @return BinNode|null interpreted from the python type definiton
	 */
	public function getMessageEventsTree() {
		if($this->typeinfo["message_eventid_typeid"] !== null) {
			return $this->initEventParser(
				//the BinNode type to read to get the message event type id
				$this->types[$this->typeinfo["message_eventid_typeid"]],
				//mapping of eventid to type and name
				$this->typeinfo["message_event_types"],
				//do not decode user ids
				false
			);
		} else {
			return null;
		}
	}

	/**
	 * method used to get a tree for parsing the replay tracker events subfile
	 * returns null if the given type is not supported by the replay version
	 *
	 * @access public
	 * @return BinNode|null interpreted from the python type definiton
	 */
	public function getTrackerEventsTree() {
		if($this->typeinfo["tracker_eventid_typeid"] !== null) {
			return $this->initEventParser(
				//the BinNode type to read to get the tracker event type id
				$this->types[$this->typeinfo["tracker_eventid_typeid"]],
				//mapping of eventid to type and name
				$this->typeinfo["tracker_event_types"],
				//do not decode user ids
				false
			);
		} else {
			return null;
		}
	}

	/**
	 * method used to create a tree for an events file using the event type mapping
	 *
	 * @access public
	 * @return BinNode|null interpreted from the python type definiton
	 */
	private function initEventParser(BinNode $eventidType, array $eventmapping, bool $decodeuserid) {
		$choiceMap = array();
		//create a mapping of event eventid to type to be read
		foreach ($eventmapping as $eventId => $eventMap) {
			list($typeLink, $name) = $eventMap;
			$choiceMap[$eventId] = new BinStruct([
				"name" => $name,
				"data" => $this->types[$typeLink]
			]);
		}

		//The user id is always there but is "None" in some old versions of the procotol
		//even though that throws errors when using the OFFIAL tool
		if($this->typeinfo["replay_userid_typeid"] === null) {
			$userIdType = new BinInteger(5);
		} else {
			$userIdType = $this->types[$this->typeinfo["replay_userid_typeid"]];
		}

		return new BinArray("read_all", new BinStruct([
			//decode the gameloop delta before each event
			"gameloop" => new BinDelta($this->types[$this->typeinfo["svaruint32_typeid"]]),
			//decode the userid before each event (if we should based on the flag)
			"userid" => ($decodeuserid ? $userIdType : null),
			//give a choice array with the game_eventid_typeid type
			//parse the event id and lookup the BinStruct with event data structure and name
			"event" => new BinChoice($eventidType, $choiceMap),
			//abuse the BinSkip class to skip 0 bytes and align the stream as each event start is byte aligned
			new BinSkip(0, true)
		]));
	}

}
