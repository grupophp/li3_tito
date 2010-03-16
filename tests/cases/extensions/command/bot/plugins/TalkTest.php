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

class MockTalk extends \li3_bot\extensions\command\bot\plugins\Talk {
}

class TalkTest extends \lithium\test\Unit {
	public function setUp() {
		$this->talk = new MockTalk(array('init' => false));
		$this->talk->request = new Request(array('input' => fopen('php://temp', 'w+')));
		$this->talk->response = new Response(array(
			'output' => fopen('php://temp', 'w+'),
			'error' => fopen('php://temp', 'w+')
		));

		$this->working = LITHIUM_APP_PATH;
		if (!empty($_SERVER['PWD'])) {
			$this->working = $_SERVER['PWD'];
		}
	}

	public function tearDown() {
		unset($this->talk);
	}

	public function testGreetings() {
		$message = 'Hola botito';
		$expected = 'mariano_iglesias: Como estas?';
		$result = $this->talk->process(array(
		 	'channel' => '#li3', 'nick'=> 'Botito',
		 	'user' => 'mariano_iglesias', 'message' => $message
		));
		$this->assertEqual($expected, $result);

		$expected = 'mariano_iglesias: Como estas?';
		$message = 'Botito: hola!';
		$result = $this->talk->process(array(
		 	'channel' => '#li3', 'nick'=> 'Botito',
		 	'user' => 'mariano_iglesias', 'message' => $message
		));
		$this->assertEqual($expected, $result);

		$message = 'Chau Botito!';
		$expected = 'mariano_iglesias: Chau que te sea leve!';
		$result = $this->talk->process(array(
		 	'channel' => '#li3', 'nick'=> 'Botito',
		 	'user' => 'mariano_iglesias', 'message' => $message
		));
		$this->assertEqual($expected, $result);
	}

	public function testHora() {
		$message = 'Botito: hora';
		$expected = '/Son las \d+:\d+ en mi reloj/i';
		$result = $this->talk->process(array(
		 	'channel' => '#li3', 'nick'=> 'Botito',
		 	'user' => 'mariano_iglesias', 'message' => $message
		));
		$this->assertPattern($expected, $result);

		$message = 'Bien, vos Botito?';
		$result = $this->talk->process(array(
		 	'channel' => '#li3', 'nick'=> 'Botito',
		 	'user' => 'mariano_iglesias', 'message' => $message
		));
		var_dump($result);
	}
}

?>
