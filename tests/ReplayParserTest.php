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
			'1.3.0.18092' => ['1.3.0.18092.SC2Replay', new parser\ressources\Replay(18092, "1.3.0.18092", 13946)],
			'1.3.1.18221' => ['1.3.1.18221.SC2Replay', new parser\ressources\Replay(18092, "1.3.1.18221", 33970)],
			'1.3.2.18317' => ['1.3.2.18317.SC2Replay', new parser\ressources\Replay(18092, "1.3.2.18317", 36425)],
			'1.3.3.18574' => ['1.3.3.18574.SC2Replay', new parser\ressources\Replay(18574, "1.3.3.18574", 7437)],
			'1.3.4.18701' => ['1.3.4.18701.SC2Replay', new parser\ressources\Replay(18574, "1.3.4.18701", 8250)],
			'1.3.5.19132' => ['1.3.5.19132.SC2Replay', new parser\ressources\Replay(19132, "1.3.5.19132", 39280)],
			'1.3.6.19269' => ['1.3.6.19269.SC2Replay', new parser\ressources\Replay(19132, "1.3.6.19269", 24337)],
			'1.4.0.19679' => ['1.4.0.19679.SC2Replay', new parser\ressources\Replay(19679, "1.4.0.19679", 30421)],
			'1.4.3.21029' => ['1.4.3.21029.SC2Replay', new parser\ressources\Replay(21029, "1.4.3.21029", 30527)],
			'1.5.3.23260' => ['1.5.3.23260.SC2Replay', new parser\ressources\Replay(23260, "1.5.3.23260", 18340)],
			'1.5.4.24540' => ['1.5.4.24540.SC2Replay', new parser\ressources\Replay(23260, "1.5.4.24540", 26533)],
			'2.0.0.23925' => ['2.0.0.23925.SC2Replay', new parser\ressources\Replay(23925, "2.0.0.23925", 8225)],
			'2.0.0.24247' => ['2.0.0.24247.SC2Replay', new parser\ressources\Replay(24247, "2.0.0.24247", 14554)],
			'2.0.3.24764' => ['2.0.3.24764.SC2Replay', new parser\ressources\Replay(24764, "2.0.3.24764", 10465)],
			'2.0.4.24944' => ['2.0.4.24944.SC2Replay', new parser\ressources\Replay(24944, "2.0.4.24944", 27840)],
			'2.0.5.25092' => ['2.0.5.25092.SC2Replay', new parser\ressources\Replay(24944, "2.0.5.25092", 15780)],
			'2.0.7.25293' => ['2.0.7.25293.SC2Replay', new parser\ressources\Replay(24944, "2.0.7.25293", 24154)],
			'2.0.8.25604' => ['2.0.8.25604.SC2Replay', new parser\ressources\Replay(24944, "2.0.8.25604", 12166)],
			'2.0.10.26490' => ['2.0.10.26490.SC2Replay', new parser\ressources\Replay(26490, "2.0.10.26490", 15592)],
			'2.0.11.26825' => ['2.0.11.26825.SC2Replay', new parser\ressources\Replay(26490, "2.0.11.26825", 19924)],
			'2.1.3.30508' => ['2.1.3.30508.SC2Replay', new parser\ressources\Replay(28667, "2.1.3.30508", 3787)],
			'2.1.4.32283' => ['2.1.4.32283.SC2Replay', new parser\ressources\Replay(32283, "2.1.4.32283", 8569)],
			'3.0.0.38215' => ['3.0.0.38215.SC2Replay', new parser\ressources\Replay(38215, "3.0.0.38215", 21868)],
			'3.0.4.38996' => ['3.0.4.38996.SC2Replay', new parser\ressources\Replay(38996, "3.0.4.38996", 8805)],
			'3.1.0.39576' => ['3.1.0.39576.SC2Replay', new parser\ressources\Replay(39576, "3.1.0.39576", 9029)],
			'3.2.2.42253' => ['3.2.2.42253.SC2Replay', new parser\ressources\Replay(42253, "3.2.2.42253", 17735)],
			'3.3.1.43199' => ['3.3.1.43199.SC2Replay', new parser\ressources\Replay(42932, "3.3.1.43199", 19386)],
		];
	}

	/**
	 * Intitialises a test data loader class for each replay in the hardcoded array
	 */
	public function replayDetailData() {
		
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

		$this->assertEquals($expected, $parser->replay);
	}

	/**
	 * @covers \HIS5\lib\Sc2repParser\ReplayParser::__construct()
	 * @uses   \Rogiel\MPQ\MPQFile::parseFile()
	 */
	public function testIdentify() {
		$parser = parser\ReplayParser::identify(__DIR__.DIRECTORY_SEPARATOR."test_replays".DIRECTORY_SEPARATOR."2.1.3.30508.SC2Replay");
		//$parser = parser\ReplayParser::identify("/home/matthias/workspace/old.hdev/age_old/workdir/sc2rep/updir/3.sc2replay");

		$this->assertEquals($expected, $parser->replay);		
	}

}