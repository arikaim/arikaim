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
use Arikaim\Core\Utils\File;

/**
 * Cache controller
*/
class Cache extends ControlPanelApiController
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
     * Clear cache
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function clear($request, $response, $data)
    { 
        $result = $this->get('cache')->clear();
        $this->setResponse($result,'cache.clear','errors.cache.clear');
    }

    /**
     * Enable cache
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function enable($request, $response, $data)
    {
        $result = File::setWritable($this->get('cache')->getCacheDir());
        if ($result === false) {
            $this->error('errors.cache.writable');
            return;
        }

        $this->get('config')->setBooleanValue('settings/cache',true);
        $result = $this->get('config')->save();

        $this->get('cache')->clear();
        $this->get('config')->reloadConfig();
        
        $this->setResponse($result,'cache.enable','errors.cache.enable');
    }

    /**
     * Disable cache
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function disable($request, $response, $data)
    {
        $this->get('config')->setBooleanValue('settings/cache',false);
        $this->get('config')->setValue('settings/cacheDriver','void');
        $result = $this->get('config')->save();
        
        $this->get('cache')->clear();
        $this->get('config')->reloadConfig();

        $this->setResponse($result,'cache.disable','errors.cache.disable');
    }

    /**
     * Set cache driver
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function setDriver($request, $response, $data)
    {
        $driverName = $data->get('name','void');
        $this->get('config')->setValue('settings/cacheDriver',$driverName);
        $result = $this->get('config')->save();
        
        $this->get('cache')->clear();
        $this->get('config')->reloadConfig();
        $this->get('cache')->setDriver($driverName);
        
        $this->setResponse($result,'cache.driver','errors.cache.driver');
    }
}
