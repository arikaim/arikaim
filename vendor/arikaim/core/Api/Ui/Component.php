<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Api\Ui;

use Arikaim\Core\Controllers\ApiController;
use Arikaim\Core\Collection\Arrays;

/**
 * Component Api controller
*/
class Component extends ApiController
{
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
        $component = $this->get('page')->createHtmlComponent($data['name'])->renderComponent();
        if (is_object($component) == false) {
            return $this->withError('Not valid component nane.')->getResponse();  
        }
        if ($component->hasError() == true) {
            return $this->withError($component->getError())->getResponse();  
        }
        // deny requets 
        if ($component->getOption('access/deny-request') == true) {
            return $this->withError($this->get('errors')->getError('ACCESS_DENIED'))->getResponse();           
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

        $component = $this->get('page')->createHtmlComponent($data['name'])->renderComponent();
        if (is_object($component) == false) {
            return $this->withError('Not valid component nane.')->getResponse();  
        }
        if ($component->hasError() == true) {
            $error = $component->getError();
            return $this->withError($this->get('errors')->getError($error['code'],$error['params']))->getResponse();            
        }
        $details = [
                'properties' => $component->getProperties(),
                'options'    => $component->getOptions(),
                'files'      => $component->getFiles()
        ];
        
        return $this->setResult($details)->getResponse();       
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
        $params = array_merge($params,$headerParams);
    
        return $this->load($data['name'],$params);
    }

    /**
     * Load html component
     *
     * @param string $name
     * @param array $params
     * @return JSON 
     */
    public function load($name, $params = [])
    {   
        $component = $this->get('page')->createHtmlComponent($name,$params)->renderComponent();
        if (is_object($component) == false) {
            return $this->withError('Not valid component nane.')->getResponse();  
        }
        
        if ($component->hasError() == true) {
            $error = $component->getError();          
            return $this->withError($this->get('errors')->getError($error['code'],$error['params']))->getResponse();          
        }
      
        if ($component->getOption('access/deny-request') == true) {
            return $this->withError('ACCESS_DENIED')->getResponse();           
        }
        $files = $this->get('view')->properties()->get('include.components.files');
        
        $result = [
            'html'       => $component->getHtmlCode(),
            'css_files'  => (isset($files['css']) == true) ? Arrays::arrayColumns($files['css'],['url','params']) : [],
            'js_files'   => (isset($files['js']) == true)  ? Arrays::arrayColumns($files['js'],['url','params'])  : [],
            'properties' => json_encode($component->getProperties())
        ];
  
        return $this->setResult($result)->getResponse();        
    }

    /**
     * Get header params
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return array
     */
    private function getHeaderParams($request)
    {       
        $headerParams = (isset($request->getHeader('Params')[0]) == true) ? $request->getHeader('Params')[0] : null;
        
        if ($headerParams != null) {
            $headerParams = json_decode($headerParams,true);
            if (is_array($headerParams) == true) {
                return $headerParams;
            }
        }

        return [];
    }
}
