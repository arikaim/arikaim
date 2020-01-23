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
use Arikaim\Core\App\Install as SystemInstall;

/**
 * Install controller
*/
class Install extends ApiController
{
    /**
     * Install Arikaim 
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function installController($request, $response, $data) 
    {           
        $this->get('access')->logout();
        
        $this->onDataValid(function($data) {             
            // save config file               
            $this->get('config')->setValue('db/username',$data->get('username'));
            $this->get('config')->setValue('db/password',$data->get('password'));
            $this->get('config')->setValue('db/database',$data->get('database'));         
            $this->get('config')->save();
              
            $result = $this->get('db')->testConnection($this->get('config')->get('db'));
          
            if ($result == true) {          
                // do install
                $install = new SystemInstall();
                $result = $install->install();   
                                      
                $this->setResponse($result,function() {                  
                    $this->message('Arikaim CMS was installed successfully.');                                          
                },'INSTALL_ERROR');
            } else {              
                $this->addErrors($install->getErrors());
            }         
        });
        $data
            ->addRule("text:min=2","database")
            ->addRule("text:min=2","username")
            ->addRule("text:min=2","password")
            ->validate();      
    }

    /**
     * Repair installation Arikaim 
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function repairController($request, $response, $data) 
    {
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) {  
            
            $install = new SystemInstall();
            $result = $install->install();   
            
            $this->setResponse($result,function() {                  
                $this->message('Arikaim CMS was installed successfully.');                                          
            },'INSTALL_ERROR');
        });
        $data->validate();  
    }
}
