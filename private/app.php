<?php
if (!defined("FROM_PUBLIC")) {
	die("fatal error: request not from public/index.php");
}
if (!is_writable(__DIR__ . "/logs/")) {
	die("fatal error: folder private/logs is not writable");
}
if (!is_file(__DIR__ . "/settings.php")) {
	die("fatal error: settings.php not found");
}

## Vendor
use Psr\Log\LogLevel;
require '../vendor/autoload.php';

## App
session_start();
$settings = require "settings.php";
$app = new \Slim\App($settings);
$routes = ['admin', 'user', 'task']; 

## Dependencies
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

## Middleware
# ACL
$app->add(function($request, $response, $next) {
	# Config
	$this->view->offsetSet('title', $this->get('base')['title']);
    $this->view->offsetSet('background', $this->get('base')['background']);
    $this->view->offsetSet('logo', $this->get('base')['logo']);
    $this->view->offsetSet('logo_sq', $this->get('base')['logo_sq']);
	
	# Verify login
	$route = trim($request->getUri()->getPath(), "/");
    $route_ok = ['user/login', 'default_css'];
    if (!in_array($route, $route_ok)) {
		if (!isset($_SESSION['user_id'])) {
			// redirects to login
			return $response->withRedirect($this->router->pathFor('user-login'), 303);
		}
		else {
			// refresh user info
			$datas = $this->db->select("user", "*", ["id" => $_SESSION["user_id"]]);
			# session
			$_SESSION["user"] = $datas[0];
			# Add user info to templates
			$this->view->offsetSet('user', $datas[0]);
		}
	}
	
	# CSRF -> view
	$this->view->offsetSet('csrf_name', $request->getAttribute('csrf_name'));
	$this->view->offsetSet('csrf_value', $request->getAttribute('csrf_value'));
	
	# Controller/Function -> view
	$this->view->offsetSet('route', $route);

	# Verify ACL
	## todo
	
	# Log
	$this->log->debug($route);
	
	# Process normal route
	return $next($request, $response);	
});

# CSRF protection
$app->add(new \Slim\Csrf\Guard);

## Routes
$app->get('/', function ($request, $response) {
	return $response->withRedirect($this->router->pathFor('task-list'), 303);
});
$app->get('/about', function ($request, $response) {
	return $this->view->render($response, 'about.html');
});
$app->get('/default_css', function ($request, $response) {
    return $this->view->render($response->withHeader('Content-type', 'text/css'), 'default.css');
});

function error_404($e, $response) {
    return $e->view->render($response->withStatus(404), 'layout_404.html');
}
foreach ($routes as $route) {
	require_once ("routes/" . $route . ".php");
}

## Run
$app->run();