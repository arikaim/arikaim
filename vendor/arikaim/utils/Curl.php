<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Utils;

/**
 * Curl wrapper
 */
class Curl 
{   
    const TIMEOUT = 60;

    /**
     * User agent
     *
     * @var string|null
     */
    public static $userAgent = null;

    /**
     * Verbose option
     *
     * @var boolean
     */
    public static $verbose = false;

    /**
     * Response code
     *
     * @var mixed
     */
    private static $responseCode = null;

    /**
     * Return true if php curl extension is installed
     *
     * @return boolean
     */
    public static function isInsatlled(): bool
    {
        return \extension_loaded('curl');
    }

    /**
     * Create curl 
     *
     * @param string $url
     * @param integer $timeout
     * @param boolean $returnTransfer   
     * @return object|null
     */
    private static function create(string $url, $timeout = 30, bool $returnTransfer = true)
    {
        if (Self::isInsatlled() == false) {
            return null;
        }
        $curl = \curl_init();
        \curl_setopt($curl,CURLOPT_URL,$url);
        \curl_setopt($curl,CURLOPT_VERBOSE,Self::$verbose);
        \curl_setopt($curl,CURLOPT_RETURNTRANSFER,$returnTransfer);
        \curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,$timeout);
        if (empty(Self::$userAgent) == false) {
            \curl_setopt($curl,CURLOPT_USERAGENT,Self::$userAgent);
        }
       
        return $curl;
    }

    /**
     * Fet response code
     *
     * @return mixed
     */
    public static function getResponseCode()
    {
        return Self::$responseCode;
    }

    /**
     * Run curl command
     *
     * @param object $curl
     * @return mixed
     */
    private static function exec($curl)
    {
        Self::$responseCode = null;
        $response = \curl_exec($curl);
        $error = ($response === false) ? \curl_error($curl) : null;
        Self::$responseCode = \curl_getinfo($curl,CURLINFO_HTTP_CODE);

        \curl_close($curl);

        return (empty($error) == true) ? $response : $error;      
    }

    /**
     * Run curl request
     *
     * @param string $url
     * @param string $method
     * @param array|string|null $data
     * @param array $headers
     * @param integer $timeout
     * @return mixed
     */
    public static function request(string $url, string $method, $data = null, ?array $headers = null, int $timeout = Self::TIMEOUT)
    {
        $curl = Self::create($url,$timeout);
    
        if (empty($curl) == true) {
            return false;
        }

        \curl_setopt($curl,CURLOPT_CUSTOMREQUEST,$method);

        if (empty($data) == false) {    
            $data = (\is_array($data) == true) ? json_encode($data) : $data;
            $curl = Self::setData($curl,$data,$method);        
            $headers = (\is_array($headers) == true) ? $headers : [];
            $headers = \array_merge($headers,[              
                'Content-Length: ' . \strlen($data)
            ]);              
        }
    
        if (\is_array($headers) == true) {
            \curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);
        }

        return Self::exec($curl);
    }

    /**
     * Set post data
     *
     * @param object $curl
     * @param mixed $data
     * @param string $method
     * @return object
     */
    public static function setData($curl, $data, string $method = 'POST')
    {       
        if (empty($data) == true) {
            return $curl;
        }

        if ($method == 'POST' || $method == 'PUT') {
            \curl_setopt($curl,CURLOPT_POST,1);
            \curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
        }

        return $curl;
    } 

    /**
     * Run POST request
     *
     * @param string $url
     * @param array|string|null $data
     * @param array|null $headers
     * @param integer $timeout
     * @return mixed
     */
    public static function post(string $url, $data = null, ?array $headers = null, int $timeout = Self::TIMEOUT)
    {
        return Self::request($url,'POST',$data,$headers,$timeout);
    }

    /**
     * Run GET request
     *
     * @param string $url
     * @param array|null $data
     * @param array|null $headers
     * @param integer $timeout
     * @return mixed
     */
    public static function get(string $url, ?array $data = null, ?array $headers = null, int $timeout = Self::TIMEOUT)
    {
        return Self::request($url,'GET',$data,$headers,$timeout);
    }

    /**
     * Run DELETE request.
     *
     * @param string $url
     * @param array|null $data
     * @param array|null $headers
     * @param integer $timeout
     * @return mixed
     */
    public static function delete(string $url, ?array $data = null, ?array $headers = null, $timeout = Self::TIMEOUT)
    {
        return Self::request($url,'DELETE',$data,$headers,$timeout);
    }

    /**
     * Run PUT request
     *
     * @param string $url
     * @param array|null $data
     * @param array|null $headers
     * @param integer $timeout
     * @return mixed
     */
    public static function put(string $url, ?array $data = null, ?array $headers = null, int $timeout = Self::TIMEOUT)
    {
        return Self::request($url,'PUT',$data,$headers,$timeout);
    }

    /**
     * Download file
     *
     * @param string $url
     * @param string $fileName   
     * @param string|null $method
     * @param array|null $headers
     * @return boolean
     */
    public static function downloadFile(string $url, string $fileName, ?string $method = null, ?array $headers = null): bool
    {             
        $file = \fopen($fileName,'w+');
        $curl = Self::create($url);
          
        $curl = Self::downloadFileInit($curl,$method,$headers);
        \curl_setopt($curl,CURLOPT_FILE,$file);   

        $result = Self::exec($curl,60);
       
        \fclose($file);

        return ($result !== false);                
    }

    /**
     * Get file content
     *
     * @param string $url
     * @param string|null $method
     * @param array|null $headers
     * @return mixed
     */
    public static function getFileContent(string $url, ?string $method = null, ?array $headers = null)
    {        
        $curl = Self::create($url);     
        $curl = Self::downloadFileInit($curl,$method,$headers);

        return Self::exec($curl,60);       
    } 

    /**
     * Init curl for file download
     *
     * @param object $curl
     * @param string|null $method
     * @param array|null $headers
     * @return object
     */
    public static function downloadFileInit($curl, ?string $method = null, ?array $headers = null)
    {
        $method = $method ?? 'GET';

        \curl_setopt($curl,CURLOPT_VERBOSE,0);
        \curl_setopt($curl,CURLOPT_BINARYTRANSFER,true);
        \curl_setopt($curl,CURLOPT_FOLLOWLOCATION,true);
        \curl_setopt($curl,CURLOPT_AUTOREFERER,false);
        \curl_setopt($curl,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);     
        \curl_setopt($curl,CURLOPT_CUSTOMREQUEST,$method);
        \curl_setopt($curl,CURLOPT_HEADER,0);
        \curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);

        if (\is_array($headers) == true) {
            \curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);
        }

        return $curl;
    }
}
