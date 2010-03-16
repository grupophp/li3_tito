<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_tito\controllers;

use \li3_tito\models\Log;

class LogsController extends \lithium\action\Controller {
	public function __construct($config = array()) {
		parent::__construct($config);
		$this->_render['layout'] = '../../../../../views/layouts/default';
	}
	public function index($channel = null) {
		$channels = Log::find('all');
		$breadcrumbs = array('bot' => 'Channels');
		$logs = null;

		if ($channel) {
			$breadcrumbs['#'] = '#'.$channel;
			$logs = Log::find('all', compact('channel'));

			natsort($logs);
			$logs = array_reverse($logs);
		}

		return(compact('channels', 'channel', 'logs', 'breadcrumbs'));
	}

	public function view($channel, $date = null) {
		if (is_null($date)) {
			return $this->index($channel);
		}
		$breadcrumbs = array('bot' => 'Channels');
		$breadcrumbs['bot/'.$channel] = '#'.$channel;
		$breadcrumbs['#'] = $date;

		$channels = Log::find('all');
		$log = Log::read($channel, $date);
		$previous = date('Y-m-d', strtotime($date) - (60 * 60 * 24));
		$next = date('Y-m-d', strtotime($date) + (60 * 60 * 24));

		if (!Log::exists($channel, $previous)) {
			$previous = null;
		}
		if (!Log::exists($channel, $next)) {
			$next = null;
		}

		return(compact('channels', 'channel', 'log', 'date', 'breadcrumbs', 'previous', 'next'));
	}
}

?>