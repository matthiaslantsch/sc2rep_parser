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
use PHPUnit\Framework\TestCase;

/**
 * The ReplayParserTest tests the ReplayParser class for correct values
 *
 * @author  {AUTHOR}
 * @version {VERSION}
 * @package HIS5\lib\Sc2repParser\tests
 */
class ReplayParserTest extends TestCase {

	/**
	 * Contains hardcoded details about my replay files in order to test our code
	 */
	public function replayProvider() {
		return [
			//beta phase 1
			'beta patch 1' => ['0.2.0.13891/0.2.0.13891.SC2Replay', new parser\ressources\Replay(13891, "0.2.0.13891", 7680, "WoL Beta")],
			'beta patch 3' => ['0.4.0.14133/0.4.0.14133.SC2Replay', new parser\ressources\Replay(14133, "0.4.0.14133", 48112, "WoL Beta")],
			'beta patch 4' => ['0.6.0.14259/0.6.0.14259.SC2Replay', new parser\ressources\Replay(14259, "0.6.0.14259", 33552, "WoL Beta")],
			'beta patch 5' => ['0.7.0.14356/0.7.0.14356.SC2Replay', new parser\ressources\Replay(14356, "0.7.0.14356", 35008, "WoL Beta")], //"36 minutes"
			'beta patch 6' => ['0.8.0.14593/0.8.0.14593.SC2Replay', new parser\ressources\Replay(14593, "0.8.0.14593", 16288, "WoL Beta")],
			'beta patch 7' => ['0.9.0.14621/0.9.0.14621.SC2Replay', new parser\ressources\Replay(14621, "0.9.0.14621", 3632, "WoL Beta")],
			'0.10.0.14803' => ['0.10.0.14803/0.10.0.14803.SC2Replay', new parser\ressources\Replay(14803, "0.10.0.14803", 21936, "WoL Beta")], //00:22:51
			//new replay format with initdata => replay version 2
			'0.11.0.15097' => ['0.11.0.15097/0.11.0.15097.SC2Replay', new parser\ressources\Replay(15097, "0.11.0.15097", 20416, "WoL Beta")],
			'0.13.0.15250' => ['0.13.0.15250/0.13.0.15250.SC2Replay', new parser\ressources\Replay(15250, "0.13.0.15250", 44720, "WoL Beta")],
			'0.16.0.15580' => ['0.16.0.15580/0.16.0.15580.SC2Replay', new parser\ressources\Replay(15580, "0.16.0.15580", 14048, "WoL Beta")],
			'0.17.0.15623' => ['0.17.0.15623/0.17.0.15623.SC2Replay', new parser\ressources\Replay(15623, "0.17.0.15623", 12992, "WoL Beta")],
			//beta phase 2 => header packed struct
			'0.19.0.15976' => ['0.19.0.15976/0.19.0.15976.SC2Replay', new parser\ressources\Replay(15976, "0.19.0.15976", 2282, "WoL Beta")],
			'1.3.0.18092' => ['1.3.0.18092/1.3.0.18092.SC2Replay', new parser\ressources\Replay(18092, "1.3.0.18092", 13946, "WoL")],
			'1.3.1.18221' => ['1.3.1.18221/1.3.1.18221.SC2Replay', new parser\ressources\Replay(18092, "1.3.1.18221", 33970, "WoL")],
			'1.3.2.18317' => ['1.3.2.18317/1.3.2.18317.SC2Replay', new parser\ressources\Replay(18092, "1.3.2.18317", 36425, "WoL")],
			'1.3.3.18574' => ['1.3.3.18574/1.3.3.18574.SC2Replay', new parser\ressources\Replay(18574, "1.3.3.18574", 7437, "WoL")],
			'1.3.4.18701' => ['1.3.4.18701/1.3.4.18701.SC2Replay', new parser\ressources\Replay(18574, "1.3.4.18701", 8250, "WoL")],
			'1.3.5.19132' => ['1.3.5.19132/1.3.5.19132.SC2Replay', new parser\ressources\Replay(19132, "1.3.5.19132", 39280, "WoL")],
			'1.3.6.19269' => ['1.3.6.19269/1.3.6.19269.SC2Replay', new parser\ressources\Replay(19132, "1.3.6.19269", 24337, "WoL")],
			'1.4.0.19679' => ['1.4.0.19679/1.4.0.19679.SC2Replay', new parser\ressources\Replay(19679, "1.4.0.19679", 30421, "WoL")],
			'1.4.3.21029' => ['1.4.3.21029/1.4.3.21029.SC2Replay', new parser\ressources\Replay(21029, "1.4.3.21029", 30527, "WoL")],
			'1.5.3.23260' => ['1.5.3.23260/1.5.3.23260.SC2Replay', new parser\ressources\Replay(23260, "1.5.3.23260", 18340, "WoL")],
			'1.5.4.24540' => ['1.5.4.24540/1.5.4.24540.SC2Replay', new parser\ressources\Replay(23260, "1.5.4.24540", 26533, "WoL")],
			'2.0.0.23925' => ['2.0.0.23925/2.0.0.23925.SC2Replay', new parser\ressources\Replay(23925, "2.0.0.23925", 8225, "WoL")],
			'2.0.0.24247' => ['2.0.0.24247/2.0.0.24247.SC2Replay', new parser\ressources\Replay(24247, "2.0.0.24247", 14554, "WoL")],
			'2.0.3.24764' => ['2.0.3.24764/2.0.3.24764.SC2Replay', new parser\ressources\Replay(24764, "2.0.3.24764", 10465, "WoL")],
			'2.0.4.24944' => ['2.0.4.24944/2.0.4.24944.SC2Replay', new parser\ressources\Replay(24944, "2.0.4.24944", 27840, "WoL")],
			'2.0.5.25092' => ['2.0.5.25092/2.0.5.25092.SC2Replay', new parser\ressources\Replay(24944, "2.0.5.25092", 15780, "WoL")],
			'2.0.7.25293' => ['2.0.7.25293/2.0.7.25293.SC2Replay', new parser\ressources\Replay(24944, "2.0.7.25293", 24154, "WoL")],
			'2.0.8.25604' => ['2.0.8.25604/2.0.8.25604.SC2Replay', new parser\ressources\Replay(24944, "2.0.8.25604", 12166, "WoL")],
			'2.0.10.26490' => ['2.0.10.26490/2.0.10.26490.SC2Replay', new parser\ressources\Replay(26490, "2.0.10.26490", 15592, "HotS")],
			'2.0.11.26825' => ['2.0.11.26825/2.0.11.26825.SC2Replay', new parser\ressources\Replay(26490, "2.0.11.26825", 19924, "HotS")],
			'2.1.3.30508' => ['2.1.3.30508/2.1.3.30508.SC2Replay', new parser\ressources\Replay(28667, "2.1.3.30508", 3787, "HotS")],
			'2.1.4.32283' => ['2.1.4.32283/2.1.4.32283.SC2Replay', new parser\ressources\Replay(32283, "2.1.4.32283", 8569, "HotS")],
			'3.0.0.38215' => ['3.0.0.38215/3.0.0.38215.SC2Replay', new parser\ressources\Replay(38215, "3.0.0.38215", 21868, "HotS")],
			'3.0.4.38996' => ['3.0.4.38996/3.0.4.38996.SC2Replay', new parser\ressources\Replay(38996, "3.0.4.38996", 8805, "LotV Beta")],
			'3.1.0.39576' => ['3.1.0.39576/3.1.0.39576.SC2Replay', new parser\ressources\Replay(39576, "3.1.0.39576", 9029, "LotV")],
			'3.2.2.42253' => ['3.2.2.42253/3.2.2.42253.SC2Replay', new parser\ressources\Replay(42253, "3.2.2.42253", 17735, "LotV")],
			'3.3.1.43199' => ['3.3.1.43199/3.3.1.43199.SC2Replay', new parser\ressources\Replay(42932, "3.3.1.43199", 19386, "LotV")],
			'3.4.0.44401' => ['3.4.0.44401/3.4.0.44401.SC2Replay', new parser\ressources\Replay(44401, "3.4.0.44401", 7008, "LotV")],
		];
	}

	/**
	 * Intitialises a test data loader class for each replay in the hardcoded array
	 */
	public function replayDetailData() {
		foreach ($this->replayProvider() as $name => $arr) {
			$ret[$name] = [$arr[0], new TestDataLoader($arr[1]->version)];
		}

		return $ret;
	}

	/**
	 * @covers \HIS5\lib\Sc2repParser\ReplayParser::__construct()
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
	 * @dataProvider replayProvider
	 */
	public function testHeaderDecode($path, $expected) {
		$parser = new parser\ReplayParser(__DIR__.DIRECTORY_SEPARATOR."test_replays".DIRECTORY_SEPARATOR.$path);

		$this->assertEquals($expected, $parser->replay);
	}

	/**
	 * @covers \HIS5\lib\Sc2repParser\ReplayParser::compare()
	 * @uses   \HIS5\lib\Sc2repParser\ReplayParser::identify()
	 */
	public function testReplayHash() {
		$identifyFirst = parser\ReplayParser::identify(__DIR__.DIRECTORY_SEPARATOR."test_replays"
			.DIRECTORY_SEPARATOR."same_match".DIRECTORY_SEPARATOR."Full.SC2Replay");
		$identifySecond = parser\ReplayParser::identify(__DIR__.DIRECTORY_SEPARATOR."test_replays"
					.DIRECTORY_SEPARATOR."same_match".DIRECTORY_SEPARATOR."Partial.SC2Replay");

		$this->assertEquals(true, parser\ReplayParser::compare($identifyFirst["repHash"], $identifySecond["repHash"]));
	}

	/**
	 * @covers \HIS5\lib\Sc2repParser\ReplayParser::identify()
	 * @dataProvider replayDetailData
	 */
	public function testIdentify($repFile, $dataLoader) {
		$identify = parser\ReplayParser::identify(__DIR__.DIRECTORY_SEPARATOR."test_replays".DIRECTORY_SEPARATOR.$repFile);

		$expected = $dataLoader->load("identify");
		$this->assertEquals($expected, $identify);
	}

	/**
	 * @covers \HIS5\lib\Sc2repParser\ReplayParser::decode()
	 * @dataProvider replayDetailData
	 */
	public function testMessageEventsDecoder($repFile, $dataLoader) {
		$parser = new parser\ReplayParser(__DIR__.DIRECTORY_SEPARATOR."test_replays".DIRECTORY_SEPARATOR.$repFile);
		$parser->doIdentify();
		$parser->decodeFile("replay.message.events", parser\decoders\MessageEventsDecoder::class);

		$expected = $dataLoader->load("message.events");
		$this->assertEquals($expected, $parser->replay->eventArray("message"));
	}

	/**
	 * @covers \HIS5\lib\Sc2repParser\ReplayParser::decode()
	 * @dataProvider replayDetailData
	 */
	public function testGameEventsDecoder($repFile, $dataLoader) {
		$parser = new parser\ReplayParser(__DIR__.DIRECTORY_SEPARATOR."test_replays".DIRECTORY_SEPARATOR.$repFile);
		$parser->doIdentify();

		if($parser->replay->baseBuild <= 15623) {
			$this->markTestIncomplete(
			  'Game event parsing for beta builds has not been implemented yet.'
			);
		} else {
			$parser->decodeFile("replay.game.events", parser\decoders\GameEventsDecoder::class);
			$expected = $dataLoader->load("game.events");
			$this->assertEquals($expected, $parser->replay->eventArray("game"));
		}
	}

}
