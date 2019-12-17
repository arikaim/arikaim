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
 * Settings controller
*/
class Settings extends ApiController
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
     * Save debug setting
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function setDebug($request, $response, $data)
    {
        $this->requireControlPanelPermission();
        
        $debug = $data->get('debug',false);
       
        $this->get('config')->setBooleanValue('settings/debug',$debug);
        // save and reload config file
        $result = $this->get('config')->save();
        $this->setResponse($result,'settings.save','errors.settings.save');

        $this->get('cache')->clear();
        
        return $this->getResponse();
    }

    /**
     * Disable install page
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function disableInstallPage($request, $response, $data)
    {
        $this->requireControlPanelPermission();
        
        $installPage = $data->get('install_page',false);
       
        $this->get('config')->setBooleanValue('settings/disableInstallPage',$installPage);
        // save and reload config file
        $result = $this->get('config')->save();
        $this->setResponse($result,'settings.save','errors.settings.save');

        $this->get('cache')->clear();

        return $this->getResponse();
    }
}
