<?php
if (!isset($app)) { die(); }

# Files
$app->add(function($request, $response, $next) {
    # Route check
    $route = trim($request->getUri()->getPath(), "/");
    $id = $request->getAttribute('route')->getArgument('id');
        
    # File upload
    if (count($_FILES)) {				
		if (!$id) {
            # Save current objects
			$response = $next($request, $response);
            # Fetch new ID
		}
		# save upload
		## todo
	}
    
    # Load uploaded files for current route
    
    
    
	# Process normal route
	return $next($request, $response);		
});

# Init
$app->add(function($request, $response, $next) {
    # Template init
    $route = trim($request->getUri()->getPath(), "/");
	$this->view->offsetSet('title', $this->get('base')['title']);
    $this->view->offsetSet('background', $this->get('base')['background']);
    $this->view->offsetSet('logo', $this->get('base')['logo']);
    $this->view->offsetSet('logo_sq', $this->get('base')['logo_sq']);
	$this->view->offsetSet('route', $route);
	$this->view->offsetSet('csrf_name', $request->getAttribute('csrf_name'));
	$this->view->offsetSet('csrf_value', $request->getAttribute('csrf_value'));
    
    # Process normal route
	return $next($request, $response);		
});

# ACL
$app->add(function($request, $response, $next) {
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

	# Verify ACL
	## todo
	
	# Process normal route
	return $next($request, $response);		
});

# Logging
$app->add(function($request, $response, $next) {
	# Log
    $route = trim($request->getUri()->getPath(), "/");
	$this->log->debug($_SERVER['REMOTE_ADDR'] . ": " . $route);
    
    # Process normal route
	return $next($request, $response);		
});

# CSRF protection
$app->add(new \Slim\Csrf\Guard);