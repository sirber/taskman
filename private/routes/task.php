<?php
if (!isset($app)) { die(); }

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