<?php
return [
	'base' => [
		'title' => 'TaskMan',
        'logo' => 'logo.png', /* see public/img/ */
        'logo_sq' => 'logo_carre.png', /* see public/img/ */
		'background' => 'background1o', /* see public/img/ , no file extention*/
	],
    	
	'database' => [
		/* MySQL */	
		// required
		'database_type' => 'mysql',
		'database_name' => 'app_stylorouge',
		'server' => 'localhost',
		'username' => 'root',
		'password' => '',
		'charset' => 'utf8',
	 
		// [optional]
		'port' => 3306,
		
		/* SQLite */
		/*'database_type' => 'sqlite',
		'database_file' => 'my/database/path/database.db'*/
	 
		/* Other */		
		// [optional] Table prefix
		'prefix' => '',
	 
		// driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
		'option' => [
			PDO::ATTR_CASE => PDO::CASE_NATURAL
		],
		
		// [optional] Medoo will execute those commands after connected to the database for initialization
		/*'command' => [
			'SET SQL_MODE=ANSI_QUOTES'
		]*/
	],
	
	'settings' => [
        'displayErrorDetails' => true,
        'determineRouteBeforeAppMiddleware' => true,
    ],
];