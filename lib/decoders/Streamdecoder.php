<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the abstract stream decoder base class
 */

namespace HIS5\lib\Sc2repParser\decoders;

/**
 * The BitPackedDecoder class is a wrapper around a file stream returned from the mpq library
 * reads from the internal stream and offers methods to make the access easier (e.g. read an entire Uint8 with one call)
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\decoders
 */
abstract class InitdataDecoder {

	/**
	 * property containing the stream ressource
	 *
	 * @access 	private
	 * @var 	ressource stream | the opened stream ressource coming from the mpq archive
	 */
	private $bytestream;

	/**
	 * property containing a reference to the Replay object
	 *
	 * @access 	private
	 * @var 	Replay object | object containing the parsed data
	 */
	private $replay;

	/**
	 * constructor method for the stream decoder
	 * will take a stream ressource as an argument
	 *
	 * @access private
	 * @param  object replay | the replay object
	 * @param  ressource stream | the opened bytestream to decode from
	 */
	private function __construct($replay, $stream) {
		$this->replay = $replay;
		$this->bytestream = $stream;
	}

	/**
	 * static caller method to intialise the decoder 
	 *
	 * @access private
	 * @param  object replay | the replay object
	 * @param  ressource stream | the opened bytestream to decode from
	 */
	public static function decode($replay, $stream) {
		$me = new static($replay, $stream);
		return $me();
	}

	/**
	 * wrapper method around unpack() to read an entire byte from our stream
	 *
	 * @access public
	 * @return one read byte from the byte stream or false if the stream is finished already
	 */
	public function readByte() {
		// the following checks that there are enough bytes left in the stream
		if (!($read = $this->stream->readByte())) { 
			return false;
		}

		return unpack("C", $read)[1];
	}

	/**
	 * wrapper method around readBytes() from our stream to read a fixed size string
	 *
	 * @access public
	 * @param  integer size | integer determing the size of the string in bytes
	 * @return read string as big as size
	 */
	public function readString($size) {
		// the following checks that there are enough bytes left in the stream
		if (!($read = $this->stream->readBytes($size))) { 
			return false;
		}

		return utf8_encode($read);		
	}

	/**
	 * wrapper method around readBytes() from our stream to read a boolean from our stream
	 *
	 * @access public
	 * @return read boolean
	 */
	public function read_bool() {

	}

}