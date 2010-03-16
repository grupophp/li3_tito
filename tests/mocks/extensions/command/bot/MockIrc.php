<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_bot\tests\mocks\extensions\command\bot;

class MockIrc extends \li3_bot\extensions\command\bot\Irc {
	
	public function process() {
		return $this->_process();
	}
	
}