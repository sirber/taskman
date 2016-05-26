<?php
if (!isset($app)) { die(); }

# Default
$app->get('/', function ($request, $response) {
	return $response->withRedirect($this->router->pathFor('task-list'), 303);
});
$app->get('/about', function ($request, $response) {
	return $this->view->render($response, 'about.html');
});

$app->group('/file', function () {
    $this->get('/download/{id}', function ($request, $response, $args) {
        // Disable Output Buffering
        @ob_end_clean(); 
        // IE Required
        if(ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }
        
        // Fetch from DB
        $file = $this->db->select("file", "*", ["id" => $args["id"]]);
        $this->db->update('file', ['nb_download' => $file[0]['nb_download']+1], ["id" => $args["id"]]);
        
        // Send Headers
        header('Content-Type: ' . $file[0]['content_type']);
        header('Content-Disposition: attachment; filename="' . $file[0]['filename'] . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $file[0]['size']);    
        
        // Download    
        echo file_get_contents(dirname(__DIR__) . "/upload/" . $args["id"]);
        die();
    });
    
    $this->get('/delete/{id}', function ($request, $response, $args) {
        $this->db->update('file', ['active' => 0], ["id" => $args["id"]]);
        
        return $response->withRedirect($_SERVER['HTTP_REFERER'], 303);
    });
});

function error_404($e, $response) {
    return $e->view->render($response->withStatus(404), 'layout_404.html');
}

# Admin
$app->group('/admin', function () {
    # todo    
});

# User / Client
$app->group('/user', function () {
	$this->get('/list', function ($request, $response, $args) {
		$datas = $this->db->select("user", "*");
		return $this->view->render($response, 'user_list.html', ['datas' => $datas]);
	})->setName("user-list");
    
    $this->get('/hash/{pass}', function ($request, $response, $args) {
        return $response->getBody()->write(password_hash($args["pass"], PASSWORD_DEFAULT));
    });
	
	$this->map(['GET', 'POST'], '/view/{id}', function ($request, $response, $args) {
		if ($request->isPost()) {
			#todo
		}
		$datas = $this->db->select("user", "*", ['id' => $args["id"]]);
		return $this->view->render($response, 'user_view.html', ['datas' => $datas]);
	})->setName("user-view");
    
    $this->map(['GET', 'POST'], '/new', function ($request, $response, $args) {
		if ($request->isPost()) {
            #user (new, always insert)
			$id = $this->db->insert("user", $_POST['fields']);
            $this->offsetSet("last_insert_id", $id);
            
            return $response->withRedirect($this->router->pathFor('user-view', ["id" => $id]), 303);
		}
		
		return $this->view->render($response, 'user_view.html', []);
	})->setName("user-new");
      
	$this->map(['GET', 'POST'], '/login', function ($request, $response) {
		if ($request->isPost()) {
			$args = $request->getParsedBody();
			$datas = $this->db->select("user", "*", ["username" => $args["username"]]);
			if (isset($datas[0])) {
				$user = $datas[0];
				if (password_verify($args["password"], $user["password"])) {
					session_regenerate_id(); // session security
					$_SESSION["user_id"] = $user["id"]; // basic
                    $_SESSION["admin"] = $user["admin"]?true:false; // admin
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
                
                # prune extra (deleted) row
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
            $_POST['fields']['user_id'] = $_SESSION['user_id'];
			$task_id = $this->db->insert("task", $_POST['fields']);
            $this->offsetSet("last_insert_id", $task_id);
            
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