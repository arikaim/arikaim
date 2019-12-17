<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Dashboard;

use Arikaim\Core\Extension\Extension;

/**
 * Dashboard class
 */
class Dashboard extends Extension
{
    /**
     * Install extension
     *
     * @return boolean
     */
    public function install()
    {
        // Events
        $this->registerEvent('dashboard.get.items','Trigger on show dashboard page');
     
        return true;
    }
    
    /**
     * UnInstall extension
     *
     * @return boolean
     */
    public function unInstall()
    {
        return true;
    }
}
