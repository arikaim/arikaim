<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Controllers;

use Psr\Http\Message\ResponseInterface;

use Arikaim\Core\Http\Session;

use Arikaim\Core\Controllers\Traits\Base\BaseController;
use Arikaim\Core\Controllers\Traits\Base\PageErrors;
use Arikaim\Core\Controllers\Traits\Base\Multilanguage;
use Arikaim\Core\Controllers\Traits\Base\UserAccess;

/**
 * Base class for all Controllers
*/
class Controller
{
    use 
        BaseController,
        Multilanguage,
        UserAccess,
        PageErrors;

    /**
     * Constructor
     *
     * @param Container $container
     */
    public function __construct($container = null)
    {      
        $this->container = $container;
        $this->init();
    }
 
    /**
     * Init controller, override this method in child classes
     *
     * @return void
    */
    public function init()
    {
    }

    /**
     * Call 
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {       
        $name .= 'Page';        
        if (\method_exists($this,$name) == true) {            
            $this->resolveRouteParams($arguments[0]);
            $result = ([$this,$name])($arguments[0],$arguments[1],$arguments[2]);               
            
            if ($result === false) {
                return $this->pageNotFound($arguments[1],$arguments[2]->toArray());  
            }

            return (empty($result) == true) ? $this->pageLoad($arguments[0],$arguments[1],$arguments[2]) : $result;
        }   

        return $this->pageNotFound($arguments[1],$arguments[2]->toArray());    
    }

    /**
     * Load page
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param CollectionInterface|array $data   
     * @param string|null $pageName     
     * @param boolean $resolveParams 
     * @param string|null $language
     * @param bool $dispatchEvent
     * @return \Psr\Http\Message\ResponseInterface
    */
    public function pageLoad(
        $request, 
        $response, 
        $data, 
        ?string $pageName = null, 
        ?string $language = null, 
        bool $dispatchEvent = false
    )
    {       
        $this->resolveRouteParams($request);                        
       
        $data = (\is_object($data) == true) ? $data->toArray() : $data;
        if (empty($pageName) == true) {
            $pageName = $data['page_name'] ?? $this->pageName;
        }

        if (empty($pageName) == true) {
            return $this->pageNotFound($response,$data);    
        } 

        // get current page language
        if (empty($language) == true) {   
            $language = $this->getPageLanguage($data,false);              
        }
        
        // set current language
        $this->get('page')->setLanguage($language);
       
        // current url path     
        $data['current_path'] = $request->getUri()->getPath();
        
        $component = $this->get('page')->render($pageName,$data,$language);
        $response->getBody()->write($component->getHtmlCode());

        if ($dispatchEvent === true) {
            $this->container->get('event')->dispatch('core.page.load',[
                'page_name'    => $pageName,
                'language'     => $language,
                'query_params' => $request->getQueryParams(),
                'data'         => $data
            ]);
        }
       
        return $response;
    }

    /**
     * Write XML to reponse body
     *
     * @param ResponseInterface $response
     * @param string $xml
     * @return ResponseInterface
     */
    public function writeXml(ResponseInterface $response, string $xml)
    {
        $response->getBody()->write($xml);

        return $response->withHeader('Content-Type','text/xml');
    }
}
