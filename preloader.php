<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) 2017-2018 Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license.html
 * 
*/

include_once "vendor/autoload.php";
include_once "arikaim/core/System/Preloader.php";

(new Arikaim\Core\System\Preloader([],false))
    ->load('vendor/arikaim/container/')
    ->load('vendor/arikaim/core/')
    ->load('vendor/arikaim/core/framework/')
    ->load('vendor/arikaim/core/controllers/');
