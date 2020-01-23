<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\System\Error;

use Arikaim\Core\Interfaces\SystemErrorInterface;
use Arikaim\Core\Interfaces\View\HtmlPageInterface;
use Arikaim\Core\Utils\Text;
use Arikaim\Core\Collection\Collection;
use Arikaim\Core\System\System;
use Arikaim\Core\Http\Request;
use Arikaim\Core\System\Error\PhpError;
use Arikaim\Core\System\Error\Renderer\ConsoleErrorRenderer;
use Arikaim\Core\System\Error\Renderer\JsonErrorRenderer;

/**
 * Errors
 */
class Errors extends Collection implements SystemErrorInterface
{
    /**
     *  Error page names
     */
    const PAGE_NOT_FOUND         = 'page-not-found';
    const SYSTEM_ERROR_PAGE      = 'system-error';
    const APPLICATION_ERROR_PAGE = 'application-error';

    /**
     * Errors
     *
     * @var array
     */
    private $errors;

    /**
     * Html page
     *
     * @var HtmlPageInterface
     */
    private $page; 

    /**
     * Constructor
     * 
     * @param HtmlPageInterface $page
     * @param array $systemErrors
     */
    public function __construct(HtmlPageInterface $page, array $systemErrors) 
    {
        $this->errors = [];
        $this->data = $systemErrors;
        $this->page = $page;
    }

    /**
     * Show system errors
     *
     * @param Request $request
     * @param string|null $error
     * @return string
     */
    public function renderSystemErrors($request, $error = null, $params = [])
    {
        if (empty($error) == false) {
            $this->addError($error,$params);
        }

        $errors = [];
        foreach ($this->getErrors() as $item) {
            $errorDetails['type'] = E_ERROR;
            $errorDetails['message'] = $item;
            $errors[] = PhpError::toArray($errorDetails);
        }
           
        if (System::isConsole() == true) {
            $render = new ConsoleErrorRenderer();
            return $render->render($errors);  
        } elseif (Request::isJsonContentType($request) == true) {
            $render = new JsonErrorRenderer();
            return $render->render($errors);  
        } 

        $output = $this->renderSystemError($errors)->getHtmlCode();   
        echo $output;        
    }

    /**
     * Add error
     *
     * @param string $errorCode
     * @param array $params
     * @return bool
     */
    public function addError($errorCode, $params = [])
    {       
        $message = ($this->hasErrorCode($errorCode) == true) ? $this->getError($errorCode,$params) : $errorCode;
        array_push($this->errors,$message);
     
        return true;
    }
    
    /**
     * Ger errors count
     *
     * @return integer
     */
    public function count()
    {
        return count($this->errors);
    }

    /**
     * Return true if have error
     *
     * @return boolean
     */
    public function hasError()
    {       
        return ($this->count() > 0) ? true : false;         
    }

    /**
     * Return true if error code exists
     *
     * @param string $code
     * @return boolean
     */
    public function hasErrorCode($code)
    {
        return $this->has($code);
    }

    /**
     * Get errors list
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get error code
     *
     * @param string $errorCode
     * @param string|null $default
     * @param array $params
     * @return string
     */
    public function getError($errorCode, $params = [], $default = 'UNKNOWN_ERROR') 
    {
        $error = $this->get($errorCode,null);
        $error = (empty($error) == true) ? $this->get($default,null) : $error;

        return (empty($error) == true) ? null : Text::render($error['message'], $params);      
    }

    /**
     * Get upload error message
     *
     * @param integer $errorCode
     * @return string
     */
    public function getUplaodFileError($errorCode)
    {
        switch ($errorCode) {
            case UPLOAD_ERR_OK:
                return "";// no error                
            case UPLOAD_ERR_INI_SIZE:
                return $this->getError("UPLOAD_ERR_INI_SIZE");
            case UPLOAD_ERR_FORM_SIZE:
                return $this->getError("UPLOAD_ERR_FORM_SIZE");
            case UPLOAD_ERR_PARTIAL:
                return $this->getError("UPLOAD_ERR_PARTIAL");
            case UPLOAD_ERR_NO_FILE:
                return $this->getError("UPLOAD_ERR_NO_FILE");
            case UPLOAD_ERR_NO_TMP_DIR:
                return $this->getError("UPLOAD_ERR_NO_TMP_DIR");
            case UPLOAD_ERR_CANT_WRITE:
                return $this->getError("UPLOAD_ERR_CANT_WRITE");
            case UPLOAD_ERR_EXTENSION:
                return $this->getError("UPLOAD_ERR_EXTENSION");
        }

        return "";
    }
    
    /**
     * Resolve error page name
     *
     * @param string $type
     * @param string|null $extension
     * @return string
     */
    public function resoveErrorPageName($type, $extension = null)
    {
        $pageName = (empty($extension) == true) ? 'system:' . $type : $extension . ">" . $type;  
        
        return ($this->has($pageName) == true) ? $pageName : 'system:' . $type;
    }

    /**
     * Load system error page.
     *
     * @param Response $response
     * @param array $data
     * @param string|null $language
     * @param string|null $extension
     * @return Response
     */
    public function loadSystemError($response, $data = [], $language = null, $extension = null)
    {        
        $name = $this->resoveErrorPageName(Self::SYSTEM_ERROR_PAGE,$extension);
        $data = array_merge([
            'errors' => $this->getErrors()
        ],$data);

        $response = $this->page->load($response,$name,$data,$language);   

        return $response->withStatus(404); 
    }

    /**
     * Load page not found error page.
     *
     * @param Response $response
     * @param array $data
     * @param string|null $language
     * @param string|null $extension
     * @return Response
     */
    public function loadPageNotFound($response, $data = [], $language = null, $extension = null)
    {        
        $name = $this->resoveErrorPageName(Self::PAGE_NOT_FOUND,$extension);
        $response = $this->page->load($response,$name,$data,$language);   

        return $response->withStatus(404); 
    }

     /**
     * Render page not found 
     *
     * @param array $data
     * @param string|null $language
     * @param string|null $extension
     * @return Component
     */
    public function renderPageNotFound($data = [], $language = null, $extension = null)
    {
        $name = $this->resoveErrorPageName(Self::PAGE_NOT_FOUND,$extension);

        return $this->page->render($name,$data,$language);
    }

    /**
     * Render application error
     *
     * @param array $data
     * @param string|null $language
     * @param string|null $extension
     * @return Component
     */
    public function renderApplicationError($data = [], $language = null, $extension = null)
    {
        $name = $this->resoveErrorPageName(Self::APPLICATION_ERROR_PAGE,$extension);
        
        return $this->page->render($name,$data,$language);
    }

    /**
     * Render system error(s)
     *
     * @param array $data
     * @param string|null $language
     * @param string|null $extension
     * @return Component
     */
    public function renderSystemError($data = [], $language = null, $extension = null)
    {
        $name = $this->resoveErrorPageName(Self::SYSTEM_ERROR_PAGE,$extension);
            
        return $this->page->render($name,['errors' => $data],$language);      
    }
}
