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
		'debug'				=> false,
		'debugTrace'		=> true,
		'jwtKey'			=> '49ec7df6-ea4f-4364-bd59-1c6609b1840a',
		'defaultLanguage'	=> 'en',
        'cache'				=> false,     
        'primaryTemplate'	=> 'system',
        'cacheDriver'       => 'void',
        'logEvents'			=> false,
        'console'           => [
            'log'		=> false,
            'logErrors'	=> false
        ],
        'timeZone'			=> 'UTC',
        'dateFormat'		=> 'F j, Y',
		'timeFormat'		=> 'g:i:s A',
		'numberFormat'		=> 'accounting',
        'logger'			=> true,
        'sessionInterval'	=> '2',
        'loggerHandler'		=> 'file'             
	],
	// database settings
	'db' => [
		'database'		=> 'arikaim',
		'username'		=> '',
		'password'		=> '',
		'driver'		=> 'mysql',
		'host'			=> 'localhost',
		'charset'		=> 'utf8',
		'collation'		=> 'utf8_bin',
        'prefix'		=> '',
        'engine'        => 'InnoDB ROW_FORMAT=DYNAMIC'
    ],
    // middlewares
    'middleware' => [     
    ],
    'headers' => [       
    ],
    'environment' => [ 
    ]	   
];
