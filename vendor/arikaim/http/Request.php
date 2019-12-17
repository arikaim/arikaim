<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Http;

/**
 * Request helpers
 */
class Request 
{  
    /**
     * Return content type 
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param string $default
     * @return void
     */
    public static function getContentType($request, $default = 'text/html')
    {        
        $content = $request->getHeaderLine('Content-Type');
        if (empty($content) == true) {
            $accept = $request->getHeaderLine('Accept');
            $tokens = \explode(',',$accept);
            if (empty($tokens[0]) == false) {
                return $tokens[0];
            }
        }

        return (empty($content) == true) ? $default : $content;
    }

    /**
     * Return true if content type is json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return boolean
     */
    public static function isJsonContentType($request)
    {
        // try with content type
        $header = Self::getContentType($request);             
        if (strpos($header,'json') !== false) {
            return true;
        }
        
        // request method
        $method = $request->getMethod();
        if ($method != "GET") {
            return true;
        }
      
        return false;
    }
    
    /**
     * Return true if content type is xml
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return boolean
     */
    public static function isXmlContentType($request)
    {
        $content = Self::getContentType($request);

        return (substr($content,-3) == 'xml') ? true : false;
    }

    /**
     * Return true if content type is html
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return boolean
     */
    public static function isHtmlContentType($request)
    {
        $content = Self::getContentType($request);

        return (substr($content,-4) == 'html') ? true : false;
    }

    /**
     * Parse accept header
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return array
     */
    public static function parseAcceptHeader($request)
    {
        $accept = $request->getHeaderLine('Accept');
        $parts = explode(';',$accept);

        return explode(',',$parts[0]);
    }

    /**
     * Return true if request accept json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return boolean
     */
    public static function acceptJson($request)
    {
        $contentTypes = Self::parseAcceptHeader($request);
        foreach ($contentTypes as $item) {
            if (substr($item,-4) == 'json') {
                return true;
            }
        }

        return false;
    }

    /**
     * Return true if request accept xml
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return boolean
     */
    public static function acceptXml($request)
    {
        $contentTypes = Self::parseAcceptHeader($request);
        foreach ($contentTypes as $item) {
            if (substr($item,-3) == 'xml') {
                return true;
            }
        }

        return false;
    }

    /**
     * Get browser name
     *
     * @return string
     */
    public static function getBrowserName()
    {      
        $userAgent =  " " . strtolower($_SERVER['HTTP_USER_AGENT']);
        switch ($userAgent) {
            case (strpos($userAgent,'opera') != false):
                return 'Opera';                
            case (strpos($userAgent,'edge') != false):
                return 'Edge';
            case (strpos($userAgent,'firefox') != false):
                return 'Firefox';    
            case (strpos($userAgent,'chrome') != false):
                return 'Chrome';  
            case (strpos($userAgent,'safari') != false):
                return 'Safari';    
            case (strpos($userAgent,'msie') != false):
                return 'Internet Explorer';  
            case (strpos($userAgent,'mobile') != false):
                return 'Mobile Browser'; 
            case (strpos($userAgent,'android') != false):
                return 'Mobile Browser';                
            default: 
                return null;
        }       
    }
}
