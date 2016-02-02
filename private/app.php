<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

## Vendor
require '../vendor/autoload.php';

## App
$settings = require "settings.php";
$app = new \Slim\App($settings);
session_start();

## Database
$db = new medoo($settings['database']);

## Dependencies
$container = $app->getContainer();
# view renderer
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

## Middleware
# CSRF protection
$app->add(new \Slim\Csrf\Guard);
# ACL
$app->add(function($request, $response, $next) {
		
	$response = $next($request, $response);
	return $response;
});


## Routes
$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");
	
    return $response;
});
$app->run();