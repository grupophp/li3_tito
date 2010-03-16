<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_bot\tests\cases\extensions\command\bot\plugins;

use \lithium\console\Request;
use \lithium\console\Response;

class MockTellModel extends \li3_bot\models\Tell {

	public static function __init() {
		static::$path = LITHIUM_APP_PATH . '/resources/tmp/tests/test_tells.ini';
	}
}

class MockTell extends \li3_bot\extensions\command\bot\plugins\Tell {

	protected $_classes = array(
		'model' => '\li3_bot\tests\cases\extensions\command\bot\plugins\MockTellModel'
	);
}

class TellTest extends \lithium\test\Unit {

	public function setUp() {
		MockTellModel::__init();
		$this->tell = new MockTell(array('init' => false));
		$this->tell->request = new Request(array('input' => fopen('php://temp', 'w+')));
		$this->tell->response = new Response(array(
			'output' => fopen('php://temp', 'w+'),
			'error' => fopen('php://temp', 'w+')
		));

		$this->working = LITHIUM_APP_PATH;
		if (!empty($_SERVER['PWD'])) {
			$this->working = $_SERVER['PWD'];
		}
	}

	public function tearDown() {
		unset($this->tell);
		$this->_cleanUp();
	}

	public function testProcess() {
		$expected = 'gwoo, I do not know about cool';
		$result = $this->tell->process(array(
		 	'channel' => '#li3', 'nick'=> 'Li3Bot',
		 	'user' => 'gwoo', 'message' => '~cool'
		));
		$this->assertEqual($expected, $result);

		$expected = 'gwoo, I will remember lithium';
		$result = $this->tell->process(array(
		 	'channel' => '#li3', 'nick'=> 'Li3Bot',
		 	'user' => 'gwoo', 'message' => 'Li3Bot: lithium is cool'
		));
		$this->assertEqual($expected, $result);

		$expected = 'gwoo, lithium is cool';
		$result = $this->tell->process(array(
		 	'channel' => '#li3', 'nick'=> 'Li3Bot',
		 	'user' => 'gwoo', 'message' => '~lithium'
		));
		$this->assertEqual($expected, $result);

		$expected = 'bob, lithium is cool';
		$result = $this->tell->process(array(
		 	'channel' => '#li3', 'nick'=> 'Li3Bot',
		 	'user' => 'gwoo', 'message' => '~tell bob about lithium'
		));
		$this->assertEqual($expected, $result);

		$expected = 'gwoo, I do not know about something';
		$result = $this->tell->process(array(
		 	'channel' => '#li3', 'nick'=> 'Li3Bot',
		 	'user' => 'gwoo', 'message' => '~tell bob about something'
		));
		$this->assertEqual($expected, $result);
	}

	public function testForget() {
		MockTellModel::reset();
		$expected = 'gwoo, I will remember lithium';
		$result = $this->tell->process(array(
		 	'channel' => '#li3', 'nick'=> 'Li3Bot',
		 	'user' => 'gwoo', 'message' => 'Li3Bot: lithium is cool'
		));
		$this->assertEqual($expected, $result);

		$expected = 'gwoo, I forgot about lithium';
		$result = $this->tell->process(array(
		 	'channel' => '#li3', 'nick'=> 'Li3Bot',
		 	'user' => 'gwoo', 'message' => '~forget lithium'
		));
		$this->assertEqual($expected, $result);

		$expected = 'gwoo, I never knew about lithium';
		$result = $this->tell->process(array(
		 	'channel' => '#li3', 'nick'=> 'Li3Bot',
		 	'user' => 'gwoo', 'message' => '~forget lithium'
		));
		$this->assertEqual($expected, $result);
	}
}

?>