<?php
/**
 * This file is part of the hdev activerecord library
 * (c) Matthias Lantsch
 *
 * class file for the ReplayParserTest PHPUnit test class
 */

namespace HIS5\lib\Sc2repParser\tests;

use HIS5\lib\common as co;
use HIS5\lib\Sc2repParser as parser;

/**
 * The ReplayParserTest tests the ReplayParser class for correct values
 * 
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\tests
 */
class ReplayParserTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Contains hardcoded details about my replay files in order to test our code
	 */
    public function replayProvider() {
        return [
            'adding zeros'  => [0, 0, 0],
            'zero plus one' => [0, 1, 1],
            'one plus zero' => [1, 0, 1],
            'one plus one'  => [1, 1, 3]
        ];
    }

	/**
	 * @covers \HIS5\lib\Sc2repParser\ReplayParser::__construct()
	 * @uses   \Rogiel\MPQ\MPQFile::parseFile()
	 */
	public function testFileNotFoundException() {
		$msg = null;

		try {
			new parser\ReplayParser("some file.sc2replay");
		} catch (\HIS5\lib\Sc2repParser\ParserException $e) {
			$msg = $e->getMessage();
		}

		$this->assertEquals($msg, "The replay file 'some file.sc2replay' could not be found/read");
	}

	/**
	 * @covers \HIS5\lib\Sc2repParser\ReplayParser::__construct()
	 * @uses   \Rogiel\MPQ\MPQFile::parseFile()
     * @dataProvider replayProvider
	 */
	public function testHeaderDecode($path, $expected) {
		$parser = new parser\ReplayParser(__DIR__.DIRECTORY_SEPARATOR."test_replays".DIRECTORY_SEPARATOR.$path);

		$this->assertEquals($parser->replay, $expected);
	}

}