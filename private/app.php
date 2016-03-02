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

# User / Client
$app->group('/user', function () {
	$this->get('/list', function ($request, $response, $args) {
		$datas = $this->db->select("user", "*");
		return $this->view->render($response, 'user_list.html', ['datas' => $datas]);
	})->setName("user-list");
	
	$this->map(['GET', 'POST'], '/view/{id}', function ($request, $response, $args) {
		if ($request->isPost()) {
			#todo
		}
		$datas = $this->db->select("user", "*", ['id' => $args["id"]]);
		return $this->view->render($response, 'user_view.html', []);
	})->setName("user-view");
      
	$this->map(['GET', 'POST'], '/login', function ($request, $response) {
		if ($request->isPost()) {
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
			$this->view->offsetSet('error', 'error: user not found');
		}		
		
		if (isset($_SESSION['user_id'])) { // already logged in
			return $response->withRedirect($this->router->pathFor('task-list'), 303);
		}
		return $this->view->render($response, 'user_login.html');
	})->setName('user-login');
	
    $this->get('/logout', function ($request, $response) {
        session_destroy();
        return $response->withRedirect($this->router->pathFor('user-login'), 303);
    });
});

# Task
$app->group('/task', function () {
    $this->get('/list', function ($request, $response, $args) {
        $datas = $this->db->select('task', '*');
        return $this->view->render($response, 'task_list.html', ['datas' => $datas]);
    })->setName('task-list');        
    
    $this->map(['GET', 'POST'], '/view/{id}', function ($request, $response, $args) {
		if ($request->isPost()) {
			#save
			if (isset($args["id"])) {
				$this->db->update("task", $_POST['fields'], ["id" => $args["id"]]);
			}
			else {
				$this->db->insert("task", $_POST['fields']);
			}
		}
        $datas = array();
        if (isset($args["id"])) {
            $datas = $this->db->select('task', '*', ["id" => $args["id"]]);
            if (!count($datas)) {
                return $response->withRedirect($this->router->pathFor('404'), 404);
            }
        }
        $datas['csrf_name'] = $request->getAttribute('csrf_name');
        $datas['csrf_value'] = $request->getAttribute('csrf_value');

        # Refs
        $ref_category = $this->db->select('ref_task_category', '*', ['active'=>1]);
        $ref_user = $this->db->select('user', '*', ['admin'=>0]);
        
        return $this->view->render($response, 'task_view.html', 
            ['datas' => $datas, 'ref_category' => $ref_category,
            'ref_user' => $ref_user]);
    })->setName('task-view');        
    
});

# Admin
$app->get('/404', function ($request, $response, $args) {
    return $this->view->render($response, 'layout_404.html');
})->setName('404'); ;

$app->group('/admin', function () {
    # todo    
});

## Run
$app->run();