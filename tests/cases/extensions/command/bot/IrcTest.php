<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_bot\tests\cases\extensions\command\bot;

use \lithium\console\Request;
use \lithium\console\Response;
use \li3_bot\tests\mocks\extensions\command\bot\MockIrc;
use li3_bot\tests\mocks\extensions\command\MockIrcSocket;


class IrcTest extends \lithium\test\Unit {

	public function setUp() {
		$this->irc = new MockIrc(array('init' => false, 'host' => 'localhost'));
		$this->irc->request = new Request(array('input' => fopen('php://temp', 'w+')));
		$this->irc->response = new Response(array(
			'output' => fopen('php://temp', 'w+'),
			'error' => fopen('php://temp', 'w+')
		));
		$this->irc->socket = new MockIrcSocket();
	}

	public function tearDown() {
		unset($this->irc);
	}

	public function testRun() {
		$result = $this->irc->run();
		$resource = $this->irc->socket->resource();
		rewind($resource);
		rewind($this->irc->response->output);

		$expected = "connected\n";
		$result = fgets($this->irc->response->output);
		$this->assertEqual($expected, $result);

		$expected = "NICK li3_bot\r\nUSER li3_bot localhost botts :li3_bot\r\n";
		$result = fread($resource, 1024);
		$this->assertEqual($expected, $result);
	}

	public function testProcess() {
		$this->irc->run();
		$resource = $this->irc->socket->resource();
		rewind($resource);
		fwrite($resource, 'something');
		rewind($resource);
		$result = $this->irc->process();

		rewind($this->irc->response->output);

		$expected = "connected\n";
		$result = fgets($this->irc->response->output);
		$this->assertEqual($expected, $result);

		$expected = "something";
		$result = fread($resource, 1024);
		$this->assertEqual($expected, $result);
	}
}

?>