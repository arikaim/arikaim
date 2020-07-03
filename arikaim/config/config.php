<?php 
/**
* Arikaim
* @link        http://www.arikaim.com
* @copyright   Copyright (c) 2017-2019 Konstantin Atanasov <info@arikaim.com>
* @license     http://www.arikaim.com/license
*/

return [
 	// application settings
	'settings' => [
		'debug'				=> true,
		'debugTrace'		=> true,
		'jwt_key'			=> "jwt_key_1",
		'defaultLanguage'	=> "en",
        'cache'				=> false,
        'CacheControl'      => 'max-age=3600,public'
	],
	// database settings
	'db' => [
		'database'		=> "arikaim",
		'username'		=> "",
		'password'		=> "",
		'driver'		=> "mysql",
		'host'			=> "localhost",
		'charset'		=> "utf8",
		'collation'		=> "utf8_bin",
        'prefix'		=> "",
        'engine'        => "InnoDB ROW_FORMAT=DYNAMIC"
	] 
];
