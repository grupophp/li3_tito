<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_bot\tests\mocks\extensions\command;

class MockIrcSocket extends \lithium\util\Socket {

	protected $_data = null;

	public function open() {
		$this->_resource = fopen('php://memory', 'w+');
		return true;
	}

	public function close() {
		return true;
	}

	public function eof() {
		return true;
	}

	public function read() {
		fgets($this->_resource);
	}

	public function write($data) {
		fwrite($this->_resource, $data);
	}

	public function timeout($time) {
		return true;
	}

	public function encoding($charset) {
		return true;
	}

	public function send($message, $options = array()) {
		return true;
	}
}

?>