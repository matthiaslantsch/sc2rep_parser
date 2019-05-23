<?php
/**
 * This file is part of the hdev activerecord library
 * (c) Matthias Lantsch
 *
 * class file for the BitwiseDecoderBaseTest PHPUnit test class
 */

namespace HIS5\lib\Sc2repParser\tests;

use HIS5\lib\common as co;
use HIS5\lib\Sc2repParser as parser;
use PHPUnit\Framework\TestCase;

/**
 * The BitwiseDecoderBaseTest tests the bit wise reading operations of our stream wrapper
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\tests
 */
class BitwiseDecoderBaseTest extends TestCase {
/*
*/
	/**
	 * @covers \HIS5\lib\Sc2repParser\decoders\BitwiseDecoderBase::readBits()
	 */
	public function testReadFirst3() {
		$data = pack('H*', base_convert("01010111", 2, 16));
		$decoder = new TestDecoder(new parser\utils\StringStream($data));

		$this->assertEquals("00000111", decbin($decoder->readBits(3)));
	}

	/**
	 * @covers \HIS5\lib\Sc2repParser\decoders\BitwiseDecoderBase::readBits()
	 */
	public function testReadLast5() {
		$data = pack('H*', base_convert("11111010", 2, 16));
		$decoder = new TestDecoder(new parser\utils\StringStream($data));

		//skip 3 bits
		$decoder->readBits(3);

		$this->assertEquals("11111", decbin($decoder->readBits(5)));
	}

	/**
	 * @covers \HIS5\lib\Sc2repParser\decoders\BitwiseDecoderBase::readBits()
	 */
	public function testRead10() {
		$data = pack('H*', base_convert("10101001 11111101", 2, 16));
		$decoder = new TestDecoder(new parser\utils\StringStream($data));

		$this->assertEquals("1010100101", decbin($decoder->readBits(10)));
	}

	/**
	 * @covers \HIS5\lib\Sc2repParser\decoders\BitwiseDecoderBase::readBits()
	 */
	public function testReadSkip4Read8() {
		$data = pack('H*', base_convert("01011111 01101010", 2, 16));
		$decoder = new TestDecoder(new parser\utils\StringStream($data));

		//skip 4 bits
		$decoder->readBits(4);

		$this->assertEquals("01011010", decbin($decoder->readBits(8)));
	}

	/**
	 * @covers \HIS5\lib\Sc2repParser\decoders\BitwiseDecoderBase::readBits()
	 */
	public function testRead16() {
		$data = pack('H*', base_convert("01011111 01101010", 2, 16));
		$decoder = new TestDecoder(new parser\utils\StringStream($data));

		$this->assertEquals("0101111101101010", decbin($decoder->readBits(16)));
	}

	/**
	 * @covers \HIS5\lib\Sc2repParser\decoders\BitwiseDecoderBase::readBits()
	 */
	public function testReadSkip2Read16() {
		$data = pack('H*', base_convert("01011111 01101010 01101010", 2, 16));
		$decoder = new TestDecoder(new parser\utils\StringStream($data));

		//skip 2 bits
		$decoder->readBits(2);

		$this->assertEquals("0101110110101010", decbin($decoder->readBits(16)));
	}

	/**
	 * @covers \HIS5\lib\Sc2repParser\decoders\BitwiseDecoderBase::readBits()
	 */
	public function testReadSkip6Read10() {
		$data = pack('H*', base_convert("01011111 01101010 01101010", 2, 16));
		$decoder = new TestDecoder(new parser\utils\StringStream($data));

		//skip 6 bits
		$decoder->readBits(6);

		$this->assertEquals("0101101010", decbin($decoder->readBits(10)));
	}

	/**
	 * @covers \HIS5\lib\Sc2repParser\decoders\BitwiseDecoderBase::readBoolean()
	 */
	public function testReadBoolean() {
		$data = pack('H*', base_convert("10001000", 2, 16));
		$decoder = new TestDecoder(new parser\utils\StringStream($data));

		//skip 3 bits
		$decoder->readBits(3);

		$this->assertEquals(true, $decoder->readBoolean());
	}

	/**
	 * @covers \HIS5\lib\Sc2repParser\decoders\BitwiseDecoderBase::readUint8()
	 */
	public function testReadSkip4Uint8() {
		$data = pack('H*', base_convert("10000011 01110100", 2, 16));
		$decoder = new TestDecoder(new parser\utils\StringStream($data));

		//skip 4 bits
		$decoder->readBits(4);

		$this->assertEquals("10000100", decbin($decoder->readUint8()));
	}

	/**
	 * @covers \HIS5\lib\Sc2repParser\decoders\BitwiseDecoderBase::readBytes()
	 */
	public function testReadString() {
		//test
		$data = pack('H*', base_convert("01110100 01100101 01110011 01110100", 2, 16));
		$decoder = new TestDecoder(new parser\utils\StringStream($data));

		$this->assertEquals("test", $decoder->readBytes(4));
	}

	/**
	 * @covers \HIS5\lib\Sc2repParser\decoders\BitwiseDecoderBase::readAlignedBytes()
	 */
	public function testReadAlignedBytes() {
		$data = pack('H*', base_convert("01011111 01101010 01101010", 2, 16));
		$decoder = new TestDecoder(new parser\utils\StringStream($data));

		//skip 1 bit
		$decoder->readBits(1);

		$value = unpack('H*', $decoder->readAlignedBytes(1));
		$this->assertEquals("01101010", base_convert($value[1], 16, 2));
	}

}



/**
 * The TestDecoder is our fake decoder used to test the bit by bit reading
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\tests
 */
 class TestDecoder extends parser\decoders\BitwiseDecoderBase {
	protected function doDecode() {}
}
