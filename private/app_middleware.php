<?php
if (!isset($app)) { die(); }
use Ikimea\Browser\Browser;

# Files
$app->add(function($request, $response, $next) {
    # Route check
    $route = trim($request->getUri()->getPath(), "/");
    $id = $request->getAttribute('route')->getArgument('id');

    # File upload
    if (count($_FILES) && $_FILES["file_upload"]["tmp_name"] && $id) {				
        # Save upload
        foreach ($_FILES as $index => $file) {
            ## todo: get description from form/post
            
            # Save to the database
            $data = ['route' => $route, 
                'filename' => $file['name'],
                'size' => $file['size'], 
                'content_type' => $file['type'],
                'description' => $_POST['file_description'],
            ];
            $file_id = $this->db->insert("file", $data);
            
            # Keep the file
            move_uploaded_file($file['tmp_name'], __DIR__ . "/upload/" . $file_id);
        }
	}
    
    # Load uploaded files for current route and add it to the template
    $files = $this->db->select('file', "*", ['AND' => ['route' => $route, 'active' => 1]]);
    $this->view->offsetSet('files', $files);    
    $this->view->offsetSet('files_show', $id?true:false);
    
	# Process normal route
	return $next($request, $response);		
});

# Init
$app->add(function($request, $response, $next) {
    # Detect browser    
    $browser = new Browser();
    switch ($browser->getBrowser()) {
        case Browser::BROWSER_CHROME:
        case Browser::BROWSER_OPERA:
            $ext = '.webp'; # save bandwith
            break; 

        default:
            $ext = '.jpg';
            break;
    }
    
    # Template init
    $route = trim($request->getUri()->getPath(), "/");
	$this->view->offsetSet('title', $this->get('base')['title']);
    $this->view->offsetSet('background', $this->get('base')['background'] . $ext);
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

# CSRF protection
$app->add(new \Slim\Csrf\Guard);

# Logging
$app->add(function($request, $response, $next) {
	# Log
    $route = trim($request->getUri()->getPath(), "/");
	$this->log->debug($_SERVER['REMOTE_ADDR'] . ": " . $route);
    
    # Process normal route
	return $next($request, $response);		
});