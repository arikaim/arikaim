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

use Arikaim\Core\Utils\File;

/**
 * Curl wrapper
 */
class Curl 
{   
    const TIMEOUT = 60;

    /**
     * Return true if php curl extension is installed
     *
     * @return boolean
     */
    public static function isInsatlled()
    {
        return extension_loaded('curl');
    }

    /**
     * Create curl 
     *
     * @param string $url
     * @param integer $timeout
     * @param boolean $returnTransfer
     * @return object
     */
    private static function create($url, $timeout = 30, $returnTransfer = true)
    {
        if (Self::isInsatlled() == false) {
            return null;
        }
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL,$url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,$returnTransfer);
        curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,$timeout);

        return $curl;
    }

    /**
     * Run curl command
     *
     * @param object $curl
     * @return mixed
     */
    private static function exec($curl)
    {
        $response = curl_exec($curl);
        curl_close($curl);

        return ($response == false) ? curl_error($curl) : $response;      
    }

    /**
     * Run curl request
     *
     * @param string $url
     * @param string $method
     * @param array $data
     * @param array $headers
     * @param integer $timeout
     * @return mixed
     */
    public static function request($url, $method, array $data = null, array $headers = null, $timeout = Self::TIMEOUT)
    {
        $curl = Self::create($url,$timeout);
        if (empty($curl) == true) {
            return false;
        }

        if (is_array($headers) == true) {
            curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);
        }

        curl_setopt($curl,CURLOPT_CUSTOMREQUEST,$method);
        if (is_array($data) == true) {
            curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
        }

        return Self::exec($curl);
    }

    /**
     * Run POST request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param integer $timeout
     * @return mixed
     */
    public static function post($url, array $data = null, array $headers = null, $timeout = Self::TIMEOUT)
    {
        return Self::request($url,"POST",$data,$headers,$timeout);
    }

    /**
     * Run GET request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param integer $timeout
     * @return mixed
     */
    public static function get($url, array $data = null, array $headers = null, $timeout = Self::TIMEOUT)
    {
        return Self::request($url,"GET",$data,$headers,$timeout);
    }

    /**
     * Run DELETE request.
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param integer $timeout
     * @return mixed
     */
    public static function delete($url, array $data = null, array $headers = null, $timeout = Self::TIMEOUT)
    {
        return Self::request($url,"DELETE",$data,$headers,$timeout);
    }

    /**
     * Run PUT request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param integer $timeout
     * @return mixed
     */
    public static function put($url, array $data = null, array $headers = null, $timeout = Self::TIMEOUT)
    {
        return Self::request($url,"PUT",$data,$headers,$timeout);
    }

    /**
     * Download file
     *
     * @param string $url
     * @param string $destinationPath
     * @return boolean
     */
    public static function downloadFile($url, $destinationPath)
    {
        $writable = File::setWritable($destinationPath);
        if ($writable == false) {
            throw new \Exception("Destination path: $destinationPath is not writable");
            return false;
        }
        $file = fopen($destinationPath, 'w+');

        $curl = Self::create($url);
        curl_setopt($curl,CURLOPT_BINARYTRANSFER,true);
        curl_setopt($curl,CURLOPT_FILE,$file);     
        $result = Self::exec($curl);
        fclose($file);

        if ($result === false) {
            unlink($destinationPath);            
            return $result;
        }

        return File::exists($destinationPath);
    }
}
