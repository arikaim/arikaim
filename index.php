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
// load config file
$config = include_once "arikaim/config/config.php";

Arikaim\Core\Arikaim::run(0,$config);
