<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Api;

use Arikaim\Core\Controllers\ApiController;
use Arikaim\Core\System\Update as SystemUpdate;
use Arikaim\Core\Arikaim;

/**
 * Update controller
*/
class Update extends ApiController
{
    /**
     * Init controller
     *
     * @return void
     */
    public function init()
    {
        $this->loadMessages('system:admin.messages');
    }

    /**
     * Update Arikaim
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function updateController($request, $response, $data) 
    {           
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) { 
            $package = $data->get('package',Arikaim::getCorePackageName());
            $update = new SystemUpdate($package);
            $update->update();
            $version = $update->getCurrentVersion();
       
            return $this->setResponse(true,function() use($version) {
                $this
                    ->message('core.update')
                    ->field('version',$version);                            
            },'errors.core.update');
        });
        $data->validate();    
                 
    }
    
    /**
     * Get last package version
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function getLastVersion($request, $response, $data) 
    {           
        $this->requireControlPanelPermission();
        
        $package = $data->get('package',Arikaim::getCorePackageName());
        $update = new SystemUpdate($package);
        $version = $update->getLastVersion();
       
        $this->setResponse($version,function() use($version) {
            $this->field('version',$version);             
        },'errors.core.update');
        
        return $this->getResponse();  
    }
}
