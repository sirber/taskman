<?php
if (!isset($app)) { die(); }

$app->get('/', function ($request, $response) {
	return $response->withRedirect($this->router->pathFor('task-list'), 303);
});
$app->get('/about', function ($request, $response) {
	return $this->view->render($response, 'about.html');
});
$app->get('/default_css', function ($request, $response) {
    return $this->view->render($response->withHeader('Content-type', 'text/css'), 'default.css');
});

function error_404($e, $response) {
    return $e->view->render($response->withStatus(404), 'layout_404.html');
}