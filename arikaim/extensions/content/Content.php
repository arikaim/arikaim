<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Content;

use Arikaim\Core\Extension\Extension;
use Arikaim\Core\Db\Model;

/**
 * Content extension
*/
class Content extends Extension
{
    /**
     * Install extension routes, events, jobs
     *
     * @return void
    */
    public function install()
    {        
        // Api
        $this->addApiRoute('POST','/api/content/add','ContentApi','add','session');        
        $this->addApiRoute('PUT','/api/content/update','ContentApi','update','session');        
        $this->addApiRoute('DELETE','/api/content/delete/{uuid}','ContentApi','delete','session');     
        $this->addApiRoute('PUT','/api/content/status','ContentApi','setStatus','session'); 
        // Register db tables
        $this->createDbTable('Content');
        $this->createDbTable('TextContent');
        $this->createDbTable('LinksContent');
        $this->createDbTable('SmsContent');
        // Register system content types
        $this->registerContentType('Classes\\TextContentType');
        $this->registerContentType('Classes\\EmailContentType');
        $this->registerContentType('Classes\\LinkContentType');
        $this->registerContentType('Classes\\SmsContentType');
        // Register content prviders
        $this->registerContentProvider(Model::TextContent('content'));
        $this->registerContentProvider(Model::LinksContent('content'));
        $this->registerContentProvider(Model::SmsContent('content'));
        // Console Commands
        $this->registerConsoleCommand('ModelExportCommand');   
        $this->registerConsoleCommand('ModelImportCommand');   
    }   
}
