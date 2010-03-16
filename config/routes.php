<?php

use \lithium\net\http\Router;

Router::connect('/bot/view/{:args}', array(
	'plugin' => 'li3_bot', 'controller' => 'logs', 'action' => 'view'
));
Router::connect('/bot/{:args}', array('plugin' => 'li3_bot', 'controller' => 'logs'));


?>
