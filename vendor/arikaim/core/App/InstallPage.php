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

use Arikaim\Core\App\Install;
use Arikaim\Core\Controllers\Controller;

/**
 * Page loader controller
*/
class InstallPage extends Controller 
{   
    /**
     * Load install page
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function loadInstallPage($request, $response, $data)
    {
        $this->get('cache')->clear();   

        $disableInstallPage = $this->get('config')->getByPath('settings/disableInstallPage');
        if ($disableInstallPage == true) {           
            return $this->get('errors')->loadPageNotFound($response); 
        }

        if (Install::isInstalled() == false) { 
            return $this->loadPage($request,$response,['page_name' => 'system:install']);                   
        }
        $this->get('errors')->addError('INSTALLED_ERROR');
        
        return $this->get('errors')->loadSystemError($response); 
    }
}
