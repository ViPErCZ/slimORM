<?php

// absolute filesystem path to the web root
define('WWW_DIR', __DIR__);

// absolute filesystem path to the application root
define('APP_DIR', WWW_DIR . '/app');

// absolute filesystem path to the libraries
define('LIBS_DIR', WWW_DIR . '/libs');

// absolute filesystem path to the models
define('MODELS_DIR', APP_DIR . '/models');

// absolute filesystem path to the temp
define('TEMP_DIR', WWW_DIR . '/temp');

// absolute filesystem path to the log
define('LOGS_DIR', WWW_DIR.'/log');

$container = require __DIR__ . '/app/bootstrap.php';

if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
	$container->getByType('Nette\Application\Application')->run();
}

return $container;