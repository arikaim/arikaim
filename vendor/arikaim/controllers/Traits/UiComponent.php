<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Controllers\Traits;

use Arikaim\Core\Api\Traits\UiComponent as UiComponentTrait;

/**
 * Ui Component Api controller
*/
trait UiComponent
{
    use UiComponentTrait;

    /**
     * Get components list
     *
     * @return array
     */
    protected function getAllwovedComponnets(): array
    {
        return $this->allowedComponents ?? [];
    } 

    /**
     * Return true if component access is allowed
     *
     * @param string $name
     * @return boolean
     */
    protected function isAllwoved(string $name): bool
    {
        return \in_array($name,$this->getAllwovedComponnets());
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
      
        // check access
        if ($this->isAllwoved($params['name']) == false) {
            $this->error('errors.access');
            return $this->getResponse();       
        }

        $language = $this->getPageLanguage($params);    
        $this->get('page')->setLanguage($language);
        $type = $params['component_type'] ?? null;
 
        return $this->load($data['name'],$params,$language,$type);
    }
}
