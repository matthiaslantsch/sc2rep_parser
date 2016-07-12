<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the StringStream wrapper class
 */

namespace HIS5\lib\Sc2repParser\utils;

/**
 * The StringStream class wraps around a byte stream, exposing methods to access it like a php stream
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\utils
 */
class StringStream {

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

		$ret = $this->byteStr[$this->pointer];

		$this->pointer++;
		return $ret;
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
	 * emulation method to copy the stream behaviour to enable calls to eof() to check if the stream has more bytes
	 *
	 * @access public
	 * @param  integer length | number of bytes to read
	 * @return given number of read bytes from the byte string or false if the string is finished already
	 */
	public function eof() {
		if ($this->pointer >= strlen($this->byteStr)) { 
			return true;
		}
	}

}