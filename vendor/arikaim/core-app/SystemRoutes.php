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

/**
 * Routes
 */
class SystemRoutes 
{
    /**
     * System routes
     *
     * @var array
     */
    public static $routes = [
        'GET' => [           
            // Ui component
            [
                'pattern' => '/core/api/ui/component/properties/{name}[/{params:.*}]',
                'handler' => 'Arikaim\Core\Api\Ui\Component:componentProperties',
                'auth'    => null               
            ],
            [
                'pattern' => '/core/api/ui/component/details/{name}[/{params:.*}]',
                'handler' => 'Arikaim\Core\Api\Ui\Component:componentDetails',
                'auth'    => 'session'               
            ],
            [
                'pattern' => '/core/api/ui/component/{name}[/{params:.*}]',
                'handler' => 'Arikaim\Core\Api\Ui\Component:loadComponent',
                'auth'    => null            
            ],
            // UI Library
            [
                'pattern' => '/core/api/ui/library/{name}',
                'handler' => 'Arikaim\Core\Api\Ui\Page:loadLibraryDetails',
                'auth'    => null            
            ], 
            // Paginator 
            [
                'pattern' => '/core/api/ui/paginator/[{namespace}]',
                'handler' => 'Arikaim\Core\Api\Ui\Paginator:getPage',
                'auth'    => null            
            ],
            [
                'pattern' => '/core/api/ui/paginator/view/type/[{namespace}]',
                'handler' => 'Arikaim\Core\Api\Ui\Paginator:getViewType',
                'auth'    => null            
            ],
            // Order by column     
            [
                'pattern' => '/core/api/ui/order/[{namespace}]',
                'handler' => 'Arikaim\Core\Api\Ui\OrderBy:getOrderBy',
                'auth'    => 'session'            
            ],
            // Options 
            [
                'pattern' => '/core/api/options/{key}',
                'handler' => 'Arikaim\Core\Api\Options:read',
                'auth'    => 'session'            
            ],          
            // Mailer 
            [
                'pattern' => '/core/api/mailer/test/email[/{component}]',
                'handler' => 'Arikaim\Core\Api\Mailer:sendTestEmail',
                'auth'    => 'session'            
            ],
            // Session
            [
                'pattern' => '/core/api/session/',
                'handler' => 'Arikaim\Core\Api\Session:getInfo',
                'auth'    => null       
            ],
            // Logout
            [
                'pattern' => '/core/api/user/logout',
                'handler' => 'Arikaim\Core\Api\Users:logout',
                'auth'    => null            
            ]
        ],
        'POST' => [                     
            // Arikaim Store
            [
                'pattern' => '/core/api/store/product',
                'handler' => 'Arikaim\Core\Api\Store:saveOrder',
                'auth'    => null                
            ],
            // Ui component
            [
                'pattern' => '/core/api/ui/component/{name}[/{params:.*}]',
                'handler' => 'Arikaim\Core\Api\Ui\Component:loadComponent',
                'auth'    => null            
            ],
            // User
            [
                'pattern' => '/core/api/user/login',
                'handler' => 'Arikaim\Core\Api\Users:adminLogin',
                'auth'    => null            
            ],
            [
                'pattern' => '/core/api/user/details',
                'handler' => 'Arikaim\Core\Api\Users:changeDetails',
                'auth'    => null            
            ],
            [
                'pattern' => '/core/api/user/password',
                'handler' => 'Arikaim\Core\Api\Users:changePassword',
                'auth'    => null            
            ],
            // Languages  
            [
                'pattern' => '/core/api/language/add',
                'handler' => 'Arikaim\Core\Api\Language:add',
                'auth'    => 'session'            
            ],
            // Options 
            [
                'pattern' => '/core/api/options/',
                'handler' => 'Arikaim\Core\Api\Options:saveOptions',
                'auth'    => 'session'            
            ],
            // Options and relations used for all extensions  
            [
                'pattern' => '/core/api/orm/relation',
                'handler' => 'Arikaim\Core\Api\Orm\Relations:addRelation',
                'auth'    => 'session'            
            ],
            // Packages
            [
                'pattern' => '/core/api/packages/upload',
                'handler' => 'Arikaim\Core\Api\UploadPackages:upload',
                'auth'    => 'session'            
            ],
            [
                'pattern' => '/core/api/packages/config',
                'handler' => 'Arikaim\Core\Api\Packages:saveConfig',
                'auth'    => 'session'            
            ],
            // Prepare install
            [
                'pattern' => '/core/api/install/prepare',
                'handler' => 'Arikaim\Core\Api\Install:prepare',
                'auth'    => null         
            ],
            // Install
            [
                'pattern' => '/core/api/install/',
                'handler' => 'Arikaim\Core\Api\Install:install',
                'auth'    => null         
            ]
        ],
        'PUT' => [                                  
            // Arikaim Store remove order
            [
                'pattern' => '/core/api/store/product/remove',
                'handler' => 'Arikaim\Core\Api\Store:removeOrder',
                'auth'    => null                
            ],
            // Paginator 
            [
                'pattern' => '/core/api/ui/paginator/page-size',
                'handler' => 'Arikaim\Core\Api\Ui\Paginator:setPageSize',
                'auth'    => null            
            ],
            [
                'pattern' => '/core/api/ui/paginator/page',
                'handler' => 'Arikaim\Core\Api\Ui\Paginator:setPage',
                'auth'    => null            
            ],
            [
                'pattern' => '/core/api/ui/paginator/view/type',
                'handler' => 'Arikaim\Core\Api\Ui\Paginator:setViewType',
                'auth'    => null            
            ],     
            // Search
            [
                'pattern' => '/core/api/ui/search/',
                'handler' => 'Arikaim\Core\Api\Ui\Search:setSearch',
                'auth'    => null            
            ],
            [
                'pattern' => '/core/api/ui/search/condition/[{namespace}]',
                'handler' => 'Arikaim\Core\Api\Ui\Search:setSearchCondition',
                'auth'    => null            
            ],
            // Order by column     
            [
                'pattern' => '/core/api/ui/order/[{namespace}]',
                'handler' => 'Arikaim\Core\Api\Ui\OrderBy:setOrderBy',
                'auth'    => 'session'            
            ],
            // Position
            [
                'pattern' => '/core/api/ui/position/shift',
                'handler' => 'Arikaim\Core\Api\Ui\Position:shift',
                'auth'    => 'session'            
            ],
            [
                'pattern' => '/core/api/ui/position/swap',
                'handler' => 'Arikaim\Core\Api\Ui\Position:swap',
                'auth'    => 'session'            
            ],
            // Languages
            [
                'pattern' => '/core/api/language/update',
                'handler' => 'Arikaim\Core\Api\Language:update',
                'auth'    => 'session'            
            ],
            [
                'pattern' => '/core/api/language/change/{language_code}',
                'handler' => 'Arikaim\Core\Api\Language:changeLanguage',
                'auth'    => null         
            ],
            [
                'pattern' => '/core/api/language/status',
                'handler' => 'Arikaim\Core\Api\Language:setStatus',
                'auth'    => 'session'            
            ],
            [
                'pattern' => '/core/api/language/default',
                'handler' => 'Arikaim\Core\Api\Language:setDefault',
                'auth'    => 'session'            
            ],
            // Options 
            [
                'pattern' => '/core/api/options/',
                'handler' => 'Arikaim\Core\Api\Options:save',
                'auth'    => 'session'            
            ],
            // Drivers 
            [
                'pattern' => '/core/api/driver/status',
                'handler' => 'Arikaim\Core\Api\Drivers:setStatus',
                'auth'    => 'session'            
            ],
            [
                'pattern' => '/core/api/driver/config',
                'handler' => 'Arikaim\Core\Api\Drivers:saveConfig',
                'auth'    => 'session'            
            ],
            // Session
            [
                'pattern' => '/core/api/session/recreate',
                'handler' => 'Arikaim\Core\Api\SessionApi:recreate',
                'auth'    => 'session'            
            ],
            [
                'pattern' => '/core/api/session/restart',
                'handler' => 'Arikaim\Core\Api\SessionApi:restart',
                'auth'    => 'session'            
            ],
            // Settings
            [
                'pattern' => '/core/api/settings/install-page',
                'handler' => 'Arikaim\Core\Api\Settings:disableInstallPage',
                'auth'    => 'session'            
            ],
            [
                'pattern' => '/core/api/settings/update/option',
                'handler' => 'Arikaim\Core\Api\Settings:updateOption',
                'auth'    => 'session'            
            ],
            // Cache
            [
                'pattern' => '/core/api/cache/enable',
                'handler' => 'Arikaim\Core\Api\Cache:enable',
                'auth'    => 'session'            
            ],
            [
                'pattern' => '/core/api/cache/disable',
                'handler' => 'Arikaim\Core\Api\Cache:disable',
                'auth'    => 'session'            
            ],
            [
                'pattern' => '/core/api/cache/driver',
                'handler' => 'Arikaim\Core\Api\Cache:setDriver',
                'auth'    => 'session'            
            ],
            // Options and relations used for all extensions
            [
                'pattern' => '/core/api/orm/relation/delete',
                'handler' => 'Arikaim\Core\Api\Orm\Relations:deleteRelation',
                'auth'    => 'session'            
            ],
            [
                'pattern' => '/core/api/orm/options',
                'handler' => 'Arikaim\Core\Api\Orm\Options:saveOptions',
                'auth'    => 'session'            
            ],
            // Packages
            [
                'pattern' => '/core/api/packages/upload/confirm',
                'handler' => 'Arikaim\Core\Api\UploadPackages:confirmUpload',
                'auth'    => 'session'            
            ],
            [
                'pattern' => '/core/api/packages/install',
                'handler' => 'Arikaim\Core\Api\Packages:install',
                'auth'    => 'session'            
            ],
            [
                'pattern' => '/core/api/packages/composer/update',
                'handler' => 'Arikaim\Core\Api\Packages:updateComposerPackages',
                'auth'    => 'session'            
            ],
            [
                'pattern' => '/core/api/packages/repository/download',
                'handler' => 'Arikaim\Core\Api\Repository:repositoryDownload',
                'auth'    => 'session'            
            ],
            [
                'pattern' => '/core/api/packages/status',
                'handler' => 'Arikaim\Core\Api\Packages:setStatus',
                'auth'    => 'session'            
            ],
            [
                'pattern' => '/core/api/packages/uninstall',
                'handler' => 'Arikaim\Core\Api\Packages:unInstall',
                'auth'    => 'session'            
            ],
            [
                'pattern' => '/core/api/packages/update',
                'handler' => 'Arikaim\Core\Api\Packages:update',
                'auth'    => 'session'            
            ],
            [
                'pattern' => '/core/api/packages/primary',
                'handler' => 'Arikaim\Core\Api\Packages:setPrimary',
                'auth'    => 'session'            
            ],
            // Ui library
            [
                'pattern' => '/core/api/packages/library/params',
                'handler' => 'Arikaim\Core\Api\Packages:setLibraryParams',
                'auth'    => 'session'            
            ],
            [
                'pattern' => '/core/api/packages/library/status',
                'handler' => 'Arikaim\Core\Api\Packages:setLibraryStatus',
                'auth'    => 'session'            
            ],
            // Install
            [
                'pattern' => '/core/api/install/extensions',
                'handler' => 'Arikaim\Core\Api\Install:installExtensions',
                'auth'    => null         
            ],
            [
                'pattern' => '/core/api/install/modules',
                'handler' => 'Arikaim\Core\Api\Install:installModules',
                'auth'    => null         
            ],
            [
                'pattern' => '/core/api/install/actions',
                'handler' => 'Arikaim\Core\Api\Install:postInstallActions',
                'auth'    => null         
            ],
            [
                'pattern' => '/core/api/install/repair',
                'handler' => 'Arikaim\Core\Api\Install:repair',
                'auth'    => 'session'         
            ],
            [
                'pattern' => '/core/api/install/storage',
                'handler' => 'Arikaim\Core\Api\Install:initStorage',
                'auth'    => 'session'         
            ]
        ],
        'DELETE' => [
            // Uninstall Driver
            [
                'pattern' => '/core/api/driver/uninstall/{name}',
                'handler' => 'Arikaim\Core\Api\Drivers:uninstall',
                'auth'    => 'session'            
            ],
            // Paginator 
            [
                'pattern' => '/core/api/ui/paginator/{namespace}',
                'handler' => 'Arikaim\Core\Api\Ui\Paginator:remove',
                'auth'    => null            
            ],
            // Search
            [
                'pattern' => '/core/api/ui/search/condition/{field}/[{namespace}]',
                'handler' => 'Arikaim\Core\Api\Ui\Search:deleteSearchCondition',
                'auth'    => null            
            ],
            [
                'pattern' => '/core/api/ui/search/[{namespace}]',
                'handler' => 'Arikaim\Core\Api\Ui\Search:clearSearch',
                'auth'    => null            
            ],
            // Order by column     
            [
                'pattern' => '/core/api/ui/order/[{namespace}]',
                'handler' => 'Arikaim\Core\Api\Ui\OrderBy:deleteOrderBy',
                'auth'    => 'session'            
            ],
            // Languages
            [
                'pattern' => '/core/api/language/{uuid}',
                'handler' => 'Arikaim\Core\Api\Language:remove',
                'auth'    => 'session'            
            ],           
            // Cache
            [
                'pattern' => '/core/api/cache/clear',
                'handler' => 'Arikaim\Core\Api\Cache:clear',
                'auth'    => 'session'            
            ],
            // Logs  
            [
                'pattern' => '/core/api/logs/clear',
                'handler' => 'Arikaim\Core\Api\Logger:clear',
                'auth'    => 'session'            
            ]    
        ]      
    ];
}
