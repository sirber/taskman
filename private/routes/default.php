<?php
if (!isset($app)) { die(); }

$app->get('/', function ($request, $response) {
	return $response->withRedirect($this->router->pathFor('task-list'), 303);
});
$app->get('/about', function ($request, $response) {
	return $this->view->render($response, 'about.html');
});

$app->get('/download/{id}', function ($request, $response, $args) {
    // Disable Output Buffering
    @ob_end_clean(); 
    // IE Required
    if(ini_get('zlib.output_compression')) {
    	ini_set('zlib.output_compression', 'Off');
    }
    
    // Fetch from DB
    $file = $this->db->select("file", "*", ["id" => $args["id"]]);
    
    // Send Headers
    header('Content-Type: ' . $file[0]['content_type']);
    header('Content-Disposition: attachment; filename="' . $file[0]['filename'] . '"');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . $file[0]['size']);    
    
    // Download    
    echo file_get_contents(dirname(__DIR__) . "/upload/" . $args["id"]);
    die();
});

function error_404($e, $response) {
    return $e->view->render($response->withStatus(404), 'layout_404.html');
}