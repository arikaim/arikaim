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
 * Settings controller
*/
class Settings extends ControlPanelApiController
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
     * Disable install page
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function disableInstallPage($request, $response, $data)
    {
        $installPage = $data->get('install_page',false);
        $this->get('cache')->clear();
        
        $this->get('config')->setBooleanValue('settings/disableInstallPage',$installPage);
        // save and reload config file
        $result = $this->get('config')->save();
        $this->get('cache')->clear();
        $this->get('config')->reloadConfig();      

        $this->setResponse($result,'settings.save','errors.settings.save');
    }

    /**
     * Update setting option vars
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function updateOption($request, $response, $data)
    {
        $key = $data->getString('key',null);
        $value = $data->getString('value',null);
        $type = $data->getString('type',null);

        if ($this->get('config')->hasWriteAccess($key) == false) {
            $this->error('access.denied');
            return false;
        }

        $this->get('cache')->clear();
        if ($type == 'boolean') {
            $this->get('config')->setBooleanValue($key,$value);
        } else {
            $this->get('config')->setValue($key,$value);
        }
       
        // save and reload config file
        $result = $this->get('config')->save();
        $this->get('cache')->clear();
        $this->get('config')->reloadConfig();     

        $this->setResponse($result,'settings.save','errors.settings.save');       
    }
}
