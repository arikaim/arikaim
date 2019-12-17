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

use Arikaim\Core\Http\Url;
use Arikaim\Core\Collection\Arrays;
use Arikaim\Core\View\Html\HtmlComponent;

/**
 * Base class for all Controllers
*/
class Controller
{
    /**
     * Extension name
     *
     * @var string|null
     */
    protected $extensionName;   

    /**
     * Response messages
     *
     * @var array
     */
    protected $messages;

    /**
     * Container
     *
     * @var Container
     */
    protected $container;

    /**
     * Undocumented variable
     *
     * @var Model
     */
    protected $routes;

    /**
     * Constructor
     */
    public function __construct($container)
    { 
        $this->extensionName = $container->getItem('contoller.extension');
        $this->messages = [];
        $this->container = $container;
        $this->routes = ($container->has('routes') == true) ? $container->get('routes') : null;

        $this->init();
    }

    /**
     * Get item from container
     *
     * @param string $id
     * @return mixed
     */
    public function get($id)
    {
        return $this->container->get($id);
    }

    /**
     * Return tru if container item esist
     *
     * @param string $id
     * @return mixed
     */
    public function has($id)
    {
        return $this->container->has($id);
    }

    /**
     * Get extension name
     *
     * @return string|null
     */
    public function getExtensionName()
    {
        return $this->extensionName;
    }

    /**
     * Set extension name
     *
     * @param string $name
     * @return void
     */
    public function setExtensionName($name)
    {
        $this->extensionName = $name;
    }
    
    /**
     * Add system error
     *
     * @param string $name
     * @return boolean
    */
    public function addError($name)
    {
        $message = $this->getMessage($name);
        $message = (empty($message) == true) ? $name : $message;
        
        if ($this->has('errors') == true) {
            return $this->get('errors')->addError($message);
        }
        
        return false;
    }

    /**
     * Get url
     *
     * @param ServerRequestInterface $request 
     * @param boolean $relative
     * @return string
     */
    public function getUrl($request, $relative = false)
    {
        $path = $request->getUri()->getPath();
        return ($relative == true ) ? $path : Url::BASE_URL . '/' . $path;
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
     * Load messages from html component json file
     *
     * @param string $componentName
     * @param string $language
     * @return void
     */
    public function loadMessages($componentName, $language = null)
    {
        $messages = $this->get('page')->createHtmlComponent($componentName,[],$language)->getProperties();
        $this->messages = (is_object($messages) == true) ? $messages->toArray() : [];
    }

    /**
     * Get message
     *
     * @param string $name
     * @return string
     */
    public function getMessage($name)
    {
        return (isset($this->messages[$name]) == true) ? $this->messages[$name] : Arrays::getValue($this->messages,$name,'.');        
    }

    /**
     * Return current logged user
     *
     * @return mixed
     */
    public function user()
    {
        return ($this->has('access') == true) ? $this->get('access')->getUser() : false;         
    }

    /**
     * Set callback for validation errors
     *
     * @param \Closure $callback
     * @return void
    */
    public function onValidationError(\Closure $callback)
    {
        $function = function($event) use(&$callback) {
            return $callback($event->toArray());
        };
        if ($this->has('event') == true) {
            $this->get('event')->subscribeCallback('validator.error',$function,true);
        }   
    }
    
    /**
     * Set callback for validation done
     *
     * @param \Closure $callback
     * @return void
     */
    public function onDataValid(\Closure $callback)
    {
        $function = function($event) use(&$callback) {
            return $callback($event->toCollection());
        };
        if ($this->has('event') == true) {
            $this->get('event')->subscribeCallback('validator.valid',$function,true);
        }
    }

    /**
     * Get request params
     *
     * @param object $request
     * @return array
     */
    public function getParams($request)
    {
        $params = explode('/', $request->getAttribute('params'));
        $params = array_filter($params);
        $vars = $request->getQueryParams();

        return array_merge($params, $vars);       
    }

    /**
     * Require control panel permission
     *
     * @return void
     */
    public function requireControlPanelPermission()
    {
        if ($this->has('access') == false) {
            return false;
        }

        return $this->requireAccess($this->get('access')->getControlPanelPermission(),$this->get('access')->getFullPermissions());
    }
    
    /**
     * Reguire permission check if current user have permission
     *
     * @param string $name
     * @param mixed $type
     * @return bool
     */
    public function requireAccess($name, $type = null)
    {       
        if ($this->has('access') == false) {
            return false;
        }

        if ($this->get('access')->hasAccess($name,$type) == true) {
            return true;
        }
        $response = $this->get('errors')->loadSystemError($this->response);
        Response::emit($response); 
          
        exit();
    }

    /**
     * Get page language
     *
     * @param array $data
     * @return string
    */
    public function getPageLanguage($data)
    {
        return (isset($data['language']) == true) ? $data['language'] : HtmlComponent::getLanguage();           
    }

    /**
     * Load page
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param CollectionInterface $data   
     * @param string|null $name Page name  
     * @return Psr\Http\Message\ResponseInterface
    */
    public function loadPage($request, $response, $data, $pageName = null)
    {       
        $language = $this->getPageLanguage($data);
        if (empty($pageName) == true) {
            $pageName = (isset($data['page_name']) == true) ? $data['page_name'] : $this->resolvePageName($request);
        } 
      
        $data = (is_object($data) == true) ? $data->toArray() : $data;
        if (empty($pageName) == true) {
            return $this->get('errors')->loadPageNotFound($response,$data,$language);    
        } 
        
        return $this->get('page')->load($response,$pageName,$data,$language);
    }

    /**
     * Resolve page name
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request 
     * @return string|null
     */
    protected function resolvePageName($request)
    {            
        // try from reutes db table
        $route = $request->getAttribute('route');  
        if ((is_object($route) == true) && ($this->has('routes') == true)) {
            $pattern = $route->getPattern();          
            $routeData = $this->get('routes')->getRoute('GET',$pattern);            
            return (is_array($routeData) == true) ? $routeData['page_name'] : null;             
        } 

        return null;
    }

    /**
     * Display page not found
     *    
     * @param ResponseInterface $response
     * @param array $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function pageNotFound($response, $data = [])
    {     
        $language = $this->getPageLanguage($data);

        return $this->get('errors')->loadPageNotFound($response,$data,$language,$this->getExtensionName());    
    }

    /**
     * Display system error page
     *    
     * @param ResponseInterface $response
     * @param array $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function systemErrorPage($response, $data = [])
    {     
        $language = $this->getPageLanguage($data);

        return $this->get('errors')->loadSystemError($response,$data,$language,$this->getExtensionName());    
    }

    /**
     * Write XML to reponse body
     *
     * @param ResponseInterface $response
     * @param string $xml
     * @return ResponseInterface
     */
    public function writeXml(ResponseInterface $response, $xml)
    {
        $response->getBody()->write($xml);

        return $response->withHeader('Content-Type','text/xml');
    }
}
