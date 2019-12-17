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

/**
 * Drivers controller
*/
class Drivers extends ApiController
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
     * Save driver config
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function saveConfigController($request, $response, $data)
    {
        $this->onDataValid(function($data) {            
            $driverName = $data->get('name');           
            $data->offsetUnset('name');

            $config = $this->get('driver')->getConfig($driverName);
            // change config valus
            $config->setPropertyValues($data->toArray());
            $result = $this->get('driver')->saveConfig($driverName,$config);
        
            $this->setResponse($result,'drivers.config','errors.drivers.config');
        });
        $data->validate();       
    }

     /**
     * Read driver config
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function readConfigController($request, $response, $data)
    {
        $this->onDataValid(function($data) {            
            $driverName = $data->get('name'); 
            $result = $this->get('driver')->getConfig($driverName);

            $this->setResponse($result,'drivers.config','errors.drivers.config');
        });
        $data->validate();       
    }


    /**
     * Set driver driver status (enable, disable)
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function setStatusController($request, $response, $data)
    {
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) {    
            $name = $data->get('name');
            $status = $data->get('status');
            $result = ($status == 0) ? $this->get('driver')->disable($name) : $this->get('driver')->enable($name);
        
            $this->setResponse($result,'drivers.enable','errors.drivers.enable');    
        });
        $data->validate();         
    }
}
