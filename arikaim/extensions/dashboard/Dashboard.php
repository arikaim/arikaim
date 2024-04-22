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
     * @return void
     */
    public function install()
    {
        // Control Panel
        $this->addApiRoute('PUT','/api/admin/dashboard/hide','DashboardControlPanel','hidePanel','session');      
        $this->addApiRoute('PUT','/api/admin/dashboard/show','DashboardControlPanel','showPanel','session');  
        // Events
        $this->registerEvent('dashboard.get.items','Trigger on show dashboard page');
        // Db Models
        $this->createDbTable('DashboardSchema');    
    }
    
    /**
     * UnInstall extension
     *
     * @return void
     */
    public function unInstall()
    {  
    }
}
