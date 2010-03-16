<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_tito\extensions\command\plugins;

/**
 * Log plugin
 *
 */
class Logging extends \li3_tito\extensions\command\Plugin {
	public static $weight = -100;
	protected $_log = false;
	protected $_classes = array(
		'model' => '\li3_tito\models\Log',
		'response' => '\lithium\console\Response'
	);
	protected $_stopOnHandle = false;

	/**
	 * Tells if no more plugins should catch the event if this one processed it
	 *
	 * @return boolean
	 */
	public function stopOnHandle() {
		return $this->_stopOnHandle;
	}

	/**
	 * log messages
	 *
	 * @param array data
	 * @return array
	 */
	public function process($data) {
		$output = null;
		extract($data);

		$this->_stopOnHandle = false;
		if (strpos($message, '~log ') === 0 || strcasecmp($message, '~log') == 0) {
			if (strpos($message, ' ') !== false) {
				$command = preg_split("/[\s]/", $message, 2);
				if (strtolower($command[0]) == '~log') {
					$command[1] = strtolower($command[1]);
					if (in_array($command[1], array('on', 'off'))) {
						$this->_log = ($command[1] == 'on');
					}
				}
			}

			$output = $user . ": Logging " . (empty($command) ? "esta " : "") . ($this->_log ? "activado" : "desactivado");
			$this->_stopOnHandle = true;
		}

		if ($this->_log) {
			$model = $this->_classes['model'];
			$model::save($data);
		}

		return $output;
	}
}
?>