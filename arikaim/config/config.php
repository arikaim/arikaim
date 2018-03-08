<?php 
/**
* Arikaim
* @link        http://www.arikaim.com
* @copyright   Copyright (c) 2017-2018 Konstantin Atanasov <info@arikaim.com>
* @license     http://www.arikaim.com/license.html
*/

// database settings
$db['database'] = "arikaim";
$db['username'] = "";
$db['password'] = "";
$db['driver'] = "mysql";
$db['host'] = "localhost";
$db['charset'] = "utf8";
$db['collation'] = "utf8_unicode_ci";
$db['prefix'] = "";

// application settings
$settings['displayErrorDetails'] = true;
$settings['determineRouteBeforeAppMiddleware'] = true;
$settings['debug'] = true;
$settings['debugTrace'] = false;
$settings['httpVersion'] = "1.1";
$settings['responseChunkSize'] = "165096";
$settings['outputBuffering'] = false;
$settings['addContentLengthHeader'] = true;
$settings['routerCacheFile'] = false;
$settings['jwt_key'] = "jwt_enc_key_1";
$settings['defaultLanguage'] = "en";

$config['settings'] = $settings;
$config['db'] = $db;
return $config;
