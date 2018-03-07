<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) 2016-2018 Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license.html
 * 
*/
namespace Arikaim\Extensions\Dashboard\Classes;

use Arikaim\Core\Arikaim;

class DashboardItems
{
    public function __construct() 
    { 
    }

    public function getItems()
    {
        $items = Arikaim::event()->trigger('dashboard.get.items');
        if (is_array($items) == false) {
            return [];
        }       
        return $items;
    }
}
