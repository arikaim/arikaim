<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Middleware;

use Http\Factory\Guzzle\StreamFactory;

use Slim\Middleware\ContentLengthMiddleware;
use Slim\Middleware\OutputBufferingMiddleware;
use Slim\Middleware\BodyParsingMiddleware;

use Arikaim\Core\Db\Schema;
use Arikaim\Core\Utils\Factory;
use Arikaim\Core\Arikaim;
use Arikaim\Core\Packages\ModulePackage;
use Arikaim\Core\System\Error\ApplicationError;
use Arikaim\Core\Middleware\CoreMiddleware;
use Arikaim\Core\Http\Response;
use Arikaim\Core\System\Error\Renderer\HtmlPageErrorRenderer;

/**
 * Middleware Manager
 */
class MiddlewareManager 
{
    /**
     * Add modules middleware
     *   
     * @return boolean
     */
    public static function addModules()
    {
        $modules = Arikaim::cache()->fetch('middleware.list');
        if (is_array($modules) == false) {   
            if (Schema::hasTable('modules') == false) {
                return false;
            }            
            $modules = Arikaim::packages()->create('module')->getPackgesRegistry()->getPackagesList([
                'type'   => ModulePackage::getTypeId('middleware'), 
                'status' => 1    
            ]);         
            Arikaim::cache()->save('middleware.list',$modules,2);    
        }    

        foreach ($modules as $module) {             
            $instance = Factory::createModule($module['name'],$module['class']);
            if (is_object($instance) == true) {
                Arikaim::$app->add($instance);  
            }         
        }
        
        return true;
    }

    /**
     * Add core and module middlewares
     */
    public static function init()
    {
        Arikaim::$app->addRoutingMiddleware();

        $errorMiddleware = Arikaim::$app->addErrorMiddleware(true,true,true);
        $errorRenderer = new HtmlPageErrorRenderer(Arikaim::errors());
        $applicationError = new ApplicationError(Response::create(),$errorRenderer);
        
        $errorMiddleware->setDefaultErrorHandler($applicationError);
        Arikaim::$app->add(new ContentLengthMiddleware());
        Arikaim::$app->add(new BodyParsingMiddleware());
        Arikaim::$app->add(new OutputBufferingMiddleware(new StreamFactory(),OutputBufferingMiddleware::APPEND));
        
        // sanitize request body and client ip
        Arikaim::$app->add(new CoreMiddleware());    
       
        // add modules middlewares 
        Self::addModules();      
    }
}
