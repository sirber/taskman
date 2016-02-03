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
	return new medoo($container->get("database"));
};

## Middleware
# CSRF protection
$app->add(new \Slim\Csrf\Guard);
# ACL
$app->add(function($request, $response, $next) {
	# Verify login
	if (!isset($_SESSION['user_id']) && ($request->getUri()->getPath() != "user/login")) {
		// redirects to login
		return $response->withRedirect($this->router->pathFor('login'), 303);
	}
	
	# Verify ACL
	
	# Process normal route
	return $next($request, $response);	
});

## Routes
# Default
$app->get('/', function ($request, $response) {
	return $response->withRedirect($this->router->pathFor('task'), 303);
});

# User
$app->group('/user', function () {
    $this->map(['GET', 'DELETE', 'POST'], '', function ($request, $response) {
        // Find, delete, patch or replace user identified by $args['id']
    })->setName('user');
    
	$this->get('/login', function ($request, $response) {
		if (isset($_SESSION['user_id'])) { // already logged in
			return $response->withRedirect($this->router->pathFor('task'), 303);
		}
		
		// CSRF protection
		$csrf_name = $request->getAttribute('csrf_name');
		$csrf_value = $request->getAttribute('csrf_value');
		
		return $this->view->render($response, 'user_login.html', ['csrf_name' => $csrf_name, 'csrf_value' => $csrf_value]);
	})->setName('login');
	
	$this->post('/login', function ($request, $response) {
		$args = $request->getParsedBody();
		
		// Verify login, if CSRF checks out
		$datas = $this->db->select("user", "*", ["username" => $args["username"]]);
		if (isset($datas[0])) {
			$user = $datas[0];
			if (password_verify($args["password"], $user["password"])) {
				// wohoo!
				$_SESSION["user_id"] = $user["id"]; // basic
                $_SESSION["user"] = $user; // full
                
				session_regenerate_id();	
				
				return $response->withRedirect($this->router->pathFor('task', []), 303);
			}		
		}
		
		return $response->withRedirect($this->router->pathFor('login'), 303);
	});
    
    $this->get('/logout', function ($request, $response) {
        session_destroy();
        return $response->withRedirect($this->router->pathFor('login'), 303);
    });
});

# Task
$app->group('/task', function () {
    $this->get('', function ($request, $response, $args) {
        $datas = $this->db->select('task', '*', []);
        return $this->view->render($response, 'task.html', ['user' => $_SESSION['user'], 'datas' => $datas]);
    })->setName('task');        
});

# Admin
$app->group('/admin', function () {
    
    
});

## Run
$app->run();