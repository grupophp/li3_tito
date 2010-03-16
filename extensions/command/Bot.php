<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_tito\extensions\command;

use \lithium\net\socket\Stream;
use \lithium\core\Libraries;

class Bot extends \lithium\console\Command {
	/**
	 * INI file location to configure the bot
	 *
	 * @var string
	 */
	public $config = null;

	protected $_socket = null;
	protected $_running = false;
	protected $_resource = null;
	protected $_nick = 'li3_tito';
	protected $_channels = array();
	protected $_joined = array();
	protected $_plugins = array();
	protected $_listeners = array('poll' => array(), 'process' => array());

	/**
	 * Constructor.
	 *
	 * @param array $config
	 * @return void
	 */
	/*
	public function __construct($config = array()) {
		$this->params = isset($config['params']) ? $config['params'] : null;
		parent::__construct(array_diff_key($config, array('params'=>null)));
	}
	*/

	public function _init() {
		parent::_init();
		if (!empty($this->params)) {
			foreach ($this->params as $key => $param) {
				$this->{$key} = $param;
			}
		}

		if (empty($this->config)) {
			$this->config = dirname(dirname(dirname(__DIR__))) . '/config/tito.ini';
		}

		if (!is_file($this->config)) {
			$this->error("The configuration file {$this->config} could not be read");
			$this->stop();
		}

		$this->_config += parse_ini_file($this->config);
		foreach ($this->_config as $key => $value) {
			$key = "_{$key}";
			if (isset($this->{$key}) && $key !== '_classes') {
				$this->{$key} = $value;
				if ($value && strpos($value, ',') !== false) {
					$this->{$key} = array_map('trim', (array) explode(',', $value));
				}
			}
		}

		$this->_socket = new Stream($this->_config);

		if (empty($this->_plugins)) {
			$classes = Libraries::locate('command.plugins');
			$plugins = array_flip($classes);
			foreach($plugins as $class => $weight) {
				$plugins[$class] = isset($class::$weight) ? $class::$weight : 0;
			}
			asort($plugins);
			$this->_plugins = array_keys($plugins);
		} else {
			foreach($this->_plugins as &$plugin) {
				if (strpos($plugin, '\\') === false) {
					$plugin = "li3_tito\\extensions\\command\\plugins\\{$plugin}";
				}
			}
		}

		foreach ($this->_plugins as $class) {
			if (method_exists($class, 'poll')) {
				$this->_listeners['poll'][] = new $class($this->_config);
			}
			if (method_exists($class, 'process')) {
				$this->_listeners['process'][] = new $class($this->_config);
			}
		}
	}

	public function run() {
		try {
			$this->_running = (bool) $this->_socket->open();
			$this->_resource = $this->_socket->resource();
		} catch (Exception $e) {
			$this->out($e);
		}

		if ($this->_running) {
			$this->out('connected');
			$this->_connect();
		}

		while($this->_running && !$this->_socket->eof()) {
			$this->_process();
		}
	}

	public function __call($method, $params) {
		if ($method[0] === '_') {
			$value = empty($params) ? $this->{$method} : $params[0];
			$command = strtoupper(ltrim($method, '_')) . " {$value}\r\n";
			return $this->_socket->write($command);
		}
	}

	protected function _connect() {
		$this->_nick();
		$this->_user("{$this->_nick} {$this->_config['host']} botts :{$this->_nick}");
	}

	protected function _process() {
		$line =	 fgets($this->_resource);

		if (stripos($line, 'PING') !== false) {
			list($ping, $pong) = $this->_parse(':', $line, 2);
			$this->_pong($pong);
			$this->_triggerAction('poll');
			return true;
		}

		if ($line{0} === ':') {
			$params = $this->_parse("\s:", $line, 5);

			if (isset($params[2])) {

				$cmd = $params[2];
				$msg = !empty($params[4]) ? $params[4] : null;

				switch ($cmd) {
					case 'PRIVMSG':
						$channel = $params[3];
						$user = $this->_parse("!", $params[1], 3);
						$this->_triggerAction('process', array(
							'channel' => $channel, 'nick'=> $this->_nick,
							'user' => $user[0], 'message' => $msg
						));
					break;

					case '461':
					case '422':
					case '376':
						foreach ((array)$this->_channels as $channel) {
							if (empty($this->_joined[$channel])) {
								$this->_join($channel);
								$this->out("{$this->_nick} joined {$channel}");
								$this->_joined[$channel] = true;
							}
						}
					break;

					case '433': //Nick already registerd
						$this->out($msg);
						$this->_nick = $this->_nick . '_';
						$this->_connect();
					break;

					case '353':
						$this->out('Names on ' . str_replace('=', '', $msg));
					break;

					default:
						$this->out($msg);
					break;
				}
			}
		}
	}

	protected function _triggerAction($action, $data = null) {
		if (empty($this->_listeners[$action])) {
			return false;
		}
		foreach ($this->_listeners[$action] as $class) {
			if ($action == 'poll') {
				$responses = $class->poll();
			} elseif ($action == 'process') {
				$responses = $class->process($data);
			}
			$this->_respond($this->_channels, $responses);
			if (!empty($responses) && $class->stopOnHandle()) {
				break;
			}
		}
	}

	protected function _respond($channels, $responses) {
		if (empty($responses)) {
			return;
		}
		foreach ((array)$channels as $channel) {
			$this->out('Sending ' . count($responses) . ' messages to ' . $channel);
			foreach ((array)$responses as $response) {
				$this->_privmsg("{$channel} :{$response}");
			}
		}
	}

	protected function _parse($regex, $string, $offset = -1) {
		return str_replace(array("\r\n", "\n"), '', preg_split("/[{$regex}]+/", $string, $offset));
	}
}
?>