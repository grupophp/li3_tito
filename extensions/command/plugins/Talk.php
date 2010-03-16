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
 * Log plugin
 *
 */
class Talk extends \li3_tito\extensions\command\Plugin {
	public static $weight = -50;

	protected $_classes = array(
		'response' => '\lithium\console\Response'
	);

	/**
	 * possible responses
	 *
	 * @var array
	 */
	protected $_responses = array(
		'/^hola/' => 'Como estas?',
		'/^chau/' => 'Chau que te sea leve!',
		array(
			'regex' => array(
				'/como\s+(te\s+va|andas?|(es)?tas?)/',
				'/bien([\s,;]+)vos\??/'
			),
			'response' => array(
				'Yo? Joya! :]',
				'Y aca, evaluando... La vida de un robot no es facil',
				'Bien',
				'Mejor ahora que preguntas',
				'Medio dormido... Me obligan a estar online todo el dia!',
				'Esperando una llamada de R2D2 que no llega nunca... :[',
				'Haciendo dieta, tratando de perder unos megabytes'
			)
		),
		'/\bhora\b/' => '_time'
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
	 * log messages
	 *
	 * @param array data
	 * @return array
	 */
	public function process($data) {
		$output = null;
		extract($data);

		if (strpos($message, $nick . ':') === 0 || preg_match('/\b' . preg_quote($nick) . '\b/i', $message)) {
			if (strpos($message, $nick . ':') === 0) {
				$message = strtolower(preg_replace('/^' . preg_quote($nick) . '\s*:\s*/', '', $message));
			} else {
				$message = preg_replace('/\s*' . preg_quote($nick) . '\s*/i', '', $message);
			}

			foreach($this->_responses as $regex => $response) {
				if (is_numeric($regex) && is_array($response) && !empty($response['regex'])) {
					extract($response);
				}

				foreach((array) $regex as $currentRegex) {
					if ($currentRegex[strlen($currentRegex) - 1] == '/') {
						$currentRegex .= 'i';
					}

					if (preg_match($currentRegex, $message)) {
						$output = $response;
						if (is_string($response) && is_callable(array($this, $response))) {
							$output = $this->$response($message);
						}
						break;
					}
				}
				if (!empty($output)) {
					break;
				}
			}
		}
		if (!empty($output)) {
			if (is_array($output)) {
				$output = $output[array_rand($output)];
			}
			$output = "{$user}: {$output}";
		}
		return $output;
	}

	public function _time($message) {
		date_default_timezone_set('America/Buenos_Aires');
		return 'Son las ' . date('H:i') . ' en mi reloj';
	}
}
?>