<?php
if (!defined("FROM_PUBLIC"))
	die("fatal error: request not from public/index.php");

## Vendor
require '../vendor/autoload.php';

## App
session_start();
$settings = require "settings.php";
$app = new \Slim\App($settings);

## Dependencies
$container = $app->getContainer();
# view renderer (template)
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
# database
$container['db'] = function ($container) {
    $db = new medoo($container->get("database"));
	#seems to work ok now. medoo is supposed to do it
    #$db->query("SET sql_mode = 'ANSI'"); # info: mariadb.com/kb/en/mariadb/sql_mode/
	return $db;
};

## Middleware
# ACL
$app->add(function($request, $response, $next) {
	# Verify login
	$route = trim($request->getUri()->getPath(), "/");
	if ($route != 'user/login') {
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
	
	# Process normal route
	return $next($request, $response);	
});
# CSRF protection
$app->add(new \Slim\Csrf\Guard);

## Routes
# Default
$app->get('/', function ($request, $response) {
	return $response->withRedirect($this->router->pathFor('task-list'), 303);
});
$app->get('/about', function ($request, $response) {
	return $this->view->render($response, 'about.html', []);
});
function error_404($e, $response) {
    return $e->view->render($response->withStatus(404), 'layout_404.html');
}

# Custom
foreach ($settings['routes'] as $route) {
	require_once ("routes/" . $route . ".php");
}

## Run
$app->run();