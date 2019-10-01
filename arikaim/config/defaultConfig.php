<?php 
/**
* Arikaim
* @link        http://www.arikaim.com
* @copyright   Copyright (c) 2016-2018 Konstantin Atanasov <info@arikaim.com>
* @license     http://www.arikaim.com/license.html
*/

return [
    // application settings
	'settings' => [
		'displayErrorDetails'					=> true,
		'determineRouteBeforeAppMiddleware'		=> true,
		'debug'									=> false,
		'debugTrace'							=> true,
		'httpVersion'							=> "1.1",
		'responseChunkSize'						=> "165096",
		'outputBuffering'						=> "append",
		'addContentLengthHeader'				=> true,
		'jwt_key'								=> "jwt_key_1",
		'defaultLanguage'						=> "en",
		'cache'									=> true
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
		'prefix'		=> ""
	] 
];
