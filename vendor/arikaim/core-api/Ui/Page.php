<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     CoreAPI
*/
namespace Arikaim\Core\Api\Ui;

use Arikaim\Core\Controllers\ApiController;

/**
 * Page Api controller
*/
class Page extends ApiController 
{
   /**
     * Load library details 
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data    
     * @return Psr\Http\Message\ResponseInterface
     */
    public function loadLibraryDetails($request, $response, $data) 
    {        
        $libraryName = $data->get('name',null);
        $data = $this->get('page')->getLibraryDetails($libraryName);
          
        return $this->setResult([
            'name'        => $libraryName,
            'css'         => $data['files']['css'] ?? [],
            'js'          => $data['files']['js'] ?? [],
            'async'       => $data['async'],
            'crossorigin' => $data['crossorigin']
        ])->getResponse();       
    }
}
