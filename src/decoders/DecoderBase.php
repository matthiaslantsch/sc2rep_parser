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
use holonet\bitstream\format\BinaryFormatParser;
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
	 * do the parsing using a BinaryFormatParser
	 *
	 * @access protected
	 * @param  string $subfile The subfile to decode
	 * @return array with the read data
	 */
	protected function binaryFormatParse(string $subfile) {
		$formatTree = $this->replay->versionClass()::getFileFormat($subfile);
		$parser = new BinaryFormatParser($this->stream, $formatTree);
		$data = $parser->parse();

		if(!$this->stream->eof()) {
			throw new ParserException("Did not use all bytes while decoding {$subfile}", 1000);
		}

		return $data;
	}

	/**
	 * force each decoder class to implement a doDecode method for the actual decoding process
	 *
	 * @access protected
	 * @return void
	 */
	abstract protected function doDecode();

}
