<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) 2017-2018 Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license.html
 * 
*/

include_once "core/Arikaim.php";
use Arikaim\Core\Arikaim;

$path = Arikaim::getConsoleRootPath() . Arikaim::getConsoleBasePath();

include_once  $path . "/vendor/autoload.php";
include_once "core/System/ClassLoader.php";

Arikaim::create();
Arikaim::jobs()->run();
