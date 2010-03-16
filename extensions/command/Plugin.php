<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_tito\extensions\command;

/**
 * Abstract class for Bot plugins
 *
 */
abstract class Plugin extends \lithium\console\Command {
	/**
	 * Tells if no more plugins should catch the event if this one processed it
	 *
	 * @return boolean
	 */
	public function stopOnHandle() {
		return false;
	}
}
?>