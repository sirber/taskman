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
    $db->query("SET sql_mode = 'ANSI'"); # info: mariadb.com/kb/en/mariadb/sql_mode/
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
        $datas = $this->db->select('task', ["[>]user" => ["user_id" => "id"]], 
            ['task.id', 'task.name', 'task.date_start', 'task.date_end', 'task.priority', 'user.fullname']);
        return $this->view->render($response, 'task_list.html', ['datas' => $datas]);
    })->setName('task-list');        
    
    $this->map(['GET', 'POST'], '/view/{id}', function ($request, $response, $args) {
        $aSubTables = ['task_marker', 'task_work', 'task_billing'];
        
		# Save?
		if ($request->isPost()) {
            # task, always update
			$this->db->update("task", $_POST['fields'], ["id" => $args["id"]]);
            
            # sub tables, might be update, insert or delete
            foreach ($aSubTables as $sTable) {
                if (!isset($_POST[$sTable]))
                    continue;
                
                $aIds = [];
                foreach ($_POST[$sTable] as $elem) {
                    $elem['task_id'] = $args["id"];
                    if (isset($elem['id'])) {
                        # update
                        $aIds[] = $elem['id'];
                        $this->db->update($sTable, $elem, ['id' => $elem['id']]);
                    }
                    else {
                        # insert
                        $aIds[] = $this->db->insert($sTable, $elem);
                    }
                }    
                
                # prube extra (deleted) row
                $this->db->update($sTable, ['active' => 0], ['id[!]' => $aIds]);
            }            
		}
		
		# Load
        $aData = [];
		$aData["datas"] = $this->db->select('task', '*', ["id" => $args["id"]]);
		if (!count($aData["datas"])) {
			return error_404($this, $response);
		}
        
        # Sub tables
        foreach ($aSubTables as $sTable) {
            $aData[$sTable] = $this->db->select($sTable, '*', ["AND" => ["active" => 1, "task_id" => $args["id"]]]);
        }
        
        # Refs
        $aData["ref_category"] = $this->db->select('ref_task_category', '*', ['active'=>1]);
        $aData['ref_user'] = $this->db->select('user', '*', ['admin'=>0]);
        
        return $this->view->render($response, 'task_view.html', $aData);
    })->setName('task-view');
    
	$this->map(['GET', 'POST'], '/new', function ($request, $response, $args) {
        $aSubTables = ['task_marker', 'task_work', 'task_billing'];
		if ($request->isPost()) { #save!
			#task (new, always insert)
			$task_id = $this->db->insert("task", $_POST['fields']);
            
            #sub tables (new, always insert)
            foreach ($aSubTables as $sTable) {
                if (!isset($_POST[$sTable]))
                    continue;
                
                foreach ($_POST[$sTable] as $elem) {
                    $elem['task_id'] = $task_id;
                    $this->db->insert("task_marker", $elem);
                }    
            }
              
			return $response->withRedirect($this->router->pathFor('task-view', ["id" => $task_id]), 303);
		}
		
		$datas = [];
		
		# Refs
        $ref_category = $this->db->select('ref_task_category', '*', ['active'=>1]);
        $ref_user = $this->db->select('user', '*', ['admin'=>0]);
        
        return $this->view->render($response, 'task_view.html', 
            ['datas' => $datas, 'ref_category' => $ref_category,
            'ref_user' => $ref_user]);
	})->setName('task-new');
});

# Admin
$app->group('/admin', function () {
    # todo    
});

# Error 
function error_404($e, $response) {
    return $e->view->render($response->withStatus(404), 'layout_404.html');
}

## Run
$app->run();