<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/

include_once "vendor/autoload.php";

// set start time
define('APP_START_TIME',microtime(true));

Arikaim\Core\Arikaim::run();
