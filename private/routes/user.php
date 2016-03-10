<?php
if (!isset($app)) { die(); }

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