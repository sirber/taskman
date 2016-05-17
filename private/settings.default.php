<?php
return [
	'base' => [
		'title' => 'TaskMan',
        'logo' => 'logo.png', /* see public/img/ */
        'logo_sq' => 'logo_carre.png', /* see public/img/ */
		'background' => 'background1o-compressor.jpg', /* see public/img/ */
	],
    	
	'database' => [
		// required
		'database_type' => 'mysql',
		'database_name' => 'app_stylorouge',
		'server' => 'localhost',
		'username' => 'root',
		'password' => '',
		'charset' => 'utf8',
	 
		// [optional]
		'port' => 3306,
	 
		// [optional] Table prefix
		'prefix' => '',
	 
		// driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
		'option' => [
			PDO::ATTR_CASE => PDO::CASE_NATURAL
		]
	],
	
	'settings' => [
        'displayErrorDetails' => true,
        'determineRouteBeforeAppMiddleware' => true,
    ],
];