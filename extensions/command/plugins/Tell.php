<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_tito\extensions\command\plugins;

use \lithium\util\String;

/**
 * Tell plugin
 *
 */
class Tell extends \li3_tito\extensions\command\Plugin {
	protected $_classes = array(
		'model' => '\li3_tito\models\Tell',
		'response' => '\lithium\console\Response'
	);

	/**
	 * possible responses
	 *
	 * @var array
	 */
	protected $_responses = array(
		'forgot' => '{:user}, me olvide de {:tell}',
		'forget_unknown' => '{:user}, Nunca supe de {:tell}',
		'success' => '{:user}, {:tell} es {:answer}',
		'unknown' => '{:user}, No se de {:tell}',
		'known' => '{:user}, yo creia que {:tell} era {:answer}',
		'remember' => '{:user}, Voy a acordarme de {:tell}',

	);

	protected static $_commands = array(
		'tell' => array('~decile', '~contale'),
		'forget' => array('~olvidate', '~olvidar')
	);

	protected static $_words = array(
		'about' => 'sobre',
		'is' => 'es',
		'to' => 'a'
	);

	/**
	 * Tells if no more plugins should catch the event if this one processed it
	 *
	 * @return boolean
	 */
	public function stopOnHandle() {
		return true;
	}

	/**
	 * Process incoming messages
	 *
	 * @param string $data
	 * @return string
	 */
	public function process($data) {
		$responses = $this->_responses;
		$model = $this->_classes['model'];
		$tells = $model::find('all');
		$key = null;
		extract($data);

		if ($message[0] == '~') {
			$words = preg_split("/[\s]/", $message, 4);

			if (in_array($words[0], (array) self::$_commands['tell'])) {
				if (!empty(self::$_words['to']) && in_array($words[1], (array) self::$_words['to'])) {
					$words = array_values(array_diff_key($words, array(1=>true)));
					if (strpos($words[2], ' ') !== false) {
						$innerWords = preg_split('/[\s]/', $words[2], 2);
						$words[2] = $innerWords[0];
						for($i=1, $limiti=count($innerWords); $i < $limiti; $i++) {
							$words[2 + $i] = $innerWords[$i];
						}
					}
				}
				$to = $words[1];
				if (in_array($words[2], (array) self::$_words['about'])) {
					$key = $words[3];
				} else {
					$key = $words[2];
				}
			} else {
				$to = $user;
				if (in_array($words[0], (array) self::$_commands['forget'])) {
					$response = $this->_forget($words[1]);
					return String::insert($response, array(
						'user' => $to, 'tell' => $words[1]
					));
				}
			}
			if (empty($key)) {
				$key = substr($words[0], 1);
			}
			$answer = null;
			$response = $responses['unknown'];
			if (isset($tells[$key])) {
				$user = $to;
				$answer = $tells[$key];
				$response = $responses['success'];
			}

			return String::insert($response, array(
				'user' => $user, 'tell' => $key, 'answer' => $answer
			));
		}
		if (stripos($message, $nick) !== false) {
			$words = preg_split("/[\s]/", $message, 4);

			if (in_array($words[1], (array) self::$_commands['forget'])) {
				$response = $this->_forget($words[2]);
				return String::insert($response, array(
					'user' => $user, 'tell' => $words[2]
				));
			}

			if (!empty($words[2]) && in_array($words[2], (array) self::$_words['is'])) {
				if (isset($tells[$words[1]])) {
					$answer = $tells[$words[1]];
					return String::insert($responses['known'], array(
						'user' => $user, 'tell' => $words[1], 'answer' => $answer
					));
				}
				if ($model::save(array($words[1] => $words[3]))) {
					return String::insert($responses['remember'], array(
						'user' => $user, 'tell' => $words[1]
					));
				}
			}
		}
	}

	protected function _forget($tell) {
		$model = $this->_classes['model'];
		$response = $this->_responses['forget_unknown'];

		if ($model::delete($tell)) {
			$response = $this->_responses['forgot'];
		}
		return $response;
	}
}
?>