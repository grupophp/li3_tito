<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_bot\tests\cases\models;

class MockFeed extends \li3_bot\models\Feed {

	protected static $_config = array('feeds' => array(
		'lithium' => 'http://rad-dev.org/lithium/timeline.rss'
	));
}

class FeedTest extends \lithium\test\Unit {

	public function setUp() {
	}

	public function tearDown() {
		MockFeed::reset();
	}

	public function testFindFirst() {
		$expected = array();
		$result = MockFeed::find('first');
		$this->assertEqual($expected, $result);
	}

	public function testFindNew() {
		$initialize = MockFeed::find('lithium');

		$expected = 1;
		$result = MockFeed::find('new', array('ping' => false, 'name' => 'lithium'));
		$this->assertEqual($expected, count($result));
	}

	public function testFindAll() {
		$initialize = MockFeed::find('lithium');

		$expected = 4;
		$result = MockFeed::find('all', array('ping' => false, 'name' => 'lithium'));
		$this->assertEqual($expected, count($result));
	}

}
?>