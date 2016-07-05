<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the abstract stream decoder base class
 */

namespace HIS5\lib\Sc2repParser\decoders;

/**
 * The BitwiseDecoderBase class is a wrapper around a file stream returned from the mpq library/our own string stream object
 * reads from the internal stream and offers methods to make the access easier (e.g. read an entire Uint16 with one call)
 * allows to read bit by bit as well with internal byte cache
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\decoders
 */
abstract class BitwiseDecoderBase {

	/**
	 * property containing bit masks used to extract a certain number of bits
	 *
	 * @access  private
	 * @var     array includeBitmask | array with bitmasks used to extract a byte partiallc
	 */
	private static $includeBitmask = [0x01, 0x03, 0x07, 0x0F, 0x1F, 0x3F, 0x7F, 0xFF];

	/**
	 * property containing the stream ressource
	 *
	 * @access  private
	 * @var     ressource stream | the opened stream ressource coming from the mpq archive/string stream object
	 */
	private $bytestream;

	/**
	 * property containing the started byte to continue reading from there
	 *
	 * @access  private
	 * @var     char nextbyte | the started byte
	 */
	private $nextbyte;

	/**
	 * property containing the number of bits already used from the started byte
	 *
	 * @access  private
	 * @var     integer byteshift | number of bits already used
	 */
	private $byteshift = 0;

	/**
	 * property containing a reference to the Replay object
	 *
	 * @access  protected
	 * @var     Replay object | object containing the parsed data
	 */
	protected $replay;

	/**
	 * constructor method for the stream decoder
	 * will take a stream ressource as an argument
	 *
	 * @access public
	 * @param  ressource stream | the opened bytestream to decode from
	 */
	public function __construct($stream) {
		$this->bytestream = $stream;
	}

	/**
	 * method to do the decoding
	 *
	 * @access private
	 * @param  object replay | the replay object
	 */
	public function decode($replay) {
		$this->replay = $replay;
		return $this->doDecode();
	}

	/**
	 * wrapper method around our stream to read an entire byte from our stream
	 *
	 * @access public
	 * @return one read byte from the byte stream or false if the stream is finished already
	 */
	public function readByte() {
		// the following checks that there are enough bytes left in the stream
		if (!($read = $this->readBits(8))) {
			return false;
		}

		return chr($read);
	}

	/**
	 * wrapper method around our stream to read a specified number of bytes from our stream/fake stream
	 *
	 * @access public
	 * @param  integer len | number of bytes to read
	 * @return specified number of read bytes from the byte stream or false if the stream is finished already
	 */
	public function readBytes($len) {
		if($this->nextbyte === null) {
			//just return the bytes from the stream directly
			//the following checks that there are enough bytes left in the stream
			if (!($ret = $this->bytestream->readBytes($len))) {
				return false;
			}
		} else {
			//use our readbits() method
			$ret = "";
			while($len--) {
				if (!($read = $this->readBits(8))) {
					return false;
				}
				$ret .= chr($read);
			}
		}

		return $ret;
	}

	/**
	 * wrapper method around our stream to read a specified number of bits, using out internal byte cache to save the started byte
	 *
	 * @access public
	 * @param  integer len | number of bits to read
	 * @return specified number of read bits from the byte stream or false if the stream is finished already
	 */
	public function readBits($len) {
		if($len === 0) {
			return 0;
		}

		if($this->nextbyte === null) {
			//no byte has been started yet
			if($len % 8 == 0) {
				//don't start a byte with the cache, even number of bytes
				$ret = 0;
				//just return byte count not bit count
				$len /= 8;
				while ($len--) {
					$byte = $this->bytestream->readByte();
					if ($byte === false) {
						return false;
					}      

					$ret = ($ret << 8) | ord($byte);
				}
				return $ret;
			} else {
				$this->nextbyte = ord($this->bytestream->readByte());
				$this->byteshift = 0;
			}           
		}

		if($len <= 8 && $this->byteshift + $len <= 8) {
			//get the bitmask e.g. 00000111 for 3
			$bitmask = self::$includeBitmask[$len - 1];

			//can be satisfied with the remaining bits
			$ret = $this->nextbyte & $bitmask;

			//shift by len
			$this->nextbyte >>= $len;
			$this->byteshift += $len;
		} else {
			//read the remaining bits first
			$bitsremaining = 8 - $this->byteshift;
			$ret = $this->readBits($bitsremaining);

			//decrease len by the amount bits remaining
			$len -= $bitsremaining;

			//set the internal byte cache to null
			$this->nextbyte = null;

			if($len > 8) {
				//read entire bytes as far as possible
				for ($i = intval($len / 8); $i > 0; $i--) {
					$byte = $this->bytestream->readByte();
					if ($byte === false) {
						return false;
					}      

					$ret = ($ret << 8) | ord($byte);
				}

				//reduce len to the rest of the requested number
				$len = $len % 8;
			}

			//read a new byte to get the rest required
			$newbyte = $this->readBits($len);
			$ret = ($ret << $len) | $newbyte;
		}

		if($this->byteshift === 8) {
			//delete the cached byte
			$this->nextbyte = null;
		}

		return $ret;
	}

	/**
	 * wrapper method around unpack() to read an unsigned 8 bit integer from the byte stream
	 *
	 * @access public
	 * @return the next byte as an unsigned 8 bit integer or false if the stream is finished already
	 */
	public function readUInt8() {
		//check if we are in the middle of a byte
		if($this->nextbyte !== null) {
			return $this->readBits(8);
		}

		//read with unpack instead (faster?!?)
		$byte = $this->bytestream->readByte();
		if($byte === false) {
			return false;
		}

		$ret = unpack("C", $byte);
		return $ret[1];     
	}

	/**
	 * wrapper method around unpack() to read an unsigned 16 bit integer from the byte stream
	 *
	 * @access public
	 * @return the next two bytes as an unsigned 16 bit integer or false if the stream is finished already
	 */
	public function readUInt16() {
		//check if we are in the middle of a byte
		if($this->nextbyte !== null) {
			return $this->readBits(16);
		}

		//read with unpack instead (faster?!?)
		$bytes = $this->bytestream->readBytes(2);
		if($bytes === false) {
			return false;
		}

		$ret = unpack("v", $bytes);
		return $ret[1];
	}

	/**
	 * wrapper method around unpack() to read an unsigned 32 bit integer from the byte stream
	 *
	 * @access public
	 * @return the next 4 bytes as an unsigned 32 bit integer or false if the stream is finished already
	 */
	public function readUInt32() {
		//check if we are in the middle of a byte
		if($this->nextbyte !== null) {
			return $this->readBits(32);
		}

		//read with unpack instead (faster?!?)
		$bytes = $this->bytestream->readBytes(4);
		if($bytes === false) {
			return false;
		}

		$ret = unpack("V", $bytes);
		return $ret[1];
	}

	/**
	 * wrapper method around readUInt32() to read an unsigned 64 bit integer from the byte stream
	 *
	 * @access public
	 * @return the next 8 bytes as an unsigned 64 bit integer or false if the stream is finished already
	 */
	public function readUInt64() {
		$higher = $this->readUInt32();
		$lower = $this->readUInt32();
		if($higher === false || $lower === false) {
			return false;
		}

		return ($higher << 32 | $lower);
	}

	/**
	 * wrapper method around readBits() to read a boolean (bit length 1) from the byte stream
	 *
	 * @access public
	 * @return read boolean
	 */ 
	public function readBoolean() {
		return ($this->readBits(1) === 1);
	}

	/**
	 * wrapper method around unpack() to read a variable length number from the byte stream
	 *
	 * @access public
	 * @return read variable length number
	 */ 
	public function parseVLFNumber() {
		$number = 0;
		$first = true;
		$multiplier = 1;
		$i = $this->readUInt8();
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

			$i = $this->readUInt8();
			$bytes++;
		}

		$number *= $multiplier;
		$number /= 2; // can't use right-shift because the datatype will be float for large values on 32-bit systems
		return $number;
	}

	/**
	 * function used to skip the rest of the started byte and continuing at a new one
	 *
	 * @access public
	 */ 
	public function align() {
		$this->nextbyte = null;
		$this->byteshift = 0;
	}

	/**
	 * function used to skip the rest of the started byte and then reading a new byte
	 *
	 * @access public
	 * @return read byte
	 */ 
	public function readAlignedByte() {
		$this->align();
		return $this->readByte();
	}

	/**
	 * function used to skip the rest of the started byte and then reading new bytes
	 *
	 * @access public
	 * @param  integer len | the length of the requested string
	 * @return read byte
	 */ 
	public function readAlignedBytes($len) {
		$this->align();
		return $this->readBytes($len);	
	}

	/**
	 * wrapper method around the methods of this class to parse serialized data into an array
	 *
	 * @access public
	 * @return mixed unserialized data
	 */
	public function parseSerializedData() {
		$dataType = $this->readUInt8();
		switch ($dataType) {
			case 0x02: // binary data
				$dataLen = $this->parseVLFNumber();
				return $this->readBytes($dataLen);
				break;
			case 0x04: // simple array
				$array = array();
				$this->bytestream->readBytes(2); // skip 01 00
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
				return $this->readUInt8();
				break;
			case 0x07: // number of four bytes
				return $this->readUInt32();
				break;
			case 0x09: // number in VLF
				return $this->parseVLFNumber();
				break;
			default:
				//unknown datatype!!
				return false;
		}
	}

	/**
	 * force each decoder class to implement a doDecode method for the actual decoding process
	 *
	 * @access protected
	 */
	abstract protected function doDecode();

}