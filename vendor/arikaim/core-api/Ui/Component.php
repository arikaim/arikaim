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
use Arikaim\Core\Api\Traits\UiComponent;

/**
 * Component Api controller
*/
class Component extends ApiController
{
    use UiComponent;

    /**
     * Get html component properties
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function componentProperties($request, $response, $data)
    {
        $componentName = \urldecode($data['name']);

        $language = $this->getPageLanguage($data);
        $component = $this->get('view')->createComponent($componentName,$language,'json');

        if ($component->hasError() == true) {
            $error = $component->getError();                       
            return $this->withError($error)->getResponse();  
        }
        
        return $this->setResult($component->getProperties())->getResponse();        
    }

    /**
     * Get html component details
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function componentDetails($request, $response, $data)
    {
        // control panel only
        $this->requireControlPanelPermission();

        $componentName = \urldecode($data['name']);
        $language = $this->getPageLanguage($data);
        $type = $data->get('component_type','arikaim'); 
       
        $component = $this->get('view')->createComponent($componentName,$language,$type);
    
        if ($component->hasError() == true) {          
            return $this->withError($component->getError())->getResponse();            
        }
 
        return $this->setResult([
            'properties' => $component->getProperties(),
            'options'    => $component->getOptions(),
            'files'      => $component->getFiles()
        ])->getResponse();       
    }

    /**
     * Load html component
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function loadComponent($request, $response, $data)
    {       
        $params = $this->getParams($request);
       
        // get header params
        $headerParams = $this->getHeaderParams($request);
        $params = \array_merge($params,$headerParams);
        $params = \array_merge($params,$data->toArray());
      
        $language = $this->getPageLanguage($params);    
        $this->get('page')->setLanguage($language);
        $type = $params['component_type'] ?? null;

        // access
        $this->get('access')->withProvider('session');
        
        return $this->load($data['name'],$params,$language,$type);
    }
}
