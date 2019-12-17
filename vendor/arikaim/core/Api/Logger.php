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
 * Logger controller
*/
class Logger extends ApiController
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
     * Remove system logs
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function clearController($request, $response, $data)
    {
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) {            
            $result = $this->get('logger')->deleteSystemLogs();
            $this->setResponse($result,'logs.clear','errors.logs.clear');
        });
        $data->validate();
    }
}
