<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) 2016-2018 Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license.html
 * 
*/
namespace Arikaim\Extensions\Dashboard;

use Arikaim\Core\Extension\Extension;

class Dashboard extends Extension
{
    public function __construct() 
    { 
    }

    public function install()
    {
        // Events
        $this->registerEvent('dashboard.get.items','Trigger on show dashboard page');
        return true;
    }   
}
