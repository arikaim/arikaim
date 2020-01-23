<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\App;

use Arikaim\Core\Arikaim;
use Arikaim\Core\Utils\Factory;

/**
 * Routes
 */
class SystemRoutes 
{
    /**
     * Map core routes
     *   
     * @return boolean
     */
    public static function mapSystemRoutes()
    {
        $apiNamespace = Factory::API_CONTROLLERS_NAMESPACE;
        $sessionAuth = Arikaim::access()->middleware('session');

        // Control Panel
        Arikaim::$app->get('/admin[/{language:[a-z]{2}}/]',"Arikaim\Core\App\ControlPanel:loadControlPanel");    
        // Api Access
        Arikaim::$app->post('/core/api/create/token/',"$apiNamespace\Client:createToken");
        Arikaim::$app->post('/core/api/verify/request/',"$apiNamespace\Client:verifyRequest");      
        // UI Component
        Arikaim::$app->get('/core/api/ui/component/properties/{name}[/{params:.*}]',"$apiNamespace\Ui\Component:componentProperties");

        Arikaim::$app->get('/core/api/ui/component/details/{name}[/{params:.*}]',"$apiNamespace\Ui\Component:componentDetails")->add($sessionAuth);
        Arikaim::$app->get('/core/api/ui/component/{name}[/{params:.*}]',"$apiNamespace\Ui\Component:loadComponent");      
        Arikaim::$app->post('/core/api/ui/library/upload',"$apiNamespace\Ui\Library:upload")->add($sessionAuth);

        // UI Page  
        Arikaim::$app->get('/core/api/ui/page/{name}',"$apiNamespace\Ui\Page:loadPageHtml");
        Arikaim::$app->get('/core/api/ui/page/properties/',"$apiNamespace\Ui\Page:loadPageProperties");  
        // Paginator 
        Arikaim::$app->group('/core/api/ui/paginator',function($group) use($apiNamespace) {  
            $group->put('/page-size',"$apiNamespace\Ui\Paginator:setPageSize");
            $group->put('/page',"$apiNamespace\Ui\Paginator:setPage");
            $group->get('/[{namespace}]',"$apiNamespace\Ui\Paginator:getPage");
            $group->put('/view/type',"$apiNamespace\Ui\Paginator:setViewType");
            $group->get('/view/type/[{namespace}]',"$apiNamespace\Ui\Paginator:getViewType");
            $group->delete('/{namespace}',"$apiNamespace\Ui\Paginator:remove");
        });     
        // Search 
        Arikaim::$app->group('/core/api/ui/search',function($group) use($apiNamespace) { 
            $group->put('/',"$apiNamespace\Ui\Search:setSearch"); 
            $group->put('/condition/[{namespace}]',"$apiNamespace\Ui\Search:setSearchCondition");      
            $group->delete('/condition/{field}/[{namespace}]',"$apiNamespace\Ui\Search:deleteSearchCondition");
            $group->delete('/[{namespace}]',"$apiNamespace\Ui\Search:clearSearch");
        });
        // Order by column
        Arikaim::$app->group('/core/api/ui/order',function($group) use($apiNamespace) { 
            $group->put('/[{namespace}]',"$apiNamespace\Ui\OrderBy:setOrderBy"); 
            $group->get('/[{namespace}]',"$apiNamespace\Ui\OrderBy:getOrderBy");      
            $group->delete('/[{namespace}]',"$apiNamespace\Ui\OrderBy:deleteOrderBy");
        })->add($sessionAuth);        
        // Position
        Arikaim::$app->group('/core/api/ui/position',function($group) use($apiNamespace) { 
            $group->put('/shift',"$apiNamespace\Ui\Position:shift");
            $group->put('/swap',"$apiNamespace\Ui\Position:swap");
        })->add($sessionAuth);              
        // Control Panel user
        Arikaim::$app->group('/core/api/user',function($group) use($apiNamespace) {  
            $group->post('/login',"$apiNamespace\Users:adminLogin");
            $group->post('/update',"$apiNamespace\Users:changeDetails");           
            $group->get('/logout',"$apiNamespace\Users:logout");
        });      
        // Languages  
        Arikaim::$app->group('/core/api/language',function($group) use($apiNamespace) {      
            $group->post('/add',"$apiNamespace\Language:add");
            $group->put('/update',"$apiNamespace\Language:update");
            $group->delete('/{uuid}',"$apiNamespace\Language:remove");
            $group->put('/change/{language_code}',"$apiNamespace\Language:changeLanguage"); 
            $group->put('/status',"$apiNamespace\Language:setStatus");
            $group->put('/default',"$apiNamespace\Language:setDefault");
        })->add($sessionAuth);        
        // Options
        Arikaim::$app->group('/core/api/options',function($group) use($apiNamespace) {
            $group->get('/{key}',"$apiNamespace\Options:get");
            $group->put('/',"$apiNamespace\Options:save");
            $group->post('/',"$apiNamespace\Options:saveOptions");
        })->add($sessionAuth);
        // Queue
        Arikaim::$app->group('/core/api/queue',function($group) use($apiNamespace) {
            $group->put('/cron/install',"$apiNamespace\Queue:installCron");
            $group->delete('/cron/uninstall',"$apiNamespace\Queue:unInstallCron");
            $group->delete('/jobs',"$apiNamespace\Queue:deleteJobs");
            $group->put('/worker/start',"$apiNamespace\Queue:startWorker");
            $group->delete('/worker/stop',"$apiNamespace\Queue:stopWorker");
        })->add($sessionAuth);
        // Jobs
        Arikaim::$app->group('/core/api/jobs',function($group) use($apiNamespace) {
            $group->delete('/delete/{uuid}',"$apiNamespace\Jobs:deleteJob");
            $group->put('/status',"$apiNamespace\Jobs:setStatus");          
        })->add($sessionAuth);        
        // Drivers
        Arikaim::$app->group('/core/api/driver',function($group) use($apiNamespace) { 
            $group->put('/status',"$apiNamespace\Drivers:setStatus");          
            $group->get('/config/{name}',"$apiNamespace\Drivers:readConfig");
            $group->put('/config',"$apiNamespace\Drivers:saveConfig");
        })->add($sessionAuth);
        // Update
        Arikaim::$app->group('/core/api/update',function($group) use($apiNamespace) {
            $group->put('/',"$apiNamespace\Update:update");
            $group->get('/check/version',"$apiNamespace\Update:checkVersion"); 
            $group->get('/last/version/[{package}]',"$apiNamespace\Update:getLastVersion");           
        })->add($sessionAuth);
        // Session
        Arikaim::$app->group('/core/api/session',function($group) use($apiNamespace) {        
            $group->put('/recreate',"$apiNamespace\SessionApi:recreate");
            $group->put('/restart',"$apiNamespace\SessionApi:restart");
        })->add($sessionAuth);
        // Access tokens
        Arikaim::$app->group('/core/api/tokens',function($group) use($apiNamespace) {
            $group->delete('/delete/{token}',"$apiNamespace\AccessTokens:delete");
            $group->delete('/delete/expired/{uuid}',"$apiNamespace\AccessTokens:deleteExpired");
        })->add($sessionAuth);
        // Settings
        Arikaim::$app->group('/core/api/settings',function($group) use($apiNamespace) {
            $group->put('/debug',"$apiNamespace\Settings:setDebug");
            $group->put('/install-page',"$apiNamespace\Settings:disableInstallPage");
        })->add($sessionAuth);
        // Mailer
        Arikaim::$app->group('/core/api/mailer',function($group) use($apiNamespace) {
            $group->get('/test/email',"$apiNamespace\Mailer:sendTestEmail");
        })->add($sessionAuth);
        // Cache
        Arikaim::$app->group('/core/api/cache',function($group) use($apiNamespace) {
            $group->delete('/clear',"$apiNamespace\Cache:clear");
            $group->put('/enable',"$apiNamespace\Cache:enable");
            $group->put('/disable',"$apiNamespace\Cache:disable");
        })->add($sessionAuth);
        // Logs
        Arikaim::$app->group('/core/api/logs',function($group) use($apiNamespace) {
            $group->delete('/clear',"$apiNamespace\Logger:clear");
        })->add($sessionAuth);
        // options and relations used for all extensions
        Arikaim::$app->group('/core/api/orm',function($group) use($apiNamespace) {
            $group->put('/relation/delete',"$apiNamespace\Orm\Relations:deleteRelation");
            $group->post('/relation',"$apiNamespace\Orm\Relations:addRelation");
            $group->put('/options',"$apiNamespace\Orm\Options:saveOptions");
            $group->post('/options/type/add',"$apiNamespace\Orm\Options:addOptionType");
            $group->put('/options/type/update',"$apiNamespace\Orm\Options:updateOptionType");
            $group->put('/options/type/delete',"$apiNamespace\Orm\Options:deleteOptionType");
            $group->post('/options/list/add',"$apiNamespace\Orm\Options:addOptionList");    
            $group->put('/options/list/update',"$apiNamespace\Orm\Options:updateOptionList");       
            $group->put('/options/list/delete',"$apiNamespace\Orm\Options:deleteOptionList");              
        })->add($sessionAuth);
        // Packages
        Arikaim::$app->group('/core/api/packages',function($group) use($apiNamespace) {
            $group->put('/install',"$apiNamespace\Packages:install");    
            $group->put('/repository/update',"$apiNamespace\Packages:repositoryUpdate");      
            $group->put('/repository/install',"$apiNamespace\Packages:repositoryInstall");          
            $group->put('/status',"$apiNamespace\Packages:setStatus");
            $group->put('/uninstall',"$apiNamespace\Packages:unInstall");
            $group->put('/update',"$apiNamespace\Packages:update");
            $group->post('/config',"$apiNamespace\Packages:saveConfig");
            $group->put('/primary',"$apiNamespace\Packages:setPrimary");
            $group->put('/theme/current',"$apiNamespace\Packages:setCurrentTheme");
            $group->put('/library/params',"$apiNamespace\Packages:setLibraryParams");
        })->add($sessionAuth);
        // Session
        Arikaim::$app->get('/core/api/session/',"$apiNamespace\Session:getInfo");
        // Install
        Arikaim::$app->post('/core/api/install',"$apiNamespace\Install:install");
        Arikaim::$app->put('/core/api/install/repair',"$apiNamespace\Install:repair",$sessionAuth);
        // Install page
        Arikaim::$app->get('/admin/install',"Arikaim\Core\App\InstallPage:loadInstall");
                   
        return true;      
    }
}
