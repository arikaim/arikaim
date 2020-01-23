<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\App;

use Arikaim\Core\Controllers\Controller;

/**
 * Page loader controller
*/
class ControlPanel extends Controller 
{   
    /**
     * Load control panel page
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function loadControlPanel($request, $response, $data) 
    {          
        return $this->pageLoad($request,$response,['page_name' => 'system:admin']);       
    }
}
