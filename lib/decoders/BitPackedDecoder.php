<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the BitPackedDecoder decoder class
 */

namespace HIS5\lib\Sc2repParser\decoders;

/**
 * The BitPackedDecoder class is a wrapper around the a byte string and it reads using unpack()
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\decoders
 */
class BitPackedDecoder {

	/**
	 * property containing the data string
	 *
	 * @access 	private
	 * @var 	string byteStr | string containing the raw byte data
	 */
	private $byteStr;

	/**
	 * property containing an integer to point where we are in the parsing progress
	 *
	 * @access 	private
	 * @var 	integer pointer | pointer to the current place in the reading process
	 */
	private $pointer = 0;

	/**
	 * constructor method taking the byte stream as an argument
	 *
	 * @access public
	 * @param  string bytes | byte string to be decoded
	 */
	public function __construct($bytes) {
		$this->byteStr = $bytes;
	}

	/**
	 * wrapper method around unpack() to read an entire byte from our string
	 *
	 * @access public
	 * @return one read byte from the byte string or false if the string is finished already
	 */
	public function readByte() {
		// the following checks that there are enough bytes left in the string
		if ($this->pointer >= strlen($this->byteStr)) { 
			return false;
		}

		$ret = unpack("C", substr($this->byteStr, $this->pointer, 1));
		$this->pointer++;
		return $ret[1];
	}

	/**
	 * wrapper method around unpack() to read a specified number of bytes from our string
	 *
	 * @access public
	 * @param  integer length | number of bytes to read
	 * @return given number of read bytes from the byte string or false if the string is finished already
	 */
	public function readBytes($length) {
		// the following checks that there are enough bytes left in the string
		if (strlen($this->byteStr) - $this->pointer - $length < 0) { 
			return false;
		}

		$ret = substr($this->byteStr, $this->pointer, $length);
		$this->pointer += $length;
		return $ret;
	}

	/**
	 * wrapper method around unpack() to read an unsigned 16 bit integer from the byte string
	 *
	 * @access public
	 * @return read unsigned 16 bit integer or false if the string is finished already
	 */
	public function readUInt16() {
		$bytes = $this->readBytes(2);
		if($bytes === false) {
			return false;
		}

		$ret = unpack("v", $bytes);
		return $ret[1];
	}

	/**
	 * wrapper method around unpack() to read an unsigned 32 bit integer from the byte string
	 *
	 * @access public
	 * @return read unsigned 32 bit integer or false if the string is finished already
	 */
	public function readUInt32() {
		$bytes = $this->readBytes(4);
		if($bytes === false) {
			return false;
		}

		$ret = unpack("V", $bytes);
		return $ret[1];
	}

	/**
	 * wrapper method around unpack() to read a variable length number from the byte string
	 *
	 * @access public
	 * @return read variable length number
	 */	
	public function parseVLFNumber() {
		$number = 0;
		$first = true;
		$multiplier = 1;
		for ($i = $this->readByte(), $bytes = 0; true; $i = $this->readByte(), $bytes++) {
			$number += ($i & 0x7F) * pow(2,$bytes * 7);
			if ($first) {
				if ($number & 1) {
					$multiplier = -1;
					$number--;
				}
				$first = false;
			}
			if (($i & 0x80) == 0) break;
		}
		$number *= $multiplier;
		$number /= 2; // can't use right-shift because the datatype will be float for large values on 32-bit systems
		return $number;
	}

	/**
	 * wrapper method around the methods of this class to parse serialized data into an array
	 *
	 * @access public
	 * @return mixed unserialized data
	 */
	public function parseSerializedData() {
		$dataType = $this->readByte();
		switch ($dataType) {
			case 0x02: // binary data
				$dataLen = $this->parseVLFNumber();
				return $this->readBytes($dataLen);
				break;
			case 0x04: // simple array
				$array = array();
				$this->pointer += 2; // skip 01 00
				$numElements = $this->parseVLFNumber();
				while ($numElements > 0) {
					$array[] = $this->parseSerializedData();
					$numElements--;
				}
				return $array;
				break;
			case 0x05: // array with keys
				$array = array();
				$numElements = $this->parseVLFNumber();
				while ($numElements > 0) {
					$index = $this->parseVLFNumber();
					$array[$index] = $this->parseSerializedData();
					$numElements--;
				}				
				return $array;
				break;
			case 0x06: // number of one byte
				return $this->readByte();
				break;
			case 0x07: // number of four bytes
				return $this->readUInt32();
				break;
			case 0x09: // number in VLF
				return $this->parseVLFNumber();
				break;
			default:
//				if ($this->debug) $this->debug(sprintf("Unknown data type in function parseDetailsValue (%d)",$dataType));
				return false;
		}
	}

}