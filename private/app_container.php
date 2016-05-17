<?php
if (!isset($app)) { die(); }
use Psr\Log\LogLevel;

$container = $app->getContainer();

# template: twig
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/templates', [
        'cache' => false
    ]);
    $view->addExtension(new \Slim\Views\TwigExtension(
        $container['router'],
        $container['request']->getUri()
    ));
    return $view;
};

# database: medoo
$container['db'] = function ($container) {
	try {
		$db = new medoo($container->get("database"));
	}
	catch (Exception $e) {
		die("fatal error: cannot connect to database.");
	}
	return $db;
};

# logging: klogger
$container['log'] = function ($container) {
	/* These are in order of highest priority to lowest.
	LogLevel::EMERGENCY; LogLevel::ALERT; LogLevel::CRITICAL; LogLevel::ERROR;
	LogLevel::WARNING; LogLevel::NOTICE; LogLevel::INFO; LogLevel::DEBUG;
	*/	
	$log = new Katzgrau\KLogger\Logger(__DIR__.'/logs', LogLevel::NOTICE);
	return $log;
};
