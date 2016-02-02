<?php
if (!FROM_PUBLIC)
	die("fatal error: not from public/index.php");

## Vendor
require '../vendor/autoload.php';

## App
session_start();
$settings = require "settings.php";
$app = new \Slim\App($settings);

# Database
$GLOBALS["db"] = new medoo($settings['database']);

## Dependencies
$container = $app->getContainer();
<<<<<<< HEAD
=======
# database 
$container['database'] = function ($container) {
	var_dump($container->get("settings")['database']);
	return new medoo($container->get("settings")['database']);
};
>>>>>>> added: working login
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


## Middleware
# CSRF protection
$app->add(new \Slim\Csrf\Guard);
# ACL
$app->add(function($request, $response, $next) {
<<<<<<< HEAD
	# Verify login
=======
>>>>>>> added: working login
	if (!isset($_SESSION['user_id']) && ($request->getUri()->getPath() != "user/login")) {
		// redirects to login
		return $response->withRedirect($this->router->pathFor('login'), 303);
	}
<<<<<<< HEAD
	
	# Verify ACL
	
	# Process normal route
	return $next($request, $response);	
=======
	else {
		// process normal route
		return $next($request, $response);	
	}	
>>>>>>> added: working login
});

## Routes
$app->get('/', function ($request, $response) {
	// Tasks
	$response->getBody()->write("logged in");	
	return $response;
})->setName('root');

# User
$app->group('/user', function () {
	$this->get('/login', function ($request, $response) {
		if (isset($_SESSION['user_id'])) { // already logged in
			return $response->withRedirect($this->router->pathFor('root'), 303);
		}
		
		// CSRF
		$csrf_name = $request->getAttribute('csrf_name');
		$csrf_value = $request->getAttribute('csrf_value');
		
		return $this->view->render($response, 'user_login.html', ['csrf_name' => $csrf_name, 'csrf_value' => $csrf_value]);
	})->setName('login');	
	
	$this->post('/login', function ($request, $response) {
		$args = $request->getParsedBody();
		
		// Verify login, if CSRF checks out
		$datas = $GLOBALS["db"]->select("user", "*", ["username" => $args["username"]]);
		if (isset($datas[0])) {
			$user = $datas[0];
			if (password_verify($args["password"], $user["password"])) {
				// wohoo!
				$_SESSION["user_id"] = $user["id"]; // basic user info
				session_regenerate_id();	
				
				return $response->withRedirect($this->router->pathFor('root'), 303);
			}		
		}
		
		return $response->withRedirect($this->router->pathFor('login'), 303);
	});
});

## Run
$app->run();