<?php
/***
 * TaskMan Web Application
 * (c) Stéphane Bérubé <sirber@hotmail.com>
 * Licence: private
 ***/

# Check
require 'app_check.php';
 
# Vendor
require_once '../vendor/autoload.php';

# Framework
session_start();
$settings = require_once "settings.php";
$app = new \Slim\App($settings);

# Dependencies
require_once "app_container.php";

# Middleware
require_once "app_middleware.php";

# Routes
require_once "app_route.php";

# Run!
$app->run();