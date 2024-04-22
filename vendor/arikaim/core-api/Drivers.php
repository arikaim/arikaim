<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     CoreAPI
*/
namespace Arikaim\Core\Api;

use Arikaim\Core\Controllers\ControlPanelApiController;

/**
 * Drivers controller
*/
class Drivers extends ControlPanelApiController
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
    public function saveConfig($request, $response, $data)
    {
        $data
            ->addRule('text:required','name')               
            ->validate(true);      

        $driverName = $data->get('name');           
        $data->offsetUnset('name');

        $config = $this->get('driver')->getConfig($driverName);
        $config->setPropertyValues($data->toArray());
        $result = $this->get('driver')->saveConfig($driverName,$config);
    
        $this->setResponse($result,'drivers.config','errors.drivers.config');
    }

    /**
     * Set driver driver status (enable, disable)
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function setStatus($request, $response, $data)
    {
        $data->validate(true);  

        $name = $data->get('name');
        $status = $data->get('status');
        $result = ($status == 0) ? $this->get('driver')->disable($name) : $this->get('driver')->enable($name);
    
        $this->setResponse($result,'drivers.enable','errors.drivers.enable');                  
    }

    /**
     * Uninstall driver
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function uninstall($request, $response, $data)
    {
        $data->validate(true);  

        $result = $this->get('driver')->unInstall($data['name']);
    
        $this->setResponse($result,'drivers.uninstall','errors.drivers.uninstall');    
    }
}
