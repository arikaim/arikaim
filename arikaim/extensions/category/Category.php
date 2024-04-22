<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Category;

use Arikaim\Core\Extension\Extension;

/**
 * Category extension
*/
class Category extends Extension
{
    /**
     * Install extension routes, events, jobs ..
     *
     * @return void
    */
    public function install()
    {        
        // Control Panel
        $this->addApiRoute('POST','/api/admin/category/add','CategoryControlPanel','add','session');   
        $this->addApiRoute('PUT','/api/admin/category/update','CategoryControlPanel','update','session');        
        $this->addApiRoute('PUT','/api/admin/category/update/meta','CategoryControlPanel','updateMetaTags','session');       
        $this->addApiRoute('PUT','/api/admin/category/update/description','CategoryControlPanel','updateDescription','session');       
        $this->addApiRoute('DELETE','/api/admin/category/delete/{uuid}','CategoryControlPanel','delete','session');     
        $this->addApiRoute('PUT','/api/admin/category/status','CategoryControlPanel','setStatus','session'); 
        // Api Routes
        $this->addApiRoute('GET','/api/category/{id}[/]','CategoryApi','read');        
        // Register events
        $this->registerEvent('category.create','Trigger after new category created');
        $this->registerEvent('category.update','Trigger after category is edited');
        $this->registerEvent('category.delete','Trigger after category is deleted');
        $this->registerEvent('category.status','Trigger after category status changed');
        // Create db tables
        $this->createDbTable('Category');
        $this->createDbTable('CategoryTranslations');
        $this->createDbTable('CategoryRelations');
        // console
        $this->registerConsoleCommand('CategoryDelete');
        // Relation map 
        $this->addRelationMap('category','Category');
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
