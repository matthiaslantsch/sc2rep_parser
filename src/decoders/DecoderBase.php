<?php
/**
 * This file is part of the holonet sc2 replay parser library
 * (c) Matthias Lantsch
 *
 * class file for the DecorderBase abstract base class
 *
 * @package holonet sc2 replay parser library
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\Sc2repParser\decoders;

use holonet\bitstream\Stream;
use holonet\Sc2repParser\ParserException;
use holonet\Sc2repParser\resources\Replay;

/**
 * DecoderBase class just holding constructor and stream as well as a reference
 * to the replay object being parsed
 *
 * @author  matthias.lantsch
 * @package holonet\Sc2repParser\decoders
 */
abstract class DecoderBase {

	/**
	 * property containing the stream object from the bitstream library
	 *
	 * @access  protected
	 * @var     Stream $stream The opened stream object from the bitstream library
	 */
	protected $stream;

	/**
	 * property containing a reference to the Replay object
	 *
	 * @access  protected
	 * @var     Replay $object Object containing the parsed data
	 */
	protected $replay;

	/**
	 * constructor method for the stream decoder
	 * will take a stream object as an argument
	 *
	 * @access public
	 * @param  Stream $stream The opened stream object to read from
	 * @return void
	 */
	public function __construct(Stream $stream) {
		$this->stream = $stream;
	}

	/**
	 * method to do the decoding
	 * accepts the replay object and delegates the call to the child class
	 *
	 * @access private
	 * @param  Replay replay The replay object
	 * @return array with raw data (return value of the child method)
	 */
	public function decode(Replay $replay = null) {
		$this->replay = $replay;
		return $this->doDecode();
	}

	/**
	 * wrapper method around the methods of this class to parse serialized data into an array
	 *
	 * @access public
	 * @param  integer $dataType Uint8 describing the datatype (if not given will be read first)
	 * @return mixed unserialized data
	 */
	public function parseSerializedData(int $dataType = null) {
		$this->stream->align();

		if($dataType === null) {
			$dataType = $this->stream->readUInt8();
		}

		switch ($dataType) {
			case 0x00: // array
				$array = array();
				$numElements = $this->parseVLFNumber();
				while ($numElements > 0) {
					$array[] = $this->parseSerializedData();
					$numElements--;
				}
				return $array;
			case 0x01: // bitarray
				$numBits = $this->parseVLFNumber();
				return $this->stream->readBits($numBits);
			case 0x02: // binary data
				$dataLen = $this->parseVLFNumber();
				return $this->stream->readBytes($dataLen, true);
			case 0x04: // optional
				$exists = ($this->stream->readUInt8() != 0);
				return ($exists ? $this->parseSerializedData() : false);
			case 0x05: // array with keys
				$array = array();
				$numElements = $this->parseVLFNumber();
				while ($numElements--) {
					$index = $this->parseVLFNumber();
					$array[$index] = $this->parseSerializedData();
				}
				return $array;
			case 0x06: // number of one byte
				return $this->stream->readUInt8();
			case 0x07: // number of four bytes
				return $this->stream->readUInt32();
			case 0x08: // number of 8 bytes
				return $this->stream->readUInt64();
			case 0x09: // number in VLF
				return $this->parseVLFNumber();
			default:
				return false;
		}
	}

	/**
	 * wrapper method around unpack() to read a variable length number from the byte stream
	 *
	 * @access public
	 * @return number read variable length number
	 */
	public function parseVLFNumber() {
		$number = 0;
		$first = true;
		$multiplier = 1;
		$i = $this->stream->readUInt8();
		$bytes = 0;
		while (true) {
			$number += ($i & 0x7F) * pow(2,$bytes * 7);
			if ($first) {
				if ($number & 1) {
					$multiplier = -1;
					$number--;
				}
				$first = false;
			}
			if (($i & 0x80) == 0) break; //end of the vlf number
			$i = $this->stream->readUInt8();
			$bytes++;
		}
		$number *= $multiplier;
		$number /= 2; // can't use right-shift because the datatype will be float for large values on 32-bit systems
		return $number;
	}

	/**
	 * method used to decode a cache handler string
	 *
	 * @access public
	 * @param  string $bytes If not given, the next 40 bytes in the stream will be parsed
	 * @return array with the decoded cache handle (region, extension, hash)
	 */
	public function parseCacheHandle(string $bytes = "") {
		if($bytes === "") {
			$bytes = $this->stream->readAlignedString(40);
		}

		$ret["extension"] = substr($bytes, 0, 4);
		$ret["region"] = trim(substr($bytes, 4, 4));
		// There is no SEA server, use US instead
		if($ret["region"] == "SEA") {
			$ret["region"] = "US";
		}

		$ret["hash"] = bin2hex(substr($bytes, 8));
		$ret["url"] = sprintf("http://%s.depot.battle.net:1119/%s.%s", $ret["region"], $ret["hash"], $ret["extension"]);

		return $ret;
	}

	/**
	 * method used to read a gameloop counter from the raw data
	 *
	 * @access public
	 * @return integer with the gameloop counter
	 */
	public function readLoopCount() {
		$additionalBytes = $this->stream->readBits(2);
		if($additionalBytes === false) {
			return false;
		}

		return $this->stream->readBits(6 + $additionalBytes * 8);
	}

	/**
	 * force each decoder class to implement a doDecode method for the actual decoding process
	 *
	 * @access protected
	 * @return void
	 */
	abstract protected function doDecode();

}
