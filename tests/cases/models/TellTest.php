<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_bot\tests\cases\models;

use \li3_bot\models\Tell;

class TellTest extends \lithium\test\Unit {

	public function setUp() {
		Tell::$path = LITHIUM_APP_PATH . '/resources/tmp/tests/test_tells.ini';
	}

	public function tearDown() {
		Tell::reset();
		if (file_exists(Tell::$path)) {
			unlink(Tell::$path);
		}
	}

	public function testSave() {
		$expected = true;
		$result = Tell::save(array('lithium' => 'http://li3.rad-dev.org'));
		$this->assertEqual($expected, $result);
	}

	public function testSaveTwoAndFindAll() {
		$expected = true;
		$result = Tell::save(array('lithium' => 'http://li3.rad-dev.org'));
		$this->assertEqual($expected, $result);

		$expected = array('lithium' => 'http://li3.rad-dev.org');
		$result = Tell::find('all');
		$this->assertEqual($expected, $result);
	}

	public function testSaveAndFind() {
		$expected = true;
		$result = Tell::save(array('lithium' => 'http://li3.rad-dev.org'));
		$this->assertEqual($expected, $result);

		$expected = 'http://li3.rad-dev.org';
		$result = Tell::find('lithium');
		$this->assertEqual($expected, $result);

		$expected = 'http://li3.rad-dev.org';
		$result = Tell::find();
		$this->assertEqual($expected, $result);
	}

	public function testSaveDeleteFind() {
		$expected = true;
		$result = Tell::save(array('li' => 'the most rad php framework'));
		$this->assertEqual($expected, $result);

		Tell::delete('li');

		Tell::reset();

		$result = Tell::find('li');
		$this->assertFalse($result);
	}

}
?>