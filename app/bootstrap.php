<?php
/**
 * My Application bootstrap file.
 */
use Nette\Application\Routers\Route;

// Load Nette Framework
require LIBS_DIR . '/Nette/nette.min.php';

// Configure application
$configurator = new Nette\Config\Configurator;

// Enable Nette Debugger for error visualisation & logging
//$configurator->setDebugMode($configurator::AUTO);
$configurator->enableDebugger(__DIR__ . '/../log');

// Enable RobotLoader - this will load all classes automatically
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
	->addDirectory(APP_DIR)
	->addDirectory(LIBS_DIR)
	->register();

// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__ . '/config/config.neon');
$container = $configurator->createContainer();

if (PHP_SAPI != 'cli') {
	// Setup router
	$container->router[] = new Route('index.php', 'Homepage:default', Route::ONE_WAY);
	$container->router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
} else {
	$container->application->allowedMethods = FALSE;
	$configurator->setProductionMode(FALSE);
	$container->router[] = new Route('index.php', 'Homepage:default', Route::ONE_WAY);
	$container->router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
}

// Configure and run the application!
if (PHP_SAPI == 'cli' && !isset($phpUnitTest)) {
	exit();
} else if (!isset($phpUnitTest)) {
	$container->application->run();
}
