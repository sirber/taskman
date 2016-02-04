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
	
	# Verify ACL
	
	# Process normal route
	return $next($request, $response);	
});

## Routes
# Default
$app->get('/', function ($request, $response) {
	return $response->withRedirect($this->router->pathFor('task-list'), 303);
});

# User
$app->group('/user', function () {
	$this->get('/list', function ($request, $response, $args) {
		$datas = $this->db->select("user", "*", []);
		return $this->view->render($response, 'user_list.html', $datas);
	})->setName("user-list");
	
	$this->get('/view/{id}', function ($request, $response, $args) {
		$datas = $this->db->select("user", "*", ['id' => $args["id"]]);
		return $this->view->render($response, 'user.html', []);
	})->setName("user-view");
    
	$this->get('/login', function ($request, $response) {
		if (isset($_SESSION['user_id'])) { // already logged in
			return $response->withRedirect($this->router->pathFor('task-list'), 303);
		}
		$data = ['csrf_name' => $request->getAttribute('csrf_name'), 'csrf_value' => $request->getAttribute('csrf_value')];
		return $this->view->render($response, 'user_login.html', $data);
	})->setName('user-login');
	
	$this->post('/login', function ($request, $response) {
		$args = $request->getParsedBody();
		$datas = $this->db->select("user", "*", ["username" => $args["username"]]);
		if (isset($datas[0])) {
			$user = $datas[0];
			if (password_verify($args["password"], $user["password"])) {
				session_regenerate_id(); // session security
				$_SESSION["user_id"] = $user["id"]; // basic
				return $response->withRedirect($this->router->pathFor('task-list', []), 303);
			}		
		}		
		return $response->withRedirect($this->router->pathFor('user-login'), 303);
	});
    
    $this->get('/logout', function ($request, $response) {
        session_destroy();
        return $response->withRedirect($this->router->pathFor('user-login'), 303);
    });
});

# Task
$app->group('/task', function () {
    $this->get('/list', function ($request, $response, $args) {
        $datas = $this->db->select('task', '*', []);
        return $this->view->render($response, 'task_list.html', ['datas' => $datas]);
    })->setName('task-list');        
});

# Admin
$app->group('/admin', function () {
    
    
});

## Run
$app->run();