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

use Arikaim\Core\Utils\Curl;

/**
 * Arikaim store
*/
class ArikaimStore 
{   
    /**
     *  Arikaim store host
     */
    const HOST = 'http://work.com/arikaim';
    const SIGNUP_URL =  Self::HOST . '/signup';

    /**
     * User login api url
    */   
    const LOGI_API_URL = '';

    /**
     * Constructor
     */
    public function __construct()
    {   
    }

    /**
     * Fetch packages list 
     *
     * @param string $type
     * @param integer $page
     * @param string $search
     * @return mixed
     */
    public function fetchPackages($type, $page = 1, $search = '')
    {
        $page = (empty($search) == true) ? $page : "/$page";
        $url = Self::HOST . "/api/store/product/list/$type/$search" . $page;
         
        return Curl::get($url);
    }

    /**
     * Get signup url
     */
    public function getSignupUrl()
    {
        return Self::SIGNUP_URL;
    }

    protected function getAccessToken()
    {
        
    }
}
