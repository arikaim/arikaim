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

use Arikaim\Core\Controllers\ApiController;
use Arikaim\Core\Http\Session;

/**
 * Session controller
*/
class SessionApi extends ApiController
{
    /**
     * Get session info
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function getInfo($request, $response, $data) 
    {           
        $sessionInfo = Session::getParams();   
        $sessionInfo['recreate'] = $this->get('config')['settings']['sessionInterval'] ?? 0;
        
        return $this->setResult($sessionInfo)->getResponse();       
    }

    /**
     * Recreate session
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function recreate($request, $response, $data) 
    {             
        $lifetime = $data->get('lifetime',null);
        Session::recrete($lifetime);

        $sessionInfo = Session::getParams();  
        $sessionInfo['recreate'] = $this->get('config')['settings']['sessionInterval'] ?? 0;     
        
        return $this->setResult($sessionInfo)->getResponse();       
    }

     /**
     * Restart session
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function restart($request, $response, $data) 
    { 
        $this->requireControlPanelPermission();
        
        $lifetime = $data->get('lifetime',null);
        Session::restart($lifetime);

        $sessionInfo = Session::getParams();  
        return $this->setResult($sessionInfo)->getResponse();       
    }
}
